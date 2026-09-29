@extends('layouts.app')

@section('title', 'Invoice #' . $order->order_number . ' - ' . ($shop->name ?? 'Guru Crackers'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-16">

    <!-- Action Bar (Hidden on Print) -->
    <div class="print:hidden flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2">
            <a href="{{ route('order.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-3 py-2 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Order Page</span>
            </a>
            <span class="text-xs font-mono font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                #{{ $order->order_number }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('order.public_invoice.download', $order->order_number) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                <i class="fa-solid fa-file-arrow-down text-rose-600"></i>
                <span>Download PDF</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white text-xs font-extrabold shadow-sm transition-all font-heading">
                <i class="fa-solid fa-print"></i>
                <span>Print Invoice</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 sm:p-10 space-y-6 print:border-none print:shadow-none print:p-0">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b-2 border-rose-600 pb-5">
            <div class="flex items-center gap-3">
                @if (!empty($shop->logo_url))
                    <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-14 h-14 object-contain rounded-xl border border-slate-200 p-1">
                @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-2xl shadow-md">
                        🎆
                    </div>
                @endif
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading leading-tight">
                        {{ strtoupper($shop->name ?? 'GURU CRACKERS') }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">{{ $shop->tagline ?? 'Sivakasi Direct Wholesale & Retail Crackers' }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        {{ $shop->address ?? 'Sivakasi' }}, {{ $shop->city }} - {{ $shop->pincode }}
                        @if (!empty($shop->phone)) &middot; Ph: {{ $shop->phone }} @endif
                        @if (!empty($shop->secondary_phone)) / {{ $shop->secondary_phone }} @endif
                    </p>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="inline-block bg-rose-50 text-rose-700 text-xs font-black px-3 py-1 rounded-lg uppercase tracking-wider border border-rose-200 font-heading">
                    Tax Invoice / Order Slip
                </div>
                <div class="text-xs text-slate-500 mt-1.5 font-mono">
                    Invoice #: <strong class="text-slate-800">{{ $order->order_number }}</strong>
                </div>
                <div class="text-[11px] text-slate-400">
                    Date: {{ $order->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        <!-- Customer & Order Meta -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 rounded-xl p-4 border border-slate-200/80 text-xs">
            <div>
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">
                    Bill To / Customer Details
                </div>
                <div class="text-sm font-extrabold text-slate-900">{{ $order->name }}</div>
                <div class="text-slate-600 mt-0.5">
                    <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $order->phone1 }}
                    @if ($order->phone2) / {{ $order->phone2 }} @endif
                </div>
            </div>

            <div>
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">
                    Dispatch Delivery Address
                </div>
                <div class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">
                    {{ $order->delivery_address }}
                </div>
                <div class="text-slate-600 font-semibold mt-0.5">
                    {{ $order->city }} - {{ $order->pincode }}
                </div>
            </div>
        </div>

        <!-- Desktop Items Table (Hidden on Mobile) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-rose-700 text-white uppercase text-[10px] font-bold tracking-wider">
                        <th class="py-2.5 px-3 text-left rounded-l-lg">#</th>
                        <th class="py-2.5 px-3 text-left">Cracker Item & Description</th>
                        <th class="py-2.5 px-3 text-center">Unit</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Price (₹)</th>
                        <th class="py-2.5 px-3 text-right rounded-r-lg">Total (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $i => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-3 text-slate-400 font-medium">{{ $i + 1 }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">
                                <span>{{ $item->product_name }}</span>
                                @if ($item->product?->unit)
                                    <span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md ml-1">{{ $item->product->unit }}</span>
                                @endif
                                @if ($item->product && !empty($item->product->tamil_name))
                                    <span class="text-slate-500 font-normal text-[11px] block sm:inline sm:ml-1">| {{ $item->product->tamil_name }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center text-slate-500">{{ $item->product->unit ?? 'Pcs' }}</td>
                            <td class="py-2.5 px-3 text-center font-extrabold text-slate-800 text-sm">{{ $item->quantity }}</td>
                            <td class="py-2.5 px-3 text-right text-slate-600">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2.5 px-3 text-right font-black text-rose-700 text-sm">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-300 font-bold">
                        <td colspan="3" class="py-3 px-3 text-slate-600">
                            Total Products: <strong class="text-slate-900">{{ $order->items->count() }}</strong>
                            &middot; Total Units: <strong class="text-slate-900">{{ $order->total_quantity }}</strong>
                        </td>
                        <td colspan="2" class="py-3 px-3 text-right text-slate-800 font-heading text-sm">Grand Total:</td>
                        <td class="py-3 px-3 text-right text-lg sm:text-xl font-black text-rose-700 font-heading">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Responsive Cards View (Like Selected Items Review) -->
        <div class="block sm:hidden space-y-2.5">
            <div class="flex items-center justify-between px-1 text-xs font-bold text-slate-700">
                <span class="uppercase tracking-wider text-[10px] text-slate-400 font-extrabold">Order Items</span>
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

            <!-- Mobile Grand Total Summary Box -->
            <div class="bg-gradient-to-r from-rose-50 to-amber-50 rounded-2xl p-3.5 border border-rose-200/70 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-extrabold text-slate-500 block">Total Payable</span>
                    <span class="text-xs font-bold text-slate-700">{{ $order->items->count() }} Items &middot; {{ $order->total_quantity }} Units</span>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-rose-700 font-heading">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($order->isDispatched())
            <!-- Transport & LR Tracking Card -->
            <div class="rounded-2xl bg-gradient-to-br from-teal-900 via-slate-900 to-emerald-950 text-white p-5 sm:p-6 space-y-4 shadow-lg border border-teal-500/30">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-400/30 flex items-center justify-center text-xl shrink-0">
                            🚚
                        </span>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-teal-300 font-extrabold">Transport Booking Slip / LR Details</div>
                            <div class="text-base sm:text-lg font-black font-heading text-white">
                                {{ $order->parcel_service_name ?: 'A1 Parcel Service' }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 px-3 py-1.5 rounded-xl border border-white/15 text-left sm:text-right">
                        <div class="text-[10px] text-slate-300 uppercase font-bold">LR / Bilty Number</div>
                        <div class="text-sm sm:text-base font-black font-mono text-amber-300 tracking-wider">
                            {{ $order->lr_number }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Parcel Count</span>
                        <span class="text-sm font-black text-amber-300 font-heading">{{ $order->parcel_count ?: '1 Box' }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Booking Date</span>
                        <span class="text-xs font-bold text-white">{{ $order->dispatch_date ? $order->dispatch_date->format('d M Y') : $order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Destination Hub</span>
                        <span class="text-xs font-bold text-white">{{ $order->destination_hub ?: $order->city }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Branch Helpline</span>
                        @if (!empty($order->transport_phone))
                            <a href="tel:{{ $order->transport_phone }}" class="text-xs font-bold text-emerald-300 hover:underline block truncate">
                                <i class="fa-solid fa-phone text-[10px] mr-0.5"></i> {{ $order->transport_phone }}
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Contact Hub</span>
                        @endif
                    </div>
                </div>

                @if ($order->lr_receipt_image)
                    <!-- Physical LR Slip Thumbnail -->
                    <div class="bg-white/5 rounded-xl p-3.5 border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ $order->lr_receipt_url }}" target="_blank" class="w-16 h-16 rounded-lg bg-black/40 overflow-hidden border border-white/20 shrink-0 block hover:opacity-90">
                                <img src="{{ $order->lr_receipt_url }}" alt="LR Slip" class="w-full h-full object-cover">
                            </a>
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-teal-400"></i>
                                    <span>Physical LR Receipt Slip (Paper Slip)</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Show this receipt slip photo and your ID proof at the parcel office to collect your boxes.
                                </div>
                            </div>
                        </div>
                        <a href="{{ $order->lr_receipt_url }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-xs font-bold text-white transition-colors shrink-0 font-heading">
                            <i class="fa-solid fa-expand mr-1"></i> View Full Receipt Photo
                        </a>
                    </div>
                @endif

                <div class="text-[11px] bg-white/5 rounded-xl p-3 border border-white/10 text-slate-300 leading-relaxed">
                    <strong class="text-amber-300">How to collect your parcel:</strong>
                    Your cracker parcel is being transported from Sivakasi to your nearest hub. When it arrives, the transport office will call your mobile number (<strong>{{ $order->phone1 }}</strong>). Kindly carry this <strong>LR No ({{ $order->lr_number }})</strong> and an official ID proof (Aadhaar / Driving License) to collect your boxes.
                </div>
            </div>
        @endif

        <!-- Payment Status & Verification Box -->
        <div class="border-t border-slate-200 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl p-4 {{ $order->isPaid() ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                <div class="flex items-center gap-2 mb-1.5">
                    @if ($order->isDispatched())
                        <i class="fa-solid fa-truck-fast text-teal-600 text-lg"></i>
                        <span class="font-extrabold text-teal-800 text-xs uppercase tracking-wider font-heading">
                            STATUS: DISPATCHED & IN TRANSIT
                        </span>
                    @elseif ($order->isPaid())
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <span class="font-extrabold text-emerald-800 text-xs uppercase tracking-wider font-heading">
                            PAYMENT STATUS: CONFIRMED & PAID
                        </span>
                    @else
                        <i class="fa-solid fa-clock text-amber-600 text-lg"></i>
                        <span class="font-extrabold text-amber-800 text-xs uppercase tracking-wider font-heading">
                            PAYMENT STATUS: PENDING VERIFICATION
                        </span>
                    @endif
                </div>

                <p class="text-[11px] text-slate-600 leading-relaxed">
                    @if ($order->isDispatched())
                        Your order has been handed over to {{ $order->parcel_service_name ?? 'the parcel service' }} (LR: {{ $order->lr_number }}). You can track and collect your cracker parcel using the details above.
                    @elseif ($order->isPaid())
                        Your payment of ₹{{ number_format($order->total_amount, 2) }} has been received and verified. Your parcel is being packed and prepared for dispatch!
                    @else
                        Please pay ₹{{ number_format($order->total_amount, 2) }} via UPI to complete your booking. Send your payment screenshot on WhatsApp to confirm parcel transport.
                    @endif
                </p>

                @if (!empty($shop->upi_id) && !$order->isPaid())
                    <div class="mt-2.5 pt-2 border-t border-amber-200/60 flex items-center justify-between text-xs">
                        <span class="text-slate-600">Shop UPI ID:</span>
                        <code class="font-mono font-black text-amber-900 bg-white/80 px-2 py-0.5 rounded border border-amber-300">{{ $shop->upi_id }}</code>
                    </div>
                @endif
            </div>

            <div class="text-xs text-slate-500 space-y-1.5 flex flex-col justify-center">
                <div class="font-bold text-slate-700">Dispatch & Transport Notice:</div>
                <p class="text-[11px] leading-relaxed">
                    All fireworks are packed safely with heavy corrugated boxes as per Sivakasi standard. Goods will be dispatched via certified transport to your nearest branch.
                </p>
                <div class="text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} {{ $shop->name ?? 'Guru Crackers' }} &middot; Sivakasi, Tamil Nadu
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
