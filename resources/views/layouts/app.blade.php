<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Guru Crackers - Sivakasi Direct Wholesale & Retail Crackers')</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <meta name="theme-color" content="#9f1239">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        festive: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 CDN for Festive Custom Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Cracker Fuse Vertical Scrollbar - Hide default root scrollbar */
        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width: 0px !important;
            height: 0px !important;
            display: none !important;
        }

        html,
        body {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        /* Inner scrollable containers (tables, modal dialogs) */
        .custom-inner-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-inner-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-inner-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        .custom-inner-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* ========================================================
           SIVAKASI CRACKER FUSE SCROLLBAR (வெடி திரி) STYLES
           ======================================================== */
        .cracker-fuse-unburnt {
            background: repeating-linear-gradient(-45deg,
                    #dc2626 0px,
                    #dc2626 3.5px,
                    #fef08a 3.5px,
                    #fef08a 5.5px,
                    #16a34a 5.5px,
                    #16a34a 7.5px,
                    #fff1f2 7.5px,
                    #fff1f2 10px);
            box-shadow: inset 0.5px 0 1px rgba(0, 0, 0, 0.45), inset -0.5px 0 1px rgba(0, 0, 0, 0.45);
        }

        .cracker-fuse-burnt {
            background: linear-gradient(to top, #171717, #262626),
                repeating-linear-gradient(-45deg,
                    #0a0a0a 0px,
                    #0a0a0a 3px,
                    #262626 3px,
                    #262626 5px,
                    #404040 5px,
                    #404040 7px);
            box-shadow: inset 0.5px 0 1.5px rgba(0, 0, 0, 0.9), inset -0.5px 0 1.5px rgba(0, 0, 0, 0.9);
            border-top: 2px solid #ea580c;
        }

        @keyframes sparkPulse {

            0%,
            100% {
                transform: scale(1);
                filter: drop-shadow(0 0 6px #f59e0b) drop-shadow(0 0 14px #ea580c);
            }

            50% {
                transform: scale(1.15) rotate(5deg);
                filter: drop-shadow(0 0 10px #fbbf24) drop-shadow(0 0 22px #ef4444);
            }
        }

        .animate-spark-pulse {
            animation: sparkPulse 0.8s infinite ease-in-out;
        }

        /* Pulse glow animation */
        @keyframes softPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.92;
                transform: scale(1.02);
            }
        }

        .animate-soft-pulse {
            animation: softPulse 2.5s infinite ease-in-out;
        }

        /* ==========================================
           DIWALI CUSTOM SWEETALERT2 STYLING
           ========================================== */
        div.swal2-popup.diwali-swal-popup {
            border-radius: 1.5rem !important;
            padding: 1.75rem 1.5rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border: 1.5px solid rgba(244, 63, 94, 0.25) !important;
            box-shadow: 0 25px 50px -12px rgba(159, 18, 57, 0.3) !important;
            background: #ffffff !important;
        }

        .diwali-swal-title {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            font-size: 1.25rem !important;
            padding-top: 0.5rem !important;
        }

        .diwali-swal-html {
            font-size: 0.875rem !important;
            color: #475569 !important;
            line-height: 1.6 !important;
            margin-top: 0.5rem !important;
        }

        .diwali-swal-confirm-btn {
            background: linear-gradient(135deg, #e11d48, #d97706) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            border-radius: 0.875rem !important;
            padding: 0.75rem 1.65rem !important;
            font-size: 0.8125rem !important;
            font-family: 'Outfit', sans-serif !important;
            letter-spacing: 0.025em !important;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.35) !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            margin: 0.25rem !important;
        }

        .diwali-swal-confirm-btn:hover {
            transform: translateY(-1.5px) !important;
            box-shadow: 0 8px 25px rgba(225, 29, 72, 0.5) !important;
        }

        .diwali-swal-cancel-btn {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-weight: 700 !important;
            border-radius: 0.875rem !important;
            padding: 0.75rem 1.35rem !important;
            font-size: 0.8125rem !important;
            font-family: 'Outfit', sans-serif !important;
            border: 1px solid #e2e8f0 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            margin: 0.25rem !important;
        }

        .diwali-swal-cancel-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Festive Dark Glassmorphism Toast */
        div.swal2-popup.diwali-toast {
            background: rgba(15, 23, 42, 0.96) !important;
            backdrop-filter: blur(12px) !important;
            border-radius: 1rem !important;
            border: 1px solid rgba(244, 63, 94, 0.35) !important;
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5) !important;
            padding: 0.75rem 1.15rem !important;
        }

        .diwali-toast .swal2-title {
            color: #ffffff !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .diwali-toast .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #f43f5e, #fbbf24) !important;
            height: 3px !important;
        }

        /* Modern Dark Scrollbar for Admin Sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-rose-500 selection:text-white">
    @if (request()->routeIs('admin.login', 'admin.login.*', 'admin.password.*'))
        <!-- ============================================================== -->
        <!-- ADMIN AUTHENTICATION LAYOUT (Dedicated, Centered & Secure)     -->
        <!-- ============================================================== -->
        <div class="min-h-screen flex flex-col justify-between bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950 text-slate-100 selection:bg-rose-500 selection:text-white relative">
            <!-- Decorative Festive Ambient Glows -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-rose-600/15 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-rose-500/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Top Header for Auth Pages -->
            <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-5 flex items-center justify-between z-10">
                <a href="{{ route('order.create') }}" class="flex items-center gap-3 group transition-transform hover:scale-[1.02]">
                    @if (!empty($shop->logo_url))
                        <img src="{{ $shop->logo_url }}" alt="Logo"
                            class="w-10 h-10 rounded-xl object-contain bg-white p-1 border border-white/20 shadow-md">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 flex items-center justify-center text-white shadow-md text-lg">
                            💥
                        </div>
                    @endif
                    <div>
                        <div class="font-extrabold text-sm sm:text-base text-white font-heading tracking-wide leading-none flex items-center gap-2">
                            <span>{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</span>
                            <span class="text-[9px] bg-rose-500/30 text-rose-300 border border-rose-500/40 px-1.5 py-0.5 rounded font-bold uppercase">{{ $shop->city ?? 'Sivakasi' }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Direct Wholesale & Retail Cracker Management</p>
                    </div>
                </a>

                <a href="{{ route('order.create') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-slate-200 hover:text-white bg-white/10 hover:bg-white/15 border border-white/15 px-3.5 py-2 rounded-xl transition shadow-xs active:scale-95">
                    <i class="fa-solid fa-store text-amber-400 text-xs"></i>
                    <span class="hidden sm:inline">Go to Customer Store</span>
                    <span class="sm:hidden">Store</span>
                </a>
            </header>

            <!-- Main Auth Card Container -->
            <main class="flex-1 flex items-center justify-center px-4 py-8 z-10 w-full">
                @yield('content')
            </main>

            <!-- Minimal Footer -->
            <footer class="w-full py-4 px-4 text-center text-xs text-slate-500 z-10 border-t border-white/5">
                <p>&copy; {{ date('Y') }} {{ $shop->name ?? 'Guru Crackers' }} ({{ $shop->city ?? 'Sivakasi' }}). Authorized Administrative Access Only.</p>
            </footer>
        </div>
    @elseif (request()->routeIs('admin.*'))
        <!-- ============================================================== -->
        <!-- ADMIN PANEL MODERN LEFT SIDEBAR LAYOUT                         -->
        <!-- ============================================================== -->

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="adminSidebarBackdrop" onclick="closeAdminSidebar()"
            class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 lg:hidden transition-opacity"></div>

        <!-- Admin Left Sidebar -->
        <aside id="adminSidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">

            <!-- Sidebar Brand Header -->
            <div class="h-16 px-4 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    @if (!empty($shop->logo_url))
                        <img src="{{ $shop->logo_url }}" alt="Logo"
                            class="w-8 h-8 rounded-lg object-contain bg-white p-0.5 border border-slate-700 shadow-xs">
                    @else
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-tr from-rose-600 to-amber-500 flex items-center justify-center text-white shadow-xs">
                            <span class="text-sm">🎆</span>
                        </div>
                    @endif
                    <div>
                        <div
                            class="font-extrabold text-sm text-white font-heading tracking-wide leading-none flex items-center gap-1.5">
                            <span>{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span
                                class="text-[9px] bg-rose-500/20 text-rose-300 px-1.5 py-0.2 rounded font-bold uppercase">{{ $shop->city ?? 'Sivakasi' }}</span>
                            <span class="text-[9px] text-slate-400 font-medium">Admin Panel</span>
                        </div>
                    </div>
                </a>
                <!-- Mobile Close Button (Hidden on Desktop) -->
                <button type=button onclick="closeAdminSidebar()"
                    class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition cursor-pointer"
                    title="Close Sidebar">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Sidebar Navigation Menu (Scrollable) -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-5 select-none custom-scrollbar">
                <!-- GROUP: CORE -->
                <div>
                    <div
                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-3 mb-1.5 font-heading">
                        Main Menu
                    </div>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i
                                class="fa-solid fa-gauge-high text-xs w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-amber-300' : 'text-slate-400' }}"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Orders -->
                        <a href="{{ route('admin.orders.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <div class="flex items-center gap-3">
                                <i
                                    class="fa-solid fa-boxes-stacked text-xs w-4 text-center {{ request()->routeIs('admin.orders.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>Orders</span>
                            </div>
                            <span id="adminSidebarOrdersBadge"
                                class="hidden bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-xs">
                                0
                            </span>
                        </a>

                        <!-- Products -->
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.products.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i
                                class="fa-solid fa-fire-flame-curved text-xs w-4 text-center {{ request()->routeIs('admin.products.*') ? 'text-amber-200' : 'text-amber-400' }}"></i>
                            <span>Products</span>
                        </a>

                        <!-- Categories -->
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i
                                class="fa-solid fa-tags text-xs w-4 text-center {{ request()->routeIs('admin.categories.*') ? 'text-rose-100' : 'text-blue-400' }}"></i>
                            <span>Categories</span>
                        </a>

                        <!-- Reports -->
                        <a href="{{ route('admin.reports.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i
                                class="fa-solid fa-chart-line text-xs w-4 text-center {{ request()->routeIs('admin.reports.*') ? 'text-emerald-200' : 'text-emerald-400' }}"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>

                <!-- GROUP: COMMUNICATION & TOOLS -->
                <div>
                    <div
                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-3 mb-1.5 font-heading">
                        Engagement & Chat
                    </div>
                    <div class="space-y-1">
                        <!-- WhatsApp Live Chat -->
                        <a href="{{ route('admin.whatsapp.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.whatsapp.index') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-brands fa-whatsapp text-sm w-4 text-center text-emerald-400"></i>
                                <span>WhatsApp Chat</span>
                            </div>
                            <span
                                class="text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1.5 py-0.2 rounded-full font-bold">Live</span>
                        </a>

                        <!-- Bulk Messaging -->
                        <a href="{{ route('admin.messaging.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.messaging.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i class="fa-solid fa-paper-plane text-xs w-4 text-center text-emerald-400"></i>
                            <span>Bulk Messaging</span>
                        </a>
                    </div>
                </div>

                <!-- GROUP: STORE MANAGEMENT & SETTINGS -->
                <div>
                    <div
                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-3 mb-1.5 font-heading">
                        Management & Settings
                    </div>
                    <div class="space-y-1">
                        <!-- Festival Banners -->
                        <a href="{{ route('admin.banners.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.banners.*') ? 'bg-rose-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i class="fa-solid fa-images text-xs w-4 text-center text-rose-400"></i>
                            <span>Festival Banners</span>
                        </a>

                        <!-- Shop Details -->
                        <a href="{{ route('admin.shop.edit') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.shop.*') ? 'bg-rose-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i class="fa-solid fa-store text-xs w-4 text-center text-rose-400"></i>
                            <span>Shop Details</span>
                        </a>

                        <!-- Application Audit Log -->
                        <a href="{{ route('admin.audit_logs.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.audit_logs.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-clock-rotate-left text-xs w-4 text-center text-indigo-400"></i>
                                <span>App Audit Log</span>
                            </div>
                            <span
                                class="text-[8px] bg-indigo-500/20 text-indigo-300 px-1 py-0.2 rounded font-extrabold">100%</span>
                        </a>

                        <!-- WhatsApp Audit Log -->
                        <a href="{{ route('admin.whatsapp.audit_log') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.whatsapp.audit_log') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <i class="fa-solid fa-clipboard-check text-xs w-4 text-center text-indigo-400"></i>
                            <span>WhatsApp Logs</span>
                        </a>

                        <!-- Database Manager -->
                        <a href="{{ route('admin.database.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.database.*') ? 'bg-red-700 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-database text-xs w-4 text-center text-red-400"></i>
                                <span>DB Manager</span>
                            </div>
                            <span
                                class="text-[8px] bg-red-500/20 text-red-300 px-1 py-0.2 rounded font-extrabold">⚠</span>
                        </a>

                        <!-- Admin Profile Link -->
                        <a href="{{ route('admin.profile') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.profile*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-gear text-xs w-4 text-center {{ request()->routeIs('admin.profile*') ? 'text-white' : 'text-indigo-400' }}"></i>
                                <span>My Profile</span>
                            </div>
                            <span
                                class="text-[8px] bg-indigo-500/20 text-indigo-300 px-1 py-0.2 rounded font-extrabold">ADMIN</span>
                        </a>

                        <!-- Change Password Modal -->
                        <button type="button" onclick="closeAdminSidebar(); openChangePasswordModal();"
                            class="w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all cursor-pointer">
                            <i class="fa-solid fa-key text-xs w-4 text-center text-amber-400"></i>
                            <span>Change Password</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Footer (Store link + Admin User + Logout) -->
            <div class="p-3 bg-slate-950/80 border-t border-slate-800 shrink-0 space-y-2">
                <!-- Customer Storefront Link -->
                <a href="{{ route('order.create') }}" target="_blank"
                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold bg-slate-800/80 hover:bg-slate-800 text-emerald-400 hover:text-emerald-300 border border-slate-700/60 transition group">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                        <span>Customer Store</span>
                    </span>
                    <span class="text-[9px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.2 rounded font-mono">Live
                        ↗</span>
                </a>

                <!-- Admin Profile & Logout -->
                <div class="flex items-center justify-between gap-2 pt-1">
                    <a href="{{ route('admin.profile') }}"
                        class="flex items-center gap-2.5 min-w-0 p-1.5 rounded-xl hover:bg-slate-900 transition group flex-1"
                        title="View & Edit Admin Profile">
                        <div
                            class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs group-hover:scale-105 transition">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-white truncate group-hover:text-rose-400 transition">{{ Auth::user()->name ?? 'Administrator' }}</div>
                            <div class="text-[10px] text-slate-400 truncate flex items-center gap-1">
                                <span>Profile & Security</span>
                                <i class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition"></i>
                            </div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-rose-600/80 text-slate-300 hover:text-white transition cursor-pointer"
                            title="Sign Out">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Admin Main Content Column (Offset by 256px on lg screens) -->
        <div class="lg:pl-64 flex flex-col flex-1 min-h-screen bg-slate-50/70">
            <!-- Admin Top Navigation Header -->
            <header
                class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs h-16 flex items-center justify-between px-4 sm:px-6">
                <!-- Left: Mobile Menu Toggle & Title -->
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleAdminSidebar()"
                        class="lg:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm transition cursor-pointer"
                        title="Toggle Admin Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-500 font-heading">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-slate-800 font-extrabold">Guru Crackers</span>
                        <span class="text-slate-400">/</span>
                        <span class="text-rose-600">Admin Console</span>
                    </div>
                </div>

                <!-- Right: Actions, Notification Bell, Store Link, Logout -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Quick Store Link -->
                    <a href="{{ route('order.create') }}" target="_blank"
                        class="hidden md:inline-flex items-center gap-1.5 text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 px-2.5 py-1.5 rounded-xl transition-colors shrink-0"
                        title="Open customer price list in new tab">
                        <i class="fa-solid fa-store text-xs text-emerald-600"></i>
                        <span>View Store</span>
                    </a>

                    <!-- QR Standee Link -->
                    <a href="javascript:void(0);" onclick="closeAdminSidebar(); openOrderQrModal();"
                        class="hidden md:inline-flex items-center gap-1.5 text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200/80 px-2.5 py-1.5 rounded-xl transition-colors shrink-0"
                        title="Open QR Standee">
                        <i class="fa-solid fa-qrcode text-xs text-purple-600"></i>
                        <span>QR Standee</span>
                    </a>

                    <!-- Admin Profile Quick Link -->
                    <a href="{{ route('admin.profile') }}"
                        class="hidden md:inline-flex items-center gap-1.5 text-xs font-bold {{ request()->routeIs('admin.profile*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200/80' }} border px-2.5 py-1.5 rounded-xl transition-colors shrink-0"
                        title="Admin Profile & Account Settings">
                        <i class="fa-solid fa-user-gear text-xs {{ request()->routeIs('admin.profile*') ? 'text-white' : 'text-indigo-600' }}"></i>
                        <span>Profile</span>
                    </a>

                    <!-- Live Order Notification Bell -->
                    <div class="relative" id="adminOrderNotificationContainer">
                        <button type="button" id="adminOrderNotificationBtn"
                            onclick="toggleOrderNotificationDropdown()"
                            class="relative p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 transition-all flex items-center justify-center cursor-pointer shadow-xs border border-slate-200/70"
                            title="Live Order Notifications" aria-expanded="false">
                            <i class="fa-solid fa-bell text-xs text-slate-700" id="adminNotificationBellIcon"></i>
                            <!-- Unread Badge -->
                            <span id="adminNotificationBadge"
                                class="hidden absolute -top-1 -right-1 min-w-[17px] h-[17px] bg-rose-600 text-white text-[9px] font-black rounded-full px-1 flex items-center justify-center shadow-md animate-bounce">
                                0
                            </span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="adminNotificationMenu"
                            class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 animate-fadeIn overflow-hidden">
                            <!-- Header -->
                            <div
                                class="px-4 py-3 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-bell text-amber-400 text-sm"></i>
                                    <h4 class="font-extrabold text-xs font-heading">Live Order Alerts</h4>
                                </div>
                                <!-- Action Controls -->
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="testOrderAlertSound()"
                                        class="text-[10px] bg-white/10 hover:bg-white/20 text-amber-300 px-2 py-0.5 rounded font-bold transition flex items-center gap-1 cursor-pointer"
                                        title="Test Chime Sound">
                                        <i class="fa-solid fa-volume-high text-[9px]"></i> Test Sound
                                    </button>
                                    <button type="button" id="enableDesktopNotifyBtn"
                                        onclick="requestDesktopNotificationPermission()"
                                        class="text-[10px] bg-emerald-600 hover:bg-emerald-500 text-white px-2 py-0.5 rounded font-bold transition flex items-center gap-1 cursor-pointer"
                                        title="Enable Desktop Push Alerts">
                                        <i class="fa-solid fa-desktop text-[9px]"></i> Desktop
                                    </button>
                                </div>
                            </div>

                            <!-- Unread & Pending Banner with Mark All Read -->
                            <div id="adminNotificationPendingBanner"
                                class="px-4 py-2 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-xs text-slate-700 font-bold">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    <span>Unread: <span id="adminUnreadCountText" class="text-rose-600 font-black">0</span></span>
                                    <span class="text-slate-300 font-normal">|</span>
                                    <span class="text-slate-500 font-medium">Pending: <span id="adminPendingCountText">0</span></span>
                                </span>
                                <button type="button" onclick="markAllNotificationsAsRead()" id="markAllReadBtn"
                                    class="text-[11px] text-indigo-600 hover:text-indigo-800 font-extrabold flex items-center gap-1 hover:underline cursor-pointer transition"
                                    title="Mark all notifications as read">
                                    <i class="fa-solid fa-check-double text-[10px]"></i>
                                    <span>Mark all read</span>
                                </button>
                            </div>

                            <!-- Recent Orders List -->
                            <div id="adminNotificationList"
                                class="max-h-80 overflow-y-auto divide-y divide-slate-100 bg-white">
                                <div class="p-6 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-circle-notch fa-spin text-slate-300 text-base mb-1"></i>
                                    <p>Checking for recent orders...</p>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between px-4">
                                <button type="button" onclick="markAllNotificationsAsRead()"
                                    class="text-xs font-bold text-slate-600 hover:text-indigo-600 flex items-center gap-1 transition cursor-pointer"
                                    title="Mark all notifications as read">
                                    <i class="fa-solid fa-check-double text-[11px] text-indigo-500"></i>
                                    <span>Mark All Read</span>
                                </button>
                                <a href="{{ route('admin.orders.index') }}"
                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 transition">
                                    <span>View All Orders</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Logout Button -->
                    <form method="POST" action="{{ route('admin.logout') }}" class="shrink-0 inline">
                        @csrf
                        <button type="submit"
                            class="text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl shadow-sm shadow-rose-600/20 transition-all flex items-center gap-1.5 cursor-pointer active:scale-95"
                            title="Sign Out">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Admin Main Body Container -->
            <main class="flex-1 w-full mx-auto px-4 sm:px-6 py-6">
                @if (session('status') || session('success'))
                    <div
                        class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                        <div class="flex items-center gap-2.5 font-medium">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <span>{{ session('status') ?: session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                        <div class="flex items-center gap-2.5 font-medium">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-rose-500 hover:text-rose-800 p-1 cursor-pointer">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div
                        class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                        <div class="flex items-center gap-2.5 font-medium">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-amber-500 hover:text-amber-800 p-1 cursor-pointer">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        <!-- ============================================================== -->
        <!-- CUSTOMER STOREFRONT LAYOUT                                    -->
        <!-- ============================================================== -->

        <!-- Top Announcement Bar -->
        <div
            class="bg-gradient-to-r from-amber-600 via-rose-600 to-amber-600 text-white text-xs font-semibold py-1.5 px-3 text-center tracking-wide flex items-center justify-center gap-2 shadow-inner">
            <span>{{ $shop->banner_notice ?? '✨ Sivakasi Direct Factory Prices | 100% Genuine Green Crackers | Mega Festival Discount' }}</span>
        </div>

        <!-- Customer Navigation Header -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-rose-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-3 sm:px-5 py-2 sm:py-2.5 flex items-center justify-between gap-3">
                <!-- Brand Logo & Store Name -->
                <a href="{{ route('order.create') }}" class="flex items-center gap-2 sm:gap-2.5 group shrink-0"
                    title="{{ $shop->name ?? 'Guru Crackers' }} - Sivakasi">
                    @if (!empty($shop->logo_url))
                        <img src="{{ $shop->logo_url }}" alt="{{ $shop->name ?? 'Logo' }}"
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl object-contain bg-white p-0.5 border border-rose-200 shadow-md group-hover:scale-105 transition-transform shrink-0">
                    @else
                        <div
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform shrink-0">
                            <span class="text-base sm:text-xl">🎆</span>
                        </div>
                    @endif
                    <div class="shrink-0">
                        <div
                            class="text-sm sm:text-base md:text-lg font-extrabold text-slate-900 leading-tight flex items-center gap-1.5 font-heading whitespace-nowrap">
                            <span>{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</span>
                            <span
                                class="bg-rose-100 text-rose-700 text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 rounded tracking-wider uppercase shrink-0">{{ $shop->city ?? 'Sivakasi' }}</span>
                        </div>
                        <p
                            class="text-[10px] sm:text-[11px] text-slate-500 hidden sm:block whitespace-nowrap max-w-[200px] md:max-w-xs truncate">
                            {{ $shop->tagline ?? 'Diwali Crackers Online Price List & Booking' }}</p>
                    </div>
                </a>

                <!-- Customer Navigation Links -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Direct Phone Support -->
                    @if (!empty($shop->phone))
                        <a href="tel:{{ $shop->phone }}"
                            class="hidden lg:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-2.5 py-1.5 rounded-lg transition-colors border border-slate-200/60 shrink-0"
                            title="Direct Phone Support">
                            <i class="fa-solid fa-phone text-xs text-rose-600"></i>
                            <span>{{ $shop->phone }}</span>
                        </a>
                    @endif
                    @if (!empty($shop->secondary_phone))
                        <a href="tel:{{ $shop->secondary_phone }}"
                            class="hidden xl:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-2.5 py-1.5 rounded-lg transition-colors border border-slate-200/60 shrink-0"
                            title="Alternate Phone Support">
                            <i class="fa-solid fa-phone-volume text-xs text-rose-600"></i>
                            <span>{{ $shop->secondary_phone }}</span>
                        </a>
                    @endif

                    <!-- Customer Track Order -->
                    <a href="{{ route('order.track') }}" onclick="openTrackModal(); return false;"
                        class="inline-flex items-center gap-1 sm:gap-1.5 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white text-[11px] sm:text-xs font-bold px-2 sm:px-3 py-1.5 sm:py-2 rounded-xl shadow-xs transition-all font-heading shrink-0"
                        title="Track Your Order & Transport LR Status">
                        <i class="fa-solid fa-truck-fast text-xs sm:text-sm text-amber-400"></i>
                        <span class="hidden sm:inline">Track Order</span>
                        <span class="sm:hidden">Track</span>
                    </a>

                    <!-- Top Bar Cart Button -->
                    <button type="button"
                        onclick="if (typeof scrollToCheckout === 'function') { scrollToCheckout(); } else { window.location.href = '{{ route('order.create') }}#checkoutSection'; }"
                        class="top-nav-cart-btn inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-[11px] sm:text-xs font-extrabold px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl shadow-md shadow-rose-600/20 hover:shadow-lg transition-all font-heading shrink-0 cursor-pointer"
                        title="View Cart & Checkout">
                        <i class="fa-solid fa-cart-shopping text-xs sm:text-sm"></i>
                        <span>Cart (<span class="cart-items-badge">0</span>)</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Customer Main Body Container -->
        <main class="flex-1 w-full max-w-6xl mx-auto px-3 sm:px-4 py-4 md:py-6">
            @if (session('status') || session('success'))
                <div
                    class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <span>{{ session('status') ?: session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()"
                        class="text-emerald-500 hover:text-emerald-800 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5 font-medium">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()"
                        class="text-rose-500 hover:text-rose-800 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            @endif

            @if (session('warning'))
                <div
                    class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-2xl mb-4 text-xs sm:text-sm flex items-center justify-between gap-2 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5 font-medium">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()"
                        class="text-amber-500 hover:text-amber-800 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    @endif
    @if (!request()->routeIs('admin.*'))

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 text-xs py-8 mt-12 border-t border-slate-800">
            <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="flex items-center gap-2 text-white font-bold text-base font-heading mb-2">
                        @if (!empty($shop->logo_url))
                            <img src="{{ $shop->logo_url }}" alt="{{ $shop->name ?? 'Logo' }}"
                                class="w-7 h-7 rounded-lg object-contain bg-white/10 p-0.5">
                        @else
                            <span>🎆</span>
                        @endif
                        <span>{{ $shop->name ?? 'Guru Crackers' }} {{ $shop->city ?? 'Sivakasi' }}</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        {{ $shop->tagline ?? 'Direct from Sivakasi factory. We provide 100% legal, high-quality, authentic fireworks and sparklers at wholesale direct prices.' }}
                    </p>
                    @if (!empty($shop->website))
                        <div class="mt-2.5">
                            <a href="{{ $shop->website }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-medium text-xs">
                                <i class="fa-solid fa-globe"></i>
                                <span>{{ parse_url($shop->website, PHP_URL_HOST) ?? $shop->website }}</span>
                            </a>
                        </div>
                    @endif

                    <!-- Social Network Links -->
                    <div class="mt-4 pt-3 border-t border-slate-800">
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Connect
                            With
                            Us</span>
                        <div class="flex flex-wrap items-center gap-2">
                            @if (!empty($shop->whatsapp_phone))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#25D366] text-slate-300 hover:text-white flex items-center justify-center transition-all duration-200 text-sm shadow-sm"
                                    title="Chat on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            @endif
                            @if (!empty($shop->facebook_url))
                                <a href="{{ $shop->facebook_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#1877F2] text-slate-300 hover:text-white flex items-center justify-center transition-all duration-200 text-sm shadow-sm"
                                    title="Facebook Page">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            @endif
                            @if (!empty($shop->instagram_url))
                                <a href="{{ $shop->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-gradient-to-tr hover:from-[#f09433] hover:via-[#dc2743] hover:to-[#bc1888] text-slate-300 hover:text-white flex items-center justify-center transition-all duration-200 text-sm shadow-sm"
                                    title="Instagram Profile">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            @endif
                            @if (!empty($shop->youtube_url))
                                <a href="{{ $shop->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#FF0000] text-slate-300 hover:text-white flex items-center justify-center transition-all duration-200 text-sm shadow-sm"
                                    title="YouTube Channel">
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                            @endif
                            @if (!empty($shop->maps_url))
                                <a href="{{ $shop->maps_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#EA4335] text-slate-300 hover:text-white flex items-center justify-center transition-all duration-200 text-sm shadow-sm"
                                    title="Google Maps Location">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-slate-200 font-semibold mb-2">Contact & Dispatch Address</h4>
                    <ul class="space-y-1.5 text-slate-400">
                        @if (!empty($shop->address))
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-location-dot text-rose-500 mt-0.5 shrink-0"></i>
                                <div>
                                    <span>{{ $shop->address }}, {{ $shop->city }} - {{ $shop->pincode }}</span>
                                    @if (!empty($shop->maps_url))
                                        <div class="mt-1">
                                            <a href="{{ $shop->maps_url }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 text-[11px] text-amber-400 hover:text-amber-300 font-bold">
                                                <i class="fa-solid fa-diamond-turn-right text-[10px]"></i>
                                                <span>Get Directions on Google Maps</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif
                        @if (!empty($shop->phone))
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-phone text-emerald-400 shrink-0"></i>
                                <a href="tel:{{ $shop->phone }}"
                                    class="hover:text-white transition-colors">{{ $shop->phone }}</a>
                            </li>
                        @endif
                        @if (!empty($shop->secondary_phone))
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-phone-volume text-emerald-400 shrink-0"></i>
                                <a href="tel:{{ $shop->secondary_phone }}"
                                    class="hover:text-white transition-colors">{{ $shop->secondary_phone }}</a>
                            </li>
                        @endif
                        @if (!empty($shop->whatsapp_phone))
                            <li class="flex items-center gap-2">
                                <i class="fa-brands fa-whatsapp text-emerald-400 shrink-0 text-sm"></i>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="hover:text-white transition-colors">WhatsApp:
                                    {{ $shop->whatsapp_phone }}</a>
                            </li>
                        @endif
                        @if (!empty($shop->email))
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-amber-400 shrink-0"></i>
                                <a href="mailto:{{ $shop->email }}"
                                    class="hover:text-white transition-colors">{{ $shop->email }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="text-slate-200 font-semibold mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-scale-balanced text-amber-400"></i>
                        <span>Legal Compliance Notice</span>
                    </h4>
                    <div class="text-slate-400 leading-relaxed text-[11px] space-y-1.5">
                        <p>
                            This website is intended to provide information about our products and services. Any sale, purchase, delivery, or distribution of firecrackers shall be subject to applicable laws, regulations, licensing requirements, and directions issued by the competent authorities and courts.
                        </p>
                        <p>
                            Customers are requested to comply with all applicable legal requirements relating to the purchase, possession, transportation, and use of firecrackers.
                        </p>
                    </div>
                    <button type="button" onclick="openLegalModal()"
                        class="mt-2 text-[11px] text-amber-400 hover:text-amber-300 font-bold underline inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>View Order & Delivery Process (எப்படி ஆர்டர் செய்வது?)</span>
                    </button>
                </div>
            </div>

            <!-- Full-Width Bottom Copyright Bar -->
            <div
                class="mt-8 pt-5 border-t border-slate-800 text-center text-slate-400 text-xs font-medium tracking-wide">
                Copyright &copy; {{ date('Y') }}, and all rights reserved
            </div>
        </footer>

        <!-- ==========================================
         DIWALI BOTTLE ROCKET "BACK TO TOP" WIDGET
         ========================================== -->
        <style>
            @keyframes bottleRumble {

                0%,
                100% {
                    transform: rotate(0deg) translateY(0);
                }

                20% {
                    transform: rotate(-3deg) translateY(-1px);
                }

                40% {
                    transform: rotate(3deg) translateY(1px);
                }

                60% {
                    transform: rotate(-2deg) translateY(0);
                }

                80% {
                    transform: rotate(2deg) translateY(-1px);
                }
            }

            .bottle-rumble {
                animation: bottleRumble 0.08s infinite ease-in-out;
            }

            @keyframes fuseFlame {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 0.9;
                }

                50% {
                    transform: scale(1.4);
                    opacity: 1;
                    filter: drop-shadow(0 0 6px #f59e0b);
                }
            }

            .fuse-burning {
                animation: fuseFlame 0.15s infinite alternate ease-in-out;
            }

            @keyframes thrustFlicker {

                0%,
                100% {
                    transform: scaleY(1) scaleX(1);
                    opacity: 0.95;
                }

                50% {
                    transform: scaleY(1.3) scaleX(1.15);
                    opacity: 1;
                    filter: drop-shadow(0 0 10px #f97316);
                }
            }

            .thrust-active {
                animation: thrustFlicker 0.08s infinite alternate ease-in-out;
            }

            .rocket-launch-flight {
                transform: translateY(-130vh) scale(0.65) !important;
                transition: transform 0.85s cubic-bezier(0.12, 0.75, 0.35, 1) !important;
            }

            .rocket-reload-entry {
                transform: translateY(0) scale(1) !important;
                transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            }
        </style>

        <!-- Floating Bottle Rocket Container -->
        <div id="diwaliBottleRocket" onclick="launchBottleRocket()"
            class="fixed bottom-16 sm:bottom-20 md:bottom-24 right-2 sm:right-6 z-30 cursor-pointer select-none group opacity-0 pointer-events-none translate-y-6 transition-all duration-300"
            title="🚀 தீபாவளி பாட்டில் ராக்கெட் - Click to Fire to Top!" role="button"
            aria-label="Launch Diwali Bottle Rocket to top">
            <!-- Floating Label Tooltip -->
            <div
                class="absolute -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-amber-300 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-lg border border-amber-400/40 pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:-translate-y-1 font-heading flex items-center gap-1">
                <span class="text-amber-400 animate-pulse">🔥</span>
                <span>TOP - Fire Rocket!</span>
            </div>

            <!-- Ambient Festive Glow behind Bottle -->
            <div
                class="absolute -inset-1.5 bg-gradient-to-t from-amber-500/25 via-rose-500/20 to-transparent rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            </div>

            <!-- Realistic Glass Bottle + Cracker Rocket SVG -->
            <div id="bottleContainer"
                class="relative w-13 sm:w-16 md:w-20 h-24 sm:h-28 md:h-32 transition-transform duration-200 group-hover:scale-105 active:scale-95">
                <svg viewBox="0 0 100 160" class="w-full h-full filter drop-shadow-xl overflow-visible">
                    <defs>
                        <!-- Glass Translucent Gradient -->
                        <linearGradient id="bottleGlassGrad" x1="0%" y1="0%" x2="100%"
                            y2="0%">
                            <stop offset="0%" stop-color="#064e3b" stop-opacity="0.75" />
                            <stop offset="35%" stop-color="#34d399" stop-opacity="0.3" />
                            <stop offset="65%" stop-color="#047857" stop-opacity="0.65" />
                            <stop offset="100%" stop-color="#022c22" stop-opacity="0.85" />
                        </linearGradient>

                        <!-- Glass Gloss Reflection -->
                        <linearGradient id="bottleGlassShine" x1="0%" y1="0%" x2="100%"
                            y2="0%">
                            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.65" />
                            <stop offset="40%" stop-color="#ffffff" stop-opacity="0.1" />
                            <stop offset="100%" stop-color="#ffffff" stop-opacity="0.0" />
                        </linearGradient>

                        <!-- Rocket Red Festive Body -->
                        <linearGradient id="rocketFestiveGrad" x1="0%" y1="0%" x2="100%"
                            y2="0%">
                            <stop offset="0%" stop-color="#be123c" />
                            <stop offset="30%" stop-color="#f43f5e" />
                            <stop offset="70%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#881337" />
                        </linearGradient>

                        <!-- Gold Cap & Foil Bands -->
                        <linearGradient id="goldFoilGrad" x1="0%" y1="0%" x2="100%"
                            y2="0%">
                            <stop offset="0%" stop-color="#fbbf24" />
                            <stop offset="50%" stop-color="#f59e0b" />
                            <stop offset="100%" stop-color="#d97706" />
                        </linearGradient>

                        <!-- Thrust Jet Flame -->
                        <linearGradient id="thrustFlameGrad" x1="0%" y1="0%" x2="0%"
                            y2="100%">
                            <stop offset="0%" stop-color="#ffffff" />
                            <stop offset="20%" stop-color="#fef08a" />
                            <stop offset="50%" stop-color="#f97316" />
                            <stop offset="85%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#be123c" stop-opacity="0" />
                        </linearGradient>
                    </defs>

                    <!-- Ground Shadow under Bottle -->
                    <ellipse cx="50" cy="154" rx="26" ry="4.5" fill="rgba(0,0,0,0.35)" />

                    <!-- 1. BACK BOTTLE GLASS LAYER (Rendered behind the rocket stick) -->
                    <path
                        d="M 43,68 L 43,86 C 36,93 30,100 30,112 L 30,148 C 30,154 36,155 50,155 C 64,155 70,154 70,148 L 70,112 C 70,100 64,93 57,86 L 57,68 Z"
                        fill="#022c22" fill-opacity="0.4" />

                    <!-- 2. THE DIWALI ROCKET (Bamboo Stick + Cylinder + Cap + Fuse + Exhaust) -->
                    <g id="rocketFlightGroup" class="origin-bottom">
                        <!-- Bamboo Wooden Stick (inside bottle down to base) -->
                        <line x1="50" y1="46" x2="50" y2="142" stroke="#d97706"
                            stroke-width="2.6" stroke-linecap="round" />

                        <!-- Thrust Jet Flame (Turned on upon launch) -->
                        <g id="rocketThrustGroup" class="opacity-0 transition-opacity duration-100">
                            <path d="M 45,48 Q 50,86 50,96 Q 50,86 55,48 Z" fill="url(#thrustFlameGrad)"
                                class="thrust-active origin-top" />
                            <circle cx="48" cy="74" r="2.2" fill="#fbbf24" />
                            <circle cx="52" cy="80" r="1.8" fill="#f97316" />
                            <circle cx="50" cy="90" r="2.5" fill="#fef08a" />
                        </g>

                        <!-- Fuse Wick (திரி) -->
                        <path id="rocketFusePath" d="M 46,47 Q 41,53 38,57" stroke="#78350f" stroke-width="1.8"
                            fill="none" />
                        <!-- Fuse Spark Dot -->
                        <g id="fuseSparkWrap">
                            <circle id="fuseSparkPing" cx="37" cy="58" r="2.5" fill="#f59e0b"
                                class="animate-ping" />
                            <circle id="fuseSparkCore" cx="37" cy="58" r="1.8" fill="#fef08a" />
                        </g>

                        <!-- Rocket Cylinder Body -->
                        <rect x="41" y="16" width="18" height="31" rx="2.5"
                            fill="url(#rocketFestiveGrad)" stroke="#9f1239" stroke-width="0.75" />

                        <!-- Festive Gold Foil Stripe Bands -->
                        <rect x="41" y="22" width="18" height="4.5" fill="url(#goldFoilGrad)" />
                        <rect x="41" y="34" width="18" height="4" fill="url(#goldFoilGrad)" />

                        <!-- Decorative Star & Tamil Text on Rocket -->
                        <text x="50" y="32" font-size="7.5" text-anchor="middle" fill="#ffffff" font-weight="900"
                            font-family="'Outfit', sans-serif">★</text>

                        <!-- Conical Sharp Nose Tip (Cap) -->
                        <polygon points="50,1 38,16 62,16" fill="url(#goldFoilGrad)" stroke="#b45309"
                            stroke-width="0.75" />

                        <!-- Nose Cone Shiny Tip -->
                        <circle cx="50" cy="2" r="1.5" fill="#ffffff" />
                    </g>

                    <!-- 3. FRONT BOTTLE GLASS LAYER (Rendered in front of the stick for authentic glass depth) -->
                    <g id="bottleFrontGroup" class="pointer-events-none">
                        <!-- Translucent Glass Body -->
                        <path
                            d="M 42,66 L 42,86 C 35,93 30,100 30,112 L 30,148 C 30,154 36,155 50,155 C 64,155 70,154 70,148 L 70,112 C 70,100 65,93 58,86 L 58,66 Z"
                            fill="url(#bottleGlassGrad)" />

                        <!-- Bottle Lip / Rim at Top -->
                        <ellipse cx="50" cy="66" rx="8" ry="3" fill="#34d399"
                            fill-opacity="0.8" stroke="#065f46" stroke-width="1.2" />
                        <ellipse cx="50" cy="66" rx="5.5" ry="2" fill="#022c22"
                            fill-opacity="0.7" />

                        <!-- Specular Light Reflection Strip on Glass -->
                        <path
                            d="M 33,112 L 33,146 C 33,150 36,152 42,152 C 40,150 38,146 38,142 L 38,112 C 38,103 41,96 46,90 L 45,86 C 39,93 33,101 33,112 Z"
                            fill="url(#bottleGlassShine)" />

                        <!-- Vintage Cracker Factory Label on Bottle -->
                        <rect x="36" y="117" width="28" height="21" rx="2" fill="#fffbeb"
                            fill-opacity="0.92" stroke="#b45309" stroke-width="0.5" />
                        <text x="50" y="125" font-size="5" text-anchor="middle" fill="#991b1b" font-weight="900"
                            font-family="'Outfit', sans-serif">GURU</text>
                        <text x="50" y="131" font-size="3.8" text-anchor="middle" fill="#1e293b"
                            font-weight="bold">ROCKET</text>
                        <text x="50" y="136" font-size="3.5" text-anchor="middle" fill="#d97706"
                            font-weight="extrabold">🚀 TOP 🚀</text>
                    </g>
                </svg>
            </div>

            <!-- Dynamic Particle Spark Container -->
            <div id="rocketSparksCanvas" class="absolute inset-0 pointer-events-none overflow-visible"></div>
        </div>
    @endif

    <!-- Sky Fireworks Burst Container at Top of Screen -->
    <div id="skyFireworkBurst" class="fixed top-8 right-6 sm:right-16 z-50 pointer-events-none overflow-visible">
    </div>

    <script>
        // Bottle Rocket Launcher Logic
        const bottleRocket = document.getElementById('diwaliBottleRocket');
        const bottleContainer = document.getElementById('bottleContainer');
        const rocketGroup = document.getElementById('rocketFlightGroup');
        const thrustGroup = document.getElementById('rocketThrustGroup');
        const fuseSparkWrap = document.getElementById('fuseSparkWrap');
        const sparksCanvas = document.getElementById('rocketSparksCanvas');
        const skyBurstContainer = document.getElementById('skyFireworkBurst');

        let isRocketLaunching = false;

        // Show/Hide Bottle Rocket on Scroll
        window.addEventListener('scroll', () => {
            if (!bottleRocket) return;
            if (window.scrollY > 260) {
                bottleRocket.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-6');
                bottleRocket.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            } else {
                if (!isRocketLaunching) {
                    bottleRocket.classList.add('opacity-0', 'pointer-events-none', 'translate-y-6');
                    bottleRocket.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                }
            }
        }, {
            passive: true
        });

        // Synthesize Festive Cracker Audio via Native Web Audio API (100% Free & No External Files)
        function playDiwaliSound(type) {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                if (ctx.state === 'suspended') {
                    ctx.resume().catch(() => {});
                }

                if (type === 'fuse') {
                    // Crackling fuse burn sound (short bursts of noise)
                    const bufferSize = ctx.sampleRate * 0.25;
                    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                    const output = buffer.getChannelData(0);
                    for (let i = 0; i < bufferSize; i++) {
                        output[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.5));
                    }
                    const whiteNoise = ctx.createBufferSource();
                    whiteNoise.buffer = buffer;
                    const filter = ctx.createBiquadFilter();
                    filter.type = 'bandpass';
                    filter.frequency.value = 2400;
                    whiteNoise.connect(filter);
                    filter.connect(ctx.destination);
                    whiteNoise.start();
                } else if (type === 'whoosh') {
                    // Rocket launch whoosh & whistle (sweeping frequency upwards)
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    const now = ctx.currentTime;
                    osc.frequency.setValueAtTime(320, now);
                    osc.frequency.exponentialRampToValueAtTime(1600, now + 0.6);
                    gain.gain.setValueAtTime(0.15, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.6);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.6);
                } else if (type === 'burst') {
                    // Top sky firework pop sound
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    const now = ctx.currentTime;
                    osc.frequency.setValueAtTime(180, now);
                    osc.frequency.exponentialRampToValueAtTime(40, now + 0.25);
                    gain.gain.setValueAtTime(0.2, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.25);
                }
            } catch (e) {
                // Ignore audio policy errors silently
            }
        }

        // Spawn bright sparks around the fuse
        function createFuseSparks() {
            if (!sparksCanvas) return;
            for (let i = 0; i < 12; i++) {
                const spark = document.createElement('div');
                const x = 32 + (Math.random() * 16 - 8);
                const y = 46 + (Math.random() * 16 - 8);
                const colors = ['#f59e0b', '#fbbf24', '#f97316', '#ffffff'];
                const color = colors[Math.floor(Math.random() * colors.length)];
                spark.style.cssText = `
                    position: absolute;
                    left: ${x}%;
                    top: ${y}%;
                    width: ${Math.random() * 4 + 2}px;
                    height: ${Math.random() * 4 + 2}px;
                    background: ${color};
                    border-radius: 9999px;
                    box-shadow: 0 0 6px ${color};
                    pointer-events: none;
                    transform: translate(${Math.random() * 40 - 20}px, ${Math.random() * 40 - 20}px) scale(0);
                    transition: transform 0.35s ease-out, opacity 0.35s ease-out;
                    opacity: 1;
                    z-index: 50;
                `;
                sparksCanvas.appendChild(spark);
                requestAnimationFrame(() => {
                    spark.style.transform =
                        `translate(${Math.random() * 50 - 25}px, ${Math.random() * 50 - 25}px) scale(1.4)`;
                    spark.style.opacity = '0';
                });
                setTimeout(() => spark.remove(), 400);
            }
        }

        // Firework explosion at top of the screen when rocket reaches the top
        function triggerSkyBurst() {
            if (!skyBurstContainer) return;
            playDiwaliSound('burst');
            const colors = ['#f43f5e', '#fbbf24', '#34d399', '#38bdf8', '#f97316', '#e11d48', '#ffffff'];
            for (let i = 0; i < 28; i++) {
                const p = document.createElement('div');
                const color = colors[Math.floor(Math.random() * colors.length)];
                const angle = Math.random() * Math.PI * 2;
                const distance = Math.random() * 110 + 30;
                const tx = Math.cos(angle) * distance;
                const ty = Math.sin(angle) * distance;
                p.style.cssText = `
                    position: absolute;
                    width: ${Math.random() * 6 + 3}px;
                    height: ${Math.random() * 6 + 3}px;
                    background: ${color};
                    border-radius: 9999px;
                    box-shadow: 0 0 8px ${color};
                    pointer-events: none;
                    transform: translate(0, 0) scale(1);
                    transition: transform 0.65s cubic-bezier(0.1, 0.8, 0.2, 1), opacity 0.65s ease-out;
                    opacity: 1;
                `;
                skyBurstContainer.appendChild(p);
                requestAnimationFrame(() => {
                    p.style.transform = `translate(${tx}px, ${ty}px) scale(${Math.random() * 0.8 + 0.3})`;
                    p.style.opacity = '0';
                });
                setTimeout(() => p.remove(), 700);
            }
        }

        // Complete Rocket Launch Sequence
        function launchBottleRocket() {
            if (isRocketLaunching) return;
            isRocketLaunching = true;

            // Phase 1: Ignition (0ms - 220ms)
            // Fuse sparks, bottle rumbles, audio plays
            playDiwaliSound('fuse');
            if (bottleContainer) bottleContainer.classList.add('bottle-rumble');
            if (fuseSparkWrap) fuseSparkWrap.classList.add('fuse-burning');
            createFuseSparks();

            setTimeout(() => {
                // Phase 2: Launch Off ( மேல போற மாரி )
                playDiwaliSound('whoosh');
                if (bottleContainer) bottleContainer.classList.remove('bottle-rumble');
                if (thrustGroup) thrustGroup.style.opacity = '1';

                // Accelerate rocket straight up off the viewport!
                if (rocketGroup) {
                    rocketGroup.classList.remove('rocket-reload-entry');
                    rocketGroup.classList.add('rocket-launch-flight');
                }

                // Smooth scroll page to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // Periodic thrust sparks during flight
                const sparkInterval = setInterval(() => {
                    createFuseSparks();
                }, 75);

                // Phase 3: Reach Top & Sky Firework Burst (700ms)
                setTimeout(() => {
                    clearInterval(sparkInterval);
                    if (thrustGroup) thrustGroup.style.opacity = '0';
                    triggerSkyBurst();
                }, 650);

                // Phase 4: Reload fresh rocket back into bottle (1200ms)
                setTimeout(() => {
                    if (rocketGroup) {
                        // Instantly move above bottle silently, then drop back in with bounce
                        rocketGroup.style.transition = 'none';
                        rocketGroup.style.transform = 'translateY(-40px) scale(0.95)';
                        rocketGroup.style.opacity = '0';

                        requestAnimationFrame(() => {
                            rocketGroup.style.opacity = '1';
                            rocketGroup.classList.remove('rocket-launch-flight');
                            rocketGroup.classList.add('rocket-reload-entry');
                            rocketGroup.style.transform = '';
                            rocketGroup.style.transition = '';
                        });
                    }
                    if (fuseSparkWrap) fuseSparkWrap.classList.remove('fuse-burning');
                    isRocketLaunching = false;
                }, 1100);

            }, 220);
        }
    </script>

    <!-- Global Track Order Popup Modal for Customers -->
    <div id="globalTrackModal"
        class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4"
        onclick="closeTrackModal()">
        <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-6 sm:p-7 shadow-2xl relative space-y-4 border border-rose-100"
            onclick="event.stopPropagation()">
            <button type="button" onclick="closeTrackModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition-colors"
                title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="text-center space-y-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white mx-auto flex items-center justify-center text-2xl shadow-lg shadow-rose-600/20">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 font-heading">
                    Track Cracker Order & LR Slip
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Enter your <strong>Order ID</strong> and <strong>10-digit Registered Mobile Number</strong> to view
                    invoice and transport delivery status.
                </p>
            </div>

            <form method="GET" action="{{ route('order.track') }}" class="space-y-3 pt-1"
                onsubmit="if (!this.order_number.value.trim() || !this.phone.value.trim()) { if (window.DiwaliAlert) { DiwaliAlert.toast({type:'warning', text:'Please enter both Order ID and Phone Number!'}); } else { alert('Please enter both Order ID and Phone Number'); } return false; }">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Order ID</label>
                    <input type="text" name="order_number" id="trackModalInput"
                        placeholder="Order ID (e.g. GC-20260922-XXXXX)" minlength="3" maxlength="30"
                        oninput="this.value = this.value.replace(/[^a-zA-Z0-9\-]/g, '').replace(/\-{2,}/g, '-').replace(/^\-+/, '')"
                        class="w-full px-4 py-2.5 text-sm bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-normal"
                        required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Registered Mobile Number</label>
                    <input type="tel" name="phone" id="trackModalPhoneInput"
                        placeholder="10-digit Mobile Number" pattern="[0-9]{10}" minlength="10" maxlength="10"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full px-4 py-2.5 text-sm bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-normal"
                        required>
                </div>
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-sm shadow-md font-heading transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Track My Order Securely</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Global Order QR Code Popup Modal -->
    <div id="globalOrderQrModal"
        class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
        onclick="closeOrderQrModal()">
        <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-5 sm:p-6 shadow-2xl relative space-y-4 border border-rose-100"
            onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button type="button" onclick="closeOrderQrModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition-colors cursor-pointer"
                title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Modal Header -->
            <div class="text-center space-y-1.5 pr-6">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white mx-auto flex items-center justify-center text-xl shadow-lg shadow-rose-600/20">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 font-heading">
                    {{ $shop->name ?? 'Store' }} Order QR Code
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Scan with any smartphone camera or UPI/scanner app to instantly open the order catalogue.
                </p>
            </div>

            <!-- QR Code Card -->
            <div
                class="bg-gradient-to-b from-rose-50/60 to-amber-50/40 p-4 rounded-2xl border border-rose-100 text-center flex flex-col items-center">
                <div class="bg-white p-3 rounded-2xl shadow-md border border-slate-200/80 inline-block mb-2">
                    <img src="{{ route('order.qr') }}" alt="Store Order QR Code" id="orderQrModalImage"
                        class="w-52 h-52 sm:w-60 sm:h-60 object-contain mx-auto">
                </div>
                <span
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-rose-700 bg-rose-100/70 px-2.5 py-1 rounded-full">
                    <i class="fa-solid fa-camera text-xs"></i>
                    <span>Scan to browse crackers & order</span>
                </span>
            </div>

            <!-- Copy Link Input -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Order Form
                    Link</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="orderFormUrlInput" readonly value="{{ route('order.create') }}"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-mono select-all focus:outline-none focus:ring-2 focus:ring-rose-500"
                        onclick="this.select()">
                    <button type="button" id="copyOrderQrLinkBtn" onclick="copyOrderQrLink()"
                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold shrink-0 transition-colors flex items-center gap-1 cursor-pointer"
                        title="Copy URL">
                        <i class="fa-regular fa-copy"></i>
                        <span id="copyOrderQrLinkText">Copy</span>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('order.qr') }}"
                    download="{{ \Illuminate\Support\Str::slug($shop->name ?? 'guru-crackers') }}-order-qr.svg"
                    class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs shadow-md font-heading transition-all flex items-center justify-center gap-1.5"
                    title="Download SVG QR Code">
                    <i class="fa-solid fa-download"></i>
                    <span>Download QR</span>
                </a>
                <button type="button" onclick="printOrderQrModal()"
                    class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                    title="Print QR Standee">
                    <i class="fa-solid fa-print"></i>
                    <span>Print QR</span>
                </button>
                <a href="{{ route('order.create') }}" target="_blank"
                    class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors"
                    title="Open Order Form in New Tab">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        function openTrackModal() {
            const m = document.getElementById('globalTrackModal');
            if (m) {
                m.classList.remove('hidden');
                setTimeout(() => {
                    const inp = document.getElementById('trackModalInput');
                    if (inp) inp.focus();
                }, 100);
            }
        }

        function closeTrackModal() {
            const m = document.getElementById('globalTrackModal');
            if (m) m.classList.add('hidden');
        }

        // Global Order QR Code Modal Functions
        function openOrderQrModal() {
            const m = document.getElementById('globalOrderQrModal');
            if (m) {
                m.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeOrderQrModal() {
            const m = document.getElementById('globalOrderQrModal');
            if (m) {
                m.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function copyOrderQrLink() {
            const input = document.getElementById('orderFormUrlInput');
            if (!input) return;
            navigator.clipboard.writeText(input.value).then(() => {
                const textSpan = document.getElementById('copyOrderQrLinkText');
                if (textSpan) {
                    textSpan.textContent = 'Copied!';
                    setTimeout(() => {
                        textSpan.textContent = 'Copy';
                    }, 2000);
                }
                if (window.DiwaliAlert && typeof DiwaliAlert.toast === 'function') {
                    DiwaliAlert.toast({
                        type: 'success',
                        text: 'Order form link copied to clipboard!'
                    });
                }
            }).catch(() => {
                input.select();
                document.execCommand('copy');
                alert('Link copied to clipboard!');
            });
        }

        function printOrderQrModal() {
            const qrSrc = "{{ route('order.qr') }}";
            const shopName = @json($shop->name ?? 'Guru Crackers');
            const shopTagline = @json($shop->tagline ?? 'Diwali Crackers Direct Factory Prices');
            const shopPhone = @json($shop->phone ?? '');
            const orderUrl = "{{ route('order.create') }}";

            const printWin = window.open('', '_blank', 'width=620,height=750');
            if (!printWin) {
                window.print();
                return;
            }

            printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Scan & Order - ${shopName}</title>
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                        text-align: center;
                        padding: 40px 20px;
                        margin: 0;
                        color: #1e293b;
                        background: #fff;
                    }
                    .card {
                        max-width: 420px;
                        margin: 0 auto;
                        border: 3px solid #e11d48;
                        border-radius: 24px;
                        padding: 32px 24px;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
                    }
                    h1 {
                        font-size: 26px;
                        color: #e11d48;
                        margin: 0 0 6px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .tagline {
                        font-size: 13px;
                        color: #64748b;
                        margin-bottom: 22px;
                    }
                    .qr-box {
                        padding: 16px;
                        background: #f8fafc;
                        border: 2px dashed #cbd5e1;
                        border-radius: 18px;
                        display: inline-block;
                        margin-bottom: 18px;
                    }
                    .qr-box img {
                        width: 250px;
                        height: 250px;
                        display: block;
                    }
                    .scan-text {
                        font-size: 14px;
                        font-weight: 800;
                        color: #0f172a;
                        letter-spacing: 0.5px;
                        margin-bottom: 6px;
                    }
                    .url-text {
                        font-size: 12px;
                        color: #e11d48;
                        word-break: break-all;
                        font-family: monospace;
                        margin-bottom: 16px;
                    }
                    .phone {
                        font-size: 13px;
                        font-weight: 700;
                        color: #475569;
                    }
                    @media print {
                        body { padding: 0; }
                        .card { border: 2px solid #000; box-shadow: none; }
                    }
                </style>
            </head>
            <body>
                <div class="card">
                    <h1>${shopName}</h1>
                    <div class="tagline">${shopTagline}</div>
                    <div class="qr-box">
                        <img src="${qrSrc}" alt="QR Code">
                    </div>
                    <div class="scan-text">SCAN WITH ANY MOBILE CAMERA TO ORDER</div>
                    <div class="url-text">${orderUrl}</div>
                    ${shopPhone ? `<div class="phone">📞 Contact / WhatsApp: ${shopPhone}</div>` : ''}
                </div>
                <script>
                    window.onload = function() {
                        window.focus();
                        window.print();
                    };
                <\/script>
            </body>
            </html>
        `);
            printWin.document.close();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTrackModal();
                closeOrderQrModal();
            }
        });

        // =======================================================
        // DIWALI ALERT - CUSTOM FESTIVE NOTIFICATIONS & CONFIRMS
        // =======================================================
        window.DiwaliAlert = {
            toast: function(opts) {
                if (typeof Swal === 'undefined') {
                    return;
                }
                const type = opts.type || 'info';
                const iconMap = {
                    success: 'success',
                    error: 'error',
                    warning: 'warning',
                    info: 'info'
                };
                return Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: opts.timer || 3500,
                    timerProgressBar: true,
                    icon: iconMap[type] || 'info',
                    title: opts.message || opts.title || '',
                    customClass: {
                        popup: 'diwali-toast'
                    }
                });
            },

            success: function(title, message, btnText = 'OK') {
                if (typeof Swal === 'undefined') {
                    alert(title + "\n" + (message || ''));
                    return Promise.resolve();
                }
                return Swal.fire({
                    icon: 'success',
                    title: title || 'Success! 🎉',
                    html: message || '',
                    confirmButtonText: btnText,
                    customClass: {
                        popup: 'diwali-swal-popup',
                        title: 'diwali-swal-title',
                        htmlContainer: 'diwali-swal-html',
                        confirmButton: 'diwali-swal-confirm-btn'
                    },
                    buttonsStyling: false
                });
            },

            error: function(title, message, btnText = 'OK') {
                if (typeof Swal === 'undefined') {
                    alert(title + "\n" + (message || ''));
                    return Promise.resolve();
                }
                return Swal.fire({
                    icon: 'error',
                    title: title || 'Oops! ⚠️',
                    html: message || '',
                    confirmButtonText: btnText,
                    customClass: {
                        popup: 'diwali-swal-popup',
                        title: 'diwali-swal-title',
                        htmlContainer: 'diwali-swal-html',
                        confirmButton: 'diwali-swal-confirm-btn'
                    },
                    buttonsStyling: false
                });
            },

            warning: function(title, message, btnText = 'Got It') {
                if (typeof Swal === 'undefined') {
                    alert(title + "\n" + (message || ''));
                    return Promise.resolve();
                }
                return Swal.fire({
                    icon: 'warning',
                    title: title || 'Warning 💥',
                    html: message || '',
                    confirmButtonText: btnText,
                    customClass: {
                        popup: 'diwali-swal-popup',
                        title: 'diwali-swal-title',
                        htmlContainer: 'diwali-swal-html',
                        confirmButton: 'diwali-swal-confirm-btn'
                    },
                    buttonsStyling: false
                });
            },

            info: function(title, message, btnText = 'OK') {
                if (typeof Swal === 'undefined') {
                    alert(title + "\n" + (message || ''));
                    return Promise.resolve();
                }
                return Swal.fire({
                    icon: 'info',
                    title: title || 'Notice 🎆',
                    html: message || '',
                    confirmButtonText: btnText,
                    customClass: {
                        popup: 'diwali-swal-popup',
                        title: 'diwali-swal-title',
                        htmlContainer: 'diwali-swal-html',
                        confirmButton: 'diwali-swal-confirm-btn'
                    },
                    buttonsStyling: false
                });
            },

            confirm: function(opts) {
                if (typeof Swal === 'undefined') {
                    const res = window._nativeConfirm ? window._nativeConfirm(opts.text || opts.title) : true;
                    if (res && typeof opts.onConfirm === 'function') opts.onConfirm();
                    return Promise.resolve({
                        isConfirmed: res
                    });
                }
                return Swal.fire({
                    icon: opts.icon || 'warning',
                    title: opts.title || 'Are you sure?',
                    html: opts.text || opts.message || '',
                    showCancelButton: true,
                    confirmButtonText: opts.confirmText || 'Yes, Proceed',
                    cancelButtonText: opts.cancelText || 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'diwali-swal-popup',
                        title: 'diwali-swal-title',
                        htmlContainer: 'diwali-swal-html',
                        confirmButton: 'diwali-swal-confirm-btn',
                        cancelButton: 'diwali-swal-cancel-btn'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (typeof opts.onConfirm === 'function') opts.onConfirm();
                    } else if (result.isDismissed) {
                        if (typeof opts.onCancel === 'function') opts.onCancel();
                    }
                    return result;
                });
            }
        };

        // Override browser native alert
        window._nativeAlert = window.alert;
        window.alert = function(msg) {
            DiwaliAlert.info('Notice 🎆', msg);
        };

        // Global listener for forms with data-confirm
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || !form.getAttribute) return;
            const confirmMsg = form.getAttribute('data-confirm');
            if (confirmMsg && !form.dataset.confirmed) {
                e.preventDefault();
                const title = form.getAttribute('data-confirm-title') || 'Are you sure?';
                const btnText = form.getAttribute('data-confirm-btn') || 'Yes, Proceed';
                DiwaliAlert.confirm({
                    title: title,
                    text: confirmMsg,
                    confirmText: btnText,
                    icon: form.getAttribute('data-confirm-icon') || 'warning',
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }
        });

        // Auto-trigger custom toasts for Laravel flash messages
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('status') || session('success'))
                DiwaliAlert.toast({
                    type: 'success',
                    message: @json(session('status') ?: session('success'))
                });
            @endif

            @if (session('error'))
                DiwaliAlert.error('Notice ⚠️', @json(session('error')));
            @endif

            @if (session('warning'))
                DiwaliAlert.warning('Warning 💥', @json(session('warning')));
            @endif
        });

    </script>

    {{-- ========================================================
         SUPREME COURT LEGAL NOTICE MODAL (Customer Storefront Only)
         ======================================================== --}}
    @if (!request()->routeIs('admin.*'))
        <script>
            // ==========================================
            // Supreme Court Legal Compliance Modal Logic
            // ==========================================
            window.openLegalModal = function() {
                const modal = document.getElementById('supremeCourtModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            };

            window.closeLegalModal = function() {
                const modal = document.getElementById('supremeCourtModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            };

            window.acceptLegalModal = function() {
                const chk = document.getElementById('dontShowAgainTodayCheck');
                if (chk && chk.checked) {
                    localStorage.setItem('sc_legal_notice_dismissed_date', new Date().toDateString());
                }
                sessionStorage.setItem('sc_legal_notice_shown', 'true');
                closeLegalModal();
            };

            // Auto-show on customer application load
            document.addEventListener('DOMContentLoaded', () => {
                const dismissedDate = localStorage.getItem('sc_legal_notice_dismissed_date');
                const todayStr = new Date().toDateString();
                const sessionShown = sessionStorage.getItem('sc_legal_notice_shown');

                if (dismissedDate !== todayStr && !sessionShown) {
                    setTimeout(() => {
                        openLegalModal();
                    }, 600);
                }
            });
        </script>

        {{-- ===================== HOW TO ORDER & DELIVERY FLOW MODAL POPUP ===================== --}}
        <div id="supremeCourtModal"
            class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-sm flex items-center justify-center p-2.5 sm:p-4 md:p-6 overflow-y-auto transition-all duration-300"
            onclick="closeLegalModal()">
            <div class="bg-white rounded-2xl sm:rounded-3xl max-w-lg md:max-w-5xl lg:max-w-6xl w-full max-h-[92vh] sm:max-h-[88vh] flex flex-col my-auto overflow-hidden shadow-2xl border border-amber-500/30 transform transition-all relative text-left"
                onclick="event.stopPropagation()">
                <!-- Top Festive Gradient Header (shrink-0: always fully visible at top) -->
                <div class="shrink-0 bg-gradient-to-r from-slate-950 via-rose-950 to-amber-950 text-white px-3.5 py-2.5 sm:px-6 sm:py-3.5 relative border-b border-white/10">
                    <button type="button" onclick="closeLegalModal()"
                        class="absolute top-2.5 right-2.5 sm:top-3.5 sm:right-3.5 w-8 h-8 rounded-full bg-white/20 hover:bg-white/35 active:scale-95 text-white flex items-center justify-center transition-all cursor-pointer shadow-sm z-20"
                        title="Close">
                        <i class="fa-solid fa-xmark text-sm sm:text-base"></i>
                    </button>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-0.5 sm:gap-4 pr-9 sm:pr-8">
                        <div>
                            <div
                                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-400/20 border border-amber-300/30 text-amber-300 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider mb-0.5">
                                <i class="fa-solid fa-truck-fast text-amber-300"></i>
                                <span>Order & Delivery Flow | 5 எளிய படிகள்</span>
                            </div>
                            <h3 class="text-xs sm:text-base md:text-lg font-black font-heading text-white leading-tight">
                                {{ $shop->name ?? 'Guru Crackers' }} - Order & Delivery Process
                            </h3>
                        </div>
                        <div class="hidden sm:flex items-center gap-2 text-rose-200 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Direct Sivakasi Wholesale Booking</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Body Container (flex-1 min-h-0: scrolls smoothly inside without cutting header/footer) -->
                <div class="flex-1 min-h-0 overflow-y-auto p-2.5 sm:p-4 md:p-5 space-y-2 sm:space-y-3 text-xs text-slate-600 leading-relaxed custom-inner-scrollbar">
                    
                    <!-- Top Info Pill Banner (Desktop only to keep mobile ultra-compact) -->
                    <div class="hidden md:flex bg-gradient-to-r from-rose-50 to-amber-50 border border-rose-200/70 rounded-xl sm:rounded-2xl px-3 py-2 items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-sm shrink-0">💥</span>
                            <div class="text-[11px] text-slate-800 leading-tight">
                                <strong class="text-slate-900 font-extrabold">நேரடி சிவகாசி பட்டாசு கொள்முதல் வழிகாட்டி:</strong>
                                <span class="text-slate-600"> 100% பாதுகாப்பான பேக்கிங் &amp; டிரான்ஸ்போர்ட் டெலிவரி!</span>
                            </div>
                        </div>
                        <span class="text-[9px] font-black bg-rose-600 text-white px-2 py-0.5 rounded-full uppercase shrink-0">
                            Direct Factory
                        </span>
                    </div>

                    <!-- ======================================================== -->
                    <!-- DESKTOP VIEW: HORIZONTAL 5-STEP PROCESS FLOW (md:grid)   -->
                    <!-- ======================================================== -->
                    <div class="hidden md:grid md:grid-cols-5 gap-3 items-stretch">
                        <!-- Step 1 -->
                        <div class="bg-gradient-to-b from-amber-50/60 to-white border border-amber-200/90 rounded-2xl p-3.5 flex flex-col justify-between hover:shadow-md transition-all relative group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center text-xs shadow-xs font-bold">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </span>
                                    <span class="text-[10px] font-mono font-black bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">Step 1</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs font-heading">
                                    1. Cart & Order
                                </h4>
                                <p class="text-[10px] font-bold text-amber-700 mt-0.5">தேர்வு செய்தல்</p>
                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                    பட்டாசுகளை Cart-ல் சேர்த்து பெயர், முகவரி, WhatsApp எண்ணுடன் ஆர்டர் சமர்ப்பிக்கவும்.
                                </p>
                            </div>
                            <div class="pt-2 text-right text-slate-300 group-hover:text-amber-500 transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="bg-gradient-to-b from-emerald-50/60 to-white border border-emerald-200/90 rounded-2xl p-3.5 flex flex-col justify-between hover:shadow-md transition-all relative group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center text-xs shadow-xs font-bold">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </span>
                                    <span class="text-[10px] font-mono font-black bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded">Step 2</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs font-heading">
                                    2. Confirmation
                                </h4>
                                <p class="text-[10px] font-bold text-emerald-700 mt-0.5">WhatsApp / Call</p>
                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                    24 மணி நேரத்தில் எங்கள் குழு அழைத்து ஆர்டரை உறுதி செய்து இறுதி Invoice & Payment விவரம் வழங்குவர்.
                                </p>
                            </div>
                            <div class="pt-2 text-right text-slate-300 group-hover:text-emerald-500 transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="bg-gradient-to-b from-blue-50/60 to-white border border-blue-200/90 rounded-2xl p-3.5 flex flex-col justify-between hover:shadow-md transition-all relative group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-xs shadow-xs font-bold">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </span>
                                    <span class="text-[10px] font-mono font-black bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">Step 3</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs font-heading">
                                    3. Safe Packing
                                </h4>
                                <p class="text-[10px] font-bold text-blue-700 mt-0.5">பாதுகாப்பான பேக்கிங்</p>
                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                    உரிமம் பெற்ற சிவகாசி கிடங்கில் பட்டாசுகள் சரிபார்க்கப்பட்டு வாட்டர்ப்ரூப் பெட்டிகளில் பேக் செய்யப்படும்.
                                </p>
                            </div>
                            <div class="pt-2 text-right text-slate-300 group-hover:text-blue-500 transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="bg-gradient-to-b from-purple-50/60 to-white border border-purple-200/90 rounded-2xl p-3.5 flex flex-col justify-between hover:shadow-md transition-all relative group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center text-xs shadow-xs font-bold">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </span>
                                    <span class="text-[10px] font-mono font-black bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded">Step 4</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs font-heading">
                                    4. Transport & LR
                                </h4>
                                <p class="text-[10px] font-bold text-purple-700 mt-0.5">டிரான்ஸ்போர்ட் புக்கிங்</p>
                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                    பதிவு செய்யப்பட்ட லாரி டிரான்ஸ்போர்ட் மூலம் அனுப்பப்பட்டு, Lorry Receipt (LR) WhatsApp-ல் பகிரப்படும்.
                                </p>
                            </div>
                            <div class="pt-2 text-right text-slate-300 group-hover:text-purple-500 transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="bg-gradient-to-b from-rose-50/60 to-white border border-rose-200/90 rounded-2xl p-3.5 flex flex-col justify-between hover:shadow-md transition-all relative group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 text-white flex items-center justify-center text-xs shadow-xs font-bold">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </span>
                                    <span class="text-[10px] font-mono font-black bg-rose-100 text-rose-800 px-1.5 py-0.5 rounded">Step 5</span>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-xs font-heading">
                                    5. Safe Delivery
                                </h4>
                                <p class="text-[10px] font-bold text-rose-700 mt-0.5">பார்சல் பெறுதல்</p>
                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                    பார்சல் உங்கள் ஊர் கிளைக்கு வந்ததும் தகவல் வரும். நேரில் அல்லது டோர் டெலிவரியில் பெற்று மகிழுங்கள்!
                                </p>
                            </div>
                            <div class="pt-2 text-right text-emerald-500 font-bold text-xs">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- MOBILE VIEW: ICON-DRIVEN MINIMAL 5 STEPS (md:hidden)      -->
                    <!-- ======================================================== -->
                    <div class="block md:hidden bg-slate-50/80 border border-slate-200/90 rounded-2xl p-2 space-y-1.5 shadow-inner">
                        <!-- Step 1 -->
                        <div class="flex items-center gap-2.5 p-1.5 rounded-xl bg-white border border-amber-200/70 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-black text-slate-900 text-xs">1. ஆர்டர் தேர்வு (Cart)</h5>
                                    <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.2 rounded shrink-0">படி 1</span>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-tight">பட்டாசுகளை கார்ட்டில் சேர்த்து ஆர்டர் செய்யவும்</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-center gap-2.5 p-1.5 rounded-xl bg-white border border-emerald-200/70 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-black text-slate-900 text-xs">2. WhatsApp உறுதி (Bill)</h5>
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded shrink-0">படி 2</span>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-tight">அழைத்து இறுதி பில் &amp; பேமெண்ட் உறுதி செய்வோம்</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-center gap-2.5 p-1.5 rounded-xl bg-white border border-blue-200/70 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-black text-slate-900 text-xs">3. நேரடி பேக்கிங் (Packing)</h5>
                                    <span class="text-[9px] font-bold text-blue-700 bg-blue-100 px-1.5 py-0.2 rounded shrink-0">படி 3</span>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-tight">சிவகாசி கிடங்கில் வாட்டர்ப்ரூப் பாதுகாப்பு பேக்கிங்</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-center gap-2.5 p-1.5 rounded-xl bg-white border border-purple-200/70 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-black text-slate-900 text-xs">4. லாரி டிரான்ஸ்போர்ட் (LR)</h5>
                                    <span class="text-[9px] font-bold text-purple-700 bg-purple-100 px-1.5 py-0.2 rounded shrink-0">படி 4</span>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-tight">லாரியில் அனுப்பி LR ரசீது WhatsApp-ல் வரும்</p>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="flex items-center gap-2.5 p-1.5 rounded-xl bg-white border border-rose-200/70 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-black text-slate-900 text-xs">5. பார்சல் பெறுதல் (Delivery)</h5>
                                    <span class="text-[9px] font-bold text-rose-700 bg-rose-100 px-1.5 py-0.2 rounded shrink-0">படி 5</span>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-tight">உங்கள் ஊர் கிளையில் பார்சலை பெற்றுக்கொள்ளலாம்</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Bar: Legal Compliance Statement & Trust Badges -->
                    <!-- Desktop Legal & Badges -->
                    <div class="hidden md:grid md:grid-cols-12 gap-2 pt-1">
                        <!-- Legal Notice -->
                        <div class="md:col-span-8 bg-amber-50/90 border border-amber-300/80 rounded-xl p-2.5 text-amber-950 flex items-start sm:items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-amber-600 text-sm shrink-0 mt-0.5 sm:mt-0"></i>
                            <div class="text-[10px] leading-tight text-slate-700">
                                <strong class="font-bold text-amber-950">Legal Compliance Notice:</strong>
                                This website provides info on our products &amp; services. Any sale/delivery of firecrackers is subject to applicable laws and court directions.
                            </div>
                        </div>
                        <!-- Badges -->
                        <div class="md:col-span-4 grid grid-cols-3 gap-1.5">
                            <div class="flex items-center justify-center gap-1 bg-slate-50 border border-slate-200/80 rounded-xl py-1.5 px-1 text-[9px] sm:text-[10px] font-bold text-slate-700 text-center">
                                <i class="fa-solid fa-certificate text-emerald-600 text-xs"></i>
                                <span>100% Legal</span>
                            </div>
                            <div class="flex items-center justify-center gap-1 bg-slate-50 border border-slate-200/80 rounded-xl py-1.5 px-1 text-[9px] sm:text-[10px] font-bold text-slate-700 text-center">
                                <i class="fa-solid fa-warehouse text-amber-600 text-xs"></i>
                                <span>Licensed</span>
                            </div>
                            <div class="flex items-center justify-center gap-1 bg-slate-50 border border-slate-200/80 rounded-xl py-1.5 px-1 text-[9px] sm:text-[10px] font-bold text-slate-700 text-center">
                                <i class="fa-solid fa-truck-fast text-rose-600 text-xs"></i>
                                <span>Transport</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Legal Compliance Micro Bar (Ultra-Compact) -->
                    <div class="block md:hidden bg-amber-50/80 border border-amber-200/80 rounded-xl px-2.5 py-1.5 text-slate-700">
                        <div class="flex items-center justify-between gap-1.5">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <i class="fa-solid fa-scale-balanced text-amber-600 text-xs shrink-0"></i>
                                <p class="text-[10px] leading-tight text-slate-700">
                                    <strong class="text-amber-950 font-bold">சட்டப்பூர்வ அறிவிப்பு:</strong> விதிகளுக்குட்பட்டு சிவகாசியில் இருந்து பார்சல் அனுப்பப்படுகிறது.
                                </p>
                            </div>
                            <span class="text-[9px] font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-1.5 py-0.5 rounded shrink-0">
                                <i class="fa-solid fa-shield-check mr-0.5"></i>100% Legal
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Modal Action Footer (shrink-0: always firmly anchored and visible) -->
                <div class="shrink-0 bg-slate-50 border-t border-slate-200/80 px-3 sm:px-6 py-2 sm:py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <label class="flex items-center gap-1.5 sm:gap-2 text-[10px] sm:text-[11px] text-slate-500 select-none cursor-pointer self-start sm:self-center">
                        <input type="checkbox" id="dontShowAgainTodayCheck"
                            class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 w-3.5 h-3.5">
                        <span>Don't show again today (இன்று காட்ட வேண்டாம்)</span>
                    </label>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" onclick="closeLegalModal()"
                            class="sm:hidden flex-1 py-2 px-3 rounded-xl border border-slate-300 bg-white active:bg-slate-100 text-slate-700 font-bold text-xs transition-all cursor-pointer text-center">
                            Close / மூடுக
                        </button>
                        <button type="button" onclick="acceptLegalModal()"
                            class="flex-1 sm:flex-none w-full sm:w-auto bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-xs py-2 sm:py-2.5 px-4 sm:px-5 rounded-xl shadow-md transition-all cursor-pointer font-heading flex items-center justify-center gap-1.5 active:scale-95 text-center">
                            <span>Start Shopping</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Change Password Modal & Live Notifications for Admin (Admin Routes Only) -->
    @if (auth()->check() && request()->routeIs('admin.*'))
        <div id="changePasswordModal"
            class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center transition-all"
            aria-hidden="true" role="dialog" aria-modal="true">
            <div
                class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden transform transition-all my-8 animate-fadeIn">

                <!-- Modal Header -->
                <div
                    class="bg-gradient-to-r from-rose-900 via-rose-800 to-amber-700 text-white p-5 flex items-center justify-between border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-10 h-10 rounded-2xl bg-white/10 border border-white/20 text-amber-300 flex items-center justify-center text-lg shadow-inner">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <div>
                            <h3 class="text-base font-black font-heading text-white">
                                Change Admin Password
                            </h3>
                            <p class="text-[11px] text-rose-100/80">Update your login password and WhatsApp recovery
                                number.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeChangePasswordModal()"
                        class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white flex items-center justify-center transition-colors text-sm cursor-pointer"
                        title="Close Modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form method="POST" action="{{ route('admin.change_password') }}"
                    class="p-5 sm:p-6 space-y-4 text-xs">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            Current Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="password" name="current_password" id="currPwInput" required
                                placeholder="Enter current password"
                                class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800">
                            <button type="button" onclick="togglePasswordVisibility('currPwInput', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            New Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="password" name="password" id="newPwInput" required minlength="6"
                                placeholder="Minimum 6 characters"
                                class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800">
                            <button type="button" onclick="togglePasswordVisibility('newPwInput', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            Confirm New Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-check-double absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="password" name="password_confirmation" id="confPwInput" required minlength="6"
                                placeholder="Re-type new password"
                                class="w-full pl-9 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800">
                            <button type="button" onclick="togglePasswordVisibility('confPwInput', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- WhatsApp Recovery Phone -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block font-bold text-slate-700 mb-1">
                            WhatsApp OTP Recovery Phone
                        </label>
                        <div class="relative">
                            <i
                                class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 text-sm"></i>
                            <input type="tel" name="phone"
                                value="{{ old('phone', auth()->user()->phone ?? '9789874381') }}"
                                placeholder="10-digit mobile (e.g. 9789874381)" minlength="10" maxlength="10"
                                inputmode="numeric" pattern="[6-9][0-9]{9}"
                                title="10-digit mobile starting with 6, 7, 8, or 9"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-800">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">If you forget your password, the OTP will be sent to
                            this WhatsApp number (starts with 6-9).</p>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeChangePasswordModal()"
                            class="px-4 py-2 font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold px-5 py-2 rounded-xl shadow-md transition-all font-heading active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <script>
            function toggleAdminSettingsDropdown() {
                const menu = document.getElementById('adminSettingsMenu');
                const chevron = document.getElementById('adminSettingsChevron');
                const btn = document.getElementById('adminSettingsDropdownBtn');
                if (!menu) return;
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                } else {
                    closeAdminSettingsDropdown();
                }
            }

            function closeAdminSettingsDropdown() {
                const menu = document.getElementById('adminSettingsMenu');
                const chevron = document.getElementById('adminSettingsChevron');
                const btn = document.getElementById('adminSettingsDropdownBtn');
                if (menu) menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }

            // Toggle Mobile Left Sidebar Drawer
            window.toggleAdminSidebar = function() {
                const sidebar = document.getElementById('adminSidebar');
                const backdrop = document.getElementById('adminSidebarBackdrop');
                if (!sidebar) return;
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (backdrop) backdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    if (backdrop) backdrop.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };

            window.closeAdminSidebar = function() {
                const sidebar = document.getElementById('adminSidebar');
                const backdrop = document.getElementById('adminSidebarBackdrop');
                if (sidebar) {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                }
                if (backdrop) backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            };

            function toggleAdminMobileMenu() {
                window.toggleAdminSidebar();
            }

            document.addEventListener('click', function(e) {
                const container = document.getElementById('adminSettingsDropdownContainer');
                if (container && !container.contains(e.target)) {
                    closeAdminSettingsDropdown();
                }
            });

            // Hover support for desktop
            const settingsContainer = document.getElementById('adminSettingsDropdownContainer');
            if (settingsContainer) {
                let closeTimeout;
                settingsContainer.addEventListener('mouseenter', function() {
                    clearTimeout(closeTimeout);
                    const menu = document.getElementById('adminSettingsMenu');
                    const chevron = document.getElementById('adminSettingsChevron');
                    const btn = document.getElementById('adminSettingsDropdownBtn');
                    if (menu) menu.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                });
                settingsContainer.addEventListener('mouseleave', function() {
                    closeTimeout = setTimeout(function() {
                        closeAdminSettingsDropdown();
                    }, 180);
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAdminSettingsDropdown();
                }
            });

            function openChangePasswordModal() {
                const modal = document.getElementById('changePasswordModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            }

            function closeChangePasswordModal() {
                const modal = document.getElementById('changePasswordModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            }

            @if ($errors->has('current_password') || $errors->has('password') || $errors->has('phone'))
                document.addEventListener('DOMContentLoaded', () => {
                    openChangePasswordModal();
                });
            @endif
        </script>

        <!-- Floating Live Order Toast Alert Container -->
        <div id="liveOrderToastContainer"
            class="fixed top-4 right-4 z-[999999] pointer-events-none flex flex-col gap-2.5 max-w-sm sm:max-w-md w-full px-3 sm:px-0">
        </div>

        <!-- Hidden Audio Element for Order Alert Chime -->
        <audio id="newOrderAlertAudio" preload="auto">
            <source src="{{ asset('sounds/order-alert.mp3') }}" type="audio/mpeg">
            <source src="{{ asset('sounds/order-alert.wav') }}" type="audio/wav">
        </audio>

        <!-- Live Real-Time Order Notifications System (Sound Chime, Desktop Alert, Popup Toast) -->
        <script>
            (function() {
                let lastKnownOrderId = 0;
                let isFirstPoll = true;
                let pollInterval = null;
                let audioContextInstance = null;

                // Initialize or retrieve Web Audio Context safely
                function getAudioContext() {
                    try {
                        if (!audioContextInstance) {
                            const AudioCtx = window.AudioContext || window.webkitAudioContext;
                            if (AudioCtx) audioContextInstance = new AudioCtx();
                        }
                        if (audioContextInstance && audioContextInstance.state === 'suspended') {
                            audioContextInstance.resume().catch(() => {});
                        }
                    } catch (e) {}
                    return audioContextInstance;
                }

                // Web Audio API Synthesis fallback chime
                function playSynthesizedChime() {
                    try {
                        const ctx = getAudioContext();
                        if (!ctx) return;

                        // Harmonic frequencies: E5 (659.25Hz) -> G#5 (830.61Hz) -> B5 (987.77Hz) -> E6 (1318.51Hz)
                        const notes = [659.25, 830.61, 987.77, 1318.51];
                        const delays = [0, 0.12, 0.24, 0.36];

                        notes.forEach((freq, idx) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();

                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(freq, ctx.currentTime + delays[idx]);

                            gain.gain.setValueAtTime(0, ctx.currentTime + delays[idx]);
                            gain.gain.linearRampToValueAtTime(0.45, ctx.currentTime + delays[idx] + 0.02);
                            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delays[idx] + 0.85);

                            osc.connect(gain);
                            gain.connect(ctx.destination);

                            osc.start(ctx.currentTime + delays[idx]);
                            osc.stop(ctx.currentTime + delays[idx] + 0.9);
                        });
                    } catch (err) {
                        console.warn('Synthesized chime error:', err);
                    }
                }

                // Play pleasant festive retail order chime sound
                window.playOrderAlertSound = function() {
                    try {
                        const audioEl = document.getElementById('newOrderAlertAudio');
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            const playPromise = audioEl.play();
                            if (playPromise !== undefined) {
                                playPromise.catch(() => {
                                    playSynthesizedChime();
                                });
                                return;
                            }
                        }
                    } catch (_) {}
                    playSynthesizedChime();
                };

                window.testOrderAlertSound = function() {
                    window.playOrderAlertSound();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '🔔 Sound Chime is working!',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                };

                // Request Desktop Push Notification Permission
                window.requestDesktopNotificationPermission = function() {
                    if (!("Notification" in window)) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Not Supported',
                                text: 'Your browser does not support Desktop Push Notifications.'
                            });
                        }
                        return;
                    }

                    Notification.requestPermission().then(permission => {
                        const btn = document.getElementById('enableDesktopNotifyBtn');
                        if (permission === 'granted') {
                            if (btn) btn.innerHTML = '<i class="fa-solid fa-check text-[9px]"></i> Active';
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: '🎉 Desktop Notifications Enabled!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                            try {
                                new Notification("🎉 Guru Crackers Notifications Active!", {
                                    body: "You will now receive instant desktop alerts whenever a customer places an order!",
                                    icon: "{{ asset('favicon.png') }}"
                                });
                            } catch (_) {}
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'warning',
                                    title: 'Desktop alerts were not allowed.',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
                    });
                };

                // Check existing Desktop permission status
                if ("Notification" in window && Notification.permission === "granted") {
                    const btn = document.getElementById('enableDesktopNotifyBtn');
                    if (btn) btn.innerHTML = '<i class="fa-solid fa-check text-[9px]"></i> Active';
                }

                // Safe HTML string escaping
                function escapeHtml(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                // Send Native Desktop Push Notification
                function triggerDesktopNotification(order) {
                    if (!("Notification" in window) || Notification.permission !== "granted") return;
                    try {
                        const notif = new Notification(`🎉 New Order: ${order.total_amount_formatted}`, {
                            body: `${order.name} (${order.city || 'Customer'}) placed Order #${order.order_number}`,
                            icon: "{{ asset('favicon.png') }}",
                            tag: `order-${order.id}`
                        });
                        notif.onclick = function() {
                            window.focus();
                            window.location.href = order.view_url;
                            notif.close();
                        };
                    } catch (e) {
                        console.warn('Desktop notification error:', e);
                    }
                }

                // Display Floating Toast Banner
                function showLiveOrderToast(order) {
                    const container = document.getElementById('liveOrderToastContainer');
                    if (!container) return;

                    const safeName = escapeHtml(order.name);
                    const safeCity = escapeHtml(order.city || 'Tamil Nadu');
                    const safeOrderNumber = escapeHtml(order.order_number);

                    const toast = document.createElement('div');
                    toast.className =
                        'pointer-events-auto bg-white border-2 border-emerald-500 rounded-2xl shadow-2xl p-4 transform translate-y-0 transition-all duration-300 flex flex-col gap-2.5 animate-bounce';
                    toast.innerHTML = `
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-base shadow-sm">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </span>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                        <span>🎉 NEW ORDER RECEIVED!</span>
                                    </div>
                                    <div class="text-xs font-black text-slate-900">${safeName} <span class="text-slate-500 font-semibold">(${safeCity})</span></div>
                                </div>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xs cursor-pointer close-toast-btn">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-medium">
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-400 uppercase font-bold">Order Number</span>
                                <span class="font-mono text-slate-800 font-bold">${safeOrderNumber}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] text-slate-400 uppercase font-bold">Total Amount</span>
                                <div class="font-black text-emerald-700 text-base">${escapeHtml(order.total_amount_formatted)}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-0.5">
                            <a href="${order.view_url}" class="flex-1 text-center py-2 px-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md transition active:scale-95">
                                <i class="fa-solid fa-eye text-[11px] mr-1"></i> View Order
                            </a>
                            <button type="button" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer close-toast-btn">
                                Dismiss
                            </button>
                        </div>
                    `;

                    // Add close listeners
                    toast.querySelectorAll('.close-toast-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            toast.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => toast.remove(), 200);
                        });
                    });

                    container.appendChild(toast);

                    // Auto dismiss after 14 seconds
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.classList.add('opacity-0', 'scale-95');
                            setTimeout(() => toast.remove(), 200);
                        }
                    }, 14000);
                }

                // Update Notification Badges (Bell & Mobile)
                function updateNotificationBadges(unreadCount) {
                    const badge = document.getElementById('adminNotificationBadge');
                    const mobileBadge = document.getElementById('adminMobileNotificationBadge');
                    const unreadEl = document.getElementById('adminUnreadCountText');

                    const count = parseInt(unreadCount, 10) || 0;
                    if (unreadEl) unreadEl.textContent = count;

                    if (count > 0) {
                        if (badge) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.classList.remove('hidden');
                        }
                        if (mobileBadge) {
                            mobileBadge.textContent = count > 99 ? '99+' : count;
                            mobileBadge.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (mobileBadge) mobileBadge.classList.add('hidden');
                    }
                }

                // Mark single order notification as read
                window.markSingleNotificationAsRead = async function(orderId, event) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const response = await fetch(`/admin/notifications/${orderId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            updateNotificationBadges(data.unread_count);
                            const row = document.getElementById(`notif-order-${orderId}`);
                            if (row) {
                                row.classList.remove('bg-rose-50/50');
                                row.classList.add('opacity-75');
                                const dot = row.querySelector('.notif-unread-dot');
                                if (dot) dot.remove();
                                const actionBtn = row.querySelector('.notif-mark-btn');
                                if (actionBtn) {
                                    actionBtn.outerHTML = `<span class="text-slate-300 p-1 shrink-0" title="Read"><i class="fa-solid fa-check-double text-[10px]"></i></span>`;
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Failed to mark notification as read:', e);
                    }
                };

                // Mark all order notifications as read
                window.markAllNotificationsAsRead = async function() {
                    const btn = document.getElementById('markAllReadBtn');
                    if (btn) btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i> Marking...`;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const response = await fetch(`{{ route('admin.notifications.mark_all_read') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            updateNotificationBadges(0);
                            document.querySelectorAll('#adminNotificationList .notif-order-row').forEach(row => {
                                row.classList.remove('bg-rose-50/50');
                                row.classList.add('opacity-75');
                                const dot = row.querySelector('.notif-unread-dot');
                                if (dot) dot.remove();
                                const actionBtn = row.querySelector('.notif-mark-btn');
                                if (actionBtn) {
                                    actionBtn.outerHTML = `<span class="text-slate-300 p-1 shrink-0" title="Read"><i class="fa-solid fa-check-double text-[10px]"></i></span>`;
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Failed to mark all as read:', e);
                    } finally {
                        if (btn) btn.innerHTML = `<i class="fa-solid fa-check-double text-[10px]"></i> <span>Mark all read</span>`;
                    }
                };

                // Render Recent Orders in Dropdown
                function renderNotificationList(orders) {
                    const list = document.getElementById('adminNotificationList');
                    if (!list) return;

                    if (!orders || orders.length === 0) {
                        list.innerHTML = `
                            <div class="p-6 text-center text-slate-400 text-xs space-y-1">
                                <i class="fa-regular fa-clipboard text-slate-300 text-2xl"></i>
                                <p class="font-bold text-slate-600">No recent orders</p>
                                <p class="text-[10px]">New incoming orders will appear here automatically.</p>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    orders.forEach(order => {
                        const safeName = escapeHtml(order.name);
                        const safeCity = escapeHtml(order.city || 'Customer');
                        const safeOrderNum = escapeHtml(order.order_number);
                        const safeAmt = escapeHtml(order.total_amount_formatted);
                        const safeTime = escapeHtml(order.time || '');
                        const isRead = Boolean(order.is_read);

                        const unreadDot = !isRead 
                            ? `<span class="notif-unread-dot w-2 h-2 rounded-full bg-rose-500 shrink-0 shadow-xs" title="New Unread Order"></span>` 
                            : '';
                        const bgClass = !isRead ? 'bg-rose-50/50 hover:bg-rose-100/60 font-semibold' : 'hover:bg-slate-50 opacity-80';

                        const actionBtn = !isRead ? `
                            <button type="button" onclick="markSingleNotificationAsRead(${order.id}, event)" 
                                class="notif-mark-btn text-slate-400 hover:text-emerald-600 p-1.5 rounded-lg hover:bg-slate-200/70 transition cursor-pointer shrink-0" 
                                title="Mark as read">
                                <i class="fa-solid fa-check text-xs"></i>
                            </button>
                        ` : `
                            <span class="text-slate-300 p-1 shrink-0" title="Read">
                                <i class="fa-solid fa-check-double text-[10px]"></i>
                            </span>
                        `;

                        html += `
                            <div id="notif-order-${order.id}" class="notif-order-row p-3 transition-colors flex items-center justify-between gap-2 select-none border-b border-slate-100 last:border-b-0 ${bgClass}">
                                <a href="${order.view_url}" class="flex items-start gap-2.5 flex-1 min-w-0 group">
                                    <span class="w-8 h-8 rounded-xl ${isRead ? 'bg-slate-100 text-slate-500 border-slate-200/50' : 'bg-rose-100 text-rose-700 border-rose-200'} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 border transition">
                                        <i class="fa-solid fa-receipt"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <div class="flex items-center gap-1.5 truncate">
                                                ${unreadDot}
                                                <h5 class="text-xs font-bold text-slate-900 group-hover:text-rose-600 transition truncate">${safeName}</h5>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-medium shrink-0">${safeTime}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5">
                                            <span class="font-mono text-slate-600 font-bold">${safeOrderNum}</span>
                                            <span>•</span>
                                            <span class="truncate">${safeCity}</span>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs font-extrabold text-emerald-700">${safeAmt}</span>
                                            <span class="text-[9px] px-1.5 py-0.2 rounded font-bold ${order.payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                                ${order.payment_status ? order.payment_status.toUpperCase() : 'PENDING'}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                ${actionBtn}
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                }

                // Poll the backend for new orders
                async function pollNewOrders() {
                    try {
                        const response = await fetch(
                            `{{ route('admin.notifications.check') }}?last_order_id=${lastKnownOrderId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                        if (!response.ok) return;

                        const data = await response.json();
                        if (!data.success) return;

                        // Update pending count
                        const pendingEl = document.getElementById('adminPendingCountText');
                        if (pendingEl) pendingEl.textContent = data.pending_count || 0;

                        // Update Unread Count & Bell Badges
                        updateNotificationBadges(data.unread_count !== undefined ? data.unread_count : data.pending_count);

                        // Update Sidebar Orders Badge (shows pending orders)
                        const sidebarBadge = document.getElementById('adminSidebarOrdersBadge');
                        if (sidebarBadge) {
                            if (data.pending_count > 0) {
                                sidebarBadge.textContent = data.pending_count;
                                sidebarBadge.classList.remove('hidden');
                            } else {
                                sidebarBadge.classList.add('hidden');
                            }
                        }

                        // Render recent orders in dropdown
                        if (data.recent_orders) {
                            renderNotificationList(data.recent_orders);
                        }

                        // Handle first load: initialize lastKnownOrderId without firing alert
                        if (isFirstPoll) {
                            lastKnownOrderId = data.latest_order_id || 0;
                            isFirstPoll = false;
                            return;
                        }

                        // On subsequent polls: If new orders exist!
                        if (data.new_orders && data.new_orders.length > 0) {
                            // 1. Play Sound Chime!
                            window.playOrderAlertSound();

                            // 2. Bell Ring Animation
                            const bellIcon = document.getElementById('adminNotificationBellIcon');
                            if (bellIcon) {
                                bellIcon.classList.add('text-rose-600', 'animate-bounce');
                                setTimeout(() => {
                                    bellIcon.classList.remove('text-rose-600', 'animate-bounce');
                                }, 4000);
                            }

                            // 3. Display Toast Banner & Desktop Push Notification for each new order
                            data.new_orders.forEach(order => {
                                showLiveOrderToast(order);
                                triggerDesktopNotification(order);
                            });

                            lastKnownOrderId = data.latest_order_id;
                        }

                    } catch (e) {
                        console.warn('Order notification polling failed:', e);
                    }
                }

                // Start polling every 12 seconds
                pollNewOrders();
                pollInterval = setInterval(pollNewOrders, 12000);

                // Re-sync immediately when browser tab becomes active
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        pollNewOrders();
                    }
                });

                // Resume audio context and preload audio element on first user interaction
                function unlockAudio() {
                    try {
                        getAudioContext();
                        const audioEl = document.getElementById('newOrderAlertAudio');
                        if (audioEl) audioEl.load();
                    } catch (e) {}
                }
                ['click', 'touchstart', 'keydown'].forEach(evt => {
                    document.addEventListener(evt, unlockAudio, {
                        once: true,
                        passive: true
                    });
                });

                // Dropdown Toggle
                window.toggleOrderNotificationDropdown = function() {
                    const menu = document.getElementById('adminNotificationMenu');
                    const btn = document.getElementById('adminOrderNotificationBtn');
                    if (!menu) return;

                    const isHidden = menu.classList.contains('hidden');
                    if (isHidden) {
                        menu.classList.remove('hidden');
                        if (btn) btn.setAttribute('aria-expanded', 'true');
                        getAudioContext();
                    } else {
                        menu.classList.add('hidden');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                    }
                };

                // Close on click outside
                document.addEventListener('click', function(e) {
                    const container = document.getElementById('adminOrderNotificationContainer');
                    if (container && !container.contains(e.target)) {
                        const menu = document.getElementById('adminNotificationMenu');
                        const btn = document.getElementById('adminOrderNotificationBtn');
                        if (menu) menu.classList.add('hidden');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                    }
                });
            })
            ();
        </script>
    @endif

    <!-- ========================================================
         FESTIVE SIVAKASI CRACKER FUSE SCROLLBAR (வெடி திரி)
         Burns bottom-to-top as user scrolls down, regenerates new when scrolling up!
         ======================================================== -->
    <div id="crackerFuseScrollbarContainer"
        class="fixed top-0 right-0 bottom-0 z-[99999] pointer-events-none select-none flex items-stretch transition-opacity duration-300">
        <!-- Particle Canvas for Sizzling Sparks (Shoots into page) -->
        <canvas id="crackerFuseCanvas" class="absolute top-0 right-0 pointer-events-none z-10"></canvas>

        <!-- Interactive Fuse Track (Ultra-Sleek & Thin) -->
        <div id="crackerFuseTrack"
            class="relative w-2.5 sm:w-3 h-full pointer-events-auto cursor-pointer group flex flex-col items-center">
            <!-- Background: Unburnt Fresh Cracker Fuse Rope (Full Height) -->
            <div class="absolute inset-y-0 w-1 sm:w-1.5 rounded-full overflow-hidden border border-amber-950/20 cracker-fuse-unburnt shadow-xs"
                title="Cracker Fuse (வெடி திரி)"></div>

            <!-- Burnt Ash Fuse Rope (Grows from BOTTOM to current scroll position upwards) -->
            <div id="crackerFuseBurnt"
                class="absolute bottom-0 w-1 sm:w-1.5 rounded-b-full overflow-hidden cracker-fuse-burnt transition-[height] duration-75 ease-out"
                style="height: 0%;"></div>

            <!-- Burning Spark / Flame Tip (Ascends from bottom to top on scroll) -->
            <div id="crackerFuseSpark"
                class="absolute left-1/2 -translate-x-1/2 translate-y-1/2 z-20 flex items-center justify-center cursor-grab active:cursor-grabbing transition-[bottom] duration-75 ease-out"
                style="bottom: 0%;">
                <div class="relative flex items-center justify-center">
                    <!-- Radiating Flame Glow -->
                    <div
                        class="absolute w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-amber-400/30 blur-xs animate-ping pointer-events-none">
                    </div>
                    <div
                        class="absolute w-4 h-4 sm:w-4.5 sm:h-4.5 rounded-full bg-rose-500/40 blur-xs pointer-events-none">
                    </div>

                    <!-- Glowing Burning Head with Fire Icon (Sleek & Compact) -->
                    <div
                        class="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-gradient-to-tr from-rose-600 via-amber-400 to-yellow-100 shadow-[0_0_8px_#fbbf24,0_0_14px_#ea580c] flex items-center justify-center border-1.5 border-white animate-spark-pulse group-hover:scale-125 transition-transform">
                        <i class="fa-solid fa-fire text-[8px] text-amber-950"></i>
                    </div>

                    <!-- Progress Tooltip -->
                    <div id="crackerFuseTooltip"
                        class="absolute right-5 sm:right-6 bg-slate-900/90 text-amber-300 font-extrabold text-[10px] px-2 py-0.5 rounded shadow-lg border border-amber-500/40 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none backdrop-blur-xs flex items-center gap-1">
                        <span>🔥</span>
                        <span id="crackerFusePercent">0%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const container = document.getElementById('crackerFuseScrollbarContainer');
            const track = document.getElementById('crackerFuseTrack');
            const burnt = document.getElementById('crackerFuseBurnt');
            const spark = document.getElementById('crackerFuseSpark');
            const percentEl = document.getElementById('crackerFusePercent');
            const canvas = document.getElementById('crackerFuseCanvas');
            if (!container || !track || !burnt || !spark || !canvas) return;

            const ctx = canvas.getContext('2d');
            let particles = [];
            let lastScrollY = window.scrollY || window.pageYOffset || 0;
            let isDragging = false;

            // Resize canvas to match window viewport
            function resizeCanvas() {
                canvas.width = 90;
                canvas.height = window.innerHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas, {
                passive: true
            });

            // Spark colors: fiery whites, yellows, golds, oranges, reds
            const sparkColors = ['#ffffff', '#fffbeb', '#fef08a', '#fde047', '#f59e0b', '#ea580c', '#ef4444'];

            // Emit sparks from burning tip (Spraying upwards & into page)
            function createSparks(x, y, count = 3) {
                for (let i = 0; i < count; i++) {
                    // Spray direction: leftwards (into the page) and UPWARDS!
                    const angle = Math.PI * (0.85 + Math.random() * 0.45);
                    const speed = Math.random() * 4.5 + 2.5;
                    particles.push({
                        x: x,
                        y: y,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed - 1.5,
                        size: Math.random() * 1.8 + 0.8,
                        length: Math.random() * 5 + 2.5,
                        color: sparkColors[Math.floor(Math.random() * sparkColors.length)],
                        alpha: 1.0,
                        decay: Math.random() * 0.04 + 0.02
                    });
                }
            }

            // Particle Animation Loop (60 FPS)
            function renderParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = particles.length - 1; i >= 0; i--) {
                    const p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.14; // gravity pulling sparks downward
                    p.alpha -= p.decay;

                    if (p.alpha <= 0 || p.x < 0 || p.y > canvas.height) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = Math.max(0, p.alpha);
                    ctx.strokeStyle = p.color;
                    ctx.fillStyle = p.color;
                    ctx.shadowColor = p.color;
                    ctx.shadowBlur = 6;

                    // Spark trail line
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p.x - p.vx * 1.6, p.y - p.vy * 1.6);
                    ctx.lineWidth = p.size;
                    ctx.lineCap = 'round';
                    ctx.stroke();

                    // Spark core dot
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size * 0.9, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.restore();
                }

                requestAnimationFrame(renderParticles);
            }
            requestAnimationFrame(renderParticles);

            // Update burning fuse position (Bottom-to-Top burning)
            function updateFuse() {
                const doc = document.documentElement;
                const totalHeight = doc.scrollHeight - window.innerHeight;

                if (totalHeight <= 15) {
                    container.style.opacity = '0';
                    return;
                }
                container.style.opacity = '1';

                const currentY = window.scrollY || window.pageYOffset || 0;
                const ratio = Math.max(0, Math.min(1, currentY / totalHeight));
                const percent = Math.round(ratio * 100);

                // Update burnt ash height (anchored at bottom, climbs upwards)
                burnt.style.height = `${percent}%`;

                // Update burning spark position (climbs from bottom upwards)
                spark.style.bottom = `${percent}%`;
                spark.style.top = 'auto';
                percentEl.textContent = `${percent}%`;

                // Burning sparks effect when scrolling DOWN (fire climbs up!)
                const delta = currentY - lastScrollY;
                if (delta > 1) {
                    const sparkY = (1 - ratio) * window.innerHeight;
                    const count = Math.min(8, Math.floor(Math.abs(delta) / 10) + 2);
                    createSparks(canvas.width - 10, sparkY, count);
                }

                lastScrollY = currentY;
            }

            window.addEventListener('scroll', updateFuse, {
                passive: true
            });
            window.addEventListener('resize', updateFuse, {
                passive: true
            });
            document.addEventListener('DOMContentLoaded', updateFuse);
            setTimeout(updateFuse, 150);

            // Occasional idle flicker
            setInterval(() => {
                if (particles.length < 3 && container.style.opacity !== '0') {
                    const doc = document.documentElement;
                    const totalHeight = doc.scrollHeight - window.innerHeight;
                    if (totalHeight > 15) {
                        const ratio = Math.max(0, Math.min(1, (window.scrollY || 0) / totalHeight));
                        const sparkY = (1 - ratio) * window.innerHeight;
                        createSparks(canvas.width - 10, sparkY, 1);
                    }
                }
            }, 500);

            // Click anywhere on track to scroll (Bottom = 0%, Top = 100%)
            track.addEventListener('click', function(e) {
                if (isDragging) return;
                const rect = track.getBoundingClientRect();
                const fromBottom = rect.bottom - e.clientY;
                const clickRatio = Math.max(0, Math.min(1, fromBottom / rect.height));
                const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
                window.scrollTo({
                    top: clickRatio * totalHeight,
                    behavior: 'smooth'
                });
            });

            // Drag the burning spark thumb
            function handleDrag(clientY) {
                const rect = track.getBoundingClientRect();
                const fromBottom = rect.bottom - clientY;
                const dragRatio = Math.max(0, Math.min(1, fromBottom / rect.height));
                const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
                window.scrollTo({
                    top: dragRatio * totalHeight,
                    behavior: 'auto'
                });
            }

            spark.addEventListener('mousedown', function(e) {
                isDragging = true;
                document.body.style.userSelect = 'none';
                e.preventDefault();
            });

            window.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                handleDrag(e.clientY);
            });

            window.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    document.body.style.userSelect = '';
                }
            });

            // Mobile / Touch support
            spark.addEventListener('touchstart', function() {
                isDragging = true;
            }, {
                passive: true
            });

            window.addEventListener('touchmove', function(e) {
                if (!isDragging || !e.touches[0]) return;
                handleDrag(e.touches[0].clientY);
            }, {
                passive: true
            });

            window.addEventListener('touchend', function() {
                isDragging = false;
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
