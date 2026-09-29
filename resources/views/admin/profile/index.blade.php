@extends('layouts.app')

@section('title', 'Admin Profile & Account Settings')

@section('content')
<div class="mx-auto space-y-6 pb-12">

    {{-- ═══════════════════ HEADER BANNER ═══════════════════ --}}
    <div class="relative bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl overflow-hidden border border-indigo-900/40">
        <div class="absolute -right-10 -bottom-8 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/4 -top-16 w-52 h-52 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Large Avatar -->
                <div class="relative shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-rose-600 via-purple-600 to-amber-500 text-white flex items-center justify-center font-black text-2xl sm:text-3xl shadow-xl ring-4 ring-white/10">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center text-[10px] text-white" title="Active">
                        <i class="fa-solid fa-check text-[8px]"></i>
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-wider">
                            Super Admin
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            <i class="fa-solid fa-shield-halved text-[9px]"></i> Full Access
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-heading tracking-tight flex items-center gap-2">
                        <span>{{ $user->name }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-0.5 flex items-center gap-3 flex-wrap">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-indigo-400"></i>
                            <span>{{ $user->email }}</span>
                        </span>
                        @if ($user->phone)
                            <span class="flex items-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                                <span>+91 {{ $user->phone }}</span>
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Header Quick Stats -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 flex-wrap">
                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Total Orders</span>
                    <span class="text-lg font-black text-white font-mono">{{ $stats['total_orders'] }}</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Pending</span>
                    <span class="text-lg font-black text-amber-400 font-mono">{{ $stats['pending_orders'] }}</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Member Since</span>
                    <span class="text-xs font-bold text-slate-200 block mt-1">{{ $stats['account_created'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ FLASH ALERTS ═══════════════════ --}}
    @if (session('status') || session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg shrink-0"></i>
            <span class="text-sm font-bold">{{ session('status') ?? session('success') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 px-5 py-3.5 rounded-2xl shadow-xs">
            <div class="flex items-center gap-2 font-bold text-sm mb-1 text-rose-700">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-rose-800 space-y-0.5 ml-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════════════ MAIN CONTENT GRID ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ───── LEFT 2 COLUMNS: Profile Edit & Password Change Forms ───── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── CARD 1: PROFILE INFORMATION ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold border border-indigo-200/60">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 font-heading">Personal Information</h3>
                            <p class="text-[11px] text-slate-500">Update your name, email, and WhatsApp recovery contact</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded">ID: #{{ $user->id }}</span>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="name" id="name" required
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-hidden"
                                    placeholder="Enter full name">
                            </div>
                            @error('name')
                                <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" name="email" id="email" required
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-hidden"
                                    placeholder="admin@example.com">
                            </div>
                            @error('email')
                                <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- WhatsApp Recovery Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Admin WhatsApp Phone Number
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 text-xs font-bold">
                                <i class="fa-brands fa-whatsapp text-sm mr-1"></i> +91
                            </span>
                            <input type="text" name="phone" id="phone" maxlength="10"
                                value="{{ old('phone', $user->phone) }}"
                                class="w-full pl-16 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-hidden"
                                placeholder="10-digit mobile number">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info text-slate-400 text-[10px]"></i>
                            <span>Security alerts, login notifications, and password change alerts will be sent here.</span>
                        </p>
                        @error('phone')
                            <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-check"></i>
                            <span>Save Profile Changes</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── CARD 2: CHANGE PASSWORD & SECURITY ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold border border-amber-200/60">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 font-heading">Security & Password</h3>
                            <p class="text-[11px] text-slate-500">Ensure your account is using a strong, secure password</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i> End-to-end Hashed
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.profile.password') }}" class="p-6 space-y-4">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Current Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-hidden"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('current_password', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-xs font-bold text-slate-700 mb-1.5">
                                New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-shield-cat"></i>
                                </span>
                                <input type="password" name="password" id="new_password" required minlength="6"
                                    class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-hidden"
                                    placeholder="Min 6 characters">
                                <button type="button" onclick="togglePasswordVisibility('new_password', this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Confirm New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-shield-heart"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                                    class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-hidden"
                                    placeholder="Re-type new password">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Note about WhatsApp Notification -->
                    <div class="p-3 bg-amber-50/70 border border-amber-200/70 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                        <i class="fa-solid fa-bell text-amber-600 mt-0.5 shrink-0"></i>
                        <div>
                            <span class="font-bold">Automated Security Notice:</span> When your password is changed, a WhatsApp confirmation alert will automatically be dispatched to your registered WhatsApp number for verification.
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-lock"></i>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ───── RIGHT COLUMN: Profile Info Card & Recent Audit Logs ───── --}}
        <div class="space-y-6">

            {{-- ── CARD 3: ACCOUNT SUMMARY & SYSTEM INFO ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <i class="fa-solid fa-server text-rose-400"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 font-heading">Session & Environment</h4>
                        <p class="text-[11px] text-slate-500">Current login details & server status</p>
                    </div>
                </div>

                <div class="space-y-2.5 divide-y divide-slate-100 text-xs">
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-slate-500">Role</span>
                        <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px]">Super Administrator</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-slate-500">Your IP Address</span>
                        <span class="font-mono text-slate-700 font-bold text-[11px]">{{ request()->ip() }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-slate-500">Last Login Event</span>
                        <span class="font-bold text-emerald-600 text-[11px]">{{ $stats['last_login'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-slate-500">Server Time</span>
                        <span class="font-mono text-slate-700 text-[11px]">{{ now()->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-slate-500">Shop Name</span>
                        <span class="font-bold text-slate-800 text-[11px] truncate max-w-[150px]">{{ $shop->shop_name ?? 'Guru Crackers' }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <a href="{{ route('admin.shop.edit') }}"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200/80 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-store text-slate-500"></i>
                            <span>Store Settings</span>
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    </a>

                    <a href="{{ route('admin.audit_logs.index') }}"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200/80 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-slate-500"></i>
                            <span>View All Audit Logs</span>
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    </a>

                    <a href="{{ route('admin.database.index') }}"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold border border-red-200/80 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-database text-red-500"></i>
                            <span>Database Manager</span>
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-red-400"></i>
                    </a>
                </div>
            </div>

            {{-- ── CARD 4: RECENT AUDIT ACTIVITIES ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-600 text-xs"></i>
                        <h4 class="text-xs font-bold text-slate-800 font-heading">Recent Activities</h4>
                    </div>
                    <a href="{{ route('admin.audit_logs.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition">View All</a>
                </div>

                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    @forelse ($recentLogs as $log)
                        <div class="p-3 hover:bg-slate-50/80 transition flex items-start gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                @if (str_contains($log->event, 'login') || str_contains($log->event, 'auth'))
                                    <i class="fa-solid fa-key"></i>
                                @elseif (str_contains($log->event, 'order'))
                                    <i class="fa-solid fa-receipt"></i>
                                @elseif (str_contains($log->event, 'database'))
                                    <i class="fa-solid fa-database"></i>
                                @else
                                    <i class="fa-solid fa-bolt"></i>
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[11px] font-bold text-slate-800 truncate capitalize">{{ str_replace('_', ' ', $log->event) }}</span>
                                    <span class="text-[9px] text-slate-400 font-mono shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $log->summary }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-regular fa-clipboard text-slate-300 text-xl mb-1"></i>
                            <p>No recent activity logs found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        field.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
@endsection
