@extends('layouts.app')

@section('title', 'Admin Login - Guru Crackers')

@section('content')
<div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl shadow-2xl shadow-black/40 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-rose-900 via-rose-800 to-amber-700 text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-rose-500/30 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl shadow-inner">
                    💥
                </div>
                <h1 class="text-xl sm:text-2xl font-black font-heading tracking-wide">
                    Admin Portal Login
                </h1>
                <p class="text-xs text-rose-100/80 mt-1 font-medium">
                    Guru Crackers Management System
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

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Admin Email Address
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            minlength="5"
                            maxlength="80"
                            autofocus
                            placeholder="admin@gurucrackers.com"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800 transition-colors"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Password
                        </label>
                        <a
                            href="{{ route('admin.password.request') }}"
                            class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition-colors cursor-pointer hover:underline"
                        >
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input
                            type="password"
                            name="password"
                            id="loginPasswordInput"
                            required
                            minlength="4"
                            maxlength="64"
                            placeholder="••••••••"
                            class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800 transition-colors"
                        >
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('loginPasswordInput', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1"
                            title="Show/Hide Password"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="w-3.5 h-3.5 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer"
                        >
                        <span class="text-xs font-medium text-slate-600">Keep me logged in</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full mt-2 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-600 via-rose-700 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-md shadow-rose-600/20 active:scale-95 transition-all font-heading cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
                    <span>Secure Admin Login</span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-100">
                <a href="{{ route('order.create') }}" class="text-[11px] font-semibold text-slate-500 hover:text-rose-600 transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Return to Customer Store</span>
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
