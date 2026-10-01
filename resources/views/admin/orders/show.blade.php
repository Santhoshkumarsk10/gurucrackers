@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - Admin')

@section('content')
<div class="mx-auto space-y-6 pb-16">

    <!-- Back Navigation & Order Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-sm transition-colors" title="Back to Orders">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                        {{ $order->order_number }}
                    </h1>
                    {{-- Order Status Badge --}}
                    @if ($order->status === 'dispatched')
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-truck-fast"></i> Dispatched
                        </span>
                    @elseif ($order->status === 'packed')
                        <span class="bg-purple-100 text-purple-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-boxes-packing"></i> Packed
                        </span>
                    @elseif ($order->status === 'confirmed')
                        <span class="bg-blue-100 text-blue-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Confirmed
                        </span>
                    @else
                        <span class="bg-amber-100 text-amber-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-clock"></i> Pending
                        </span>
                    @endif
                    {{-- Payment Status Badge --}}
                    @if ($order->payment_status === 'paid')
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-indian-rupee-sign"></i> Paid
                        </span>
                    @else
                        <span class="bg-rose-100 text-rose-800 text-xs font-extrabold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                            <i class="fa-solid fa-hourglass-half"></i> Payment Pending
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Placed on {{ $order->created_at->format('d M Y, h:i A') }} &middot; {{ $order->items->count() }} Products ({{ $order->total_quantity }} Units)
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.orders.packing_checklist', $order) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition-colors">
                <i class="fa-solid fa-clipboard-check text-amber-600"></i>
                <span>Packing Checklist PDF</span>
            </a>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                <i class="fa-solid fa-file-pdf text-rose-600"></i>
                <span>PDF Invoice</span>
            </a>
            <a href="{{ route('order.public_invoice', $order->order_number) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-500"></i>
                <span>Customer View</span>
            </a>
        </div>
    </div>

    {{-- ===================== 1. DIFFERENTIATED WHATSAPP DISPATCH BAR ===================== --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-rose-950 text-white rounded-2xl p-5 sm:p-6 shadow-md space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
            <div>
                <div class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                    <span>Automated WhatsApp Dispatch Hub</span>
                </div>
                <p class="text-xs text-slate-300 mt-0.5">
                    One-click WhatsApp actions for invoice, packing checklist, official PDF delivery & LR receipt sharing!
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if (!empty($gatewayStatus['connected']))
                    <span class="text-[11px] text-emerald-300 bg-emerald-500/20 border border-emerald-500/30 px-2.5 py-1 rounded-lg flex items-center gap-1 font-bold">
                        <i class="fa-solid fa-circle text-[8px] animate-pulse"></i>
                        <span>Bot Connected: {{ $gatewayStatus['user'] }}</span>
                    </span>
                @else
                    <a href="{{ route('admin.whatsapp.index') }}" class="text-[11px] text-amber-300 bg-amber-500/20 border border-amber-500/30 px-2.5 py-1 rounded-lg flex items-center gap-1 font-bold hover:bg-amber-500/30">
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Scan WhatsApp QR</span>
                    </a>
                @endif
                <span class="text-[11px] text-white/60 bg-white/10 px-2.5 py-1 rounded-lg">
                    Customer: {{ $order->phone1 }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Action 1: Customer Text Invoice -->
            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-black text-amber-300 font-heading uppercase flex items-center gap-1">
                        <i class="fa-solid fa-file-invoice"></i> 1. Text Invoice
                    </span>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        Itemized table, total ₹{{ number_format($order->total_amount, 2) }}, and UPI payment instructions.
                    </p>
                </div>
                <div class="flex items-center gap-1.5 pt-1">
                    <form action="{{ route('admin.orders.send_text_invoice', $order) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[11px] py-2 px-2.5 rounded-lg shadow-sm font-heading transition-all" title="Send directly via WhatsApp Bot">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Send via Bot</span>
                        </button>
                    </form>
                    <textarea id="rawCustomerInvoice" class="hidden">{{ $customerInvoiceText }}</textarea>
                    <button type="button" onclick="copyFromElement('rawCustomerInvoice', 'invoiceCopyBtn')" id="invoiceCopyBtn" class="px-2 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs" title="Copy Text">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                    <a href="{{ $customerInvoiceWhatsAppUrl }}" target="_blank" class="px-2 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white text-xs" title="Open in WhatsApp Web">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>

            <!-- Action 2: Warehouse Packing List -->
            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-black text-amber-300 font-heading uppercase flex items-center gap-1">
                        <i class="fa-solid fa-boxes-packing"></i> 2. Packing Checklist
                    </span>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        Sends order summary & delivers warehouse packing checklist PDF via WhatsApp Bot.
                    </p>
                </div>
                <div class="flex items-center gap-1.5 pt-1">
                    <form action="{{ route('admin.orders.send_packing_list', $order) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[11px] py-2 px-2.5 rounded-lg shadow-sm font-heading transition-all" title="Send to Warehouse via WhatsApp Bot">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Send via Bot</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.orders.packing_checklist', $order) }}" class="px-2 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-amber-300 hover:text-white text-xs" title="Download Packing Checklist PDF">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    <textarea id="rawPackingList" class="hidden">{{ $adminPackingListText }}</textarea>
                    <button type="button" onclick="copyFromElement('rawPackingList', 'packingCopyBtn')" id="packingCopyBtn" class="px-2 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs" title="Copy Text">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                    <a href="{{ $adminPackingListWhatsAppUrl }}" target="_blank" class="px-2 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white text-xs" title="Open in WhatsApp Web">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>

            <!-- Action 3: Direct Official PDF Invoice to WhatsApp -->
            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-black text-rose-300 font-heading uppercase flex items-center gap-1">
                        <i class="fa-solid fa-file-pdf"></i> 3. Send PDF File
                    </span>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        Generates official invoice PDF and delivers document directly into customer's chat.
                    </p>
                </div>
                <div class="pt-1">
                    <form action="{{ route('admin.orders.send_invoice_pdf', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-[11px] py-2 px-2.5 rounded-lg shadow-sm font-heading transition-all" title="Deliver PDF File via WhatsApp Bot">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Send PDF via Bot</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Action 4: LR Dispatch & Receipt -->
            <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-black text-teal-300 font-heading uppercase flex items-center gap-1">
                        <i class="fa-solid fa-truck-ramp-box"></i> 4. Send LR Details
                    </span>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        Sends transport name, LR receipt number & slip photo directly to customer via Bot.
                    </p>
                </div>
                <div class="flex items-center gap-1.5 pt-1">
                    <form action="{{ route('admin.orders.send_dispatch_lr', $order) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 bg-teal-600 hover:bg-teal-500 text-white font-extrabold text-[11px] py-2 px-2 rounded-lg shadow-sm font-heading transition-all" title="Send LR Tracking & Slip via WhatsApp Bot">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Send LR via Bot</span>
                        </button>
                    </form>
                    <textarea id="rawDispatchLr" class="hidden">{{ $dispatchLrText }}</textarea>
                    <button type="button" onclick="copyFromElement('rawDispatchLr', 'lrCopyBtn')" id="lrCopyBtn" class="px-2 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs" title="Copy Text">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                    <a href="{{ $dispatchLrWhatsAppUrl }}" target="_blank" class="px-2 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white text-xs" title="Open in WhatsApp Web">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== 2. ORDER LIFECYCLE & STATUS PROGRESSION ===================== --}}
    @php
        $currStatus = $order->status ?: 'pending';
        // Status step mapping (based on 'status' column, not payment_status)
        $statusStepMap = [
            'pending'    => 1,
            'confirmed'  => 2,
            'packed'     => 3,
            'dispatched' => 4,
            'cancelled'  => 0,
        ];
        $currStep = $statusStepMap[$currStatus] ?? 1;
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-timeline"></i>
                </span>
                <div>
                    <h2 class="font-extrabold text-base text-slate-900 font-heading">
                        Order Lifecycle &amp; Status Progression
                    </h2>
                    <p class="text-xs text-slate-500">
                        Pending &rarr; Confirmed (when paid) &rarr; Packed &rarr; Dispatched. Payment: only Pending / Paid.
                    </p>
                </div>
            </div>

            @if ($order->payment_status === 'paid' && $currStatus !== 'dispatched')
                <form action="{{ route('admin.orders.send_invoice_pdf', $order) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-colors">
                        <i class="fa-solid fa-file-pdf text-emerald-600"></i>
                        <span>Resend Invoice PDF via Bot</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- 4-Step Visual Progression Stepper (status-based) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            <!-- Step 1: Order Placed (Pending) -->
            <div class="p-3 rounded-xl border {{ $currStep === 1 ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-400/30' : ($currStep > 1 ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60') }}">
                <div class="flex items-center gap-1.5 font-bold text-[11px] {{ $currStep === 1 ? 'text-amber-800' : ($currStep > 1 ? 'text-emerald-700' : 'text-slate-500') }}">
                    <i class="fa-solid {{ $currStep > 1 ? 'fa-circle-check text-emerald-600' : 'fa-clock' }}"></i>
                    <span>Step 1: Order Placed</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Payment Pending</div>
            </div>

            <!-- Step 2: Paid & Confirmed -->
            <div class="p-3 rounded-xl border {{ $currStep === 2 ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-400/30' : ($currStep > 2 ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60') }}">
                <div class="flex items-center gap-1.5 font-bold text-[11px] {{ $currStep === 2 ? 'text-blue-800' : ($currStep > 2 ? 'text-emerald-700' : 'text-slate-500') }}">
                    <i class="fa-solid {{ $currStep > 2 ? 'fa-circle-check text-emerald-600' : 'fa-indian-rupee-sign' }}"></i>
                    <span>Step 2: Paid &amp; Confirmed</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Payment verified, Invoice sent</div>
            </div>

            <!-- Step 3: Packed -->
            <div class="p-3 rounded-xl border {{ $currStep === 3 ? 'bg-purple-50 border-purple-300 ring-2 ring-purple-400/30' : ($currStep > 3 ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60') }}">
                <div class="flex items-center gap-1.5 font-bold text-[11px] {{ $currStep === 3 ? 'text-purple-800' : ($currStep > 3 ? 'text-emerald-700' : 'text-slate-500') }}">
                    <i class="fa-solid {{ $currStep > 3 ? 'fa-circle-check text-emerald-600' : 'fa-boxes-packing' }}"></i>
                    <span>Step 3: Packed</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Packed in warehouse</div>
            </div>

            <!-- Step 4: Dispatched -->
            <div class="p-3 rounded-xl border {{ $currStep === 4 ? 'bg-teal-50 border-teal-400 ring-2 ring-teal-400/30' : 'bg-slate-50 border-slate-200 opacity-60' }}">
                <div class="flex items-center gap-1.5 font-bold text-[11px] {{ $currStep === 4 ? 'text-teal-800' : 'text-slate-500' }}">
                    <i class="fa-solid {{ $currStep === 4 ? 'fa-circle-check text-teal-600' : 'fa-truck-fast' }}"></i>
                    <span>Step 4: Dispatched</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Handed to transport</div>
            </div>
        </div>

        {{-- Action Buttons based on current status --}}
        @if ($currStatus === 'dispatched')
            <!-- Locked: fully dispatched -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-300 text-slate-800 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <div>
                        <div class="font-bold text-slate-900">Final Status Locked: Dispatched &amp; In Transit</div>
                        <div class="text-slate-500 text-[11px] mt-0.5">
                            Dispatched via {{ $order->parcel_service_name ?? 'Transport' }} (LR: {{ $order->lr_number }}).
                        </div>
                    </div>
                </div>
                <span class="text-[11px] font-mono bg-teal-100 text-teal-800 font-bold px-2.5 py-1 rounded-lg">LOCKED</span>
            </div>

        @elseif ($currStatus === 'packed')
            <!-- Packed: direct admin to fill parcel form below -->
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </span>
                    <div>
                        <div class="font-extrabold text-purple-950 text-sm">Order Packed! Ready for Dispatch.</div>
                        <div class="text-purple-700 text-[11px] mt-0.5">
                            Enter LR &amp; Parcel details in the <strong>Parcel Service Booking</strong> form below to mark as <strong>Dispatched</strong>.
                        </div>
                    </div>
                </div>
                <a href="#parcelBookingCard" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs transition-colors shrink-0 shadow-sm font-heading">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Fill Parcel Form &darr;</span>
                </a>
            </div>

        @elseif ($currStatus === 'confirmed')
            <!-- Confirmed: show Mark as Packed button -->
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                @csrf
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <div class="font-extrabold text-blue-900 text-sm">Payment Verified &amp; Order Confirmed!</div>
                            <div class="text-blue-700 text-xs mt-0.5">Pack the items in the warehouse, then click below to mark as Packed.</div>
                        </div>
                    </div>
                    <input type="hidden" name="status" value="packed">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs transition-colors shrink-0 shadow-sm font-heading whitespace-nowrap">
                        <i class="fa-solid fa-boxes-packing"></i>
                        <span>Mark as Packed in Warehouse</span>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500">
                    <i class="fa-solid fa-shield-halved text-slate-400"></i>
                    After marking packed, enter parcel/LR details below to dispatch.
                </p>
            </form>

        @else
            {{-- Pending: show Verify & Mark as Paid button --}}
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                @csrf
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Current Status: <strong class="text-amber-700">PENDING PAYMENT</strong> — Verify UPI payment and mark as Paid:
                        </label>
                        <input type="hidden" name="payment_status" value="paid">
                        <div class="text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-3 py-2 font-medium">
                            ⚠️ Once you click below, <strong>payment_status</strong> will be set to <strong>PAID</strong> and <strong>order status</strong> will auto-advance to <strong>CONFIRMED</strong>. Invoice PDF will be sent to customer WhatsApp automatically.
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white text-xs font-bold transition-colors shrink-0 font-heading sm:mt-5 shadow-sm whitespace-nowrap"
                        onclick="return confirm('Are you sure you have verified the UPI payment? This will mark payment as PAID and order as CONFIRMED and send Invoice PDF to customer WhatsApp.')"
                    >
                        <i class="fa-solid fa-indian-rupee-sign mr-1"></i>
                        Verify &amp; Mark as Paid (Auto-Confirms &amp; Sends Invoice PDF)
                    </button>
                </div>
                <p class="text-[11px] text-slate-500">
                    <i class="fa-solid fa-shield-halved text-slate-400"></i>
                    Verify UPI screenshot or bank statement before marking paid.
                </p>
            </form>
        @endif
    </div>

    {{-- ===================== 3. SIVAKASI CRACKER TRANSPORT & LR DISPATCH CARD ===================== --}}
    <div id="parcelBookingCard" class="bg-white rounded-2xl border-2 {{ $order->isDispatched() ? 'border-teal-500/40 bg-teal-50/10' : ($order->isConfirmed() ? 'border-blue-400/60 ring-2 ring-blue-500/20' : 'border-slate-200 opacity-95') }} shadow-sm p-5 sm:p-6 space-y-5 scroll-mt-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-700 text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>
                <div>
                    <h2 class="font-extrabold text-base text-slate-900 font-heading flex items-center gap-2">
                        <span>Parcel Service Booking & LR Receipt (A1, MSS Transport)</span>
                        @if ($order->isDispatched())
                            <span class="text-[11px] font-bold bg-teal-100 text-teal-800 px-2.5 py-0.5 rounded-full">
                                Active Dispatch
                            </span>
                        @elseif ($order->isConfirmed())
                            <span class="text-[11px] font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">
                                Ready to Dispatch
                            </span>
                        @else
                            <span class="text-[11px] font-bold bg-slate-100 text-slate-500 px-2.5 py-0.5 rounded-full">
                                Locked
                            </span>
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500">Enter transport booking LR slip details (A1 Parcel Service, MSS, etc.) and send receipt photo to customer.</p>
                </div>
            </div>

            @if ($order->isDispatched())
                <button type="button" onclick="toggleDispatchForm()" class="text-xs font-bold text-teal-700 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg border border-teal-200 transition-colors shrink-0">
                    <i class="fa-solid fa-pen-to-square"></i> <span id="toggleDispatchBtnText">Edit Dispatch Details</span>
                </button>
            @endif
        </div>

        @if (!$order->isConfirmed())
            <!-- Locked State: Available only after order is Confirmed (Packing Ready) -->
            <div class="p-6 sm:p-8 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-200 text-slate-400 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-800">Parcel Service Booking is Locked</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                        This parcel booking form and LR dispatch will automatically unlock once the order is <strong>Confirmed or Packed</strong>. Please verify payment first (payment status must be Paid to auto-confirm).
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200 text-slate-600 text-[11px] font-bold">
                    <i class="fa-solid fa-clock"></i>
                    <span>Current Status: {{ strtoupper($currStatus) }} &middot; Step {{ $currStep }} of 4</span>
                </div>
            </div>
        @else
            @if ($order->isDispatched())
                <!-- Current Dispatch Details Display Card -->
            <div id="dispatchSummaryCard" class="bg-gradient-to-br from-slate-900 to-teal-950 text-white rounded-2xl p-5 shadow-inner space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-teal-300 font-extrabold">Parcel Service</span>
                        <div class="text-lg font-black font-heading text-white flex items-center gap-2">
                            <span>{{ $order->parcel_service_name ?? 'A1 Parcel Service' }}</span>
                            <span class="text-xs bg-teal-500/20 text-teal-300 font-mono px-2 py-0.5 rounded border border-teal-500/30">
                                LR: {{ $order->lr_number }}
                            </span>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Booking Date</span>
                        <div class="text-xs font-bold text-slate-200">
                            {{ $order->dispatch_date ? $order->dispatch_date->format('d M Y') : $order->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Parcel Count</span>
                        <span class="text-sm font-black text-amber-300 font-heading">{{ $order->parcel_count ?: '1 Box' }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Destination Hub</span>
                        <span class="text-sm font-bold text-white">{{ $order->destination_hub ?: $order->city }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Transport Helpline</span>
                        <span class="text-sm font-bold text-emerald-300">{{ $order->transport_phone ?: 'Contact Hub' }}</span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Delivery Charges</span>
                        <span class="text-sm font-black text-amber-400 font-heading">
                            {{ $order->delivery_charges !== null ? '₹' . number_format($order->delivery_charges, 2) : 'To Pay at Hub' }}
                        </span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Dispatched At</span>
                        <span class="text-xs font-bold text-slate-300">{{ $order->dispatched_at ? $order->dispatched_at->format('d M, h:i A') : 'Recorded' }}</span>
                    </div>
                </div>

                @if (!empty($order->dispatch_notes))
                    <div class="text-xs bg-white/5 rounded-xl p-3 border border-white/10 text-slate-300">
                        <strong class="text-white">Dispatch Note:</strong> {{ $order->dispatch_notes }}
                    </div>
                @endif

                @if ($order->lr_receipt_image)
                    <div class="bg-white/5 rounded-xl p-3.5 border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ $order->lr_receipt_url }}" target="_blank" class="w-16 h-16 rounded-lg bg-black/40 overflow-hidden border border-white/20 shrink-0 hover:opacity-90 block">
                                <img src="{{ $order->lr_receipt_url }}" alt="LR Receipt" class="w-full h-full object-cover">
                            </a>
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-teal-400"></i>
                                    <span>Physical LR Receipt Slip Photo</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Customer can view and download this slip online or via WhatsApp.
                                </div>
                            </div>
                        </div>
                        <a href="{{ $order->lr_receipt_url }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition-colors shrink-0">
                            <i class="fa-solid fa-eye mr-1"></i> View Full Slip
                        </a>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1 border-t border-white/10">
                    <span class="text-xs text-slate-400">
                        Need to re-deliver the LR details and receipt slip to the customer?
                    </span>
                    <form action="{{ route('admin.orders.send_dispatch_lr', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-colors font-heading shadow-sm">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Resend LR Slip & Tracking to WhatsApp</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Dispatch Booking & Update Form -->
        <form
            id="dispatchForm"
            action="{{ route('admin.orders.dispatch', $order) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4 {{ $order->isDispatched() ? 'hidden' : '' }}"
        >
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Parcel Service Selection -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Parcel / Transport Service <span class="text-rose-600">*</span>
                    </label>
                    <select
                        id="parcelServiceSelect"
                        name="parcel_service_name"
                        onchange="checkCustomParcel(this.value)"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                        required
                    >
                        @php
                            $currentService = $order->parcel_service_name ?: 'A1 Parcel Service';
                            $presetServices = [
                                'A1 Parcel Service',
                                'MSS Parcel Service',
                                'Metitur Transport',
                                'Rathimeena Transport',
                                'KPN Parcel Service',
                                'VRL Logistics',
                                'ARC Parcel Service',
                                'ABT Parcel Service',
                                'Standard Logistics',
                            ];
                            $isCustom = !in_array($currentService, $presetServices);
                        @endphp
                        @foreach ($presetServices as $preset)
                            <option value="{{ $preset }}" {{ $currentService === $preset ? 'selected' : '' }}>
                                🚚 {{ $preset }}
                            </option>
                        @endforeach
                        <option value="Other" {{ $isCustom ? 'selected' : '' }}>✏️ Other Transport (Type Name)</option>
                    </select>

                    <div id="customParcelInputWrap" class="{{ $isCustom ? '' : 'hidden' }} mt-2">
                        <input
                            type="text"
                            name="custom_parcel_service"
                            id="customParcelInput"
                            value="{{ $isCustom ? $order->parcel_service_name : '' }}"
                            minlength="2"
                            maxlength="60"
                            placeholder="Enter Custom Transport Service Name"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-900"
                        >
                    </div>
                </div>

                <!-- LR / Bilty Number -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        LR / Bilty / Receipt No <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="lr_number"
                        value="{{ old('lr_number', $order->lr_number) }}"
                        minlength="2"
                        maxlength="40"
                        placeholder="e.g. A1-789456 or MSS-55120"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-900"
                        required
                    >
                </div>

                <!-- Parcel Count (Boxes) -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Total Parcels / Boxes
                    </label>
                    <input
                        type="text"
                        name="parcel_count"
                        value="{{ old('parcel_count', $order->parcel_count ?: '1 Box') }}"
                        minlength="1"
                        maxlength="30"
                        placeholder="e.g. 1 Box or 2 Cartons"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-900"
                    >
                </div>

                <!-- Dispatch Date -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Dispatch Date
                    </label>
                    <input
                        type="date"
                        name="dispatch_date"
                        min="2020-01-01"
                        max="2099-12-31"
                        value="{{ old('dispatch_date', $order->dispatch_date ? $order->dispatch_date->format('Y-m-d') : date('Y-m-d')) }}"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-900"
                    >
                </div>

                <!-- Destination Branch Phone / Helpline -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Transport Branch Contact Phone
                    </label>
                    <input
                        type="tel"
                        name="transport_phone"
                        value="{{ old('transport_phone', $order->transport_phone) }}"
                        minlength="10"
                        maxlength="10"
                        inputmode="numeric"
                        pattern="[6-9][0-9]{9}"
                        placeholder="10-digit mobile (e.g. 9876543210)"
                        title="10-digit mobile starting with 6, 7, 8, or 9"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-900"
                    >
                </div>

                <!-- Destination Hub / City -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Destination Hub / Town
                    </label>
                    <input
                        type="text"
                        name="destination_hub"
                        value="{{ old('destination_hub', $order->destination_hub ?: $order->city) }}"
                        minlength="2"
                        maxlength="50"
                        placeholder="e.g. {{ $order->city }}"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-900"
                    >
                </div>

                <!-- Delivery / Transport Charges Amount -->
                <div class="space-y-1 sm:col-span-2 bg-amber-50/70 border border-amber-200 p-3 rounded-xl">
                    <label class="block text-xs font-extrabold text-amber-950 uppercase tracking-wider flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-indian-rupee-sign text-amber-700"></i>
                            <span>Delivery / Transport Charges (டெலிவரி கட்டணம்)</span>
                        </span>
                        <span class="text-[10px] font-bold text-amber-800 bg-amber-200/80 px-2 py-0.5 rounded">To be paid by customer at Hub</span>
                    </label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 font-black text-xs pointer-events-none">₹</span>
                        <input
                            type="number"
                            name="delivery_charges"
                            step="0.01"
                            min="0"
                            max="99999"
                            value="{{ old('delivery_charges', $order->delivery_charges) }}"
                            placeholder="Enter delivery/transport charge amount (e.g. 250.00)"
                            class="w-full pl-7 pr-3 py-2.5 text-xs bg-white border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-black text-slate-900 shadow-xs"
                        >
                    </div>
                    <p class="text-[11px] text-slate-600 mt-1">
                        இந்தத் தொகை வாடிக்கையாளரின் WhatsApp tracking மெசேஜில் <strong>"Delivery / Transport Charges: Rs. XXX"</strong> என அனுப்பப்படும். பார்சல் அலுவலகத்தில் செலுத்தி பெற வேண்டிய தொகை.
                    </p>
                </div>
            </div>

            <!-- Upload Photo of the Paper LR Receipt Slip -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Upload Physical LR Receipt Slip Photo (A1 / MSS paper receipt)
                </label>
                <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-50 p-3.5 rounded-xl border border-dashed border-slate-300">
                    <div class="shrink-0 w-24 h-24 rounded-lg bg-white border border-slate-200 overflow-hidden flex items-center justify-center text-slate-400 relative">
                        <img id="lrSlipPreview" src="{{ $order->lr_receipt_url ?: '' }}" alt="LR Slip Preview" class="w-full h-full object-cover {{ $order->lr_receipt_image ? '' : 'hidden' }}">
                        <i id="lrSlipPlaceholderIcon" class="fa-solid fa-camera text-2xl {{ $order->lr_receipt_image ? 'hidden' : '' }}"></i>
                    </div>
                    <div class="flex-1 space-y-1 text-center sm:text-left">
                        <input
                            type="file"
                            name="lr_receipt_image"
                            id="lrReceiptFileInput"
                            accept="image/*"
                            onchange="previewLrSlip(event)"
                            class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer"
                        >
                        <p class="text-[11px] text-slate-500">
                            Upload a photo of the paper receipt given by the parcel office. This photo will be delivered to the customer's WhatsApp chat. Max 10MB (JPG, PNG).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dispatch Notes -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Collection Instructions / Notes for Customer
                </label>
                <textarea
                    name="dispatch_notes"
                    rows="2"
                    maxlength="500"
                    placeholder="e.g. Please bring Aadhaar / ID proof and LR number to collect parcel at the transport branch."
                    class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-900"
                >{{ old('dispatch_notes', $order->dispatch_notes) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>Will automatically send WhatsApp tracking text & LR receipt photo.</span>
                </div>
                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-700 hover:from-teal-700 hover:to-emerald-800 text-white font-extrabold text-xs shadow-md transition-all font-heading"
                >
                    <i class="fa-solid {{ $order->isDispatched() ? 'fa-pen-to-square' : 'fa-truck-fast' }}"></i>
                    <span>{{ $order->isDispatched() ? 'Update Dispatch Details & Re-send LR' : 'Submit Parcel Booking & Advance to: Dispatched' }}</span>
                </button>
            </div>
        </form>
        @endif
    </div>

    {{-- ===================== 4. CUSTOMER & DELIVERY INFO ===================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Customer Information</span>
                <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 font-bold px-1.5 py-0.5 rounded">
                    Direct Contact
                </span>
            </div>
            <div class="text-base font-extrabold text-slate-900">{{ $order->name }}</div>
            
            <div class="text-xs text-slate-600 mt-2 space-y-1">
                <div class="flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-600 font-bold text-sm"></i>
                    <span class="text-slate-500">WhatsApp / Primary:</span>
                    <a href="tel:{{ $order->phone1 }}" class="font-extrabold text-rose-700 hover:underline">{{ $order->phone1 }}</a>
                </div>
                @if ($order->phone2)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                        <span class="text-slate-500">Alternative:</span>
                        <a href="tel:{{ $order->phone2 }}" class="font-bold text-slate-800 hover:underline">{{ $order->phone2 }}</a>
                    </div>
                @endif
            </div>

            <!-- Quick Action Call & SMS Buttons -->
            <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100">
                <a href="tel:{{ $order->phone1 }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-phone text-emerald-600"></i>
                    <span>Call Phone 1</span>
                </a>
                @if ($order->phone2)
                    <a href="tel:{{ $order->phone2 }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 text-xs font-bold transition-all shadow-sm">
                        <i class="fa-solid fa-phone-flip text-slate-600"></i>
                        <span>Call Phone 2</span>
                    </a>
                @endif
                <a href="sms:{{ $order->phone1 }}?body=Vanakkam%20{{ urlencode($order->name) }},%20your%20Guru%20Crackers%20order%20{{ $order->order_number }}%20is%20received.%20Amount:%20Rs.{{ number_format($order->total_amount, 2) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-comment-sms text-sky-600"></i>
                    <span>Send SMS</span>
                </a>
            </div>

            <p class="text-[11px] text-slate-500 mt-2.5 bg-slate-50 border border-slate-200/80 p-2 rounded-xl flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-amber-500 text-xs shrink-0"></i>
                <span>If customer is not on WhatsApp, use <strong>Call Phone 1</strong> or <strong>Send SMS</strong>.</span>
            </p>
        </div>

        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                Transport Delivery Address
            </div>
            <div class="text-xs font-medium text-slate-800 whitespace-pre-line leading-relaxed">
                {{ $order->delivery_address }}
            </div>
            <div class="text-xs font-bold text-slate-900 mt-1">
                {{ $order->city }} - {{ $order->pincode }}
            </div>
        </div>
    </div>

    {{-- ===================== 5. ORDER ITEMS PACKING TABLE ===================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm sm:text-base text-slate-900 font-heading flex items-center gap-2">
                <span>📦 Ordered Cracker Products</span>
                <span class="text-xs bg-slate-100 text-slate-700 font-bold px-2.5 py-0.5 rounded-full">
                    {{ $order->items->count() }} items &middot; {{ $order->total_quantity }} units
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4 text-left">#</th>
                        <th class="py-2.5 px-4 text-left">Product Name</th>
                        <th class="py-2.5 px-4 text-center">Unit</th>
                        <th class="py-2.5 px-4 text-center">Quantity</th>
                        <th class="py-2.5 px-4 text-right">Net Price</th>
                        <th class="py-2.5 px-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $i => $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</div>
                                @if ($item->product && !empty($item->product->tamil_name))
                                    <div class="text-xs text-rose-700 font-medium mt-0.5">
                                        {{ $item->product->tamil_name }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500 font-medium">
                                {{ $item->product->unit ?? 'Pcs' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block bg-slate-100 font-black text-slate-900 px-3 py-1 rounded-lg text-sm border border-slate-200">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600 font-medium">
                                ₹{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 text-sm">
                                ₹{{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/80 border-t-2 border-slate-200 font-bold">
                    <tr>
                        <td colspan="4" class="py-3.5 px-4 text-slate-700 text-xs">
                            Total Units to Pack: <strong class="text-slate-900 text-sm">{{ $order->total_quantity }} units</strong>
                        </td>
                        <td class="py-3.5 px-4 text-right text-slate-700 font-heading">Grand Total:</td>
                        <td class="py-3.5 px-4 text-right text-base sm:text-lg font-black text-rose-700 font-heading">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function copyFromElement(elementId, btnId) {
    const el = document.getElementById(elementId);
    if (el) {
        navigator.clipboard.writeText(el.value).then(() => {
            const btn = document.getElementById(btnId);
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-300"></i>';
                setTimeout(() => {
                    btn.innerHTML = orig;
                }, 2000);
            }
        });
    }
}

function checkCustomParcel(val) {
    const wrap = document.getElementById('customParcelInputWrap');
    const input = document.getElementById('customParcelInput');
    if (val === 'Other') {
        wrap.classList.remove('hidden');
        input.focus();
    } else {
        wrap.classList.add('hidden');
    }
}

function toggleDispatchForm() {
    const form = document.getElementById('dispatchForm');
    const btnText = document.getElementById('toggleDispatchBtnText');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        btnText.innerText = 'Close Edit Form';
    } else {
        form.classList.add('hidden');
        btnText.innerText = 'Edit Dispatch Details';
    }
}

function previewLrSlip(event) {
    const file = event.target.files[0];
    if (file) {
        const preview = document.getElementById('lrSlipPreview');
        const icon = document.getElementById('lrSlipPlaceholderIcon');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        icon.classList.add('hidden');
    }
}
</script>
@endsection
