<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Services\WhatsAppOrderService;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{
    /**
     * Display list of dispatched orders & bulk messaging interface.
     */
    public function index(Request $request)
    {
        $shop = Shop::current();
        $status = WhatsAppOrderService::checkServerStatus();

        // ONLY orders with completed & dispatched status
        $query = Order::where(function ($q) {
                $q->where('status', 'dispatched')
                  ->orWhere('payment_status', 'dispatched')
                  ->orWhereNotNull('lr_number');
            })
            ->orderByDesc('dispatched_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $raw = (string) $request->input('search');
            // Allow only letters (including Tamil), digits, spaces, #, ., -, +, /
            $cleanSearch = trim(preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}\s#\.\-\/+]/u', '', $raw));
            $cleanSearch = preg_replace('/\s+/', ' ', $cleanSearch);

            // Require at least one alphanumeric character to avoid empty or symbol-only searches (e.g. "---", "###")
            $alphaOnly = preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}]/u', '', $cleanSearch);

            if ($cleanSearch !== '' && mb_strlen($alphaOnly) >= 1) {
                $escapedSearch = addcslashes($cleanSearch, '%_');
                $query->where(function ($q) use ($escapedSearch) {
                    $q->where('name', 'like', "%{$escapedSearch}%")
                        ->orWhere('order_number', 'like', "%{$escapedSearch}%")
                        ->orWhere('phone1', 'like', "%{$escapedSearch}%")
                        ->orWhere('phone2', 'like', "%{$escapedSearch}%")
                        ->orWhere('city', 'like', "%{$escapedSearch}%")
                        ->orWhere('destination_hub', 'like', "%{$escapedSearch}%")
                        ->orWhere('lr_number', 'like', "%{$escapedSearch}%")
                        ->orWhere('parcel_service_name', 'like', "%{$escapedSearch}%");
                });
            }
        }

        if ($request->filled('hub')) {
            $query->where('destination_hub', trim($request->input('hub')));
        }

        if ($request->filled('transport')) {
            $query->where('parcel_service_name', trim($request->input('transport')));
        }

        $dispatchedOrders = $query->paginate(10)->withQueryString();
        $dispatchedQuery = fn () => Order::where(function ($q) {
            $q->where('status', 'dispatched')
              ->orWhere('payment_status', 'dispatched')
              ->orWhereNotNull('lr_number');
        });
        $totalDispatchedCount = $dispatchedQuery()->count();

        // Get unique hubs & transport names for quick filters
        $distinctHubs = $dispatchedQuery()
            ->whereNotNull('destination_hub')
            ->where('destination_hub', '!=', '')
            ->distinct()
            ->pluck('destination_hub')
            ->sort()
            ->values();

        $distinctTransports = $dispatchedQuery()
            ->whereNotNull('parcel_service_name')
            ->where('parcel_service_name', '!=', '')
            ->distinct()
            ->pluck('parcel_service_name')
            ->sort()
            ->values();

        return view('admin.messaging.index', compact(
            'dispatchedOrders',
            'totalDispatchedCount',
            'status',
            'shop',
            'distinctHubs',
            'distinctTransports'
        ));
    }

    /**
     * Send bulk WhatsApp broadcast message to selected dispatched customers.
     */
    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:3|max:2000',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
        ], [
            'message.required' => 'Please enter the message content to broadcast.',
            'order_ids.required' => 'Please select at least one dispatched order customer.',
            'order_ids.min' => 'Please select at least one dispatched order customer.',
        ]);

        $shop = Shop::current();

        // Fetch selected orders that are STRICTLY dispatched
        $orders = Order::whereIn('id', $validated['order_ids'])
            ->where(function ($q) {
                $q->where('status', 'dispatched')
                  ->orWhere('payment_status', 'dispatched')
                  ->orWhereNotNull('lr_number');
            })
            ->get();

        if ($orders->isEmpty()) {
            return back()->withErrors(['message' => 'No valid dispatched orders found for the selection.']);
        }

        $successCount = 0;
        $failCount = 0;
        $failures = [];

        foreach ($orders as $order) {
            $personalizedMessage = self::personalizeMessage($validated['message'], $order, $shop);
            $res = WhatsAppOrderService::sendDirectMessage($order->phone1, $personalizedMessage);

            if (!empty($res['success'])) {
                $successCount++;
            } else {
                $failCount++;
                $failures[] = "#{$order->order_number} ({$order->name}): " . ($res['error'] ?? 'Failed');
            }

            // Short pause (250ms) to avoid hammering the WhatsApp session
            usleep(250000);
        }

        \App\Services\AuditLogger::log('whatsapp_sent', 'bulk_messaging', "Sent bulk broadcast to {$successCount} dispatched customers (Failed: {$failCount})", recordName: "Bulk Broadcast ({$successCount} sent)");

        $statusMsg = "Broadcast complete: {$successCount} message(s) sent successfully!";
        if ($failCount > 0) {
            $statusMsg .= " ({$failCount} failed: " . implode(', ', array_slice($failures, 0, 3)) . ")";
            return back()->with('status', $statusMsg)->withErrors(['send_errors' => $failures]);
        }

        return back()->with('status', $statusMsg);
    }

    /**
     * Send individual WhatsApp message to a specific dispatched customer.
     */
    public function sendIndividual(Request $request, Order $order)
    {
        if (!$order->isDispatched()) {
            return back()->withErrors(['message' => "Order #{$order->order_number} is not in Dispatched status."]);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:3|max:2000',
        ]);

        $shop = Shop::current();
        $personalizedMessage = self::personalizeMessage($validated['message'], $order, $shop);
        $res = WhatsAppOrderService::sendDirectMessage($order->phone1, $personalizedMessage);

        if (!empty($res['success'])) {
            \App\Services\AuditLogger::log('whatsapp_sent', 'bulk_messaging', "Sent individual WhatsApp update to {$order->name} ({$order->phone1}) for Order #{$order->order_number}", record: $order);
            return back()->with('status', "WhatsApp message sent to {$order->name} ({$order->phone1}) successfully! (ID: {$res['messageId']})");
        }

        return back()->withErrors(['message' => "Failed to send message to {$order->phone1}: " . ($res['error'] ?? 'WhatsApp gateway error')]);
    }

    /**
     * Helper to replace dynamic placeholders in message template.
     */
    public static function personalizeMessage(string $template, Order $order, Shop $shop): string
    {
        $invoiceUrl = route('order.public_invoice', [
            'orderNumber' => $order->order_number,
            'token' => $order->getInvoiceSignature(),
        ]);

        $replacements = [
            '{customer_name}' => trim($order->name),
            '{order_number}' => $order->order_number,
            '{phone}' => $order->phone1,
            '{lr_number}' => $order->lr_number ?: 'N/A',
            '{transport_name}' => $order->parcel_service_name ?: 'Sivakasi Transport',
            '{transport_phone}' => $order->transport_phone ?: ($shop->phone ?: ''),
            '{destination_hub}' => $order->destination_hub ?: ($order->city ?: 'Hub'),
            '{parcel_count}' => $order->parcel_count ?: '1',
            '{city}' => $order->city ?: '',
            '{total_amount}' => '₹' . number_format($order->total_amount, 2),
            '{shop_name}' => $shop->name ?: 'Guru Crackers',
            '{shop_phone}' => $shop->phone ?: '',
            '{invoice_url}' => $invoiceUrl,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
