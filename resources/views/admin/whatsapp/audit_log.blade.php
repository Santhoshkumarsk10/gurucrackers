@extends('layouts.app')

@section('title', 'WhatsApp 100% Audit Tracking Log - Admin')

@section('content')
    <div class="mx-auto space-y-5 pb-16">

        <!-- Header & Action Bar -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-600/20 shrink-0">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-xl font-black text-slate-900 font-heading">
                            100% WhatsApp Audit Tracking Log
                        </h1>
                        <span
                            class="text-[11px] bg-indigo-100 text-indigo-800 font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            Tamper-Proof Audit Trail
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Complete delivery history of every single invoice, packing alert, LR dispatch slip, and live
                        customer chat message.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <!-- Back to Live Chat -->
                <a href="{{ route('admin.whatsapp.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition-all border border-emerald-200 shadow-xs">
                    <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                    <span>Open Live Chat</span>
                </a>

                <!-- Export CSV -->
                <a href="{{ route('admin.whatsapp.audit_log.export', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition-all">
                    <i class="fa-solid fa-file-csv text-emerald-400"></i>
                    <span>Export Audit CSV</span>
                </a>
            </div>
        </div>

        <!-- KPI Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- 1. Total Messages -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
                    <span>Total Messages</span>
                    <i class="fa-solid fa-layer-group text-slate-400"></i>
                </div>
                <div class="text-2xl font-black text-slate-900 font-heading">
                    {{ number_format($kpis['total_filtered']) }}
                </div>
                <div class="text-[11px] text-slate-400">
                    All logged interactions
                </div>
            </div>

            <!-- 2. Outbound Sent -->
            <div
                class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs space-y-1 bg-gradient-to-br from-white to-emerald-50/40">
                <div class="flex items-center justify-between text-emerald-700 text-xs font-bold">
                    <span>Outbound Sent</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-emerald-500"></i>
                </div>
                <div class="text-2xl font-black text-emerald-800 font-heading">
                    {{ number_format($kpis['total_sent']) }}
                </div>
                <div class="text-[11px] text-emerald-600 font-medium">
                    Invoices, Alerts & Admin Replies
                </div>
            </div>

            <!-- 3. Inbound Customer Replies -->
            <div
                class="bg-white p-4 rounded-2xl border border-sky-200/80 shadow-xs space-y-1 bg-gradient-to-br from-white to-sky-50/40">
                <div class="flex items-center justify-between text-sky-700 text-xs font-bold">
                    <span>Customer Inbound</span>
                    <i class="fa-solid fa-arrow-down-left-and-up-right-to-center text-sky-500"></i>
                </div>
                <div class="text-2xl font-black text-sky-800 font-heading">
                    {{ number_format($kpis['total_received']) }}
                </div>
                <div class="text-[11px] text-sky-600 font-medium">
                    Customer replies & queries
                </div>
            </div>

            <!-- 4. Delivery Success Rate -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold">
                    <span>Delivery Health</span>
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                </div>
                <div class="text-2xl font-black text-slate-900 font-heading flex items-center gap-1.5">
                    <span>{{ $kpis['success_rate'] }}%</span>
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.2 rounded-md">Healthy</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    {{ $kpis['total_failed'] }} delivery failures
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('admin.whatsapp.audit_log') }}" id="whatsappAuditFilterForm"
                onsubmit="return cleanWhatsAppFilterSubmit(this);" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Search Keywords</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Customer name, phone, order #, text..."
                                class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                        </div>
                    </div>

                    <!-- Direction -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Direction</label>
                        <select name="direction"
                            class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                            <option value="">All (Sent & Received)</option>
                            <option value="sent" {{ request('direction') === 'sent' ? 'selected' : '' }}>📤 Outbound
                                (Sent)</option>
                            <option value="received" {{ request('direction') === 'received' ? 'selected' : '' }}>📥 Inbound
                                (Received)</option>
                        </select>
                    </div>

                    <!-- Trigger Source (Only sources with recorded entries) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Trigger Source</label>
                        <select name="source"
                            class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                            <option value="all">All Sources ({{ $kpis['total_filtered'] ?? $logs->total() }})</option>
                            @foreach ($availableSources as $s)
                                @php
                                    $sKey = $s->trigger_source;
                                    $sName = match ($sKey) {
                                        'order_invoice' => '📄 Order Invoice',
                                        'warehouse_alert' => '📦 Packing Alert',
                                        'lr_dispatch' => '🚚 LR Dispatch',
                                        'payment_confirmation' => '💳 Payment Confirmation',
                                        'live_chat' => '💬 Live Chat Reply',
                                        'customer_reply' => '📥 Customer Reply',
                                        'test_message' => '🧪 Test Message',
                                        default => '🔹 ' . ucfirst(str_replace('_', ' ', $sKey)),
                                    };
                                @endphp
                                <option value="{{ $sKey }}" {{ request('source') === $sKey ? 'selected' : '' }}>
                                    {{ $sName }} ({{ $s->count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status (Only statuses with recorded entries) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                            <option value="all">All Statuses</option>
                            @foreach ($availableStatuses as $st)
                                @php
                                    $stKey = $st->status;
                                    $stName = match ($stKey) {
                                        'sent' => 'Sent / Delivered',
                                        'received' => 'Received',
                                        'failed' => 'Failed Delivery',
                                        default => ucfirst($stKey),
                                    };
                                @endphp
                                <option value="{{ $stKey }}" {{ request('status') === $stKey ? 'selected' : '' }}>
                                    {{ $stName }} ({{ $st->count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Date Range & Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500">Date Range:</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                        <span class="text-slate-400 text-xs">to</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                            <i class="fa-solid fa-filter"></i>
                            <span>Apply Filters</span>
                        </button>

                        @php
                            $hasActiveWhatsAppFilters =
                                filled(request('search')) ||
                                filled(request('direction')) ||
                                (filled(request('source')) && request('source') !== 'all') ||
                                (filled(request('status')) && request('status') !== 'all') ||
                                filled(request('date_from')) ||
                                filled(request('date_to'));
                        @endphp
                        @if ($hasActiveWhatsAppFilters)
                            <a href="{{ route('admin.whatsapp.audit_log') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- 100% Audit Log Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Date & Time</th>
                            <th class="py-3 px-3">Direction</th>
                            <th class="py-3 px-4">Customer Details</th>
                            <th class="py-3 px-3">Order Ref</th>
                            <th class="py-3 px-3">Trigger Source</th>
                            <th class="py-3 px-4">Message Content Preview</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Timestamp -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-[11px]">
                                        {{ $log->sent_at ? $log->sent_at->format('d M Y') : $log->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $log->sent_at ? $log->sent_at->format('h:i:s A') : $log->created_at->format('h:i:s A') }}
                                    </div>
                                </td>

                                <!-- Direction -->
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @if ($log->from_me)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-arrow-up text-[9px]"></i>
                                            <span>Sent</span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200">
                                            <i class="fa-solid fa-arrow-down text-[9px]"></i>
                                            <span>Received</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Customer Details -->
                                <td class="py-3.5 px-4">
                                    <div class="font-black text-slate-900 text-xs">
                                        {{ $log->order?->name ?: ($log->sender_name ?: 'Customer') }}
                                    </div>
                                    <div class="font-mono text-[11px] text-slate-500 mt-0.5">
                                        +91 {{ $log->phone }}
                                    </div>
                                    @if ($log->order?->city)
                                        <div class="text-[10px] text-slate-400">
                                            {{ $log->order->city }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Order Reference -->
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @if ($log->order)
                                        <a href="{{ route('admin.orders.show', $log->order->id) }}"
                                            class="inline-flex items-center gap-1 font-bold text-indigo-600 hover:text-indigo-800 text-[11px]">
                                            <span>#{{ $log->order->order_number }}</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                        </a>
                                        <div class="text-[10px] text-slate-400">
                                            ₹{{ number_format($log->order->total_amount, 2) }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px]">—</span>
                                    @endif
                                </td>

                                <!-- Trigger Source -->
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @php
                                        $src = $log->trigger_source ?: ($log->from_me ? 'live_chat' : 'customer_reply');
                                        $labels = [
                                            'order_invoice' => [
                                                'Invoice Dispatch',
                                                'bg-purple-50 text-purple-700 border-purple-200',
                                            ],
                                            'warehouse_alert' => [
                                                'Packing Alert',
                                                'bg-amber-50 text-amber-700 border-amber-200',
                                            ],
                                            'lr_dispatch' => [
                                                'LR Dispatch Slip',
                                                'bg-blue-50 text-blue-700 border-blue-200',
                                            ],
                                            'payment_confirmation' => [
                                                'Payment Confirmed',
                                                'bg-teal-50 text-teal-700 border-teal-200',
                                            ],
                                            'live_chat' => [
                                                'Live Chat Reply',
                                                'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ],
                                            'customer_reply' => [
                                                'Customer Message',
                                                'bg-sky-50 text-sky-700 border-sky-200',
                                            ],
                                            'test_message' => [
                                                'Test Send',
                                                'bg-slate-100 text-slate-700 border-slate-200',
                                            ],
                                        ];
                                        $info = $labels[$src] ?? [
                                            ucfirst(str_replace('_', ' ', $src)),
                                            'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $info[1] }}">
                                        {{ $info[0] }}
                                    </span>
                                </td>

                                <!-- Message Preview -->
                                <td class="py-3.5 px-4 max-w-xs">
                                    @if ($log->media_filename)
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold text-slate-700 mb-1">
                                            <i class="fa-solid fa-paperclip text-slate-500"></i>
                                            <span class="truncate max-w-[150px]">{{ $log->media_filename }}</span>
                                        </div>
                                    @endif
                                    <p class="text-slate-700 text-[11px] line-clamp-2 leading-relaxed">
                                        {{ $log->message_text }}
                                    </p>
                                </td>

                                <!-- Delivery Status -->
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @if ($log->status === 'failed')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200"
                                            title="{{ $log->error_message }}">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>Failed</span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check-double text-sky-500"></i>
                                            <span>Delivered</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.whatsapp.index') }}#{{ $log->phone }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 text-xs font-bold transition-all border border-slate-200"
                                        title="Open chat with +91 {{ $log->phone }}">
                                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                        <span>Chat</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 space-y-2">
                                    <i class="fa-solid fa-clipboard-list text-3xl text-slate-300"></i>
                                    <p class="text-xs font-bold text-slate-600">No WhatsApp audit log records found.</p>
                                    <p class="text-[11px]">Any automated invoice, LR slip, or live chat message will be
                                        tracked here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>

    <script>
        function cleanWhatsAppFilterSubmit(form) {
            if (!form) return;
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                const val = (input.value || '').trim();
                if (val === '' || val === 'all') {
                    input.disabled = true;
                }
            });
            return true;
        }
    </script>
@endsection
