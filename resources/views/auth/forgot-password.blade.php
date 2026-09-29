@extends('layouts.app')

@section('title', 'Forgot Password - Guru Crackers Admin')

@section('content')
<div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl shadow-2xl shadow-black/40 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-rose-900 via-rose-800 to-amber-700 text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-rose-500/30 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl shadow-inner">
                    🔐
                </div>
                <h1 class="text-xl sm:text-2xl font-black font-heading tracking-wide">
                    Reset Admin Password
                </h1>
                <p class="text-xs text-rose-100/80 mt-1 font-medium">
                    Instant WhatsApp OTP Verification
                </p>
            </div>
        </div>

        <!-- Body Form -->
        <div class="p-6 sm:p-8 space-y-5">
            <!-- Instructions Banner -->
            <div class="bg-emerald-50 border border-emerald-200/80 rounded-2xl p-3.5 text-xs text-emerald-900 flex items-start gap-3">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                    <i class="fa-brands fa-whatsapp"></i>
                </span>
                <div class="space-y-0.5 leading-relaxed">
                    <p class="font-bold">Fast & Secure Verification</p>
                    <p class="text-[11px] text-emerald-800/80">
                        Enter your Admin email address or registered mobile number. We will send a 6-digit OTP code directly to your WhatsApp.
                    </p>
                </div>
            </div>

            <!-- Error Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-4">
                @csrf

                <!-- Identifier Input (Email or Phone) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Admin Email or Phone Number <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user-shield absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input
                            type="text"
                            name="identifier"
                            value="{{ old('identifier') }}"
                            required
                            minlength="3"
                            maxlength="100"
                            autofocus
                            placeholder="e.g. admin@gurucrackers.com or 9789874381"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800 transition-colors"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full mt-2 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-md shadow-emerald-600/20 active:scale-95 transition-all font-heading cursor-pointer"
                >
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Send Verification Code to WhatsApp</span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-100">
                <a href="{{ route('admin.login') }}" class="text-[11px] font-semibold text-slate-500 hover:text-rose-600 transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Remember your password? Back to Login</span>
                </a>
            </div>
        </div>

    </div>
@endsection
