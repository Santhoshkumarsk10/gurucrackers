<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Services\WhatsAppOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    private function corsJson(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN',
        ]);
    }
    /**
     * Get store initialization data (shop settings & banners).
     */
    public function init(): JsonResponse
    {
        $shop = Shop::current();
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'image_url' => $banner->image ? asset('storage/' . $banner->image) : null,
                    'link' => $banner->link,
                ];
            });

        return $this->corsJson([
            'success' => true,
            'shop' => [
                'name' => $shop->name,
                'tagline' => $shop->tagline,
                'phone' => $shop->phone,
                'whatsapp' => $shop->whatsapp,
                'email' => $shop->email,
                'address' => $shop->address,
                'city' => $shop->city,
                'state' => $shop->state,
                'pincode' => $shop->pincode,
                'min_order_amount' => (float) $shop->min_order_amount,
                'discount_percent' => (float) ($shop->discount_percent ?? 0),
                'upi_id' => $shop->upi_id,
                'upi_name' => $shop->upi_name,
                'logo_url' => $shop->logo ? asset('storage/' . $shop->logo) : null,
                'upi_qr_url' => $shop->upi_qr_image ? asset('storage/' . $shop->upi_qr_image) : null,
            ],
            'banners' => $banners,
        ]);
    }

    /**
     * Get product catalog grouped by categories.
     */
    public function catalog(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('id');
            }])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products' => $category->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'category_id' => $product->category_id,
                            'name' => $product->name,
                            'tamil_name' => $product->tamil_name,
                            'unit' => $product->unit ?? 'Box',
                            'actual_rate' => (float) $product->actual_rate,
                            'discount_percent' => (float) $product->discount_percent,
                            'net_rate' => (float) $product->net_rate,
                            'image_url' => $product->image_url,
                            'is_active' => (bool) $product->is_active,
                        ];
                    }),
                ];
            });

        return $this->corsJson([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Submit a customer order from the mobile app.
     */
    public function store(Request $request): JsonResponse
    {
        // Sanitize phone inputs
        $rawPhone1 = (string) $request->input('phone1', '');
        $cleanPhone1 = preg_replace('/[^0-9]/', '', $rawPhone1);
        if (strlen($cleanPhone1) === 12 && str_starts_with($cleanPhone1, '91')) {
            $cleanPhone1 = substr($cleanPhone1, 2);
        } elseif (strlen($cleanPhone1) === 11 && str_starts_with($cleanPhone1, '0')) {
            $cleanPhone1 = substr($cleanPhone1, 1);
        } elseif (strlen($cleanPhone1) > 10) {
            $cleanPhone1 = substr($cleanPhone1, -10);
        }

        $rawPhone2 = (string) $request->input('phone2', '');
        $cleanPhone2 = null;
        if (!empty($rawPhone2)) {
            $digits2 = preg_replace('/[^0-9]/', '', $rawPhone2);
            if (strlen($digits2) === 12 && str_starts_with($digits2, '91')) {
                $digits2 = substr($digits2, 2);
            } elseif (strlen($digits2) === 11 && str_starts_with($digits2, '0')) {
                $digits2 = substr($digits2, 1);
            } elseif (strlen($digits2) > 10) {
                $digits2 = substr($digits2, -10);
            }
            $cleanPhone2 = !empty($digits2) ? $digits2 : null;
        }

        $request->merge([
            'phone1' => $cleanPhone1,
            'phone2' => $cleanPhone2,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[a-zA-Z\s.]+$/'],
            'phone1' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'phone2' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'delivery_address' => ['required', 'string', 'min:5', 'max:250', 'regex:/^[^<>{}[\]~^$*;\"\'!?=+\\\\|%]+$/'],
            'city' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z\s.\-]+$/'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'products' => 'required',
        ], [
            'name.required' => 'Customer full name is required.',
            'name.regex' => 'Customer name can only contain letters, spaces, and dots.',
            'phone1.required' => 'Primary mobile number is required.',
            'phone1.regex' => 'Primary mobile number must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'phone2.regex' => 'Alternate mobile number must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'delivery_address.required' => 'Delivery address is required.',
            'delivery_address.regex' => 'Delivery address contains invalid special characters.',
            'city.required' => 'City / Town name is required.',
            'city.regex' => 'City name can only contain letters, spaces, dots, and hyphens.',
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'Pincode must be exactly 6 digits.',
            'products.required' => 'Please select at least one product with quantity.',
        ]);

        // Normalize products whether submitted as [{product_id: 1, qty: 2}] or {"1": {"qty": 2}}
        $rawProducts = $request->input('products');
        $normalizedSelected = collect();

        if (is_array($rawProducts)) {
            foreach ($rawProducts as $key => $val) {
                if (is_array($val) && isset($val['qty'])) {
                    $pid = isset($val['product_id']) ? (int) $val['product_id'] : (int) $key;
                    $qty = (int) $val['qty'];
                    if ($pid > 0 && $qty > 0) {
                        if ($qty > 20) {
                            return $this->corsJson([
                                'success' => false,
                                'message' => 'Maximum 20 units allowed per item.',
                            ], 422);
                        }
                        $normalizedSelected->put($pid, ['qty' => $qty]);
                    }
                } elseif (is_numeric($val) && (int) $val > 0) {
                    $qty = (int) $val;
                    if ($qty > 20) {
                        return $this->corsJson([
                            'success' => false,
                            'message' => 'Maximum 20 units allowed per item.',
                        ], 422);
                    }
                    $normalizedSelected->put((int) $key, ['qty' => $qty]);
                }
            }
        }

        if ($normalizedSelected->isEmpty()) {
            return $this->corsJson([
                'success' => false,
                'message' => 'Please select at least one product with quantity.',
            ], 422);
        }

        // Duplicate order check within 120 seconds
        $recentOrder = Order::with('items')
            ->where('phone1', $validated['phone1'])
            ->where('created_at', '>=', now()->subSeconds(120))
            ->latest('id')
            ->first();

        if ($recentOrder) {
            $recentMap = $recentOrder->items->pluck('quantity', 'product_id')->toArray();
            $currentMap = $normalizedSelected->mapWithKeys(fn ($item, $pid) => [(int) $pid => (int) $item['qty']])->toArray();
            ksort($recentMap);
            ksort($currentMap);

            if ($recentMap == $currentMap) {
                $shop = Shop::current();
                return $this->corsJson([
                    'success' => true,
                    'message' => 'Your order has already been received! Showing existing order.',
                    'duplicate_prevented' => true,
                    'order_number' => $recentOrder->order_number,
                    'total_amount' => (float) $recentOrder->total_amount,
                    'upi_url' => $shop->getUpiPaymentUrl($recentOrder->total_amount, $recentOrder->order_number),
                ]);
            }
        }

        $order = DB::transaction(function () use ($validated, $normalizedSelected, $request) {
            $order = Order::create([
                'order_number' => 'GC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'name' => $validated['name'],
                'phone1' => $validated['phone1'],
                'phone2' => $validated['phone2'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'city' => $validated['city'],
                'state' => $request->input('state', 'Tamil Nadu') ?: 'Tamil Nadu',
                'pincode' => $validated['pincode'],
                'total_amount' => 0,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            $total = 0;
            $productIds = $normalizedSelected->keys()->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($normalizedSelected as $productId => $item) {
                $product = $products->get($productId);
                if (!$product) {
                    continue;
                }

                $qty = (int) $item['qty'];
                $lineTotal = $product->net_rate * $qty;
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->net_rate,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        // Trigger automated WhatsApp notification
        $shop = Shop::current();
        $autoDispatch = WhatsAppOrderService::sendAutomatedOrderInvoice($order, $shop);

        $upiUrl = $shop->getUpiPaymentUrl($order->total_amount, $order->order_number);
        $paymentScreenshotWhatsAppUrl = WhatsAppOrderService::getPaymentScreenshotWhatsAppUrl($order, $shop);
        $customerInvoiceWhatsAppUrl = WhatsAppOrderService::getCustomerInvoiceWhatsAppUrl($order, $shop);

        return $this->corsJson([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_number' => $order->order_number,
            'total_amount' => (float) $order->total_amount,
            'upi_url' => $upiUrl,
            'payment_screenshot_whatsapp_url' => $paymentScreenshotWhatsAppUrl,
            'customer_invoice_whatsapp_url' => $customerInvoiceWhatsAppUrl,
            'whatsapp_sent' => $autoDispatch['customer_sent'] ?? false,
            'order' => $order->load('items'),
        ]);
    }

    /**
     * Track orders by phone number or order number.
     */
    public function track(Request $request): JsonResponse
    {
        $queryInput = trim((string) $request->input('query', ''));
        $phoneInput = trim((string) $request->input('phone', ''));
        $orderNumberInput = trim((string) $request->input('order_number', ''));

        if (empty($orderNumberInput) && empty($phoneInput) && !empty($queryInput)) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $queryInput);
            if (strlen($cleanDigits) === 10 && !str_starts_with(strtoupper($queryInput), 'GC-')) {
                $phoneInput = $cleanDigits;
            } else {
                $orderNumberInput = $queryInput;
            }
        }

        $cleanOrderNumber = trim(preg_replace('/[^a-zA-Z0-9\-]/', '', $orderNumberInput));
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);

        if (empty($cleanOrderNumber) && empty($cleanPhone)) {
            return $this->corsJson([
                'success' => false,
                'message' => 'Please provide an Order Number (e.g. GC-...) or 10-digit mobile number.',
            ], 422);
        }

        $query = Order::with('items');

        if (!empty($cleanOrderNumber) && !empty($cleanPhone)) {
            $query->where('order_number', strtoupper($cleanOrderNumber))
                ->where(function ($q) use ($cleanPhone) {
                    $q->where('phone1', $cleanPhone)->orWhere('phone2', $cleanPhone);
                });
        } elseif (!empty($cleanOrderNumber)) {
            $query->where('order_number', strtoupper($cleanOrderNumber));
        } else {
            $query->where(function ($q) use ($cleanPhone) {
                $q->where('phone1', $cleanPhone)->orWhere('phone2', $cleanPhone);
            });
        }

        $orders = $query->latest('id')->limit(10)->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'name' => $order->name,
                'phone1' => $order->phone1,
                'city' => $order->city,
                'state' => $order->state,
                'pincode' => $order->pincode,
                'delivery_address' => $order->delivery_address,
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status ?? 'pending',
                'payment_status' => $order->payment_status ?? 'pending',
                'parcel_service_name' => $order->parcel_service_name,
                'lr_number' => $order->lr_number,
                'parcel_count' => $order->parcel_count,
                'dispatch_date' => $order->dispatch_date ? $order->dispatch_date->format('d M Y') : ($order->dispatched_at ? $order->dispatched_at->format('d M Y') : null),
                'transport_phone' => $order->transport_phone,
                'destination_hub' => $order->destination_hub,
                'lr_receipt_image' => $order->lr_receipt_image ? asset('storage/' . $order->lr_receipt_image) : null,
                'dispatched_at' => $order->dispatched_at ? $order->dispatched_at->format('d M Y, h:i A') : null,
                'invoice_url' => route('order.public_invoice', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]),
                'download_pdf_url' => route('order.public_invoice.download', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]),
                'created_at' => $order->created_at->format('d M Y, h:i A'),
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'line_total' => (float) $item->line_total,
                    ];
                }),
            ];
        });

        return $this->corsJson([
            'success' => true,
            'count' => $orders->count(),
            'orders' => $orders,
        ]);
    }

    /**
     * Get specific order details for invoice / receipt.
     */
    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return $this->corsJson([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $shop = Shop::current();

        return $this->corsJson([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'name' => $order->name,
                'phone1' => $order->phone1,
                'phone2' => $order->phone2,
                'delivery_address' => $order->delivery_address,
                'city' => $order->city,
                'state' => $order->state,
                'pincode' => $order->pincode,
                'status' => $order->status ?? 'pending',
                'payment_status' => $order->payment_status ?? 'pending',
                'total_amount' => (float) $order->total_amount,
                'created_at' => $order->created_at->format('d M Y, h:i A'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'unit_price' => (float) $item->unit_price,
                        'quantity' => $item->quantity,
                        'line_total' => (float) $item->line_total,
                    ];
                }),
            ],
            'shop' => [
                'name' => $shop->name,
                'phone' => $shop->phone,
                'whatsapp' => $shop->whatsapp,
                'upi_id' => $shop->upi_id,
            ],
            'invoice_url' => route('order.public_invoice', ['orderNumber' => $order->order_number]),
            'download_pdf_url' => route('order.public_invoice.download', ['orderNumber' => $order->order_number]),
        ]);
    }
}
