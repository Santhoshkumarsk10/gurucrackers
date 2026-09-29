@extends('layouts.app')

@section('title', 'Bulk Messaging - Dispatched Orders')

@section('content')
<div class="space-y-6 pb-20">

    <!-- Header Section -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                        Bulk Messaging
                    </h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                        <i class="fa-solid fa-truck-fast text-[10px]"></i> Dispatched Orders Only
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Broadcast personalized festival greetings, LR tracking reminders, or announcements to customers whose orders have been dispatched.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Gateway Status Pill -->
            @if (!empty($status['connected']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse text-emerald-500"></i>
                    <span>Bot Connected (+{{ $status['user'] ?: $status['allowedPhone'] ?: 'Active' }})</span>
                </span>
            @else
                <a href="{{ route('admin.whatsapp.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors">
                    <i class="fa-solid fa-qrcode text-amber-600"></i>
                    <span>Connect WhatsApp Bot</span>
                </a>
            @endif

            <a href="{{ route('admin.orders.index', ['status' => 'dispatched']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>View All Dispatched Orders</span>
            </a>
        </div>
    </div>


    <!-- Validation & Send Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1.5 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>Message Dispatch Notice:</span>
            </div>
            <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty($status['connected']))
        <!-- Helpful Gateway Tip -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-amber-900 shadow-2xs">
            <div class="flex items-start gap-2.5">
                <i class="fa-brands fa-whatsapp text-emerald-600 text-lg mt-0.5 shrink-0"></i>
                <div>
                    <strong class="font-bold">Dual Delivery Modes Available:</strong>
                    <p class="text-[11px] text-amber-800 mt-0.5">
                        You can send messages <strong>individually via Official WhatsApp Web</strong> at any time. To broadcast messages to all selected customers automatically with 1-click in the background, please link your WhatsApp Bot.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.whatsapp.index') }}" class="shrink-0 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-link"></i>
                <span>Link WhatsApp Gateway</span>
            </a>
        </div>
    @endif

    <form id="bulkMessageForm" method="POST" action="{{ route('admin.messaging.send') }}" onsubmit="return confirmBulkSend(event)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Message Composer & Template Picker (Sticky on Desktop) -->
            <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-20">
                <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2 font-black text-slate-900 font-heading text-sm">
                            <i class="fa-solid fa-pen-nib text-emerald-600"></i>
                            <span>Message Composer</span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500" id="selectionCountBadge">
                            0 customers selected
                        </span>
                    </div>

                    <!-- Quick Preset Templates -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Quick Festive Templates</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button
                                type="button"
                                onclick="applyTemplate('pickup')"
                                class="text-left p-2.5 rounded-xl border border-slate-200 hover:border-emerald-400 bg-slate-50 hover:bg-emerald-50/50 transition-all cursor-pointer group"
                            >
                                <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-truck-ramp-box text-amber-500"></i>
                                    <span>Hub Pickup Reminder</span>
                                </div>
                                <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">Parcel LR arrived at destination</p>
                            </button>

                            <button
                                type="button"
                                onclick="applyTemplate('diwali')"
                                class="text-left p-2.5 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50 hover:bg-amber-50/50 transition-all cursor-pointer group"
                            >
                                <div class="text-xs font-bold text-slate-800 group-hover:text-amber-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                                    <span>Diwali Safety & Wish</span>
                                </div>
                                <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">Festival greeting & care tips</p>
                            </button>

                            <button
                                type="button"
                                onclick="applyTemplate('feedback')"
                                class="text-left p-2.5 rounded-xl border border-slate-200 hover:border-blue-400 bg-slate-50 hover:bg-blue-50/50 transition-all cursor-pointer group"
                            >
                                <div class="text-xs font-bold text-slate-800 group-hover:text-blue-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-star text-amber-400"></i>
                                    <span>Review & Feedback</span>
                                </div>
                                <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">Ask customer experience</p>
                            </button>

                            <button
                                type="button"
                                onclick="applyTemplate('thankyou')"
                                class="text-left p-2.5 rounded-xl border border-slate-200 hover:border-rose-400 bg-slate-50 hover:bg-rose-50/50 transition-all cursor-pointer group"
                            >
                                <div class="text-xs font-bold text-slate-800 group-hover:text-rose-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-heart text-rose-500"></i>
                                    <span>Thank You Note</span>
                                </div>
                                <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">Appreciation message</p>
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Placeholder Tags -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 flex items-center justify-between">
                            <span>Dynamic Personalization Tags</span>
                            <span class="text-[10px] text-slate-400 font-normal">Click to insert</span>
                        </label>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="insertTag('{customer_name}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Customer Name">
                                + {customer_name}
                            </button>
                            <button type="button" onclick="insertTag('{order_number}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Order ID Number">
                                + {order_number}
                            </button>
                            <button type="button" onclick="insertTag('{lr_number}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Transport LR Number">
                                + {lr_number}
                            </button>
                            <button type="button" onclick="insertTag('{transport_name}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Parcel Service Name">
                                + {transport_name}
                            </button>
                            <button type="button" onclick="insertTag('{destination_hub}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Destination City/Hub">
                                + {destination_hub}
                            </button>
                            <button type="button" onclick="insertTag('{parcel_count}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Total Box Count">
                                + {parcel_count}
                            </button>
                            <button type="button" onclick="insertTag('{shop_name}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Your Shop Name">
                                + {shop_name}
                            </button>
                            <button type="button" onclick="insertTag('{invoice_url}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-mono font-semibold transition-colors cursor-pointer" title="Online Invoice URL">
                                + {invoice_url}
                            </button>
                        </div>
                    </div>

                    <!-- Message Textarea -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="broadcastMessage" class="block text-xs font-bold text-slate-800">
                                WhatsApp Message Text <span class="text-rose-600">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-mono" id="charCount">0 / 2000</span>
                        </div>
                        <textarea
                            name="message"
                            id="broadcastMessage"
                            rows="6"
                            required
                            maxlength="2000"
                            placeholder="Type your message here... Use dynamic tags like {customer_name}, {lr_number} for personalization."
                            oninput="updateMessagePreview()"
                            class="w-full p-3.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-900 leading-relaxed transition-all"
                        >வணக்கம் {customer_name}! உங்கள் ஆர்டர் #{order_number} சிவகாசியிலிருந்து {transport_name} மூலம் வெற்றிகரமாக அனுப்பப்பட்டுள்ளது. 

📦 பார்சல் எண்ணிக்கை: {parcel_count}
📍 போய்ச்சேரும் இடம்: {destination_hub}
📄 LR எண்: {lr_number}

பார்சல் உங்கள் ஊர் டிரான்ஸ்போர்ட் அலுவலகம் வந்தடைந்ததும், இந்த LR எண்ணைக் காட்டி பார்சலை பெற்றுக்கொள்ளவும்.

இனிய தீபாவளி நல்வாழ்த்துகள்! 🎆
நன்றி,
{shop_name}</textarea>
                    </div>

                    <!-- Live Message Preview Box -->
                    <div class="bg-emerald-50/50 border border-emerald-200/70 p-3.5 rounded-2xl space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i> Sample Message Preview
                            </span>
                            <span class="text-[10px] text-slate-400" id="previewSampleCustomer">Sample Customer</span>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-emerald-100 text-slate-800 text-[11px] leading-relaxed whitespace-pre-wrap font-sans shadow-2xs" id="livePreviewContainer">
                            Message preview will appear here...
                        </div>
                    </div>

                    <!-- Send Actions -->
                    <div class="space-y-2 pt-1">
                        <button
                            type="submit"
                            id="submitBulkSendBtn"
                            class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 font-heading transition-all cursor-pointer flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-paper-plane"></i>
                            <span id="bulkSendBtnLabel">Broadcast to Selected (0)</span>
                        </button>
                        <p class="text-[10px] text-center text-slate-400">
                            Sent directly to customer's registered phone number via WhatsApp.
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Dispatched Customers Table -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <!-- Filters & Search Header -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 space-y-3 bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h2 class="font-extrabold text-sm text-slate-900 font-heading">
                                    Dispatched Orders List
                                </h2>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    {{ $dispatchedOrders->total() }} Orders
                                </span>
                            </div>

                            <!-- Select All / Clear All Buttons -->
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllDispatched(true)" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 cursor-pointer transition-colors">
                                    Select All ({{ $dispatchedOrders->total() }})
                                </button>
                                <button type="button" onclick="selectAllDispatched(false)" class="text-xs font-bold text-slate-600 hover:text-slate-800 bg-white px-2.5 py-1 rounded-lg border border-slate-200 cursor-pointer transition-colors">
                                    Clear Selection
                                </button>
                            </div>
                        </div>

                        <!-- Filter Form -->
                        <div class="flex flex-col sm:flex-row items-center gap-2.5">
                            <div class="relative flex-1 w-full">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input
                                    type="text"
                                    id="tableSearchInput"
                                    placeholder="Search customer, phone, LR #, city..."
                                    value="{{ request('search') }}"
                                    maxlength="60"
                                    oninput="cleanSearchInput(this)"
                                    class="w-full pl-8 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-900"
                                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); applySearchFilter(); }"
                                >
                            </div>

                            @if ($distinctHubs->isNotEmpty())
                                <select id="filterHubSelect" class="w-full sm:w-auto px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" onchange="applySearchFilter()">
                                    <option value="">All Destination Hubs</option>
                                    @foreach ($distinctHubs as $hub)
                                        <option value="{{ $hub }}" {{ request('hub') === $hub ? 'selected' : '' }}>{{ $hub }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <button type="button" onclick="applySearchFilter()" class="w-full sm:w-auto px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer shrink-0">
                                Filter
                            </button>
                            @if (request()->hasAny(['search', 'hub', 'transport']))
                                <a href="{{ route('admin.messaging.index') }}" class="w-full sm:w-auto px-3 py-2 text-center text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl border border-rose-200 transition-colors shrink-0">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Customers Table -->
                    @if ($dispatchedOrders->isEmpty())
                        <div class="p-12 text-center space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">No Dispatched Orders Found</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                Only orders whose status is strictly marked as <strong>Dispatched</strong> with parcel transport details appear here.
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-600 font-extrabold text-[11px] uppercase tracking-wider">
                                        <th class="py-3 px-3.5 w-10 text-center">
                                            <input
                                                type="checkbox"
                                                id="masterCheckbox"
                                                checked
                                                onchange="toggleAllCheckboxes(this)"
                                                class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500 cursor-pointer"
                                                title="Select / Deselect Visible Rows"
                                            >
                                        </th>
                                        <th class="py-3 px-3">Customer</th>
                                        <th class="py-3 px-3">Order & LR Details</th>
                                        <th class="py-3 px-3">Destination Hub</th>
                                        <th class="py-3 px-3.5 text-right">Individual Send</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @foreach ($dispatchedOrders as $order)
                                        @php
                                            $orderDataJson = htmlspecialchars(json_encode([
                                                'id' => $order->id,
                                                'customer_name' => $order->name,
                                                'order_number' => $order->order_number,
                                                'phone' => $order->phone1,
                                                'lr_number' => $order->lr_number ?: '',
                                                'transport_name' => $order->parcel_service_name ?: '',
                                                'transport_phone' => $order->transport_phone ?: '',
                                                'destination_hub' => $order->destination_hub ?: $order->city,
                                                'parcel_count' => $order->parcel_count ?: '1',
                                                'city' => $order->city ?: '',
                                                'total_amount' => '₹' . number_format($order->total_amount, 2),
                                                'invoice_url' => route('order.public_invoice', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]),
                                            ]), ENT_QUOTES, 'UTF-8');
                                        @endphp
                                        <tr class="hover:bg-emerald-50/30 transition-colors order-row" data-order="{{ $orderDataJson }}">
                                            <td class="py-3 px-3.5 text-center">
                                                <input
                                                    type="checkbox"
                                                    name="order_ids[]"
                                                    value="{{ $order->id }}"
                                                    checked
                                                    onchange="updateSelectionCount()"
                                                    class="order-checkbox w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500 cursor-pointer"
                                                >
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-slate-900">{{ $order->name }}</div>
                                                <a href="tel:{{ $order->phone1 }}" class="text-[11px] font-mono text-emerald-700 hover:underline flex items-center gap-1 mt-0.5">
                                                    <i class="fa-solid fa-phone text-[9px]"></i> {{ $order->phone1 }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-mono font-bold text-slate-800">#{{ $order->order_number }}</div>
                                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                    <span class="bg-amber-100 text-amber-800 font-bold px-1.5 py-0.2 rounded text-[10px]">
                                                        LR: {{ $order->lr_number ?: 'N/A' }}
                                                    </span>
                                                    <span class="truncate max-w-[120px]" title="{{ $order->parcel_service_name }}">
                                                        {{ $order->parcel_service_name ?: 'Transport' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-slate-800">{{ $order->destination_hub ?: $order->city }}</div>
                                                <div class="text-[10px] text-slate-400">
                                                    {{ $order->dispatched_at ? $order->dispatched_at->format('d M, h:i A') : ($order->dispatch_date ? $order->dispatch_date->format('d M Y') : 'Dispatched') }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-3.5 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- 1. Send via Automated WhatsApp Bot -->
                                                    <button
                                                        type="button"
                                                        onclick="sendSingleCustomerBot({{ $order->id }}, '{{ addslashes($order->name) }}', '{{ $order->phone1 }}')"
                                                        class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition-colors cursor-pointer border border-emerald-200"
                                                        title="Send current message via Bot"
                                                    >
                                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                                    </button>

                                                    <!-- 2. Direct Official WhatsApp Chat / Web link -->
                                                    <a
                                                        href="#"
                                                        onclick="openWhatsAppWebDirect({{ $orderDataJson }}); return false;"
                                                        class="p-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors shadow-xs"
                                                        title="Open in WhatsApp Web / App directly"
                                                    >
                                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        @if ($dispatchedOrders->hasPages())
                            <div class="p-4 border-t border-slate-100">
                                {{ $dispatchedOrders->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </form>
</div>

<!-- ==================== HIDDEN FORM FOR INDIVIDUAL SEND VIA BOT ==================== -->
<form id="individualSendForm" method="POST" action="" class="hidden">
    @csrf
    <input type="hidden" name="message" id="individualMessageInput">
</form>

<script>
// Shop details for tag personalization in client-side preview
const currentShop = {
    name: @json($shop->name ?? 'Guru Crackers'),
    phone: @json($shop->phone ?? ''),
};

// Preset message templates
const templates = {
    pickup: `வணக்கம் {customer_name}! உங்கள் பட்டாசு பார்சல் Sivakasi-லிருந்து {transport_name} மூலம் அனுப்பப்பட்டு உங்கள் ஊர் ({destination_hub}) வந்தடைந்துள்ளது / வரவுள்ளது. 

📄 LR எண்: {lr_number}
📦 பார்சல் எண்ணிக்கை: {parcel_count}

தயவுசெய்து உங்கள் அடையாளச் சான்றுடன் (Aadhaar / Driving License) மற்றும் இந்த LR எண்ணைக் காட்டி பார்சலை தாமதிக்காமல் பெற்றுக்கொள்ளவும்.

✨ இனிய தீபாவளி நல்வாழ்த்துகள்! 🎇
நன்றி,
{shop_name}`,

    diwali: `✨ இனிய தீபாவளி நல்வாழ்த்துகள் {customer_name}! 🪔

உங்கள் ஆர்டர் #{order_number} வெற்றிகரமாக சிவகாசியிலிருந்து உங்களுக்கு அனுப்பிவைக்கப்பட்டுள்ளது (LR No: {lr_number}).

🎆 பட்டாசு பாதுகாப்பு குறிப்புகள்:
1️⃣ எப்போதும் பெரியவர்கள் முன்னிலையில் பட்டாசு வெடிக்கவும்.
2️⃣ ஒரு வாளி தண்ணீர் மற்றும் மணல் அருகில் வைத்திருக்கவும்.
3️⃣ தளர்வான காட்டன் உடைகளை அணியவும்.

உங்களுக்கும் உங்கள் குடும்பத்தினருக்கும் மகிழ்ச்சிகரமான தீபாவளி வாழ்த்துகள்! 💥
நன்றி - {shop_name}`,

    feedback: `வணக்கம் {customer_name}! 

எங்கள் {shop_name}-ல் நீங்கள் வாங்கிய பட்டாசுகள் உங்களுக்கு பிடித்திருந்ததா? உங்கள் மேலான அனுபவத்தையும் கருத்துக்களையும் எங்களுடன் பகிர்ந்து கொள்ளுங்கள்!

📄 உங்கள் ஆர்டர் எண்: #{order_number}

உங்கள் திருப்தியே எங்கள் மகிழ்ச்சி. அடுத்த வருடமும் சிறந்த தள்ளுபடியுடன் உங்களை மகிழ்விப்போம்! 🎆
நன்றி,
{shop_name}`,

    thankyou: `அன்பார்ந்த {customer_name},

எங்கள் {shop_name}-ஐ நம்பி பட்டாசு ஆர்டர் செய்தமைக்கு எங்களது மனமார்ந்த நன்றிகள்! 

உங்கள் பார்சல் சிவகாசி நேரடி தொழிற்சாலையிலிருந்து {transport_name} (LR: {lr_number}) மூலம் உரிய முறையில் அனுப்பப்பட்டுள்ளது. 

உங்களுக்கும் உங்கள் குடும்பத்தினர் அனைவருக்கும் நல்வாழ்த்துகள்! 🎆🎇
-{shop_name}`
};

function applyTemplate(type) {
    const textarea = document.getElementById('broadcastMessage');
    if (templates[type] && textarea) {
        textarea.value = templates[type];
        updateMessagePreview();
    }
}

function insertTag(tag) {
    const textarea = document.getElementById('broadcastMessage');
    if (!textarea) return;

    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    const textBefore = textarea.value.substring(0, startPos);
    const textAfter = textarea.value.substring(endPos, textarea.value.length);

    textarea.value = textBefore + tag + textAfter;
    textarea.selectionStart = textarea.selectionEnd = startPos + tag.length;
    textarea.focus();
    updateMessagePreview();
}

// Replaces tags in template with sample order data for live preview
function renderPersonalizedText(template, order) {
    if (!template) return '';
    return template
        .replace(/{customer_name}/g, order.customer_name || 'Customer')
        .replace(/{order_number}/g, order.order_number || 'GC-2026-0001')
        .replace(/{phone}/g, order.phone || '9876543210')
        .replace(/{lr_number}/g, order.lr_number || 'A1-12345')
        .replace(/{transport_name}/g, order.transport_name || 'Sivakasi Transport')
        .replace(/{transport_phone}/g, order.transport_phone || currentShop.phone)
        .replace(/{destination_hub}/g, order.destination_hub || 'Hub')
        .replace(/{parcel_count}/g, order.parcel_count || '1')
        .replace(/{city}/g, order.city || 'City')
        .replace(/{total_amount}/g, order.total_amount || '₹2,500.00')
        .replace(/{shop_name}/g, currentShop.name || 'Guru Crackers')
        .replace(/{shop_phone}/g, currentShop.phone || '')
        .replace(/{invoice_url}/g, order.invoice_url || 'https://gurucrackers.com');
}

function updateMessagePreview() {
    const textarea = document.getElementById('broadcastMessage');
    const previewContainer = document.getElementById('livePreviewContainer');
    const charCount = document.getElementById('charCount');
    const sampleCustomerLabel = document.getElementById('previewSampleCustomer');

    if (!textarea || !previewContainer) return;

    if (charCount) {
        charCount.innerText = `${textarea.value.length} / 2000`;
    }

    // Pick first visible order row as sample
    const firstRow = document.querySelector('.order-row');
    let sampleOrder = {
        customer_name: 'Varun Kumar',
        order_number: 'GC-202609-8812',
        phone: '9876543210',
        lr_number: 'A1-98765',
        transport_name: 'ARC Parcel Service',
        transport_phone: '04562-220000',
        destination_hub: 'Madurai',
        parcel_count: '2',
        city: 'Madurai',
        total_amount: '₹3,450.00',
        invoice_url: 'https://gurucrackers.com/order/invoice/sample'
    };

    if (firstRow && firstRow.dataset.order) {
        try {
            sampleOrder = JSON.parse(firstRow.dataset.order);
        } catch(e) {}
    }

    if (sampleCustomerLabel) {
        sampleCustomerLabel.innerText = `For: ${sampleOrder.customer_name} (#${sampleOrder.order_number})`;
    }

    const personalized = renderPersonalizedText(textarea.value, sampleOrder);
    previewContainer.innerText = personalized || 'Type a message above to see preview...';
}

function toggleAllCheckboxes(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = masterCheckbox.checked;
    });
    updateSelectionCount();
}

function selectAllDispatched(checkAll) {
    const master = document.getElementById('masterCheckbox');
    if (master) master.checked = checkAll;
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkAll;
    });
    updateSelectionCount();
}

function updateSelectionCount() {
    const checked = document.querySelectorAll('.order-checkbox:checked').length;
    const badge = document.getElementById('selectionCountBadge');
    const btnLabel = document.getElementById('bulkSendBtnLabel');
    const submitBtn = document.getElementById('submitBulkSendBtn');

    if (badge) {
        badge.innerText = `${checked} customers selected`;
    }
    if (btnLabel) {
        btnLabel.innerText = `Broadcast to Selected (${checked})`;
    }
    if (submitBtn) {
        submitBtn.disabled = (checked === 0);
        if (checked === 0) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}

// Confirmation before broadcasting to multiple users
function confirmBulkSend(event) {
    const checked = document.querySelectorAll('.order-checkbox:checked').length;
    const textarea = document.getElementById('broadcastMessage');

    if (checked === 0) {
        event.preventDefault();
        alert('Please select at least one dispatched order customer.');
        return false;
    }

    if (!textarea || !textarea.value.trim()) {
        event.preventDefault();
        alert('Please enter a message to broadcast.');
        return false;
    }

    const ok = confirm(`Are you sure you want to broadcast this WhatsApp message to ${checked} dispatched order customer(s)?`);
    if (!ok) {
        event.preventDefault();
        return false;
    }

    const submitBtn = document.getElementById('submitBulkSendBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> <span>Broadcasting WhatsApp Messages...</span>';
    }

    return true;
}

// Single customer send via automated Bot
function sendSingleCustomerBot(orderId, customerName, phone) {
    const textarea = document.getElementById('broadcastMessage');
    if (!textarea || !textarea.value.trim()) {
        alert('Please enter a message in the composer first.');
        return;
    }

    const ok = confirm(`Send personalized WhatsApp message to ${customerName} (${phone}) via Bot now?`);
    if (!ok) return;

    const form = document.getElementById('individualSendForm');
    const msgInput = document.getElementById('individualMessageInput');
    form.action = `/admin/bulk-messaging/${orderId}/send`;
    msgInput.value = textarea.value;
    form.submit();
}

// Open official WhatsApp Web / Mobile app with personalized text pre-filled
function openWhatsAppWebDirect(order) {
    const textarea = document.getElementById('broadcastMessage');
    const msgTemplate = textarea ? textarea.value : '';
    const personalized = renderPersonalizedText(msgTemplate, order);

    const cleanPhone = String(order.phone).replace(/[^0-9]/g, '');
    let finalPhone = cleanPhone;
    if (finalPhone.length === 10) {
        finalPhone = '91' + finalPhone;
    }

    const url = `https://api.whatsapp.com/send?phone=${finalPhone}&text=${encodeURIComponent(personalized)}`;
    window.open(url, '_blank');
}

function cleanSearchInput(el) {
    if (!el) return;
    // Allow only letters (including Tamil), digits, spaces, and necessary characters: #, ., -, +, /
    el.value = el.value.replace(/[^a-zA-Z0-9\u0B80-\u0BFF\s#\.\-\/+]/g, '').replace(/\-{2,}/g, '-');
}

function applySearchFilter() {
    const searchInput = document.getElementById('tableSearchInput');
    const hubSelect = document.getElementById('filterHubSelect');
    
    if (searchInput) {
        cleanSearchInput(searchInput);
    }

    const rawSearch = (searchInput?.value || '').trim();
    const hubVal = (hubSelect?.value || '').trim();
    
    // Check if search contains at least one meaningful letter/digit
    const hasAlpha = /[a-zA-Z0-9\u0B80-\u0BFF]/.test(rawSearch);
    const validSearch = hasAlpha ? rawSearch : '';

    const url = new URL(window.location.href);
    const hadSearch = url.searchParams.has('search');
    const hadHub = url.searchParams.has('hub');

    // Prevent empty filter submission if no filters are active
    if (!validSearch && !hubVal && !hadSearch && !hadHub) {
        if (searchInput && rawSearch && !hasAlpha) {
            searchInput.value = '';
        }
        return;
    }

    // Avoid unnecessary reload if search and hub values are unchanged
    const currentSearch = url.searchParams.get('search') || '';
    const currentHub = url.searchParams.get('hub') || '';
    if (validSearch === currentSearch && hubVal === currentHub) {
        return;
    }

    if (validSearch) {
        url.searchParams.set('search', validSearch);
    } else {
        url.searchParams.delete('search');
    }

    if (hubVal) {
        url.searchParams.set('hub', hubVal);
    } else {
        url.searchParams.delete('hub');
    }

    url.searchParams.delete('page');
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', () => {
    updateMessagePreview();
    updateSelectionCount();
});
</script>
@endsection
