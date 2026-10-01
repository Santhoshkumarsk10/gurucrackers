<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Services\WhatsAppOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::withCount('items')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter === 'paid') {
                $query->where('payment_status', 'paid');
            } elseif ($statusFilter === 'unpaid') {
                $query->where('payment_status', 'pending');
            } else {
                $query->where('status', $statusFilter);
            }
        }

        if ($request->filled('search')) {
            $raw = (string) $request->search;
            $s = trim(preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}\s\.\-\/]/u', '', $raw));
            $s = preg_replace('/\-+/', '-', $s);
            $s = trim($s, '-');

            $alphaOnly = preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}]/u', '', $s);
            if (mb_strlen($alphaOnly) >= 2) {
                $query->where(function ($q) use ($s) {
                    $q->where('order_number', 'like', "%{$s}%")
                      ->orWhere('name', 'like', "%{$s}%")
                      ->orWhere('phone1', 'like', "%{$s}%")
                      ->orWhere('city', 'like', "%{$s}%");
                });
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        $statusCounts = Order::selectRaw("COALESCE(status, 'pending') as status_key, count(*) as total")
            ->groupBy('status_key')
            ->pluck('total', 'status_key')
            ->toArray();
        $statusCounts['paid'] = Order::where('payment_status', 'paid')->count();
        $totalOrdersCount = Order::count();

        return view('admin.orders.index', compact('orders', 'statusCounts', 'totalOrdersCount'));
    }

    public function show(Order $order)
    {
        if (is_null($order->admin_read_at)) {
            $order->update(['admin_read_at' => now()]);
        }

        $order->load(['items.product']);
        $shop = Shop::current();

        $customerInvoiceWhatsAppUrl = WhatsAppOrderService::getCustomerInvoiceWhatsAppUrl($order, $shop);
        $adminPackingListWhatsAppUrl = WhatsAppOrderService::getAdminPackingListWhatsAppUrl($order, $shop);
        $paymentConfirmationWhatsAppUrl = WhatsAppOrderService::getPaymentConfirmationWhatsAppUrl($order, $shop);
        $dispatchLrWhatsAppUrl = WhatsAppOrderService::getDispatchLrWhatsAppUrl($order, $shop);

        $customerInvoiceText = WhatsAppOrderService::formatCustomerInvoice($order, $shop);
        $adminPackingListText = WhatsAppOrderService::formatAdminPackingList($order, $shop);
        $dispatchLrText = WhatsAppOrderService::formatDispatchLrMessage($order, $shop);

        $gatewayStatus = WhatsAppOrderService::checkServerStatus();

        return view('admin.orders.show', compact(
            'order',
            'shop',
            'customerInvoiceWhatsAppUrl',
            'adminPackingListWhatsAppUrl',
            'paymentConfirmationWhatsAppUrl',
            'dispatchLrWhatsAppUrl',
            'customerInvoiceText',
            'adminPackingListText',
            'dispatchLrText',
            'gatewayStatus'
        ));
    }

    /**
     * Update order payment or order status.
     * Specification:
     * - Initial: status = 'pending', payment_status = 'pending'
     * - When payment marked as paid: payment_status = 'paid', status = 'confirmed' (if was pending)
     * - When packed: status = 'packed'
     * - When dispatched: status = 'dispatched' (via dispatchOrder)
     * - payment_status strictly: 'pending' or 'paid'
     */
    public function updateStatus(Request $request, Order $order)
    {
        $messages = [];
        $wasPaid = $order->isPaid();

        // 1. Payment status update
        if ($request->has('payment_status')) {
            $reqPay = $request->input('payment_status');
            if (in_array($reqPay, ['pending', 'paid'])) {
                $order->payment_status = $reqPay;

                // When admin marks as paid, if status is pending, advance status to confirmed!
                if ($reqPay === 'paid' && $order->status === 'pending') {
                    $order->status = 'confirmed';
                    $messages[] = "Payment marked as PAID & Order CONFIRMED!";
                } else {
                    $messages[] = "Payment status updated to " . strtoupper($reqPay);
                }
            }
        }

        // 2. Order lifecycle status update
        if ($request->has('status')) {
            $reqStatus = $request->input('status');
            $allowedStatuses = ['pending', 'confirmed', 'packed', 'dispatched', 'cancelled'];
            if (in_array($reqStatus, $allowedStatuses)) {
                $order->status = $reqStatus;
                if ($reqStatus === 'dispatched' && is_null($order->dispatched_at)) {
                    $order->dispatched_at = now();
                }
                $messages[] = "Order status updated to " . strtoupper($reqStatus);
            }
        }

        if ($request->filled('payment_notes')) {
            $order->payment_notes = $request->input('payment_notes');
        }

        $order->save();

        // If newly marked paid: automatically send official Invoice PDF to WhatsApp
        if (!$wasPaid && $order->isPaid()) {
            $shop = Shop::current();
            $waRes = WhatsAppOrderService::sendPaymentConfirmationWithPdf($order, $shop);
            if ($waRes['success']) {
                $messages[] = "Official Invoice PDF & confirmation delivered to Customer WhatsApp (" . $order->phone1 . ") via Bot!";
            }
        }

        $msg = !empty($messages) ? implode(' | ', $messages) : "Order #{$order->order_number} updated.";
        return back()->with('status', $msg);
    }

    /**
     * Dispatch order via Cracker Parcel Service (A1, MSS, etc.) and send LR details.
     */
    public function dispatchOrder(Request $request, Order $order)
    {
        // Enforce progression: cannot dispatch unless confirmed, packed, or already dispatched
        if (!$order->isConfirmed() && !$order->isPacked() && !$order->isDispatched()) {
            return back()->with('error', "Cannot dispatch order yet! Order must be in 'Confirmed' or 'Packed' status first.");
        }

        if ($request->filled('transport_phone')) {
            $cleanTp = preg_replace('/[^0-9]/', '', (string) $request->input('transport_phone'));
            if (strlen($cleanTp) === 12 && str_starts_with($cleanTp, '91')) {
                $cleanTp = substr($cleanTp, 2);
            } elseif (strlen($cleanTp) === 11 && str_starts_with($cleanTp, '0')) {
                $cleanTp = substr($cleanTp, 1);
            } elseif (strlen($cleanTp) > 10) {
                $cleanTp = substr($cleanTp, -10);
            }
            $request->merge(['transport_phone' => !empty($cleanTp) ? $cleanTp : null]);
        }

        $validated = $request->validate([
            'parcel_service_name' => 'required|string|max:255',
            'custom_parcel_service' => 'nullable|string|max:255',
            'lr_number' => 'required|string|max:100',
            'parcel_count' => 'nullable|string|max:50',
            'dispatch_date' => 'nullable|date',
            'transport_phone' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'destination_hub' => 'nullable|string|max:100',
            'delivery_charges' => 'nullable|numeric|min:0|max:999999',
            'lr_receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB
            'dispatch_notes' => 'nullable|string|max:1000',
            'send_whatsapp' => 'nullable|boolean',
        ], [
            'transport_phone.regex' => 'Transport contact phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'delivery_charges.numeric' => 'Delivery charges must be a valid number.',
        ]);

        $parcelService = $validated['parcel_service_name'];
        if ($parcelService === 'Other' && !empty($validated['custom_parcel_service'])) {
            $parcelService = $validated['custom_parcel_service'];
        }

        $imagePath = $order->lr_receipt_image;
        if ($request->hasFile('lr_receipt_image')) {
            // Delete old image if exists
            if ($imagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('lr_receipt_image')->store('lr_receipts', 'public');
        }

        $order->update([
            'parcel_service_name' => $parcelService,
            'lr_number' => $validated['lr_number'],
            'parcel_count' => $validated['parcel_count'] ?: '1 Box',
            'dispatch_date' => $validated['dispatch_date'] ?: now()->toDateString(),
            'transport_phone' => $validated['transport_phone'] ?? null,
            'destination_hub' => $validated['destination_hub'] ?: $order->city,
            'delivery_charges' => $request->filled('delivery_charges') ? (float) $request->input('delivery_charges') : null,
            'lr_receipt_image' => $imagePath,
            'dispatch_notes' => $validated['dispatch_notes'] ?? null,
            'status' => 'dispatched',
            'payment_status' => $order->payment_status ?: 'paid',
            'dispatched_at' => $order->dispatched_at ?: now(),
        ]);

        $shop = Shop::current();
        $message = "Order #{$order->order_number} marked as DISPATCHED via {$parcelService} (LR: {$order->lr_number})!";

        // Send automated WhatsApp dispatch notification with LR slip & PDF via Bot
        $dispatchResult = WhatsAppOrderService::sendDispatchNotification($order, $shop);
        if ($dispatchResult['success']) {
            $message .= " Dispatch notification, LR receipt photo & invoice PDF successfully delivered to Customer WhatsApp ({$order->phone1}) via Bot.";
        } elseif ($dispatchResult['error']) {
            $message .= " (WhatsApp Notice: {$dispatchResult['error']})";
        }

        return back()->with('status', $message);
    }

    /**
     * Send Customer Text Invoice directly via WhatsApp Bot (no manual web link).
     */
    public function sendTextInvoice(Order $order)
    {
        $shop = Shop::current();
        $result = WhatsAppOrderService::sendTextInvoiceDirect($order, $shop);

        if ($result['success']) {
            return back()->with('status', "Customer Text Invoice successfully delivered to {$order->phone1} via WhatsApp Bot!");
        }

        if (!empty($result['notOnWhatsApp'])) {
            return back()->with('error', "Customer number ({$order->phone1}) is NOT registered on WhatsApp! Please call the customer directly using the 'Call Customer' button.");
        }

        return back()->with('error', "Could not send Text Invoice via WhatsApp Bot: " . ($result['error'] ?? 'Service unavailable'));
    }

    /**
     * Send Warehouse Packing List directly to Warehouse/Shop WhatsApp via Bot (no manual web link).
     */
    public function sendPackingList(Order $order)
    {
        $shop = Shop::current();
        $targetPhone = $shop->whatsapp_phone ?: $shop->phone;
        $result = WhatsAppOrderService::sendPackingListDirect($order, $shop);

        if ($result['success']) {
            return back()->with('status', "Packing List checklist successfully delivered to Warehouse WhatsApp ({$targetPhone}) via Bot!");
        }

        return back()->with('error', "Could not send Packing List via WhatsApp Bot: " . ($result['error'] ?? 'Please verify shop WhatsApp phone'));
    }

    /**
     * Send or re-send official Invoice PDF to Customer WhatsApp via Bot.
     */
    public function sendInvoicePdf(Order $order)
    {
        $shop = Shop::current();
        $result = WhatsAppOrderService::sendPaymentConfirmationWithPdf($order, $shop);

        if ($result['success']) {
            return back()->with('status', "Official Invoice PDF successfully delivered to Customer WhatsApp ({$order->phone1}) via Bot!");
        }

        if (!empty($result['notOnWhatsApp'])) {
            return back()->with('error', "Customer number ({$order->phone1}) is NOT registered on WhatsApp! Please call the customer directly using the 'Call Customer' button.");
        }

        return back()->with('error', "Could not send Invoice PDF via WhatsApp Bot: " . ($result['error'] ?? 'Service unavailable'));
    }

    /**
     * Send or re-send LR Dispatch Notification to Customer WhatsApp via Bot.
     */
    public function sendDispatchLr(Order $order)
    {
        if (empty($order->lr_number)) {
            return back()->with('error', "Cannot send LR details yet! Please enter the Parcel Service & LR Receipt Number in the form below first.");
        }

        $shop = Shop::current();
        $result = WhatsAppOrderService::sendDispatchNotification($order, $shop);

        if ($result['success']) {
            return back()->with('status', "LR Tracking & Dispatch details successfully delivered to Customer WhatsApp ({$order->phone1}) via Bot!");
        }

        if (!empty($result['notOnWhatsApp'])) {
            return back()->with('error', "Customer number ({$order->phone1}) is NOT registered on WhatsApp! Please call the customer directly using the 'Call Customer' button.");
        }

        return back()->with('error', "Could not send LR details via WhatsApp Bot: " . ($result['error'] ?? 'Service unavailable'));
    }

    /**
     * Generate invoice PDF for packing.
     */
    public function invoice(Order $order)
    {
        $order->load(['items.product']);
        $shop = Shop::current();

        \App\Services\AuditLogger::logExport('orders', 'PDF', "Downloaded Order #{$order->order_number} Invoice PDF");

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order', 'shop'))
            ->setPaper('a4');

        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Generate Warehouse Packing Checklist PDF.
     */
    public function packingChecklist(Order $order)
    {
        $order->load(['items.product.category']);
        $shop = Shop::current();

        \App\Services\AuditLogger::logExport('orders', 'PDF', "Downloaded Order #{$order->order_number} Packing Checklist PDF");

        $pdf = Pdf::loadView('admin.orders.packing_checklist', compact('order', 'shop'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('PackingList-' . $order->order_number . '.pdf');
    }

    /**
     * Real-time polling endpoint to check for incoming new orders.
     */
    public function checkNewOrders(Request $request)
    {
        $lastOrderId = (int) $request->query('last_order_id', 0);

        $newOrders = [];
        if ($lastOrderId > 0) {
            $newOrders = Order::withCount('items')
                ->where('id', '>', $lastOrderId)
                ->orderBy('id', 'asc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'name' => $order->name,
                        'phone' => $order->phone1,
                        'city' => $order->city,
                        'total_amount' => (float) $order->total_amount,
                        'total_amount_formatted' => '₹' . number_format($order->total_amount, 2),
                        'items_count' => $order->items_count,
                        'time' => $order->created_at->format('h:i A'),
                        'time_ago' => $order->created_at->diffForHumans(),
                        'view_url' => route('admin.orders.show', $order),
                    ];
                });
        }

        $recentOrders = Order::withCount('items')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'name' => $order->name,
                    'phone' => $order->phone1,
                    'city' => $order->city,
                    'total_amount_formatted' => '₹' . number_format($order->total_amount, 2),
                    'status' => $order->status ?? 'pending',
                    'payment_status' => $order->payment_status ?? 'pending',
                    'items_count' => $order->items_count,
                    'time' => $order->created_at->format('h:i A'),
                    'time_ago' => $order->created_at->diffForHumans(),
                    'view_url' => route('admin.orders.show', $order),
                    'is_read' => !is_null($order->admin_read_at),
                ];
            });

        $latestId = Order::max('id') ?? 0;
        $pendingCount = Order::where('status', 'pending')->count();
        $unreadCount = Order::whereNull('admin_read_at')->count();

        return response()->json([
            'success' => true,
            'latest_order_id' => $latestId,
            'new_orders' => $newOrders,
            'recent_orders' => $recentOrders,
            'pending_count' => $pendingCount,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all unread order notifications as read.
     */
    public function markAllNotificationsRead()
    {
        Order::whereNull('admin_read_at')->update(['admin_read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Mark a single order notification as read.
     */
    public function markNotificationRead(Order $order)
    {
        if (is_null($order->admin_read_at)) {
            $order->update(['admin_read_at' => now()]);
        }

        $unreadCount = Order::whereNull('admin_read_at')->count();

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'unread_count' => $unreadCount,
        ]);
    }
}
