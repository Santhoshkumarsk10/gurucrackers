@extends('layouts.app')

@section('title', 'Order Confirmed - ' . ($shop->name ?? 'Guru Crackers Sivakasi'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-16">

    @if (session('whatsapp_sent'))
        <!-- Automated WhatsApp Delivery Success Banner -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl p-4 sm:p-5 shadow-lg shadow-emerald-950/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border border-emerald-400/40 animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <div class="font-black text-sm sm:text-base font-heading flex items-center gap-1.5">
                        <span>Invoice Automatically Sent to Your WhatsApp!</span>
                        <i class="fa-solid fa-circle-check text-emerald-300"></i>
                    </div>
                    <div class="text-xs text-emerald-100 mt-0.5">
                        Your complete order invoice & UPI payment instructions were delivered to <strong>{{ $order->phone1 }}</strong>.
                    </div>
                </div>
            </div>
            <a href="{{ $customerInvoiceWhatsAppUrl }}" target="_blank" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-black shrink-0 transition-colors text-center shadow-sm font-heading">
                Open WhatsApp Chat
            </a>
        </div>
    @elseif (session('just_ordered') || request('auto_open'))
        <!-- Automatic WhatsApp Trigger Overlay -->
        <div id="waAutoTriggerOverlay" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border-2 border-emerald-400 relative">
                <button type="button" onclick="closeWaTrigger()" class="absolute top-3 right-3 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition-colors" title="Close">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl shadow-md animate-bounce">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-black text-slate-900 font-heading">
                        Sending Invoice to WhatsApp...
                    </h3>
                    <p class="text-xs text-slate-500">
                        Order <strong>#{{ $order->order_number }}</strong> confirmed! Launching WhatsApp with your invoice & payment details.
                    </p>
                </div>

                <div class="py-1">
                    <a
                        href="{{ $customerInvoiceWhatsAppUrl }}"
                        id="autoTriggerWaLink"
                        target="_blank"
                        onclick="closeWaTrigger()"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm py-3 px-4 rounded-xl shadow-md transition-all font-heading"
                    >
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Open WhatsApp Now</span>
                    </a>
                </div>

                <div class="text-[11px] text-slate-400 flex items-center justify-center gap-1">
                    <span>Opening automatically in</span>
                    <span id="waCountdownNum" class="font-black text-emerald-600 text-xs">1</span>
                    <span>second...</span>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const waUrl = @json($customerInvoiceWhatsAppUrl);
                let countdown = 1;
                const timer = setInterval(() => {
                    countdown--;
                    const el = document.getElementById('waCountdownNum');
                    if (el) el.innerText = countdown;
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = waUrl;
                    }
                }, 1000);
            });

            function closeWaTrigger() {
                const overlay = document.getElementById('waAutoTriggerOverlay');
                if (overlay) overlay.remove();
            }
        </script>
    @endif

    @if ($order->isDispatched())
        <!-- Dispatched Notice Banner -->
        <div class="bg-gradient-to-r from-teal-700 to-emerald-700 text-white rounded-2xl p-4 sm:p-5 shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border border-teal-400/40 animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl shrink-0">
                    🚚
                </div>
                <div>
                    <div class="font-black text-sm sm:text-base font-heading flex items-center gap-1.5">
                        <span>Parcel Dispatched via {{ $order->parcel_service_name ?: 'Transport' }}!</span>
                        <span class="bg-amber-400 text-slate-950 text-xs px-2 py-0.5 rounded font-mono font-bold">LR: {{ $order->lr_number }}</span>
                    </div>
                    <div class="text-xs text-teal-100 mt-0.5">
                        Your cracker parcel is in transit to {{ $order->destination_hub ?: $order->city }}.
                    </div>
                </div>
            </div>
            <a href="{{ route('order.public_invoice', $order->order_number) }}" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-white text-teal-800 hover:bg-teal-50 text-xs font-black shrink-0 transition-colors text-center shadow-sm font-heading">
                View LR Receipt & Tracking
            </a>
        </div>
    @endif

    <!-- Celebration Header Card -->
    <div class="bg-white rounded-2xl border border-emerald-100 shadow-xl shadow-emerald-900/5 p-6 sm:p-8 text-center relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-100/60 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-36 h-36 bg-amber-100/60 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <div class="w-16 h-16 bg-gradient-to-tr from-emerald-500 to-teal-400 text-white rounded-2xl mx-auto flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/20 animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold tracking-wide">
                <span>🎉 ORDER SUBMITTED SUCCESSFULLY</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                Thank You, {{ $order->name }}!
            </h1>

            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                Your Diwali cracker order has been recorded. Complete the payment below and share the screenshot on WhatsApp to confirm your transport dispatch.
            </p>

            <div class="pt-2">
                <div class="inline-block bg-slate-100 border border-slate-200 px-4 py-2 rounded-xl text-xs font-mono text-slate-800 shadow-inner">
                    <span class="text-slate-500 mr-2">Booking ID:</span>
                    <strong class="text-sm sm:text-base text-rose-700 font-extrabold">{{ $order->order_number }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== UPI PAYMENT & QR CODE CARD ===================== --}}
    <div class="bg-gradient-to-br from-emerald-700 via-teal-800 to-slate-900 text-white rounded-2xl p-6 sm:p-7 shadow-xl shadow-emerald-950/20 relative overflow-hidden space-y-5">
        <div class="absolute -top-12 -right-12 w-44 h-44 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-teal-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/15 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-xl text-amber-300 border border-white/20">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <div class="text-xs text-emerald-200 font-bold uppercase tracking-wider">Step 1: Scan & Pay via UPI</div>
                        <h2 class="text-xl sm:text-2xl font-black font-heading text-white">
                            Pay ₹{{ number_format($order->total_amount, 2) }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-xl border border-white/20">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100 hidden sm:inline">Accepted:</span>
                    @include('partials.payment-accepted-badges')
                </div>
            </div>

            <!-- QR Code & Details Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4">

                <!-- Left: QR Code Box -->
                <div class="flex flex-col items-center justify-center bg-white rounded-2xl p-4 shadow-inner text-slate-900">
                    @if (!empty($shop->upi_qr_image_url))
                        <img src="{{ $shop->upi_qr_image_url }}" alt="UPI QR Code" class="w-48 h-48 object-contain rounded-xl">
                    @elseif (!empty($upiQrSvg))
                        <div class="w-48 h-48 flex items-center justify-center">
                            {!! $upiQrSvg !!}
                        </div>
                    @else
                        <div class="w-48 h-48 flex flex-col items-center justify-center bg-slate-100 rounded-xl text-slate-400">
                            <i class="fa-solid fa-qrcode text-4xl mb-2 text-slate-300"></i>
                            <span class="text-xs font-bold">UPI ID Below</span>
                        </div>
                    @endif

                    <div class="text-[11px] font-extrabold text-slate-700 mt-2 text-center flex items-center gap-1.5">
                        <i class="fa-solid fa-camera text-emerald-600"></i>
                        <span>Scan with any UPI App</span>
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Amount Pre-filled: ₹{{ number_format($order->total_amount, 2) }}
                    </div>
                </div>

                <!-- Right: UPI ID & Payment Instructions -->
                <div class="space-y-3.5">
                    @if (!empty($shop->upi_id))
                        <div class="space-y-1">
                            <label class="text-[11px] uppercase tracking-wider text-emerald-200 font-bold block">
                                Shop UPI ID (GPay / PhonePe):
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-black/30 backdrop-blur-sm border border-white/20 rounded-xl px-3 py-2 text-sm font-mono font-bold text-amber-300 truncate" id="upiIdText">
                                    {{ $shop->upi_id }}
                                </div>
                                <button
                                    type="button"
                                    onclick="copyUpiId('{{ $shop->upi_id }}')"
                                    id="copyUpiBtn"
                                    class="px-3 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition-all shrink-0 flex items-center gap-1"
                                    title="Copy UPI ID"
                                >
                                    <i class="fa-solid fa-copy"></i>
                                    <span id="copyText">Copy</span>
                                </button>
                            </div>
                            @if (!empty($shop->upi_name))
                                <div class="text-[11px] text-white/70">
                                    Account Name: <strong class="text-white">{{ $shop->upi_name }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Step 2: Share Screenshot -->
                    <div class="bg-black/20 rounded-xl p-3 border border-white/10 space-y-2">
                        <div class="text-xs font-bold text-amber-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Step 2: Send Screenshot for Confirmation</span>
                        </div>
                        <p class="text-[11px] text-emerald-100 leading-relaxed">
                            Once payment is completed, click the button below to send your payment screenshot to our official WhatsApp number to confirm your order dispatch.
                        </p>
                    </div>

                    <!-- Direct Screenshot WhatsApp CTA Button -->
                    <a
                        href="{{ $paymentScreenshotWhatsAppUrl }}"
                        target="_blank"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-extrabold text-sm py-3 px-4 rounded-xl shadow-lg shadow-emerald-950/30 transition-all transform hover:scale-[1.02] active:scale-95 font-heading text-center"
                    >
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Share Screenshot on WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== WHATSAPP & INVOICE SHORTCUTS ===================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Send Invoice to Customer WhatsApp -->
        <a
            href="{{ $customerInvoiceWhatsAppUrl }}"
            target="_blank"
            class="bg-white hover:bg-slate-50 border border-slate-200 rounded-xl p-3.5 shadow-sm transition-all flex items-center gap-3 group"
        >
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl group-hover:scale-105 transition-transform shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div class="text-left">
                <div class="text-xs font-extrabold text-slate-800 font-heading">
                    Send Invoice to My WhatsApp
                </div>
                <div class="text-[11px] text-slate-500">
                    Get full order breakdown & rates on WhatsApp
                </div>
            </div>
        </a>

        <!-- View / Download Online Invoice -->
        <a
            href="{{ route('order.public_invoice', $order->order_number) }}"
            class="bg-white hover:bg-slate-50 border border-slate-200 rounded-xl p-3.5 shadow-sm transition-all flex items-center gap-3 group"
        >
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl group-hover:scale-105 transition-transform shrink-0">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="text-left">
                <div class="text-xs font-extrabold text-slate-800 font-heading">
                    View & Print Tax Invoice
                </div>
                <div class="text-[11px] text-slate-500">
                    Official printable order slip & PDF
                </div>
            </div>
        </a>
    </div>

    <!-- Order Details & Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </span>
                <h2 class="font-extrabold text-base text-slate-900 font-heading">
                    Order Summary
                </h2>
            </div>
            <a href="{{ route('order.public_invoice', $order->order_number) }}" class="text-xs font-semibold text-slate-600 hover:text-rose-600 bg-slate-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Full Invoice
            </a>
        </div>

        <!-- Customer Delivery Information -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 text-xs space-y-2">
            <div class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-1 flex items-center gap-1.5 text-rose-700">
                <i class="fa-solid fa-location-dot"></i> Delivery Address
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-600">
                <div>
                    <span class="font-semibold text-slate-800">{{ $order->name }}</span><br>
                    <span>{{ $order->phone1 }}</span> @if($order->phone2) / <span>{{ $order->phone2 }}</span> @endif
                </div>
                <div>
                    <span class="whitespace-pre-line">{{ $order->delivery_address }}</span><br>
                    <span>{{ $order->city }} - {{ $order->pincode }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table (Desktop) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-2.5">Cracker Product</th>
                        <th class="py-2.5 text-center">Qty</th>
                        <th class="py-2.5 text-right">Price</th>
                        <th class="py-2.5 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 font-bold text-slate-800">
                                <span>{{ $item->product_name }}</span>
                                @if ($item->product?->unit)
                                    <span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md ml-1">{{ $item->product->unit }}</span>
                                @endif
                                @if ($item->product && !empty($item->product->tamil_name))
                                    <span class="text-slate-500 font-normal text-[11px] block sm:inline sm:ml-1">| {{ $item->product->tamil_name }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-center font-extrabold text-slate-700">{{ $item->quantity }}</td>
                            <td class="py-2.5 text-right text-slate-500">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2.5 text-right font-extrabold text-rose-700">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Items Cards (Mobile Responsive - Like Selected Items Review) -->
        <div class="block sm:hidden space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-600 px-1">
                <span class="uppercase tracking-wider text-[10px] text-slate-400 font-extrabold">Booked Crackers</span>
                <span class="bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full text-[11px] font-extrabold">
                    {{ $order->items->count() }} Varieties ({{ $order->total_quantity }} Units)
                </span>
            </div>

            <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-3 divide-y divide-slate-200/70">
                @foreach ($order->items as $item)
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-slate-900">{{ $item->product_name }}</span>
                                @if ($item->product?->unit)
                                    <span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-1.5 py-0.5 rounded-md shrink-0 shadow-xs">{{ $item->product->unit }}</span>
                                @endif
                            </div>
                            @if ($item->product && !empty($item->product->tamil_name))
                                <div class="text-[10px] text-rose-700 font-semibold mt-0.5">{{ $item->product->tamil_name }}</div>
                            @endif
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                ₹{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-extrabold text-rose-700 text-sm">₹{{ number_format($item->line_total, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Total -->
        <div class="border-t-2 border-slate-200 pt-3 flex justify-between items-baseline">
            <span class="font-extrabold text-sm sm:text-base text-slate-900 font-heading">Total Payable Amount:</span>
            <span class="text-2xl font-black text-rose-700 font-heading">₹{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <a href="{{ route('order.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-xs sm:text-sm py-3 px-6 rounded-xl shadow-md transition-all font-heading">
            <i class="fa-solid fa-plus"></i>
            <span>Place Another Order</span>
        </a>
    </div>

</div>

<script>
function copyUpiId(text) {
    navigator.clipboard.writeText(text).then(() => {
        const copyText = document.getElementById('copyText');
        if (copyText) {
            copyText.innerText = 'Copied!';
            setTimeout(() => {
                copyText.innerText = 'Copy';
            }, 2000);
        }
        if (window.DiwaliAlert && window.DiwaliAlert.toast) {
            window.DiwaliAlert.toast({
                type: 'success',
                message: 'UPI ID copied to clipboard! 📋'
            });
        }
    }).catch(err => {
        if (window.DiwaliAlert && window.DiwaliAlert.error) {
            window.DiwaliAlert.error('Copy Failed', 'Please manually select and copy the UPI ID.');
        }
    });
}
</script>
@endsection
