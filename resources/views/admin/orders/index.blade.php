@extends('layouts.app')

@section('title', 'Customer Orders - Admin')

@section('content')
<div class="space-y-6 pb-16">

    <!-- Header & Stats Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading flex items-center gap-2">
                <span>📦 Customer Orders</span>
                <span class="text-xs bg-rose-100 text-rose-700 px-2.5 py-0.5 rounded-full font-bold">
                    {{ $orders->total() }} Total
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Manage online cracker bookings, verify UPI payments, and send WhatsApp invoices or packing checklists.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('order.create') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                <i class="fa-solid fa-plus text-rose-600"></i>
                <span>New Booking</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Status Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-bold">
            <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ !request('status') ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>All Orders</span>
                <span class="text-[10px] {{ !request('status') ? 'bg-slate-700 text-slate-200' : 'bg-slate-200 text-slate-700' }} px-1.5 py-0.5 rounded-full">{{ $totalOrdersCount ?? $orders->total() }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                <span>⏳ Pending</span>
                <span class="text-[10px] {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-200/80 text-amber-900' }} px-1.5 py-0.5 rounded-full">{{ $statusCounts['pending'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ request('status') === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                <span>✅ Paid</span>
                <span class="text-[10px] {{ request('status') === 'paid' ? 'bg-emerald-700 text-white' : 'bg-emerald-200/80 text-emerald-900' }} px-1.5 py-0.5 rounded-full">{{ $statusCounts['paid'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ request('status') === 'confirmed' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                <span>📦 Confirmed</span>
                <span class="text-[10px] {{ request('status') === 'confirmed' ? 'bg-blue-700 text-white' : 'bg-blue-200/80 text-blue-900' }} px-1.5 py-0.5 rounded-full">{{ $statusCounts['confirmed'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'packed']) }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ request('status') === 'packed' ? 'bg-purple-600 text-white shadow-sm' : 'bg-purple-50 text-purple-800 hover:bg-purple-100' }}">
                <span>🗃️ Packed</span>
                <span class="text-[10px] {{ request('status') === 'packed' ? 'bg-purple-700 text-white' : 'bg-purple-200/80 text-purple-900' }} px-1.5 py-0.5 rounded-full">{{ $statusCounts['packed'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'dispatched']) }}" class="px-3 py-1.5 rounded-xl transition-colors shrink-0 flex items-center gap-1.5 {{ request('status') === 'dispatched' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100' }}">
                <span>🚚 Dispatched</span>
                <span class="text-[10px] {{ request('status') === 'dispatched' ? 'bg-indigo-700 text-white' : 'bg-indigo-200/80 text-indigo-900' }} px-1.5 py-0.5 rounded-full">{{ $statusCounts['dispatched'] ?? 0 }}</span>
            </a>
        </div>

        <!-- Search Input -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="relative sm:w-72" onsubmit="const a = this.search.value.replace(/[^a-zA-Z0-9\u0B80-\u0BFF]/g, ''); if (this.search.value.trim().length > 0 && a.length < 2) { return false; }">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search order #, customer, phone..."
                minlength="2"
                maxlength="60"
                oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.\-\/\u0B80-\u0BFF]/g, '').replace(/\-{2,}/g, '-').replace(/^[\-\.\/\s]+/, '')"
                class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800"
            >
            @if (request('search'))
                <a href="{{ route('admin.orders.index', array_filter(['status' => request('status')])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1" title="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Customer</th>
                        <th class="py-3 px-4 text-left">Phone</th>
                        <th class="py-3 px-4 text-left">City / Destination</th>
                        <th class="py-3 px-4 text-center">Items</th>
                        <th class="py-3 px-4 text-right">Amount</th>
                        <th class="py-3 px-4 text-center">Order Status</th>
                        <th class="py-3 px-4 text-center">Payment</th>
                        <th class="py-3 px-4 text-left">Date</th>
                        <th class="py-3 px-4 text-center">Quick WhatsApp &amp; Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-rose-700 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $order->name }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="tel:{{ $order->phone1 }}" class="text-slate-700 hover:text-rose-600 font-medium">
                                    {{ $order->phone1 }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">
                                {{ $order->city }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-800 font-bold">
                                    {{ $order->items_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-slate-900">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($order->status === 'dispatched')
                                    <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase" title="{{ $order->parcel_service_name ? $order->parcel_service_name . ($order->lr_number ? ' (LR: ' . $order->lr_number . ')' : '') : 'Dispatched' }}">
                                        <i class="fa-solid fa-truck-fast"></i> Dispatched
                                    </span>
                                @elseif ($order->status === 'packed')
                                    <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-boxes-packing"></i> Packed
                                    </span>
                                @elseif ($order->status === 'confirmed')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-circle-check"></i> Confirmed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-clock"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($order->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-indian-rupee-sign"></i> Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-700 text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-hourglass-half"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $order->created_at->format('d M, h:i A') }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold"
                                        title="View Order Details & WhatsApp Options"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a
                                        href="{{ route('admin.orders.invoice', $order) }}"
                                        class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold"
                                        title="Download PDF"
                                    >
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-10 text-center text-slate-400">
                                <div class="text-3xl mb-2">📦</div>
                                <div class="font-bold text-sm">No orders found.</div>
                                <div class="text-xs text-slate-400 mt-1">Orders placed via the customer form will appear here.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
