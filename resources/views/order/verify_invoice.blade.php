@extends('layouts.app')

@section('title', 'Verify Phone Number - Invoice Access')

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-14 px-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 space-y-6 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 mx-auto flex items-center justify-center text-3xl shadow-sm">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                <i class="fa-solid fa-lock text-[10px]"></i> Order Verification
            </span>
            <h1 class="text-xl sm:text-2xl font-black font-heading text-slate-900">
                Invoice Security Check
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                To protect customer privacy and personal order details, please verify the <strong>10-digit mobile number</strong> used when booking <strong>#{{ $order->order_number }}</strong>.
            </p>
        </div>

        @if (!empty($verificationError))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-semibold flex items-center gap-2 text-left">
                <i class="fa-solid fa-circle-exclamation shrink-0 text-sm"></i>
                <span>{{ $verificationError }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('order.public_invoice', $order->order_number) }}" class="space-y-4">
            @csrf
            <div class="text-left space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Customer Mobile Number</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 font-semibold text-xs">
                        +91
                    </span>
                    <input
                        type="tel"
                        name="verify_phone"
                        pattern="[6-9][0-9]{9}"
                        minlength="10"
                        maxlength="10"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        placeholder="Enter 10-digit mobile..."
                        class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all"
                        required
                        autofocus
                    >
                </div>
            </div>

            <button
                type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-600 hover:to-rose-700 text-white font-extrabold text-sm shadow-md font-heading transition-all flex items-center justify-center gap-2"
            >
                <i class="fa-solid fa-unlock"></i>
                <span>Verify & View Invoice</span>
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <a href="{{ route('order.track') }}" class="hover:text-rose-600 font-medium">
                <i class="fa-solid fa-arrow-left mr-1"></i> Track Order
            </a>
            <a href="{{ route('order.create') }}" class="hover:text-amber-600 font-medium">
                Shop Crackers <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>
@endsection
