<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Services\WhatsAppOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    /**
     * Public order form - this is the page the QR code points to.
     */
    public function create()
    {
        $shop = Shop::current();
        $categories = Category::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)->orderBy('id');
            }])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('order.create', compact('categories', 'shop', 'banners'));
    }

    public function store(Request $request)
    {
        // Sanitize phone inputs: strip any leading +91, 91, 0, or non-digits, ensuring strictly 10 digits in DB
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
            'products' => 'required|array|min:1',
            'products.*.qty' => 'nullable|integer|min:0|max:500',
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

        // Filter only products with qty > 0
        $selected = collect($request->input('products'))
            ->filter(fn ($item) => isset($item['qty']) && (int) $item['qty'] > 0);

        if ($selected->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['products' => 'Please select at least one product with quantity.']);
        }

        // Duplicate order prevention: check if an identical order from same phone was placed within 120 seconds
        $recentOrder = Order::with('items')
            ->where('phone1', $validated['phone1'])
            ->where('created_at', '>=', now()->subSeconds(120))
            ->latest('id')
            ->first();

        if ($recentOrder) {
            $recentMap = $recentOrder->items->pluck('quantity', 'product_id')->toArray();
            $currentMap = $selected->mapWithKeys(fn ($item, $pid) => [(int) $pid => (int) $item['qty']])->toArray();
            ksort($recentMap);
            ksort($currentMap);

            if ($recentMap == $currentMap) {
                // Duplicate order intercepted safely - redirect to existing order
                return redirect()
                    ->route('order.success', $recentOrder->order_number)
                    ->with('order_number', $recentOrder->order_number)
                    ->with('duplicate_prevented', true);
            }
        }

        $order = DB::transaction(function () use ($validated, $selected, $request) {
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
            ]);

            $total = 0;
            $productIds = $selected->keys()->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($selected as $productId => $item) {
                $product = $products->get($productId);
                if (! $product) {
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

        // Automatically dispatch WhatsApp messages if local gateway is connected
        $shop = Shop::current();
        $autoDispatch = WhatsAppOrderService::sendAutomatedOrderInvoice($order, $shop);

        // Authorize this order in the customer's current browser session
        $request->session()->put('authorized_order_' . $order->order_number, true);

        return redirect()
            ->route('order.success', $order->order_number)
            ->with('order_number', $order->order_number)
            ->with('just_ordered', true)
            ->with('whatsapp_sent', $autoDispatch['customer_sent'] ?? false);
    }

    public function success(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();
        $shop = Shop::current();

        $token = $request->query('token');
        if (!empty($token) && $order->verifyInvoiceSignature((string) $token)) {
            $request->session()->put('authorized_order_' . $order->order_number, true);
        }

        $isAuthorized = Auth::check() || ($request->session()->get('authorized_order_' . $order->order_number) === true);

        if (!$isAuthorized) {
            return redirect()->route('order.public_invoice', ['orderNumber' => $order->order_number]);
        }

        $upiUrl = $shop->getUpiPaymentUrl($order->total_amount, $order->order_number);
        $upiQrSvg = null;
        if ($upiUrl) {
            try {
                $upiQrSvg = QrCode::size(240)->margin(1)->generate($upiUrl);
            } catch (\Throwable $e) {
                $upiQrSvg = null;
            }
        }

        $paymentScreenshotWhatsAppUrl = WhatsAppOrderService::getPaymentScreenshotWhatsAppUrl($order, $shop);
        $customerInvoiceWhatsAppUrl = WhatsAppOrderService::getCustomerInvoiceWhatsAppUrl($order, $shop);
        $adminPackingListWhatsAppUrl = WhatsAppOrderService::getAdminPackingListWhatsAppUrl($order, $shop);

        return view('order.success', compact(
            'order',
            'shop',
            'upiUrl',
            'upiQrSvg',
            'paymentScreenshotWhatsAppUrl',
            'customerInvoiceWhatsAppUrl',
            'adminPackingListWhatsAppUrl'
        ));
    }

    /**
     * Customer viewable / printable invoice page.
     * Enforces security: Requires authenticated session, matching signed token, or phone number verification.
     */
    public function publicInvoice(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();
        $shop = Shop::current();

        // 1. Check for valid signed token (e.g. sent directly to customer on WhatsApp)
        $token = $request->query('token');
        if (!empty($token) && $order->verifyInvoiceSignature((string) $token)) {
            $request->session()->put('authorized_order_' . $order->order_number, true);
        }

        // 2. Handle mobile verification challenge submission
        $verificationError = null;
        if ($request->isMethod('post') && $request->has('verify_phone')) {
            $throttleKey = 'verify_invoice_' . $order->id . '_' . $request->ip();

            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $minutes = ceil($seconds / 60);
                $verificationError = "Too many failed attempts. Verification for this order is temporarily locked for {$minutes} minute(s).";
            } else {
                $submittedPhone = preg_replace('/[^0-9]/', '', (string) $request->input('verify_phone', ''));
                if (strlen($submittedPhone) === 10 && ($submittedPhone === $order->phone1 || $submittedPhone === $order->phone2)) {
                    RateLimiter::clear($throttleKey);
                    $request->session()->put('authorized_order_' . $order->order_number, true);
                    return redirect()->route('order.public_invoice', ['orderNumber' => $order->order_number]);
                } else {
                    RateLimiter::hit($throttleKey, 900); // 15-minute lock
                    $remaining = RateLimiter::remaining($throttleKey, 5);
                    if ($remaining > 0) {
                        $verificationError = "The mobile number entered does not match our records for this order. ({$remaining} attempt(s) remaining)";
                    } else {
                        $verificationError = "Too many failed attempts. Verification for this order has been locked for 15 minutes.";
                    }
                }
            }
        }

        // 3. Check authorization status
        $isAuthorized = Auth::check() || ($request->session()->get('authorized_order_' . $order->order_number) === true);

        if (!$isAuthorized) {
            return response()->view('order.verify_invoice', compact('order', 'shop', 'verificationError'));
        }

        return view('order.invoice', compact('order', 'shop'));
    }

    /**
     * Customer downloadable invoice PDF.
     * Enforces authorization before serving PDF.
     */
    public function downloadInvoice(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();
        $shop = Shop::current();

        $token = $request->query('token');
        if (!empty($token) && $order->verifyInvoiceSignature((string) $token)) {
            $request->session()->put('authorized_order_' . $order->order_number, true);
        }

        $isAuthorized = Auth::check() || ($request->session()->get('authorized_order_' . $order->order_number) === true);

        if (!$isAuthorized) {
            return redirect()->route('order.public_invoice', ['orderNumber' => $order->order_number]);
        }

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order', 'shop'))
            ->setPaper('a4');

        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Customer order tracking lookup page.
     * Enforces privacy: Requires Order ID and Registered Mobile Number to access order & invoice.
     */
    public function trackOrder(Request $request)
    {
        $shop = Shop::current();

        $orderNumberInput = (string) $request->input('order_number', '');
        $phoneInput = (string) $request->input('phone', '');
        $queryInput = (string) $request->input('query', '');

        // If query was supplied instead of separate fields (e.g. from existing links or general search input)
        if (empty($orderNumberInput) && empty($phoneInput) && !empty($queryInput)) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $queryInput);
            if (strlen($cleanDigits) === 10 && !str_starts_with(strtoupper($queryInput), 'GC-')) {
                $phoneInput = $cleanDigits;
            } else {
                $orderNumberInput = $queryInput;
            }
        }

        $cleanOrderNumber = trim(preg_replace('/[^a-zA-Z0-9\-]/', '', $orderNumberInput));
        $cleanOrderNumber = preg_replace('/\-+/', '-', $cleanOrderNumber);
        $cleanOrderNumber = trim($cleanOrderNumber, '-');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);

        $orders = collect();
        $searched = false;
        $trackError = null;

        if (!empty($cleanOrderNumber) || !empty($cleanPhone)) {
            $searched = true;

            // Option A: Strictly require matching Order ID and 10-digit Phone Number to view order details & invoice
            if (!empty($cleanOrderNumber) && strlen($cleanPhone) === 10) {
                $trackThrottleKey = 'track_order_' . $request->ip();
                if (RateLimiter::tooManyAttempts($trackThrottleKey, 10)) {
                    $seconds = RateLimiter::availableIn($trackThrottleKey);
                    $minutes = ceil($seconds / 60);
                    $trackError = "Too many tracking attempts. Please wait {$minutes} minute(s) before trying again.";
                } else {
                    $exactOrder = Order::with('items')
                        ->where('order_number', strtoupper($cleanOrderNumber))
                        ->where(function ($q) use ($cleanPhone) {
                            $q->where('phone1', $cleanPhone)
                              ->orWhere('phone2', $cleanPhone);
                        })
                        ->first();

                    if ($exactOrder) {
                        RateLimiter::clear($trackThrottleKey);
                        $request->session()->put('authorized_order_' . $exactOrder->order_number, true);
                        return redirect()->route('order.public_invoice', $exactOrder->order_number);
                    } else {
                        RateLimiter::hit($trackThrottleKey, 600); // 10 minutes decay
                        $trackError = 'No matching order found for Order ID #' . strtoupper($cleanOrderNumber) . ' with registered mobile ' . $cleanPhone . '. Please verify both details.';
                    }
                }
            } elseif (!empty($cleanOrderNumber) && empty($cleanPhone)) {
                // If only Order ID is provided, check if order exists then redirect to invoice page (which will challenge with mobile verification)
                $exactOrder = Order::where('order_number', strtoupper($cleanOrderNumber))->first();
                if ($exactOrder) {
                    return redirect()->route('order.public_invoice', $exactOrder->order_number);
                } else {
                    $trackError = 'Order ID "' . strtoupper($cleanOrderNumber) . '" was not found. Please double-check your Order ID.';
                }
            } elseif (empty($cleanOrderNumber) && !empty($cleanPhone)) {
                // Privacy Protection: Never expose orders or delivery addresses by phone number alone
                $trackError = 'To protect customer privacy and home address details, please enter your Order ID along with your mobile number. Your Order ID was sent to your WhatsApp upon booking.';
            } else {
                $trackError = 'Please enter both your Order ID and 10-digit registered mobile number.';
            }
        }

        $query = $cleanOrderNumber ?: $cleanPhone;

        return view('order.track', compact('shop', 'cleanOrderNumber', 'cleanPhone', 'query', 'orders', 'searched', 'trackError'));
    }

    /**
     * Generates a QR code image that points to the order form URL.
     * Visit /order-qr in browser to view/download it, or embed <img src="/order-qr">.
     */
    public function qrCode()
    {
        $url = route('order.create');

        return response(
            QrCode::size(400)->generate($url)
        )->header('Content-Type', 'image/svg+xml');
    }
}
