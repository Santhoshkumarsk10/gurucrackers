@extends('layouts.app')

@section('title', 'Customized Sales & Orders Report - Admin')

@section('content')
    <!-- Flatpickr CSS for Interactive Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            border: 1px solid #f1f5f9 !important;
            font-family: inherit !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: #e11d48 !important;
            border-color: #e11d48 !important;
        }

        .flatpickr-day.inRange {
            background: #ffe4e6 !important;
            border-color: #ffe4e6 !important;
            box-shadow: -5px 0 0 #ffe4e6, 5px 0 0 #ffe4e6 !important;
        }
    </style>

    <div class="space-y-6 pb-16">

        <!-- Header & Export Action Bar -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                        Sales & Performance Report
                    </h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Customized date ranges, Indian state filters, payment status tracking, and Excel/PDF reports.
                </p>
            </div>

            <!-- Export Dropdown & Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- 1. Orders CSV Export -->
                <a href="{{ route('admin.reports.export_csv', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm shadow-emerald-600/20 active:scale-95"
                    title="Download Orders Summary as Excel/CSV">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                    <span>Orders (CSV)</span>
                </a>

                <!-- 2. Item-Wise Breakdown CSV Export -->
                <a href="{{ route('admin.reports.export_items_csv', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-sm shadow-teal-600/20 active:scale-95"
                    title="Download Cracker Item-by-Item Breakdown as CSV">
                    <i class="fa-solid fa-list-check text-xs"></i>
                    <span>Items (CSV)</span>
                </a>

                <!-- 3. PDF Report Export -->
                <a href="{{ route('admin.reports.export_pdf', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm shadow-rose-600/20 active:scale-95"
                    title="Download Formatted PDF Report">
                    <i class="fa-solid fa-file-pdf text-xs"></i>
                    <span>PDF Report</span>
                </a>

                <!-- 4. Quick Print Button -->
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all active:scale-95"
                    title="Print this Report">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>

        <!-- Customized Filter Controls Hub -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

            <!-- Filter Bar Row 1: Quick Period Chips -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-bold hide-scrollbar">
                <span class="text-slate-400 text-[11px] uppercase tracking-wider mr-1 shrink-0 flex items-center gap-1">
                    <i class="fa-regular fa-calendar-check text-xs"></i>
                    <span>Period:</span>
                </span>

                @php
                    $currentRange = request('range', 'all_time');
                    if (request('start_date') || request('end_date')) {
                        $currentRange = 'custom';
                    }
                    if (request('month') && request('year')) {
                        $currentRange = 'month_year';
                    }
                    $presets = [
                        'all_time' => 'All Time',
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_7_days' => 'Last 7 Days',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'last_30_days' => 'Last 30 Days',
                        'this_year' => 'This Year',
                    ];
                @endphp

                @foreach ($presets as $key => $name)
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['range', 'start_date', 'end_date', 'month', 'year', 'page']), ['range' => $key])) }}"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0 {{ $currentRange === $key ? 'bg-rose-600 text-white shadow-sm shadow-rose-600/30' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $name }}
                    </a>
                @endforeach
            </div>

            <!-- Filter Bar Row 2: Payment Status Quick Tabs -->
            <div
                class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-bold border-t border-slate-100 pt-3 hide-scrollbar">
                <span class="text-slate-400 text-[11px] uppercase tracking-wider mr-1 shrink-0 flex items-center gap-1">
                    <i class="fa-solid fa-tag text-xs"></i>
                    <span>Status:</span>
                </span>

                @php
                    $activeStatus = request('status', 'all');
                    $totalAll = array_sum($statusCounts ?? []);
                @endphp

                <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $activeStatus === 'all' || !$activeStatus ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    <span>All Orders</span>
                    <span
                        class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeStatus === 'all' || !$activeStatus ? 'bg-slate-700 text-slate-100' : 'bg-slate-200 text-slate-600' }}">{{ $totalAll }}</span>
                </a>

                <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $activeStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20' : 'bg-amber-50 hover:bg-amber-100 text-amber-800' }}">
                    <span>⏳ Pending</span>
                    <span
                        class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeStatus === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-200/80 text-amber-900' }}">{{ $statusCounts['pending'] ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'paid'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $activeStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800' }}">
                    <span>✅ Paid</span>
                    <span
                        class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeStatus === 'paid' ? 'bg-emerald-700 text-white' : 'bg-emerald-200/80 text-emerald-900' }}">{{ $statusCounts['paid'] ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'confirmed'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $activeStatus === 'confirmed' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/20' : 'bg-blue-50 hover:bg-blue-100 text-blue-800' }}">
                    <span>📦 Confirmed</span>
                    <span
                        class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeStatus === 'confirmed' ? 'bg-blue-700 text-white' : 'bg-blue-200/80 text-blue-900' }}">{{ $statusCounts['confirmed'] ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'dispatched'])) }}"
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $activeStatus === 'dispatched' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-800' }}">
                    <span>🚚 Dispatched</span>
                    <span
                        class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeStatus === 'dispatched' ? 'bg-indigo-700 text-white' : 'bg-indigo-200/80 text-indigo-900' }}">{{ $statusCounts['dispatched'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Filter Bar Row 3: Custom Form (Interactive Date Range Calendar, Month/Year Selector, State, City, Search) -->
            <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFilterForm"
                class="pt-3 border-t border-slate-100 space-y-3 text-xs">
                <input type="hidden" name="status" id="filterStatusInput" value="{{ request('status', 'all') }}">
                <input type="hidden" name="range" id="filterRangeInput" value="{{ $currentRange }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

                    <!-- 1. Interactive Flatpickr Date Range Calendar -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1 flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-calendar-days text-rose-600"></i>
                                <span>Custom Date Range (From - To)</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-normal">Click to pick calendar</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-calendar-range absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text" id="flatpickrDateRange" placeholder="Select date range..."
                                value="{{ $startDate && $endDate ? $startDate->format('d M Y') . ' to ' . $endDate->format('d M Y') : '' }}"
                                class="w-full pl-8 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 cursor-pointer text-xs">
                            <button type="button" onclick="clearDateRange()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                title="Clear Date">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                        <!-- Hidden real inputs for form submission -->
                        <input type="hidden" name="start_date" id="hiddenStartDate"
                            value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                        <input type="hidden" name="end_date" id="hiddenEndDate"
                            value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                    </div>

                    <!-- 2. State Filter (Indian States) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1 flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot text-indigo-600"></i>
                            <span>State (மாநிலம்)</span>
                        </label>
                        <select name="state"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-rose-500 text-xs">
                            <option value="all" {{ request('state') === 'all' || !request('state') ? 'selected' : '' }}>
                                All States</option>
                            @foreach ($availableStates as $st)
                                <option value="{{ $st }}" {{ request('state') === $st ? 'selected' : '' }}>
                                    {{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. City / Destination Filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1 flex items-center gap-1">
                            <i class="fa-solid fa-city text-amber-600"></i>
                            <span>City / Destination</span>
                        </label>
                        <select name="city"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-rose-500 text-xs">
                            <option value="all" {{ request('city') === 'all' || !request('city') ? 'selected' : '' }}>
                                All Cities</option>
                            @foreach ($availableCities as $city)
                                <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                    {{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Month & Year Quick Selector -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1 flex items-center gap-1">
                            <i class="fa-regular fa-calendar text-emerald-600"></i>
                            <span>By Month & Year</span>
                        </label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <select name="month" id="filterMonthSelect"
                                class="w-full px-2 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-rose-500 text-xs">
                                <option value="">Month</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $mName = date('M', mktime(0, 0, 0, $m, 1)); @endphp
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ $mName }}</option>
                                @endfor
                            </select>
                            <select name="year" id="filterYearSelect"
                                class="w-full px-2 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-rose-500 text-xs">
                                <option value="">Year</option>
                                @php $currY = (int) now()->format('Y'); @endphp
                                @for ($y = $currY; $y >= $currY - 2; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- 5. Action Buttons (Apply & Reset) -->
                    <div class="flex items-end gap-1.5">
                        <button type="submit"
                            class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                            <i class="fa-solid fa-filter text-xs text-amber-400"></i>
                            <span>Apply Filter</span>
                        </button>
                        <a href="{{ route('admin.reports.index') }}"
                            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"
                            title="Reset All Filters">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                        </a>
                    </div>

                </div>

                <!-- Search input bar -->
                <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                    <div class="relative flex-1">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search customer name, phone, order number, city, or pincode..."
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-xs">
                    </div>
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors">
                        Search
                    </button>
                </div>

            </form>

            <!-- Active Filter Tags (if any filter is applied) -->
            @php
                $hasActiveFilters =
                    request('range') ||
                    request('start_date') ||
                    request('end_date') ||
                    (request('status') && request('status') !== 'all') ||
                    (request('state') && request('state') !== 'all') ||
                    (request('city') && request('city') !== 'all') ||
                    request('search');
            @endphp
            @if ($hasActiveFilters)
                <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Active Filters:</span>

                    @if ($startDate && $endDate)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-medium">
                            <i class="fa-regular fa-calendar text-[10px]"></i>
                            <span>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
                        </span>
                    @endif

                    @if (request('status') && request('status') !== 'all')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 font-medium">
                            <span>Status: {{ ucfirst(request('status')) }}</span>
                        </span>
                    @endif

                    @if (request('state') && request('state') !== 'all')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                            <i class="fa-solid fa-map-pin text-[10px]"></i>
                            <span>State: {{ request('state') }}</span>
                        </span>
                    @endif

                    @if (request('city') && request('city') !== 'all')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-medium">
                            <span>City: {{ request('city') }}</span>
                        </span>
                    @endif

                    @if (request('search'))
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 font-medium">
                            <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                            <span>"{{ request('search') }}"</span>
                        </span>
                    @endif

                    <a href="{{ route('admin.reports.index') }}"
                        class="text-rose-600 hover:text-rose-800 hover:underline font-bold ml-1">
                        Clear All Filters
                    </a>
                </div>
            @endif

        </div>

        <!-- Period KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            <!-- Total Revenue -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Period Revenue</span>
                <div class="text-lg sm:text-xl font-black text-slate-900 font-heading mt-1">
                    ₹{{ number_format($totalRevenue, 2) }}
                </div>
                <span class="text-[10px] text-slate-400 block mt-0.5">Total booking value</span>
            </div>

            <!-- Total Orders -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Bookings</span>
                <div class="text-lg sm:text-xl font-black text-indigo-700 font-heading mt-1">
                    {{ number_format($totalOrders) }}
                </div>
                <span class="text-[10px] text-slate-400 block mt-0.5">Customer orders</span>
            </div>

            <!-- Paid / Dispatched Amount -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Paid / Confirmed</span>
                <div class="text-lg sm:text-xl font-black text-emerald-600 font-heading mt-1">
                    ₹{{ number_format($paidRevenue, 2) }}
                </div>
                <span class="text-[10px] text-emerald-700/80 block mt-0.5">Verified revenue</span>
            </div>

            <!-- Pending Amount -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Pending Amount</span>
                <div class="text-lg sm:text-xl font-black text-amber-600 font-heading mt-1">
                    ₹{{ number_format($pendingRevenue, 2) }}
                </div>
                <span class="text-[10px] text-amber-700/80 block mt-0.5">Awaiting verification</span>
            </div>

            <!-- Average Order Value -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Avg. Order Value</span>
                <div class="text-lg sm:text-xl font-black text-slate-800 font-heading mt-1">
                    ₹{{ number_format($avgOrderValue, 2) }}
                </div>
                <span class="text-[10px] text-slate-400 block mt-0.5">Per booking</span>
            </div>

            <!-- Total Items / Units Sold -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Cracker Units</span>
                <div class="text-lg sm:text-xl font-black text-rose-700 font-heading mt-1">
                    {{ number_format($totalItemsSold) }}
                </div>
                <span class="text-[10px] text-slate-400 block mt-0.5">Boxes / Packets sold</span>
            </div>

        </div>

        <!-- Visual Charts & Period Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Period Sales Trend Graph (2 Columns) -->
            <div
                class="lg:col-span-2 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-black text-slate-900 font-heading flex items-center gap-1.5">
                                    <i class="fa-solid fa-chart-column text-rose-600"></i>
                                    <span>Sales & Bookings Trend</span>
                                </h2>
                                <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                                    {{ count($chartLabels) }} Days Timeline
                                </span>
                            </div>
                            @if (!empty($peakRevDay) && $maxRev > 0)
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Highest Day: <span class="font-bold text-rose-600">{{ $peakRevDay }}</span>
                                    (₹{{ number_format($maxRev, 2) }}) • Total Period Orders: <span
                                        class="font-bold text-slate-700">{{ $totalOrders }}</span>
                                </p>
                            @else
                                <p class="text-[11px] text-slate-400 mt-0.5">Continuous daily performance over selected
                                    timeframe.</p>
                            @endif
                        </div>

                        <!-- Interactive Chart View Switcher Tabs -->
                        <div
                            class="inline-flex items-center p-0.5 rounded-xl bg-slate-100 border border-slate-200/80 text-[11px] font-bold self-start sm:self-auto shrink-0 shadow-2xs">
                            <button type="button" id="tabChartBoth" onclick="switchChartMode('both')"
                                class="px-2.5 py-1 rounded-lg transition-all bg-white text-slate-900 shadow-xs">
                                Combined
                            </button>
                            <button type="button" id="tabChartRevenue" onclick="switchChartMode('revenue')"
                                class="px-2.5 py-1 rounded-lg transition-all text-slate-600 hover:text-slate-900">
                                Revenue (₹)
                            </button>
                            <button type="button" id="tabChartOrders" onclick="switchChartMode('orders')"
                                class="px-2.5 py-1 rounded-lg transition-all text-slate-600 hover:text-slate-900">
                                Orders
                            </button>
                        </div>
                    </div>

                    <!-- Chart Canvas Container -->
                    <div class="mt-4 relative h-64 sm:h-72 w-full">
                        <canvas id="periodSalesChart"></canvas>
                    </div>
                </div>

                <!-- Bottom Legend / Indicator Strip -->
                <div
                    class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-2.5 rounded-xs bg-rose-600 inline-block"></span>
                            <span class="font-semibold text-slate-700">Revenue (₹)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-0.5 bg-amber-500 inline-block"></span>
                            <span class="font-semibold text-slate-700">Bookings Count</span>
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Continuous Day-by-Day</span>
                </div>
            </div>

            <!-- Top Crackers in this Filtered Period (1 Column) -->
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-black text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                        <span>Top Crackers (Filtered)</span>
                    </h2>
                </div>

                <div class="mt-3 divide-y divide-slate-100 text-xs">
                    @forelse($topProducts as $idx => $prod)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 truncate">
                                <span
                                    class="w-5 h-5 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="truncate">
                                    <span class="font-bold text-slate-800 truncate block">{{ $prod->product_name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $prod->orders_count }} orders</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-extrabold text-slate-900 block">{{ number_format($prod->total_qty) }}
                                    pkts</span>
                                <span
                                    class="text-[10px] text-emerald-600 font-bold">₹{{ number_format($prod->total_sales, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No product breakdown available for this selection.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Filtered Orders Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div
                class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-black text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-list text-slate-600"></i>
                        <span>Itemized Orders Report</span>
                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-bold">
                            {{ $orders->total() }} Records
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Showing order records matching active filters.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead
                        class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-left whitespace-nowrap">Order #</th>
                            <th class="py-3 px-4 text-left whitespace-nowrap">Date & Time</th>
                            <th class="py-3 px-4 text-left">Customer & Phone</th>
                            <th class="py-3 px-4 text-left">Destination (City, State)</th>
                            <th class="py-3 px-4 text-center">Items</th>
                            <th class="py-3 px-4 text-right">Amount (₹)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-left">Transport / LR</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="text-rose-700 hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">
                                        {{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $order->created_at ? $order->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        {{ $order->name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                                        <a href="tel:{{ $order->phone1 }}" class="hover:text-rose-600 transition-colors">
                                            {{ $order->phone1 }}
                                        </a>
                                        @if ($order->phone2)
                                            <span class="text-slate-300">|</span>
                                            <a href="tel:{{ $order->phone2 }}"
                                                class="text-[10px] text-slate-400 hover:text-rose-600 transition-colors">
                                                {{ $order->phone2 }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-700">
                                    <div class="font-semibold">{{ $order->city ?: 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $order->state ?? 'Tamil Nadu' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-bold">
                                        {{ $order->items_count }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900 font-mono">
                                    ₹{{ number_format((float) $order->total_amount, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @php
                                        $st = $order->payment_status ?: 'pending';
                                    @endphp
                                    @if ($st === 'dispatched')
                                        <span
                                            class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                            🚚 Dispatched
                                        </span>
                                    @elseif($st === 'confirmed')
                                        <span
                                            class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                            📦 Confirmed
                                        </span>
                                    @elseif($st === 'paid')
                                        <span
                                            class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                            ✅ Paid
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200/70 px-2 py-0.5 rounded-full font-bold text-[10px]">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-[11px]">
                                    @if ($order->lr_number)
                                        <div class="font-bold text-slate-800">LR: {{ $order->lr_number }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $order->parcel_service_name }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
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
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-bold">No orders found matching your filters.</p>
                                    <p class="text-xs text-slate-400 mt-1">Try clearing or widening your date range and
                                        state filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Flatpickr JS & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let fpInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialize Flatpickr Interactive Date Range Picker
            const startVal = document.getElementById('hiddenStartDate').value;
            const endVal = document.getElementById('hiddenEndDate').value;

            fpInstance = flatpickr("#flatpickrDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                defaultDate: (startVal && endVal) ? [startVal, endVal] : [],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const s = instance.formatDate(selectedDates[0], "Y-m-d");
                        const e = instance.formatDate(selectedDates[1], "Y-m-d");
                        document.getElementById('hiddenStartDate').value = s;
                        document.getElementById('hiddenEndDate').value = e;
                        document.getElementById('filterRangeInput').value = 'custom';
                    }
                }
            });
        });

        // 2. High-Clarity Multi-Mode Chart.js Trend
        let trendChartInstance = null;
        const chartLabels = @json($chartLabels);
        const chartFullDates = @json($chartFullDates ?? []);
        const chartRevenue = @json($chartRevenue);
        const chartOrders = @json($chartOrders);

        function switchChartMode(mode) {
            const btnBoth = document.getElementById('tabChartBoth');
            const btnRev = document.getElementById('tabChartRevenue');
            const btnOrd = document.getElementById('tabChartOrders');

            [btnBoth, btnRev, btnOrd].forEach(btn => {
                if (btn) {
                    btn.className = "px-2.5 py-1 rounded-lg transition-all text-slate-600 hover:text-slate-900";
                }
            });

            if (mode === 'revenue' && btnRev) {
                btnRev.className = "px-2.5 py-1 rounded-lg transition-all bg-white text-rose-700 shadow-xs";
            } else if (mode === 'orders' && btnOrd) {
                btnOrd.className = "px-2.5 py-1 rounded-lg transition-all bg-white text-amber-700 shadow-xs";
            } else if (btnBoth) {
                btnBoth.className = "px-2.5 py-1 rounded-lg transition-all bg-white text-slate-900 shadow-xs";
            }

            renderTrendChart(mode);
        }

        function renderTrendChart(mode = 'both') {
            const ctx = document.getElementById('periodSalesChart');
            if (!ctx) return;

            if (trendChartInstance) {
                trendChartInstance.destroy();
            }

            let datasets = [];
            let scales = {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 10,
                            weight: '600'
                        },
                        color: '#64748b',
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 14
                    }
                }
            };

            if (mode === 'revenue') {
                // Pure Revenue Area Curve
                datasets = [{
                    label: 'Revenue (₹)',
                    data: chartRevenue,
                    type: 'line',
                    borderColor: '#e11d48',
                    backgroundColor: 'rgba(225, 29, 72, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#e11d48',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#e11d48',
                }];
                scales.y = {
                    type: 'linear',
                    beginAtZero: true,
                    grid: {
                        color: '#f8fafc'
                    },
                    ticks: {
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        color: '#e11d48',
                        callback: function(val) {
                            return '₹' + Number(val).toLocaleString('en-IN');
                        }
                    }
                };
            } else if (mode === 'orders') {
                // Pure Orders Bar
                datasets = [{
                    label: 'Bookings Count',
                    data: chartOrders,
                    type: 'bar',
                    backgroundColor: '#f59e0b',
                    hoverBackgroundColor: '#d97706',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }];
                scales.y = {
                    type: 'linear',
                    beginAtZero: true,
                    grid: {
                        color: '#f8fafc'
                    },
                    ticks: {
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        color: '#d97706',
                        stepSize: 1,
                        precision: 0,
                        callback: function(val) {
                            return val + (val === 1 ? ' order' : ' orders');
                        }
                    }
                };
            } else {
                // Combined View: Slim bars for Revenue + Crisp floating curve for Orders
                datasets = [{
                        label: 'Revenue (₹)',
                        data: chartRevenue,
                        type: 'bar',
                        backgroundColor: 'rgba(225, 29, 72, 0.85)',
                        hoverBackgroundColor: '#be123c',
                        borderRadius: 6,
                        maxBarThickness: 22,
                        yAxisID: 'yRevenue',
                        order: 2,
                    },
                    {
                        label: 'Orders Count',
                        data: chartOrders,
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: '#ffffff',
                        borderWidth: 2.5,
                        tension: 0.35,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#f59e0b',
                        pointBorderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        yAxisID: 'yOrders',
                        order: 1,
                    }
                ];
                scales.yRevenue = {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: '#f8fafc'
                    },
                    ticks: {
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        color: '#e11d48',
                        callback: function(val) {
                            return '₹' + Number(val).toLocaleString('en-IN');
                        }
                    }
                };
                scales.yOrders = {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        color: '#d97706',
                        stepSize: 1,
                        precision: 0
                    }
                };
            }

            trendChartInstance = new Chart(ctx, {
                data: {
                    labels: chartLabels,
                    datasets: datasets
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
                            display: mode === 'both',
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(items) {
                                    if (!items || !items.length) return '';
                                    const idx = items[0].dataIndex;
                                    return chartFullDates[idx] || items[0].label;
                                },
                                label: function(ctx) {
                                    if (ctx.dataset.label.includes('Revenue')) {
                                        return ' 💰 Revenue: ₹' + Number(ctx.parsed.y).toLocaleString('en-IN', {
                                            minimumFractionDigits: 2
                                        });
                                    }
                                    return ' 📦 Bookings: ' + ctx.parsed.y + (ctx.parsed.y === 1 ? ' Order' :
                                        ' Orders');
                                }
                            }
                        }
                    },
                    scales: scales
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderTrendChart('both');
        });
    </script>
@endsection
