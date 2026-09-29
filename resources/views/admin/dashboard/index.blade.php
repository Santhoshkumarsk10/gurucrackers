@extends('layouts.app')

@section('title', 'Admin Dashboard - Guru Crackers Sivakasi')

@section('content')
<div class="space-y-6 pb-16">

    <!-- Top Greeting Banner & Quick Actions -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-900 via-rose-800 to-amber-900 text-white p-5 sm:p-7 shadow-xl shadow-rose-950/20 border border-rose-700/50">
        <!-- Festive Background Accents -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-rose-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-amber-200 text-xs font-bold border border-white/15">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Diwali Season Live Portal</span>
                    <span class="text-white/40">•</span>
                    <span>{{ now()->format('l, d F Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-heading tracking-tight text-white flex items-center gap-2.5">
                    <span>{{ $shop->shop_name ?? 'Guru Crackers' }}</span>
                    <span class="text-xs font-bold bg-amber-400 text-slate-900 px-2 py-0.5 rounded-md uppercase tracking-wider">Admin</span>
                </h1>
                <p class="text-xs sm:text-sm text-rose-100/90 max-w-2xl">
                    Real-time cracker bookings overview, revenue statistics, live order fulfillment status, and analytics.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.reports.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white text-xs font-extrabold transition-all border border-white/20 active:scale-95 shadow-sm">
                    <i class="fa-solid fa-chart-line text-amber-300"></i>
                    <span>Sales Report</span>
                </a>
                <a href="{{ route('order.create') }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 text-xs font-extrabold transition-all shadow-md shadow-amber-900/30 active:scale-95">
                    <i class="fa-solid fa-plus text-slate-900"></i>
                    <span>New Booking</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Primary 4 KPI Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Total Revenue Card -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Bookings Value</span>
                <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    ₹{{ number_format($totalRevenueAll, 2) }}
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px]">
                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                        ₹{{ number_format($totalPaidRevenue, 2) }} paid/dispatched
                    </span>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-amber-500"></div>
        </div>

        <!-- 2. Today's Bookings Card -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Today's Sales</span>
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bolt"></i>
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    ₹{{ number_format($todayRevenue, 2) }}
                </div>
                <div class="mt-2 flex items-center gap-2 text-[11px]">
                    <span class="bg-amber-100 text-amber-900 font-bold px-2 py-0.5 rounded-full">
                        {{ $todayOrdersCount }} {{ Str::plural('order', $todayOrdersCount) }} today
                    </span>
                    <span class="text-slate-400">|</span>
                    <span class="text-slate-500 font-medium">Month: ₹{{ number_format($monthRevenue, 0) }}</span>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500"></div>
        </div>

        <!-- 3. Total Orders Bookings Card -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Orders</span>
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    {{ $totalOrdersCount }}
                </div>
                <div class="mt-2 flex items-center gap-1.5 flex-wrap text-[10px] font-bold">
                    <span class="bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-md border border-amber-200/60">
                        {{ $statusCounts['pending'] }} Pending
                    </span>
                    <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded-md border border-emerald-200/60">
                        {{ $statusCounts['paid'] + $statusCounts['confirmed'] }} Confirmed
                    </span>
                    <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded-md border border-indigo-200/60">
                        {{ $statusCounts['dispatched'] }} Dispatched
                    </span>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-500"></div>
        </div>

        <!-- 4. Average Order Value & Items Card -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Avg. Order Value (AOV)</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-receipt"></i>
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    ₹{{ number_format($aov, 2) }}
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-600 font-medium">
                    <i class="fa-solid fa-box text-rose-500 text-[10px]"></i>
                    <span class="font-bold text-slate-800">{{ number_format($totalItemsSold) }}</span>
                    <span>crackers units ordered</span>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
        </div>

    </div>

    <!-- Customer Portal Traffic & Visitor Analytics Grid -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-ping"></span>
                <h2 class="text-xs sm:text-sm font-black font-heading uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-users-viewfinder text-cyan-600"></i>
                    <span>Store Traffic & Customer Portal Visitors (வாடிக்கையாளர் வருகைகள்)</span>
                </h2>
            </div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-lg border border-slate-200/80 shadow-2xs">
                    <i class="fa-solid fa-mobile-screen text-indigo-500"></i>
                    <span>{{ $mobilePercentage }}% Mobile</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-lg border border-slate-200/80 shadow-2xs">
                    <i class="fa-solid fa-laptop text-slate-500"></i>
                    <span>{{ $desktopPercentage }}% Desktop</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Store Visitors -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Unique Visitors</span>
                    <span class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users"></i>
                    </span>
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                        {{ number_format($totalUniqueVisitors) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                        <span>{{ number_format($totalPageViews) }} total views</span>
                        <span class="text-cyan-700 font-bold bg-cyan-50 border border-cyan-200/60 px-1.5 py-0.5 rounded text-[10px]">All-Time</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-blue-500"></div>
            </div>

            <!-- 2. Today's Store Visits -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Today's Visitors</span>
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                        {{ number_format($todayUniqueVisitors) }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px]">
                        <span class="text-emerald-700 font-bold bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full flex items-center gap-1 text-[10px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ number_format($todayPageViews) }} views today
                        </span>
                        <span class="text-slate-400 font-medium text-[10px]">Month: {{ number_format($monthUniqueVisitors) }}</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
            </div>

            <!-- 3. Store Conversion Rate -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Store Conversion</span>
                    <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </span>
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading flex items-baseline gap-1">
                        <span>{{ $conversionRate }}%</span>
                        <span class="text-xs font-semibold text-slate-400">rate</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-slate-600 font-medium">
                        <span class="font-bold text-purple-700">{{ $totalOrdersCount }} orders placed</span>
                        <span class="text-slate-400 text-[10px]">from visitors</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-purple-500"></div>
            </div>

            <!-- 4. Top Traffic Channel -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Top Traffic Channel</span>
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-xs group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-qrcode"></i>
                    </span>
                </div>
                <div class="mt-2.5">
                    <div class="text-lg sm:text-xl font-black text-slate-900 font-heading truncate">
                        {{ $topTrafficSources->first()->referrer_source ?? 'Direct / QR Standee' }}
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                        <span>{{ $topTrafficSources->first()->count ?? 0 }} visits</span>
                        <span class="bg-amber-50 text-amber-800 border border-amber-200/60 px-1.5 py-0.5 rounded text-[10px] font-bold">QR / Direct</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500"></div>
            </div>
        </div>
    </div>

    <!-- Charts Section: 14-Day Sales Trend & Order Status Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- 14-Day Sales Trend (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-black text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-rose-600 text-sm"></i>
                        <span>Sales & Bookings Trend (Last 14 Days)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daily total booking revenue and customer order counts.</p>
                </div>
                <span class="text-[11px] font-bold bg-rose-50 text-rose-700 px-2.5 py-1 rounded-xl shrink-0 self-start sm:self-auto">
                    Live Analytics
                </span>
            </div>

            <!-- Chart Canvas Container -->
            <div class="mt-4 relative h-64 sm:h-72 w-full">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Order Status Doughnut (1 Column) -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-black text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-amber-500 text-sm"></i>
                        <span>Order Status</span>
                    </h2>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Distribution</span>
                </div>

                <div class="relative h-48 sm:h-52 w-full mt-4 flex items-center justify-center">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>

            <!-- Status Legends Breakdown -->
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-xl bg-amber-50/60 border border-amber-100">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <div>
                        <div class="text-[10px] text-amber-800 font-semibold">Pending</div>
                        <div class="font-extrabold text-slate-900">{{ $statusCounts['pending'] }} orders</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 p-2 rounded-xl bg-emerald-50/60 border border-emerald-100">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <div>
                        <div class="text-[10px] text-emerald-800 font-semibold">Paid</div>
                        <div class="font-extrabold text-slate-900">{{ $statusCounts['paid'] }} orders</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 p-2 rounded-xl bg-blue-50/60 border border-blue-100">
                    <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0"></span>
                    <div>
                        <div class="text-[10px] text-blue-800 font-semibold">Confirmed</div>
                        <div class="font-extrabold text-slate-900">{{ $statusCounts['confirmed'] }} orders</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 p-2 rounded-xl bg-indigo-50/60 border border-indigo-100">
                    <span class="w-3 h-3 rounded-full bg-indigo-600 shrink-0"></span>
                    <div>
                        <div class="text-[10px] text-indigo-800 font-semibold">Dispatched</div>
                        <div class="font-extrabold text-slate-900">{{ $statusCounts['dispatched'] }} orders</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Secondary Widgets: Top 5 Best Sellers & System Operations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Top Best Selling Crackers (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-black text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-fire text-rose-500"></i>
                        <span>Top Selling Crackers</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Most in-demand products by units sold.</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline">
                    View Catalog →
                </a>
            </div>

            <div class="mt-4 divide-y divide-slate-100">
                @forelse($topProducts as $idx => $prod)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-6 h-6 rounded-lg {{ $idx === 0 ? 'bg-amber-400 text-slate-900' : ($idx === 1 ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-600') }} flex items-center justify-center font-extrabold text-xs shrink-0">
                                #{{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-xs text-slate-800 truncate">{{ $prod->product_name }}</div>
                                <div class="text-[11px] text-slate-400">In {{ $prod->orders_count }} separate bookings</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-extrabold text-slate-900">
                                {{ number_format($prod->total_qty) }} boxes sold
                            </div>
                            <div class="text-[11px] font-semibold text-emerald-600">
                                ₹{{ number_format($prod->total_sales, 2) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-box-open text-2xl mb-1 text-slate-300"></i>
                        <p>No products sold yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Operations & Store Status (1 Column) -->
        <div class="space-y-4">
            
            <!-- WhatsApp Bot Status Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                        <span>WhatsApp Gateway</span>
                    </span>
                    @if(!empty($gatewayStatus['online']) && !empty($gatewayStatus['connected']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Online & Ready
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Offline
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    Automated WhatsApp bills, packing slips, payment confirmations, and LR dispatch notifications.
                </p>
                <div class="mt-3">
                    <a href="{{ route('admin.whatsapp.index') }}" 
                       class="text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                        <span>Check WhatsApp Gateway Status</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Store Catalog Quick Stats -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-store text-rose-600"></i>
                    <span>Store Catalog Overview</span>
                </span>
                
                <div class="grid grid-cols-2 gap-2 mt-3">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <div class="text-lg font-black text-slate-800 font-heading">{{ $totalProducts }}</div>
                        <div class="text-[10px] text-slate-500 font-semibold">Total Crackers</div>
                        <div class="text-[9px] text-emerald-600 font-bold mt-0.5">{{ $activeProducts }} Active</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <div class="text-lg font-black text-slate-800 font-heading">{{ $totalCategories }}</div>
                        <div class="text-[10px] text-slate-500 font-semibold">Categories</div>
                        <div class="text-[9px] text-rose-600 font-bold mt-0.5">Sivakasi Direct</div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('admin.products.create') }}" class="font-bold text-rose-600 hover:underline">
                        + Add New Cracker
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="text-slate-500 hover:text-slate-800 font-medium">
                        Categories →
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900 font-heading flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-600"></i>
                    <span>Recent Customer Bookings</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Latest online cracker orders received.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors shrink-0 self-start sm:self-auto">
                <span>View All {{ $totalOrdersCount }} Orders</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left">Order #</th>
                        <th class="py-3 px-4 text-left">Customer</th>
                        <th class="py-3 px-4 text-left">Phone</th>
                        <th class="py-3 px-4 text-left">Destination</th>
                        <th class="py-3 px-4 text-center">Items</th>
                        <th class="py-3 px-4 text-right">Amount</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-left">Date</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-rose-700 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $order->name }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono">
                                {{ $order->phone1 }}
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <div>{{ $order->city ?: 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-normal">{{ $order->state ?? 'Tamil Nadu' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-bold">
                                    {{ $order->items_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900 font-mono">
                                ₹{{ number_format((float) $order->total_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $st = $order->payment_status ?: 'pending';
                                @endphp
                                @if($st === 'dispatched')
                                    <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                        🚚 Dispatched
                                    </span>
                                @elseif($st === 'confirmed')
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                        📦 Confirmed
                                    </span>
                                @elseif($st === 'paid')
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                        ✅ Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $order->created_at ? $order->created_at->format('d M, h:i A') : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 transition-colors"
                                       title="View Order Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.invoice', $order) }}" 
                                       class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 transition-colors"
                                       title="Download Invoice PDF">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                No orders received yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js CDN for Interactive Graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sales & Orders Trend Line/Bar Chart
    const trendCtx = document.getElementById('salesTrendChart');
    if (trendCtx) {
        const labels = @json($chartLabels);
        const revenues = @json($chartRevenue);
        const orders = @json($chartOrders);
        const visitors = @json($chartVisitors);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (₹)',
                        data: revenues,
                        borderColor: '#e11d48',
                        backgroundColor: 'rgba(225, 29, 72, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#e11d48',
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        yAxisID: 'yRevenue',
                    },
                    {
                        label: 'Orders Count',
                        data: orders,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        type: 'bar',
                        borderRadius: 6,
                        barThickness: 12,
                        yAxisID: 'yOrders',
                    },
                    {
                        label: 'Visitors',
                        data: visitors,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.05)',
                        borderDash: [3, 3],
                        tension: 0.3,
                        borderWidth: 2,
                        pointBackgroundColor: '#06b6d4',
                        pointRadius: 2.5,
                        pointHoverRadius: 5,
                        yAxisID: 'yOrders',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return ' Revenue: ₹' + Number(context.parsed.y).toLocaleString('en-IN');
                                }
                                if (context.dataset.label === 'Visitors') {
                                    return ' Visitors: ' + context.parsed.y;
                                }
                                return ' Orders: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#64748b' }
                    },
                    yRevenue: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 10 },
                            color: '#e11d48',
                            callback: function(val) { return '₹' + val; }
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#d97706',
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // 2. Order Status Doughnut Chart
    const statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx) {
        const counts = @json($statusCounts);
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Confirmed', 'Dispatched'],
                datasets: [{
                    data: [counts.pending, counts.paid, counts.confirmed, counts.dispatched],
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#6366f1'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        cornerRadius: 8,
                        padding: 10
                    }
                }
            }
        });
    }
});
</script>
@endsection
