@extends('layouts.app')

@section('title', '100% Application Audit Log | Guru Crackers')

@section('content')
    <div class="mx-auto space-y-6">

        <!-- Top Header Banner -->
        <div
            class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-indigo-900/40">
            <!-- Decorative Background glow -->
            <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div class="absolute left-1/3 -top-20 w-60 h-60 bg-rose-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            100% Comprehensive Audit Trail
                        </span>
                        <span class="text-xs text-slate-400 font-medium">Enterprise Security & Compliance</span>
                    </div>
                    <h1
                        class="text-2xl sm:text-3xl lg:text-4xl font-black font-heading tracking-tight text-white flex items-center gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i>
                        <span>Application Audit Log</span>
                    </h1>
                    <p class="text-sm text-slate-300 mt-1.5 max-w-2xl font-normal leading-relaxed">
                        Complete transparent activity log tracking every action: Orders, Product updates, Category
                        changes, Shop settings, Dispatches, Admin logins, and WhatsApp notifications with full
                        Before/After diffs.
                    </p>
                </div>

                <!-- Action Buttons: Export & Refresh -->
                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <a href="{{ route('admin.audit_logs.export_pdf', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition-all active:scale-95"
                        title="Download Filtered Audit Report as PDF Document">
                        <i class="fa-solid fa-file-pdf text-sm"></i>
                        <span>Export PDF</span>
                    </a>
                    <a href="{{ route('admin.audit_logs.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all active:scale-95"
                        title="Download Filtered Logs as Excel CSV">
                        <i class="fa-solid fa-file-csv text-sm"></i>
                        <span>Export CSV</span>
                    </a>
                    <a href="{{ route('admin.audit_logs.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-all active:scale-95"
                        title="Reset Filters">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                        <span>Refresh</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Total Logged Events -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Audit Logs</p>
                        <h3 class="text-2xl sm:text-3xl font-black font-heading text-slate-900 mt-1">
                            {{ number_format($totalLogsCount) }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium">All recorded operations</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>

            <!-- Metric 2: Today's Actions -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Today's Activity</p>
                        <h3 class="text-2xl sm:text-3xl font-black font-heading text-emerald-600 mt-1">
                            {{ number_format($todayLogsCount) }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Events logged today</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
            </div>

            <!-- Metric 3: High Impact Actions -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">High Impact Changes</p>
                        <h3 class="text-2xl sm:text-3xl font-black font-heading text-amber-600 mt-1">
                            {{ number_format($highImpactCount) }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Dispatches, deletes, status</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>

            <!-- Metric 4: Active Operators -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Operators & Roles</p>
                        <h3 class="text-2xl sm:text-3xl font-black font-heading text-purple-600 mt-1">
                            {{ number_format($activeOperatorsCount) }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Admins & system services</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls Bar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs space-y-4">
            <form method="GET" action="{{ route('admin.audit_logs.index') }}" id="auditFilterForm"
                onsubmit="event.preventDefault(); submitCleanForm(this);">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">

                    <!-- Search Input -->
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Search Anything</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </span>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Search summary, order #, user, or IP..."
                                class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                        </div>
                    </div>

                    <!-- Module Filter -->
                    <div class="lg:col-span-3">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Module</label>
                        <select name="module" onchange="submitCleanForm(this.form)"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium bg-white">
                            <option value="all">All Modules ({{ $totalLogsCount }})</option>
                            @foreach ($availableModules as $mod)
                                @php
                                    $modKey = $mod->module;
                                    $modCount = $mod->count;
                                    $modName = match ($modKey) {
                                        'orders' => '📦 Orders',
                                        'products' => '🎆 Products',
                                        'categories' => '🏷️ Categories',
                                        'shop' => '🏪 Shop Settings',
                                        'banners' => '🖼️ Banners',
                                        'whatsapp' => '💬 WhatsApp Gateway',
                                        'auth' => '🔐 Security & Logins',
                                        'reports' => '📊 Reports & Exports',
                                        'bulk_messaging' => '📢 Bulk Messaging',
                                        'audit_logs' => '📋 Audit Logs',
                                        default => '🔹 ' . ucfirst($modKey),
                                    };
                                @endphp
                                <option value="{{ $modKey }}" {{ $module === $modKey ? 'selected' : '' }}>
                                    {{ $modName }} ({{ $modCount }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Event Type Filter (Only active events with records) -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Event Type</label>
                        <select name="event" onchange="submitCleanForm(this.form)"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium bg-white">
                            <option value="all">All Events ({{ $totalLogsCount }})</option>
                            @foreach ($availableEvents as $ev)
                                @php
                                    $evKey = $ev->event;
                                    $evCount = $ev->count;
                                    $evName = match ($evKey) {
                                        'created' => '✨ Created',
                                        'updated' => '✏️ Updated',
                                        'status_change' => '🔄 Status Change',
                                        'dispatched' => '🚚 Dispatched',
                                        'deleted' => '🗑️ Deleted',
                                        'restored' => '♻️ Restored',
                                        'login' => '🔑 Login',
                                        'logout' => '🚪 Logout',
                                        'password_change' => '🔒 Password Change',
                                        'bulk_upload' => '📑 Bulk Upload',
                                        'export' => '📥 Data Export',
                                        'whatsapp_sent' => '🟢 WhatsApp Sent',
                                        default => '🔸 ' . ucfirst(str_replace('_', ' ', $evKey)),
                                    };
                                @endphp
                                <option value="{{ $evKey }}" {{ $event === $evKey ? 'selected' : '' }}>
                                    {{ $evName }} ({{ $evCount }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Preset Filter -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Timeframe</label>
                        <select name="date_preset" id="datePresetSelect" onchange="handleDatePresetChange(this.value)"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium bg-white">
                            <option value="all_time" {{ $datePreset === 'all_time' ? 'selected' : '' }}>All Time
                            </option>
                            <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>Yesterday
                            </option>
                            <option value="last_7_days" {{ $datePreset === 'last_7_days' ? 'selected' : '' }}>Last 7
                                Days</option>
                            <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>This Month
                            </option>
                            <option value="last_30_days" {{ $datePreset === 'last_30_days' ? 'selected' : '' }}>Last 30
                                Days</option>
                            <option value="custom" {{ $datePreset === 'custom' ? 'selected' : '' }}>Custom Dates...
                            </option>
                        </select>
                    </div>

                    <!-- Apply / Reset Buttons -->
                    <div class="flex items-center gap-1.5 lg:col-span-1">
                        <button type="button" onclick="submitCleanForm(document.getElementById('auditFilterForm'))"
                            class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition-colors flex items-center justify-center cursor-pointer"
                            title="Filter">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.audit_logs.index') }}"
                            class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors flex items-center justify-center"
                            title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                </div>

                <!-- Custom Date Range Picker Container (Collapsible) -->
                <div id="customDateRangeBox"
                    class="{{ $datePreset === 'custom' ? '' : 'hidden' }} pt-3 mt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                    </div>
                </div>
            </form>
        </div>

        <!-- Audit Trail Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div
                class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <span
                        class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 font-heading">Live System Audit Trail</h2>
                        <p class="text-xs text-slate-500">Showing {{ $logs->firstItem() ?? 0 }} -
                            {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} log entries</p>
                    </div>
                </div>

                @php
                    $hasActiveFilters =
                        !empty($search) ||
                        ($module !== 'all' && !empty($module)) ||
                        ($event !== 'all' && !empty($event)) ||
                        ($datePreset !== 'all_time' && !empty($datePreset) && $datePreset !== 'custom') ||
                        (!empty($startDate) || !empty($endDate));
                @endphp
                @if ($hasActiveFilters)
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs text-slate-400 font-medium">Active filters:</span>
                        @if (!empty($search))
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                "{{ $search }}"
                            </span>
                        @endif
                        @if ($module !== 'all' && !empty($module))
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                Module: {{ ucfirst($module) }}
                            </span>
                        @endif
                        @if ($event !== 'all' && !empty($event))
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                Event: {{ ucfirst($event) }}
                            </span>
                        @endif
                        @if ($datePreset !== 'all_time' && !empty($datePreset))
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                Time: {{ ucfirst(str_replace('_', ' ', $datePreset)) }}
                            </span>
                        @endif
                        <a href="{{ route('admin.audit_logs.index') }}"
                            class="text-xs font-bold text-rose-600 hover:text-rose-700 ml-1 underline">
                            Clear all
                        </a>
                    </div>
                @endif
            </div>

            <!-- Table Responsive Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-slate-100/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4">Operator & IP</th>
                            <th class="py-3 px-4">Module</th>
                            <th class="py-3 px-4">Event</th>
                            <th class="py-3 px-4">Record & Target</th>
                            <th class="py-3 px-4">Summary Activity</th>
                            <th class="py-3 px-4 text-center">Changes Diff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($logs as $log)
                            @php
                                $eventBadge = $log->event_badge;
                                $moduleBadge = $log->module_badge;
                                $hasDiff = !empty($log->old_values) || !empty($log->new_values);
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors">

                                <!-- 1. Timestamp -->
                                <td class="py-3.5 px-4 whitespace-nowrap align-top">
                                    <div class="font-bold text-slate-900 text-xs">
                                        {{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                        <i class="fa-regular fa-clock text-[9px]"></i>
                                        <span>{{ $log->created_at->format('h:i:s A') }}</span>
                                    </div>
                                    <div class="text-[10px] text-indigo-500 mt-0.5 font-medium">
                                        {{ $log->created_at->diffForHumans() }}</div>
                                </td>

                                <!-- 2. Operator & IP -->
                                <td class="py-3.5 px-4 align-top">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center text-[10px] font-black shrink-0">
                                            @if ($log->user_role === 'admin')
                                                <i class="fa-solid fa-user-shield text-indigo-600"></i>
                                            @elseif($log->user_role === 'system')
                                                <i class="fa-solid fa-robot text-teal-600"></i>
                                            @else
                                                <i class="fa-solid fa-user text-slate-400"></i>
                                            @endif
                                        </span>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">
                                                {{ $log->user_name ?: 'System / Guest' }}
                                            </div>
                                            <div
                                                class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5 font-mono">
                                                <span
                                                    class="bg-slate-100 px-1 py-0.2 rounded">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                                                <span
                                                    class="uppercase font-bold text-[9px] text-slate-500">{{ $log->user_role }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Module Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap align-top">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $moduleBadge['bg'] }}">
                                        <i class="fa-solid {{ $moduleBadge['icon'] }} text-[10px]"></i>
                                        <span>{{ $moduleBadge['label'] }}</span>
                                    </span>
                                </td>

                                <!-- 4. Event Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap align-top">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $eventBadge['bg'] }}">
                                        <i class="fa-solid {{ $eventBadge['icon'] }} text-[10px]"></i>
                                        <span>{{ $eventBadge['label'] }}</span>
                                    </span>
                                </td>

                                <!-- 5. Record & Target Link -->
                                <td class="py-3.5 px-4 align-top">
                                    @if ($log->record_name)
                                        <div class="font-bold text-slate-900 leading-snug">
                                            @if ($log->module === 'orders' && $log->auditable_id)
                                                <a href="{{ route('admin.orders.show', $log->auditable_id) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                                    <span>{{ $log->record_name }}</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                </a>
                                            @elseif($log->module === 'products' && $log->auditable_id)
                                                <a href="{{ route('admin.products.edit', $log->auditable_id) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                                    <span>{{ $log->record_name }}</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                </a>
                                            @elseif($log->module === 'categories' && $log->auditable_id)
                                                <a href="{{ route('admin.categories.edit', $log->auditable_id) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                                    <span>{{ $log->record_name }}</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                </a>
                                            @elseif($log->module === 'shop')
                                                <a href="{{ route('admin.shop.edit') }}"
                                                    class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                                    <span>Shop Settings</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                </a>
                                            @else
                                                <span>{{ $log->record_name }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono text-[11px]">—</span>
                                    @endif

                                    @if ($log->auditable_type)
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 6. Summary Activity Description -->
                                <td class="py-3.5 px-4 align-top">
                                    <div class="text-slate-800 font-medium leading-relaxed max-w-md">
                                        {{ $log->summary }}
                                    </div>
                                    @if ($log->url)
                                        <div class="text-[9px] text-slate-400 font-mono truncate max-w-xs mt-1"
                                            title="{{ $log->url }}">
                                            {{ parse_url($log->url, PHP_URL_PATH) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 7. Changes Diff Modal Trigger -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-center align-top">
                                    @if ($hasDiff)
                                        <button type="button" onclick="openAuditDiffModal({{ $log->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 font-bold text-xs transition-all shadow-2xs group cursor-pointer"
                                            title="View Before & After JSON Changes">
                                            <i
                                                class="fa-solid fa-code-compare text-xs text-indigo-500 group-hover:text-white transition-colors"></i>
                                            <span>View Diff</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">No field diff</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="max-w-md mx-auto space-y-3">
                                        <div
                                            class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">No audit logs found</h3>
                                        <p class="text-xs text-slate-500">
                                            No actions matching your filter criteria were recorded yet. Every action
                                            taken across products, orders, categories, or settings is tracked
                                            automatically.
                                        </p>
                                        @if ($search || $module !== 'all' || $event !== 'all' || $datePreset !== 'all_time')
                                            <a href="{{ route('admin.audit_logs.index') }}"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs">
                                                Reset Filters
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($logs->hasPages())
                <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==========================================
             CHANGES DIFF INSPECTOR MODAL
             ========================================== -->
    <div id="auditDiffModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-fadeIn">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="p-4 sm:p-6 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <span
                        class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-code-compare"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold font-heading text-white" id="modalDiffTitle">Audit Changes Diff
                        </h3>
                        <p class="text-xs text-slate-400" id="modalDiffSubtitle">Log ID & Timestamp</p>
                    </div>
                </div>
                <button type="button" onclick="closeAuditDiffModal()"
                    class="w-9 h-9 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-sm transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body Content -->
            <div class="p-5 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto" id="modalDiffBody">
                <div class="flex items-center justify-center py-10" id="modalLoadingSpinner">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-600"></i>
                </div>

                <div id="modalDiffContent" class="hidden space-y-4">
                    <!-- Summary Card -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Activity Summary
                        </div>
                        <div class="text-sm font-semibold text-slate-900" id="modalDiffSummaryText"></div>
                        <div class="text-xs text-slate-500 mt-2 flex flex-wrap gap-3 font-mono" id="modalDiffMeta"></div>
                    </div>

                    <!-- Side-by-side or Stacked Diff -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Before (Old Values) -->
                        <div class="bg-rose-50/60 rounded-2xl p-4 border border-rose-200">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-xs font-bold text-rose-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Before (Previous Values)
                                </span>
                                <span
                                    class="text-[10px] bg-rose-200/70 text-rose-900 px-2 py-0.5 rounded-full font-bold">Old</span>
                            </div>
                            <pre class="bg-white/80 p-3 rounded-xl border border-rose-100 text-[11px] font-mono text-slate-800 overflow-x-auto max-h-64"
                                id="modalOldValuesJson"></pre>
                        </div>

                        <!-- After (New Values) -->
                        <div class="bg-emerald-50/60 rounded-2xl p-4 border border-emerald-200">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-xs font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> After (Updated Values)
                                </span>
                                <span
                                    class="text-[10px] bg-emerald-200/70 text-emerald-900 px-2 py-0.5 rounded-full font-bold">New</span>
                            </div>
                            <pre class="bg-white/80 p-3 rounded-xl border border-emerald-100 text-[11px] font-mono text-slate-800 overflow-x-auto max-h-64"
                                id="modalNewValuesJson"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-mono" id="modalDiffIp">—</span>
                <button type="button" onclick="closeAuditDiffModal()"
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors cursor-pointer">
                    Close Diff Inspector
                </button>
            </div>
        </div>
    </div>

    <script>
        function submitCleanForm(form) {
            if (!form) return;
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                const val = (input.value || '').trim();
                if (val === '' || val === 'all' || val === 'all_time') {
                    input.disabled = true;
                }
            });
            form.submit();
        }

        function handleDatePresetChange(val) {
            const box = document.getElementById('customDateRangeBox');
            if (val === 'custom') {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
                submitCleanForm(document.getElementById('auditFilterForm'));
            }
        }

        function openAuditDiffModal(logId) {
            const modal = document.getElementById('auditDiffModal');
            const spinner = document.getElementById('modalLoadingSpinner');
            const content = document.getElementById('modalDiffContent');

            modal.classList.remove('hidden');
            spinner.classList.remove('hidden');
            content.classList.add('hidden');

            fetch(`/admin/audit-logs/${logId}`)
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    content.classList.remove('hidden');

                    const log = data.log;
                    document.getElementById('modalDiffTitle').innerText =
                        `${log.module.toUpperCase()} — ${log.event.toUpperCase()}`;
                    document.getElementById('modalDiffSubtitle').innerText =
                        `Log ID #${log.id} • ${data.created_at_human}`;
                    document.getElementById('modalDiffSummaryText').innerText = log.summary;

                    document.getElementById('modalDiffMeta').innerHTML = `
                    <span><strong>User:</strong> ${log.user_name || 'System / Guest'}</span>
                    <span><strong>IP:</strong> ${log.ip_address || '127.0.0.1'}</span>
                    <span><strong>Target:</strong> ${log.record_name || 'N/A'}</span>
                `;

                    document.getElementById('modalDiffIp').innerText =
                        `IP: ${log.ip_address || '127.0.0.1'} | Agent: ${(log.user_agent || '').substring(0, 50)}...`;

                    document.getElementById('modalOldValuesJson').innerText = data.old_values ?
                        JSON.stringify(data.old_values, null, 2) :
                        '// No prior values recorded for this action';

                    document.getElementById('modalNewValuesJson').innerText = data.new_values ?
                        JSON.stringify(data.new_values, null, 2) :
                        '// No new attributes';
                })
                .catch(err => {
                    spinner.classList.add('hidden');
                    alert('Failed to load audit diff: ' + err.message);
                    closeAuditDiffModal();
                });
        }

        function closeAuditDiffModal() {
            document.getElementById('auditDiffModal').classList.add('hidden');
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuditDiffModal();
            }
        });

        // Close on outside click
        document.getElementById('auditDiffModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAuditDiffModal();
            }
        });
    </script>
@endsection
