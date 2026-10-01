@extends('layouts.app')

@section('title', 'Track Your Order - ' . ($shop->name ?? 'Guru Crackers'))

@section('content')
<div class="max-w-xl mx-auto space-y-6 py-6 sm:py-10">

    <!-- Header Card -->
    <div class="bg-gradient-to-br from-slate-900 via-rose-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 text-center space-y-3 shadow-xl relative overflow-hidden">
        <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-amber-300 mx-auto flex items-center justify-center text-3xl shadow-md">
            <i class="fa-solid fa-truck-fast animate-pulse"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black font-heading text-white">
            Track Your Cracker Order
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-sm mx-auto leading-relaxed">
            Enter your <strong>Booking Order ID</strong> (e.g. GC-20260922-XXXXX) and <strong>Registered Mobile Number</strong> to view invoice, payment & transport LR tracking slip.
        </p>

        <!-- Search Form -->
        <form method="GET" action="{{ route('order.track') }}" class="pt-3" onsubmit="if (!this.order_number.value.trim() || !this.phone.value.trim()) { if (window.DiwaliAlert) { DiwaliAlert.toast({type:'warning', text:'Please enter both Order ID and 10-digit Phone Number!'}); } else { alert('Please enter both Order ID and 10-digit Phone Number'); } return false; }">
            <div class="space-y-3 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-inner text-left">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-amber-300 mb-1">
                            <i class="fa-solid fa-receipt mr-1 text-[10px]"></i> Order ID
                        </label>
                        <input
                            type="text"
                            name="order_number"
                            value="{{ $cleanOrderNumber ?? ($query ?? '') }}"
                            placeholder="e.g. GC-20260922-XXXXX"
                            minlength="3"
                            maxlength="30"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\-]/g, '').replace(/\-{2,}/g, '-').replace(/^\-+/, '')"
                            class="w-full px-3.5 py-2.5 text-sm bg-white text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400 font-bold placeholder:text-slate-400 placeholder:font-normal"
                            required
                            autofocus
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-amber-300 mb-1">
                            <i class="fa-solid fa-phone mr-1 text-[10px]"></i> Registered Mobile
                        </label>
                        <input
                            type="tel"
                            name="phone"
                            value="{{ $cleanPhone ?? '' }}"
                            placeholder="10-digit Mobile Number"
                            pattern="[0-9]{10}"
                            minlength="10"
                            maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-3.5 py-2.5 text-sm bg-white text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400 font-bold placeholder:text-slate-400 placeholder:font-normal"
                            required
                        >
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-600 hover:to-rose-700 text-white font-extrabold text-sm shadow-md font-heading transition-all flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Track Order Securely</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @if ($searched)
        @if ($orders->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center space-y-3 border border-slate-200 shadow-sm">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base font-heading">
                    {{ !empty($trackError) ? 'Invalid Track Search' : 'No Orders Found' }}
                </h3>
                <p class="text-xs text-slate-500 max-w-xs mx-auto">
                    @if (!empty($trackError))
                        {{ $trackError }}
                    @else
                        We could not find any orders matching "<strong>{{ $query }}</strong>". Please double check your order number or phone number.
                    @endif
                </p>
                <div class="pt-2">
                    <a href="{{ route('order.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold font-heading hover:bg-rose-700 transition-colors">
                        <span>Place New Order</span>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 px-1">
                    Matching Orders ({{ $orders->count() }})
                </h3>

                @foreach ($orders as $ord)
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-rose-300 transition-all space-y-3">
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div>
                                <span class="font-mono text-xs font-extrabold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                    #{{ $ord->order_number }}
                                </span>
                                <span class="text-[11px] text-slate-400 ml-2">
                                    {{ $ord->created_at->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            @if ($ord->isDispatched())
                                <span class="bg-teal-100 text-teal-800 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                                    <i class="fa-solid fa-truck-fast"></i> Dispatched
                                </span>
                            @elseif ($ord->isPaid())
                                <span class="bg-emerald-100 text-emerald-800 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Paid
                                </span>
                            @else
                                <span class="bg-amber-100 text-amber-800 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                                    <i class="fa-solid fa-clock"></i> Payment Pending
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Customer</span>
                                <strong class="text-slate-900">{{ $ord->name }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Destination</span>
                                <span class="text-slate-700 font-semibold">{{ $ord->city }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Amount</span>
                                <strong class="text-rose-700 font-extrabold">₹{{ number_format($ord->total_amount, 2) }}</strong>
                            </div>
                        </div>

                        @if ($ord->isDispatched())
                            <div class="bg-teal-50 border border-teal-200 rounded-xl p-3 text-xs text-teal-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                <div>
                                    <strong class="font-heading font-bold">{{ $ord->parcel_service_name ?: 'A1 Parcel Service' }}</strong>
                                    <span class="text-[11px] text-teal-700 ml-1 font-mono font-bold">(LR: {{ $ord->lr_number }})</span>
                                    @if ($ord->delivery_charges !== null && (float)$ord->delivery_charges > 0)
                                        <div class="text-[11px] text-amber-800 font-bold mt-0.5">
                                            Delivery Charges: ₹{{ number_format($ord->delivery_charges, 2) }} (To Pay at Hub)
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-teal-800 bg-teal-200/60 px-2 py-0.5 rounded">
                                    {{ $ord->parcel_count ?: '1 Box' }}
                                </span>
                            </div>
                        @endif

                        <div class="pt-1 flex items-center justify-end">
                            <a
                                href="{{ route('order.public_invoice', $ord->order_number) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold font-heading transition-colors shadow-sm"
                            >
                                <span>View Invoice & LR Details</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

</div>
@endsection
