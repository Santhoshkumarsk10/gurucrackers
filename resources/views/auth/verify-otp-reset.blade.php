@extends('layouts.app')

@section('title', 'Verify WhatsApp OTP - Guru Crackers Admin')

@section('content')
<div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl shadow-2xl shadow-black/40 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-teal-500/30 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl shadow-inner text-emerald-400">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-black font-heading tracking-wide">
                    Verify WhatsApp Code
                </h1>
                <p class="text-xs text-emerald-100/80 mt-1 font-medium">
                    Sent to {{ $maskedPhone }}
                </p>
            </div>
        </div>

        <!-- Body Form -->
        <div class="p-6 sm:p-8 space-y-5">
            <!-- Status Alert -->
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Error Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
                @csrf

                <!-- OTP Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">
                        Enter 6-Digit WhatsApp OTP
                    </label>
                    <div class="relative max-w-[240px] mx-auto">
                        <input
                            type="text"
                            name="otp"
                            id="otpInput"
                            value="{{ old('otp') }}"
                            required
                            minlength="6"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            autofocus
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.6em] text-xl font-mono py-2.5 bg-slate-50 border-2 border-emerald-300 rounded-2xl focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-600 font-black text-slate-900 transition-all placeholder:tracking-normal placeholder:font-sans placeholder:text-slate-300"
                        >
                    </div>
                    <p class="text-[11px] text-center text-slate-400 mt-1">Code is valid for 10 minutes</p>
                </div>

                <div class="border-t border-slate-100 pt-3 space-y-3">
                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            New Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input
                                type="password"
                                name="password"
                                id="newPasswordInput"
                                required
                                minlength="6"
                                maxlength="64"
                                placeholder="At least 6 characters"
                                class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-800 transition-colors"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('newPasswordInput', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1"
                                title="Show/Hide Password"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Confirm New Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock-open absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="confirmPasswordInput"
                                required
                                minlength="6"
                                maxlength="64"
                                placeholder="Re-type new password"
                                class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-800 transition-colors"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('confirmPasswordInput', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1"
                                title="Show/Hide Password"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full mt-2 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-md shadow-emerald-600/20 active:scale-95 transition-all font-heading cursor-pointer"
                >
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                    <span>Set New Password & Login</span>
                </button>
            </form>

            <!-- Resend / Back Links -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                <a href="{{ route('admin.password.request') }}" class="font-bold text-emerald-600 hover:text-emerald-800 transition-colors inline-flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Resend OTP Code</span>
                </a>

                <a href="{{ route('admin.login') }}" class="font-medium text-slate-500 hover:text-slate-800 transition-colors">
                    Back to Login
                </a>
            </div>
        </div>

    </div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
