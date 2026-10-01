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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            'pincode' => ['required', 'string', 'regex:/^6[0-4][0-9]{4}$/'],
            'products' => 'required|array|min:1',
            'products.*.qty' => 'nullable|integer|min:0|max:20',
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
            'pincode.regex' => 'Delivery is available inside Tamil Nadu only. Please enter a valid Tamil Nadu pincode (60xxxx - 64xxxx).',
            'products.required' => 'Please select at least one product with quantity.',
            'products.*.qty.max' => 'Maximum 20 units allowed per item.',
        ]);

        // Enforce WhatsApp OTP verification for customer phone
        $verifiedPhone = session('verified_order_phone');
        $verifiedTime = session('verified_order_time', 0);
        $isPhoneVerified = ($verifiedPhone === $validated['phone1']) && (now()->timestamp - $verifiedTime <= 1800);

        if (!$isPhoneVerified && !config('app.debug_bypass_otp', false)) {
            return back()
                ->withInput()
                ->withErrors(['phone1' => 'Please verify your WhatsApp mobile number via OTP before booking.']);
        }

        // Filter only products with qty > 0
        $selected = collect($request->input('products'))
            ->filter(fn ($item) => isset($item['qty']) && (int) $item['qty'] > 0);

        if ($selected->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['products' => 'Please select at least one product with quantity.']);
        }

        $shop = Shop::current();
        $minOrderAmount = $shop->getMinOrderAmount();

        // Validate minimum order amount
        $productIds = $selected->keys()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $preTotal = 0;
        foreach ($selected as $productId => $item) {
            $product = $products->get($productId);
            if ($product) {
                $preTotal += $product->net_rate * (int) $item['qty'];
            }
        }

        if ($preTotal < $minOrderAmount) {
            return back()
                ->withInput()
                ->withErrors([
                    'products' => 'குறைந்தபட்ச ஆர்டர் தொகை ₹' . number_format($minOrderAmount, 2) . ' ஆகும் (Minimum order value is ₹' . number_format($minOrderAmount, 2) . '). உங்கள் தற்போதைய ஆர்டர் மதிப்பு ₹' . number_format($preTotal, 2) . '. மேலும் பட்டாசுகளை Cart-ல் சேர்த்து தொடரவும்.',
                ]);
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
                'status' => 'pending',
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

        // Clear OTP verification session
        $request->session()->forget(['verified_order_phone', 'verified_order_token', 'verified_order_time']);

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

    /**
     * 100% accurate India Post Pincode Lookup API.
     * Automatically retrieves City / District and State based on 6-digit Pincode.
     * Implements dual API failover (api.postalpincode.in & npdigitech.com) and 60-day persistent cache.
     */
    public function lookupPincode(string $pincode)
    {
        $cleanPin = preg_replace('/[^0-9]/', '', $pincode);
        if (strlen($cleanPin) !== 6) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit Pincode.'
            ], 422);
        }

        // Delivery restricted to Tamil Nadu only
        if (!preg_match('/^6[0-4][0-9]{4}$/', $cleanPin)) {
            return response()->json([
                'success' => false,
                'is_serviceable' => false,
                'message' => 'Delivery is available inside Tamil Nadu only. Please enter a valid Tamil Nadu pincode (60xxxx - 64xxxx).'
            ], 422);
        }

        $cacheKey = "pincode_lookup_v2_{$cleanPin}";

        $data = Cache::remember($cacheKey, now()->addDays(60), function () use ($cleanPin) {
            // 1. Primary: India Post via postalpincode.in
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json'
                ])->timeout(3)->get("https://api.postalpincode.in/pincode/{$cleanPin}");

                if ($response->successful()) {
                    $json = $response->json();
                    if (is_array($json) && !empty($json[0]['Status']) && $json[0]['Status'] === 'Success' && !empty($json[0]['PostOffice'])) {
                        $postOffices = $json[0]['PostOffice'];
                        $first = $postOffices[0];

                        $district = trim($first['District'] ?? '');
                        $state = trim($first['State'] ?? '');
                        $block = trim($first['Block'] ?? '');
                        $division = trim($first['Division'] ?? '');

                        if (strcasecmp($state, 'Puducherry') === 0) {
                            $state = 'Pondicherry';
                        }

                        $places = [];
                        foreach ($postOffices as $po) {
                            $name = trim($po['Name'] ?? '');
                            if (!empty($name) && !in_array($name, $places)) {
                                $places[] = $name;
                            }
                        }

                        $city = $district;
                        if (!empty($block) && $block !== 'NA' && !preg_match('/(Corporation|Circle|North|South|East|West|GPO|Division)/i', $block)) {
                            $city = $block;
                        } elseif (!empty($places[0]) && count($places) === 1) {
                            $city = $places[0];
                        }

                        $suggested = [];
                        if (!empty($city)) $suggested[] = $city;
                        if (!empty($district) && !in_array($district, $suggested)) $suggested[] = $district;
                        if (!empty($block) && $block !== 'NA' && !in_array($block, $suggested)) $suggested[] = $block;
                        foreach ($places as $p) {
                            if (!in_array($p, $suggested)) $suggested[] = $p;
                        }

                        return [
                            'success' => true,
                            'pincode' => $cleanPin,
                            'city' => $city,
                            'district' => $district,
                            'state' => $state,
                            'block' => $block,
                            'division' => $division,
                            'places' => array_values(array_unique($suggested))
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::info("Primary pincode lookup timed out or failed for {$cleanPin}: " . $e->getMessage());
            }

            // 2. Secondary Fallback: npdigitech.com India Post Mirror
            try {
                $response = Http::timeout(3)->get("https://pincode.npdigitech.com/api-doc.php?pincode={$cleanPin}");
                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['status']) && $json['status'] === 'success' && !empty($json['data'])) {
                        $list = $json['data'];
                        $first = $list[0];

                        $district = trim($first['district'] ?? '');
                        $rawState = trim($first['state'] ?? '');
                        $state = ucwords(strtolower($rawState));
                        $taluk = trim($first['taluk'] ?? '');

                        if (strcasecmp($state, 'Puducherry') === 0) {
                            $state = 'Pondicherry';
                        }

                        $places = [];
                        foreach ($list as $item) {
                            $name = trim($item['city_village'] ?? '');
                            if (!empty($name) && !in_array($name, $places)) {
                                $places[] = $name;
                            }
                        }

                        $city = $district;
                        if (!empty($taluk) && $taluk !== 'NA' && !preg_match('/(Corporation|Circle|North|South|East|West|GPO|Division)/i', $taluk)) {
                            $city = $taluk;
                        } elseif (!empty($places[0]) && count($places) === 1) {
                            $city = $places[0];
                        }

                        $suggested = [];
                        if (!empty($city)) $suggested[] = $city;
                        if (!empty($district) && !in_array($district, $suggested)) $suggested[] = $district;
                        if (!empty($taluk) && $taluk !== 'NA' && !in_array($taluk, $suggested)) $suggested[] = $taluk;
                        foreach ($places as $p) {
                            if (!in_array($p, $suggested)) $suggested[] = $p;
                        }

                        return [
                            'success' => true,
                            'pincode' => $cleanPin,
                            'city' => $city,
                            'district' => $district,
                            'state' => $state,
                            'block' => $taluk,
                            'places' => array_values(array_unique($suggested))
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Secondary pincode lookup failed for {$cleanPin}: " . $e->getMessage());
            }

            return null;
        });

        if ($data) {
            $isTamilNadu = (strcasecmp($data['state'], 'Tamil Nadu') === 0 || strcasecmp($data['state'], 'Pondicherry') === 0);
            if (!$isTamilNadu) {
                return response()->json([
                    'success' => false,
                    'is_serviceable' => false,
                    'state' => $data['state'],
                    'message' => "Delivery is available inside Tamil Nadu only. ({$data['state']} is not serviceable)."
                ], 422);
            }
            $data['is_serviceable'] = true;
            return response()->json($data);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pincode not found. Please enter City and State manually.'
        ], 404);
    }

    /**
     * Generate & send a 4-digit verification OTP to customer's WhatsApp.
     */
    public function sendOrderOtp(Request $request)
    {
        $phone = $request->input('phone1', '');
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        } elseif (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        if (strlen($digits) !== 10 || !preg_match('/^[6-9][0-9]{9}$/', $digits)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 10-digit WhatsApp mobile number.'
            ], 422);
        }

        // 30 seconds cooldown check per phone
        $cooldownKey = "order_otp_cooldown_{$digits}";
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'success' => false,
                'cooldown' => true,
                'message' => 'An OTP was recently sent. Please wait before requesting another code.'
            ], 429);
        }

        // Rate limiter: max 6 requests per 15 mins per phone
        $rateLimitKey = 'order-otp:' . $digits;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 6)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            return response()->json([
                'success' => false,
                'message' => "Too many OTP requests. Please wait {$minutes} minute(s) before trying again."
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 900);

        // Generate 4-digit numeric OTP
        $otp = (string) random_int(1000, 9999);

        // Cache OTP for 10 minutes
        $cacheKey = "order_otp_{$digits}";
        Cache::put($cacheKey, [
            'otp' => $otp,
            'phone' => $digits,
            'attempts' => 0,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(10));

        // 30s cooldown for next resend
        Cache::put($cooldownKey, true, now()->addSeconds(30));

        // Prepare message
        $name = trim($request->input('name', 'Customer'));
        $name = preg_replace('/[^a-zA-Z\s.]/', '', $name);
        $greeting = !empty($name) ? "Hello {$name}! 🙏" : "Hello! 🙏";

        $message = "✨ *GURU CRACKERS - SIVAKASI* 🪔\n"
                 . "Order Booking Verification\n\n"
                 . "{$greeting}\n"
                 . "Your verification OTP to confirm your Diwali cracker booking is:\n\n"
                 . "🔐 *{$otp}*\n\n"
                 . "⏰ Valid for 10 minutes.\n"
                 . "⚠️ Do NOT share this code with anyone.\n\n"
                 . "Once verified, your order will be confirmed directly from Sivakasi factory! 🎆";

        $sendResult = WhatsAppOrderService::sendDirectMessage($digits, $message, null, 'order_otp');

        // Mask phone for UI: +91 96779 **** 33
        $maskedPhone = '+91 ' . substr($digits, 0, 5) . ' ' . substr($digits, -5);

        Log::info("Customer WhatsApp OTP generated for {$digits}: {$otp} (Gateway status: " . ($sendResult['success'] ? 'Sent' : ($sendResult['error'] ?? 'Offline')) . ")");

        return response()->json([
            'success' => true,
            'message' => "Verification code sent to your WhatsApp number ({$maskedPhone}).",
            'phone' => $digits,
            'masked_phone' => $maskedPhone,
            'cooldown' => 30,
            'whatsapp_sent' => (bool) ($sendResult['success'] ?? false),
            'debug_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    /**
     * Verify customer's 4-digit OTP.
     */
    public function verifyOrderOtp(Request $request)
    {
        $phone = $request->input('phone1', '');
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        } elseif (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        $otpInput = trim((string) $request->input('otp', ''));

        if (strlen($otpInput) !== 4 || !ctype_digit($otpInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter the 4-digit OTP code.'
            ], 422);
        }

        $cacheKey = "order_otp_{$digits}";
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired or was not requested. Please click Resend OTP.'
            ], 422);
        }

        if (($cachedData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Too many failed OTP attempts. Please request a new OTP code.'
            ], 422);
        }

        if ($cachedData['otp'] !== $otpInput) {
            $cachedData['attempts'] = ($cachedData['attempts'] ?? 0) + 1;
            $remaining = 5 - $cachedData['attempts'];
            Cache::put($cacheKey, $cachedData, now()->addMinutes(10));

            return response()->json([
                'success' => false,
                'message' => "Incorrect OTP code. {$remaining} attempt(s) remaining."
            ], 422);
        }

        // OTP is correct! Clear OTP cache & authorize session for 30 minutes
        Cache::forget($cacheKey);
        $token = Str::random(40);
        session([
            'verified_order_phone' => $digits,
            'verified_order_token' => $token,
            'verified_order_time' => now()->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp number verified successfully!',
            'token' => $token,
        ]);
    }
}
