@extends('layouts.app')

@section('title', 'WhatsApp Live Customer Chat & Dispatch - Admin')

@section('content')
<div class="mx-auto space-y-3 pb-8">

    <!-- Compact Navigation & Status Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm shadow-emerald-500/20 shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base sm:text-lg font-black text-slate-900 font-heading">
                        WhatsApp Gateway & Live Chat
                    </h1>
                    @if ($status['connected'])
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Linked: +{{ $status['user'] }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Disconnected</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">
                    Real-time two-way WhatsApp messaging with customers.
                </p>
            </div>
        </div>

        <!-- Top Action Tabs -->
        <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
            <!-- 1. Live Chat Tab (Active) -->
            <a
                href="{{ route('admin.whatsapp.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-sm shadow-emerald-600/20 transition-all"
            >
                <i class="fa-solid fa-comments"></i>
                <span>Live Chat</span>
            </a>

            <!-- 2. 100% Audit Log Tab -->
            <a
                href="{{ route('admin.whatsapp.audit_log') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200"
                title="100% WhatsApp Audit Trail & Delivery History"
            >
                <i class="fa-solid fa-clipboard-check text-indigo-600"></i>
                <span>Audit Tracking Log</span>
                @if(isset($stats['total_messages']) && $stats['total_messages'] > 0)
                    <span class="text-[10px] bg-slate-200 text-slate-700 font-extrabold px-1.5 py-0.2 rounded-full">
                        {{ $stats['total_messages'] }}
                    </span>
                @endif
            </a>

            <!-- 3. Test Message Button -->
            @if ($status['connected'])
                <button
                    type="button"
                    onclick="toggleTestMessageBox()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200"
                >
                    <i class="fa-solid fa-paper-plane text-emerald-600 text-xs"></i>
                    <span>Test Send</span>
                </button>
            @endif

            <!-- 4. Disconnect Button -->
            @if ($status['connected'])
                <form action="{{ route('admin.whatsapp.logout') }}" method="POST" data-confirm="Are you sure you want to disconnect this WhatsApp number from the gateway?" data-confirm-title="Disconnect WhatsApp?" data-confirm-btn="Yes, Disconnect" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all border border-rose-200">
                        <i class="fa-solid fa-power-off text-[11px]"></i>
                        <span>Disconnect</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($status['connected'])

        <!-- Collapsible Test Message Box -->
        <div id="testMessageBox" class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-4 transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-vial"></i>
                    </span>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Quick Direct Test Message</h3>
                </div>
                <button type="button" onclick="toggleTestMessageBox()" class="text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('admin.whatsapp.test') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="testPhone" class="block text-[11px] font-bold text-slate-600 mb-1">Mobile Number (10 digits)</label>
                        <div class="relative flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white overflow-hidden">
                            <span class="inline-flex items-center px-2.5 text-slate-700 font-extrabold text-xs bg-slate-100 border-r border-slate-200 select-none">🇮🇳 +91</span>
                            <input type="tel" name="phone" id="testPhone" value="{{ old('phone', substr(preg_replace('/[^0-9]/', '', $shop->phone), -10)) }}" required class="w-full px-3 py-1.5 text-xs bg-transparent border-none focus:outline-none font-mono font-bold text-slate-900" placeholder="10-digit number">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="testMessage" class="block text-[11px] font-bold text-slate-600 mb-1">Message</label>
                        <div class="flex gap-2">
                            <input type="text" name="message" id="testMessage" value="{{ old('message', 'Hello from Guru Crackers! Automated WhatsApp Live Chat is working 100% free!') }}" required class="flex-1 px-3 py-1.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shrink-0 transition-colors">
                                <i class="fa-solid fa-paper-plane text-emerald-400 text-xs"></i>
                                <span>Send Test</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ========================================================================= --}}
        {{--                     MAIN TWO-PANE WHATSAPP LIVE CHAT                      --}}
        {{-- ========================================================================= --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden grid grid-cols-1 lg:grid-cols-12 h-[calc(100vh-175px)] min-h-[550px] max-h-[860px]">

            {{-- ------------------- LEFT COLUMN: CONVERSATION LIST (4 Cols) ------------------- --}}
            <div class="lg:col-span-4 border-r border-slate-200 flex flex-col bg-slate-50/70 h-full overflow-hidden">

                <!-- Left Column Header -->
                <div class="p-3 bg-white border-b border-slate-200/80 space-y-2 shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider font-heading">Conversations</span>
                            <span id="chatCountBadge" class="text-[10px] font-extrabold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200">
                                {{ count($chats) }}
                            </span>
                        </div>

                        <!-- Live Sync Indicator -->
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Sync</span>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input
                            type="text"
                            id="chatSearchInput"
                            placeholder="Search name, phone, order #..."
                            oninput="filterChats(this.value)"
                            class="w-full pl-8 pr-7 py-1.5 bg-slate-100 focus:bg-white text-xs border border-transparent focus:border-emerald-500 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium text-slate-800"
                        >
                        <button
                            type="button"
                            id="clearSearchBtn"
                            onclick="clearChatSearch()"
                            class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                        >
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>

                    <!-- Quick Filter Tabs -->
                    <div class="flex items-center gap-1 text-[11px] font-bold">
                        <button
                            type="button"
                            onclick="setFilterMode('all')"
                            id="filterTabAll"
                            class="flex-1 py-1 px-2 rounded-lg bg-slate-900 text-white text-center transition-all text-[11px]"
                        >
                            All
                        </button>
                        <button
                            type="button"
                            onclick="setFilterMode('unread')"
                            id="filterTabUnread"
                            class="flex-1 py-1 px-2 rounded-lg bg-slate-200/80 hover:bg-slate-200 text-slate-700 text-center transition-all text-[11px]"
                        >
                            Unread <span id="unreadTabCount" class="hidden ml-0.5 bg-emerald-600 text-white rounded-full px-1 text-[9px]">0</span>
                        </button>
                        <button
                            type="button"
                            onclick="setFilterMode('orders')"
                            id="filterTabOrders"
                            class="flex-1 py-1 px-2 rounded-lg bg-slate-200/80 hover:bg-slate-200 text-slate-700 text-center transition-all text-[11px]"
                        >
                            Orders
                        </button>
                    </div>
                </div>

                <!-- Chat Items Scrollable List -->
                <div id="chatListContainer" class="flex-1 overflow-y-auto divide-y divide-slate-100 bg-white min-h-0">
                    @forelse ($chats as $chat)
                        <div
                            id="chat-item-{{ $chat['phone'] }}"
                            onclick="openChat('{{ $chat['phone'] }}')"
                            data-phone="{{ $chat['phone'] }}"
                            data-name="{{ strtolower($chat['name']) }}"
                            data-order="{{ strtolower($chat['order_number'] ?? '') }}"
                            data-unread="{{ $chat['unread_count'] }}"
                            class="chat-conversation-item p-3 hover:bg-slate-50 cursor-pointer transition-all border-l-4 border-transparent flex items-start gap-2.5 select-none"
                        >
                            <!-- Avatar with Customer Initial -->
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                {{ strtoupper(substr($chat['name'] ?: 'C', 0, 1)) }}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <h4 class="text-xs font-black text-slate-900 truncate">
                                        {{ $chat['name'] }}
                                    </h4>
                                    <span class="chat-time-text text-[10px] text-slate-400 font-medium shrink-0">
                                        {{ $chat['last_message']['time'] ?: $chat['time_formatted'] }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-0.5">
                                    <span class="font-mono text-[10px] text-slate-400">+91 {{ $chat['phone'] }}</span>
                                    @if ($chat['order_number'])
                                        <span class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.2 rounded border border-slate-200 truncate">
                                            #{{ $chat['order_number'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="chat-snippet-row flex items-center justify-between gap-2 mt-1">
                                    <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                        @if ($chat['last_message']['from_me'])
                                            <span class="text-sky-500 text-[10px]" title="Delivered"><i class="fa-solid fa-check-double"></i></span>
                                        @endif
                                        <span class="chat-snippet-text">{{ Str::limit($chat['last_message']['text'], 36) }}</span>
                                    </p>

                                    @if ($chat['unread_count'] > 0)
                                        <span class="unread-badge inline-flex items-center justify-center min-w-[18px] h-[18px] bg-emerald-500 text-white text-[10px] font-black rounded-full px-1 shrink-0 shadow-sm animate-bounce">
                                            {{ $chat['unread_count'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 space-y-2">
                            <i class="fa-regular fa-comments text-3xl text-slate-300"></i>
                            <p class="text-xs font-bold">No WhatsApp conversations yet.</p>
                            <p class="text-[11px]">When orders are booked or customers message, they appear here.</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- ------------------- RIGHT COLUMN: ACTIVE CHAT THREAD (8 Cols) ------------------- --}}
            <div class="lg:col-span-8 flex flex-col h-full bg-[#efeae2] relative overflow-hidden">

                <!-- 1. EMPTY STATE (When no chat is selected) -->
                <div id="chatEmptyState" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50 space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl shadow-sm border border-emerald-200">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="max-w-md space-y-1">
                        <h2 class="text-base font-black text-slate-900 font-heading">
                            WhatsApp Live Customer Chat
                        </h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Click any customer on the left to see sent invoices, customer replies, and chat in real-time.
                        </p>
                    </div>

                    @if (!empty($chats))
                        <button
                            type="button"
                            onclick="openChat('{{ $chats[0]['phone'] }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all"
                        >
                            <i class="fa-solid fa-comment-dots"></i>
                            <span>Open Latest Chat (+91 {{ $chats[0]['phone'] }})</span>
                        </button>
                    @endif
                </div>

                <!-- 2. ACTIVE CHAT ROOM (Pinned Flex Container) -->
                <div id="chatActiveRoom" class="hidden flex-1 flex flex-col h-full min-h-0 overflow-hidden">

                    <!-- A. Pinned Chat Header (shrink-0) -->
                    <div class="p-3 bg-white border-b border-slate-200 flex items-center justify-between gap-3 shadow-xs shrink-0 z-10">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div id="headerAvatar" class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                C
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 id="headerCustomerName" class="text-xs sm:text-sm font-black text-slate-900 truncate">
                                        Customer Name
                                    </h3>
                                    <span id="headerOrderStatusBadge" class="hidden text-[10px] px-2 py-0.5 rounded-full font-bold"></span>
                                </div>

                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span id="headerPhone" class="font-mono font-bold text-slate-700 text-[11px]">+91 0000000000</span>
                                    <span class="text-slate-300">•</span>
                                    <span id="headerCity" class="text-[11px] truncate">City</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Direct View Order Button -->
                            <a
                                id="headerOrderViewBtn"
                                href="#"
                                target="_blank"
                                class="hidden inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-all border border-slate-200"
                            >
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-500"></i>
                                <span id="headerOrderNumberText">View Order</span>
                            </a>

                            <!-- Jump to Reply Input -->
                            <button
                                type="button"
                                onclick="focusChatInput()"
                                title="Jump to Chat Reply Box"
                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold border border-emerald-200 transition-colors flex items-center gap-1"
                            >
                                <i class="fa-solid fa-pen text-[10px]"></i>
                                <span>Reply</span>
                            </button>

                            <!-- Refresh Chat Button -->
                            <button
                                type="button"
                                onclick="refreshCurrentChat(true)"
                                title="Refresh this conversation"
                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold border border-slate-200 transition-colors"
                            >
                                <i class="fa-solid fa-rotate-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- B. Scrollable Messages Stream (flex-1 min-h-0 overflow-y-auto) -->
                    <div
                        id="chatMessagesStream"
                        class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0 bg-[#efeae2]"
                        style="background-image: radial-gradient(rgba(0,0,0,0.04) 1px, transparent 0); background-size: 24px 24px;"
                    >
                        <!-- Messages dynamically injected here by JS -->
                    </div>

                    <!-- C. Quick Response Chips Bar (shrink-0) -->
                    <div class="px-3 py-1 bg-slate-100/90 border-t border-slate-200/80 flex items-center gap-1.5 overflow-x-auto text-[11px] shrink-0 no-scrollbar">
                        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider shrink-0 flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-amber-500 text-[9px]"></i> Quick:
                        </span>
                        <button type="button" onclick="insertQuickMessage('✅ Your order has been received and confirmed! We will update you once packed.')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 text-slate-700 text-[11px] font-medium shrink-0 transition-colors">
                            📦 Confirmed
                        </button>
                        <button type="button" onclick="insertQuickMessage('🚚 Your Diwali parcel is dispatched! Please check your Transport LR receipt slip attached above.')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 text-slate-700 text-[11px] font-medium shrink-0 transition-colors">
                            🚚 Dispatched
                        </button>
                        <button type="button" onclick="insertQuickMessage('💳 Payment received successfully. Thank you for booking with Guru Crackers!')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 text-slate-700 text-[11px] font-medium shrink-0 transition-colors">
                            💳 Payment Received
                        </button>
                        <button type="button" onclick="insertQuickMessage('🪔 Wishing you and your family a Sparkling, Safe & Happy Diwali from Guru Crackers, Sivakasi! ✨')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 text-slate-700 text-[11px] font-medium shrink-0 transition-colors">
                            🪔 Happy Diwali
                        </button>
                    </div>

                    <!-- D. 100% Pinned Chat Message Input Bar (shrink-0) - ALWAYS VISIBLE! -->
                    <div class="p-3 bg-white border-t-2 border-emerald-400 shadow-md shrink-0 z-20">
                        <form id="chatSendForm" onsubmit="handleSendChatMessage(event)" class="space-y-1.5">
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 px-0.5">
                                <span class="flex items-center gap-1.5 text-emerald-800">
                                    <i class="fa-solid fa-paper-plane text-emerald-600 text-xs"></i>
                                    <span>Send Live WhatsApp Reply to <span id="replyToCustomerName" class="underline">Customer</span>:</span>
                                </span>
                                <span class="text-[10px] text-slate-400 font-normal">Press <kbd class="px-1 py-0.2 bg-slate-100 border border-slate-200 rounded font-mono text-slate-600">Enter</kbd> to Send</span>
                            </div>

                            <div class="flex items-end gap-2">
                                <div class="flex-1 bg-slate-50 rounded-xl border-2 border-slate-200 focus-within:border-emerald-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all p-1.5 px-3">
                                    <textarea
                                        id="chatInputText"
                                        rows="2"
                                        placeholder="Type your WhatsApp reply here... (e.g. Hello, your order is ready!)"
                                        onkeydown="handleInputKeydown(event)"
                                        class="w-full bg-transparent text-xs text-slate-900 placeholder-slate-400 border-none focus:outline-none resize-none font-medium leading-relaxed"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    id="chatSendBtn"
                                    class="px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white flex items-center gap-1.5 text-xs font-black shadow-md shadow-emerald-600/30 transition-all shrink-0 uppercase tracking-wider"
                                >
                                    <span>Send</span>
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    @else
        {{-- ===================== 2. DISCONNECTED / SCAN QR CODE STATE ===================== --}}
        <!-- Strict Security Notice Card -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 text-amber-950 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-sm">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-sm shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <div>
                    <div class="font-extrabold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <span>Strict Security Enforcement Active</span>
                        <span class="bg-amber-200 text-amber-900 text-[10px] px-2 py-0.5 rounded-full font-bold">Locked to Shop</span>
                    </div>
                    <div class="text-slate-600 text-[11px] mt-0.5">
                        Only your official Shop WhatsApp number (<strong class="font-mono text-slate-900">{{ $shop->whatsapp_phone ?: $shop->phone }}</strong>) can link to this application. Any other number will be automatically rejected.
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.shop.edit') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-amber-300 text-amber-900 font-bold text-[11px] transition-colors shrink-0">
                <i class="fa-solid fa-gear"></i>
                <span>Edit Shop Number</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border-2 border-emerald-300 shadow-md p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 font-heading">
                            Link Shop WhatsApp Account
                        </h2>
                        <p class="text-xs text-slate-500">
                            Scan this QR code with WhatsApp on your shop phone once to enable free automated delivery and live chat.
                        </p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 text-xs text-amber-700 font-bold bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Waiting for scan...</span>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- QR Code Box -->
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
                    <div id="qrContainer" class="w-64 h-64 bg-white p-3 rounded-2xl shadow-md border border-slate-200 flex items-center justify-center overflow-hidden relative">
                        @if (!empty($status['qr']))
                            <img src="{{ $status['qr'] }}" alt="WhatsApp QR Code" id="liveQrImage" class="w-full h-full object-contain">
                        @else
                            <div class="text-center text-slate-400 space-y-2">
                                <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-600"></i>
                                <p class="text-xs font-bold">Generating QR Code...</p>
                            </div>
                        @endif
                    </div>

                    <div class="text-xs font-bold text-slate-700 mt-3 flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                        <span>Scan from Authorized Phone: {{ $shop->whatsapp_phone ?: $shop->phone }}</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Auto-refreshing every 5 seconds.
                    </p>
                </div>

                <!-- Instructions -->
                <div class="space-y-4 text-xs">
                    <div class="font-extrabold text-sm text-slate-900 font-heading border-b border-slate-100 pb-2">
                        How to Connect in 3 Simple Steps:
                    </div>

                    <ol class="space-y-3.5 text-slate-600">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center shrink-0 text-xs">1</span>
                            <div>
                                <strong class="text-slate-800">Open WhatsApp</strong> on your authorized shop phone (<strong>{{ $shop->whatsapp_phone ?: $shop->phone }}</strong>).
                                <div class="text-slate-400 text-[11px]">உங்கள் கடைக் கைபேசியில் WhatsApp-ஐத் திறக்கவும்.</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center shrink-0 text-xs">2</span>
                            <div>
                                Tap <strong class="text-slate-800">Menu (3 dots)</strong> or <strong class="text-slate-800">Settings</strong> &rarr; Select <strong class="text-emerald-700">Linked Devices</strong> (இணைக்கப்பட்ட சாதனங்கள்).
                                <div class="text-slate-400 text-[11px]">அமைப்புகளில் Linked Devices என்பதைத் தேர்வு செய்யவும்.</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center shrink-0 text-xs">3</span>
                            <div>
                                Tap <strong class="text-emerald-700">"Link a Device"</strong> and point your camera at the QR code on the left!
                                <div class="text-slate-400 text-[11px]">"Link a Device" கிளிக் செய்து QR Code-ஐ ஸ்கேன் செய்யவும்!</div>
                            </div>
                        </li>
                    </ol>

                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-[11px] text-emerald-800">
                        <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                        <strong>100% Safe & Authorized:</strong> Only the official shop phone can connect. All unauthorized scans are rejected instantly.
                    </div>
                </div>
            </div>
        </div>

        <!-- Polling QR Code Status -->
        <script>
            let qrPollTimer = setInterval(checkStatus, 5000);

            async function checkStatus() {
                try {
                    const res = await fetch("{{ route('admin.whatsapp.status') }}");
                    const data = await res.json();

                    if (data.connected) {
                        clearInterval(qrPollTimer);
                        window.location.reload();
                        return;
                    }

                    if (data.qr) {
                        const img = document.getElementById('liveQrImage');
                        if (img) {
                            img.src = data.qr;
                        } else {
                            const container = document.getElementById('qrContainer');
                            if (container) {
                                container.innerHTML = `<img src="${data.qr}" alt="WhatsApp QR Code" id="liveQrImage" class="w-full h-full object-contain">`;
                            }
                        }
                    }
                } catch (e) {}
            }
        </script>
    @endif

</div>

@if ($status['connected'])
{{-- ========================================================================= --}}
{{--                      REAL-TIME CHAT JAVASCRIPT LOGIC                       --}}
{{-- ========================================================================= --}}
<script>
    let activePhone = null;
    let activeFilter = 'all';
    let isSending = false;
    let pollInterval = null;

    // Toggle Direct Test Message Box
    function toggleTestMessageBox() {
        const box = document.getElementById('testMessageBox');
        if (box) box.classList.toggle('hidden');
    }

    // Focus into chat input
    function focusChatInput() {
        const input = document.getElementById('chatInputText');
        if (input) {
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Insert quick chip text into textarea
    function insertQuickMessage(text) {
        const input = document.getElementById('chatInputText');
        if (!input) return;
        input.value = text;
        input.focus();
    }

    // Keydown handler: Enter to send, Shift+Enter for newline
    function handleInputKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendChatMessage(e);
        }
    }

    // Set filter mode: 'all', 'unread', 'orders'
    function setFilterMode(mode) {
        activeFilter = mode;

        ['all', 'unread', 'orders'].forEach(m => {
            const btn = document.getElementById('filterTab' + m.charAt(0).toUpperCase() + m.slice(1));
            if (!btn) return;
            if (m === mode) {
                btn.className = 'flex-1 py-1 px-2 rounded-lg bg-slate-900 text-white text-center transition-all text-[11px] font-bold';
            } else {
                btn.className = 'flex-1 py-1 px-2 rounded-lg bg-slate-200/80 hover:bg-slate-200 text-slate-700 text-center transition-all text-[11px] font-bold';
            }
        });

        applyChatFilters();
    }

    // Search input filtering
    function filterChats(query) {
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !query);
        }
        applyChatFilters();
    }

    function clearChatSearch() {
        const input = document.getElementById('chatSearchInput');
        if (input) {
            input.value = '';
            filterChats('');
        }
    }

    function applyChatFilters() {
        const search = (document.getElementById('chatSearchInput')?.value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.chat-conversation-item');

        items.forEach(item => {
            const phone = item.getAttribute('data-phone') || '';
            const name = item.getAttribute('data-name') || '';
            const order = item.getAttribute('data-order') || '';
            const unread = parseInt(item.getAttribute('data-unread') || '0', 10);

            // Filter by search
            const matchSearch = !search || phone.includes(search) || name.includes(search) || order.includes(search);

            // Filter by tab
            let matchTab = true;
            if (activeFilter === 'unread') {
                matchTab = unread > 0;
            } else if (activeFilter === 'orders') {
                matchTab = Boolean(order);
            }

            item.classList.toggle('hidden', !(matchSearch && matchTab));
        });
    }

    // Format WhatsApp markdown into HTML
    function formatWhatsAppMarkdown(text) {
        if (!text) return '';

        // Escape HTML
        let escaped = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Format bold *text*
        escaped = escaped.replace(/\*([^*]+)\*/g, '<strong class="font-black text-slate-900">$1</strong>');

        // Format italic _text_
        escaped = escaped.replace(/_([^_]+)_/g, '<em class="italic">$1</em>');

        // Format code `code`
        escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-black/10 px-1 py-0.5 rounded font-mono text-[11px]">$1</code>');

        // Convert URLs to clickable links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        escaped = escaped.replace(urlRegex, '<a href="$1" target="_blank" class="text-sky-700 underline font-bold hover:text-sky-900 break-all">$1</a>');

        // Convert newlines to <br>
        return escaped.replace(/\n/g, '<br>');
    }

    // Scroll chat message stream smoothly to the bottom
    function scrollToBottom() {
        const stream = document.getElementById('chatMessagesStream');
        if (!stream) return;
        stream.scrollTop = stream.scrollHeight;
        setTimeout(() => { stream.scrollTop = stream.scrollHeight; }, 60);
        setTimeout(() => { stream.scrollTop = stream.scrollHeight; }, 200);
    }

    let currentRenderedHash = '';

    // Open chat conversation for a customer phone
    async function openChat(phone) {
        activePhone = phone;
        currentRenderedHash = '';

        // Highlight selected chat item in left sidebar
        document.querySelectorAll('.chat-conversation-item').forEach(el => {
            el.classList.remove('bg-emerald-50/70', 'border-emerald-600', 'font-bold');
            el.classList.add('border-transparent');
        });

        const selectedItem = document.getElementById('chat-item-' + phone);
        if (selectedItem) {
            selectedItem.classList.add('bg-emerald-50/70', 'border-emerald-600');
            selectedItem.classList.remove('border-transparent');

            // Clear unread badge locally
            const unreadBadge = selectedItem.querySelector('.unread-badge');
            if (unreadBadge) unreadBadge.remove();
            selectedItem.setAttribute('data-unread', '0');
        }

        // Show active chat room, hide empty state
        document.getElementById('chatEmptyState')?.classList.add('hidden');
        document.getElementById('chatActiveRoom')?.classList.remove('hidden');

        // Fetch messages and render
        await refreshCurrentChat(true);
    }

    // Refresh active chat messages
    async function refreshCurrentChat(isManual = false) {
        if (!activePhone) return;

        const stream = document.getElementById('chatMessagesStream');

        try {
            const res = await fetch(`{{ route('admin.whatsapp.messages') }}?phone=${encodeURIComponent(activePhone)}`);
            const data = await res.json();

            if (!data.success) return;

            const customer = data.customer;
            const messages = data.messages;

            // Generate content hash to avoid unnecessary DOM re-rendering/flicker
            const contentHash = messages.map(m => `${m.id}_${m.time}_${m.message_text}_${m.media_url || ''}`).join('|');
            if (!isManual && contentHash === currentRenderedHash) {
                return; // Nothing changed, skip DOM re-render!
            }
            currentRenderedHash = contentHash;

            // Update Header
            document.getElementById('headerCustomerName').innerText = customer.name;
            document.getElementById('replyToCustomerName').innerText = customer.name;
            document.getElementById('headerAvatar').innerText = (customer.name || 'C').charAt(0).toUpperCase();
            document.getElementById('headerPhone').innerText = customer.formatted_phone;
            document.getElementById('headerCity').innerText = customer.order ? `${customer.order.city || ''} ${customer.order.state ? '(' + customer.order.state + ')' : ''}` : 'Customer';

            // Order button & status badge
            const orderViewBtn = document.getElementById('headerOrderViewBtn');
            const orderNumberText = document.getElementById('headerOrderNumberText');
            const orderStatusBadge = document.getElementById('headerOrderStatusBadge');

            if (customer.order) {
                orderViewBtn.classList.remove('hidden');
                orderViewBtn.href = customer.order.view_url;
                orderNumberText.innerText = `#${customer.order.order_number} (${customer.order.total_amount_formatted})`;

                orderStatusBadge.classList.remove('hidden');
                if (customer.order.is_dispatched) {
                    orderStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-800';
                    orderStatusBadge.innerText = '🚚 Dispatched';
                } else if (customer.order.payment_status === 'confirmed' || customer.order.payment_status === 'paid') {
                    orderStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800';
                    orderStatusBadge.innerText = '💳 Paid / Confirmed';
                } else {
                    orderStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800';
                    orderStatusBadge.innerText = '⏳ Pending Payment';
                }
            } else {
                orderViewBtn.classList.add('hidden');
                orderStatusBadge.classList.add('hidden');
            }

            // Render Messages
            let html = '';
            let lastDate = '';

            messages.forEach(msg => {
                // Add Date Divider if new date
                if (msg.date && msg.date !== lastDate) {
                    html += `
                        <div class="flex justify-center my-3">
                            <span class="text-[10px] font-bold bg-white/90 backdrop-blur-xs text-slate-600 px-3 py-1 rounded-full shadow-xs border border-slate-200 uppercase tracking-wider">
                                ${msg.is_today ? 'Today' : msg.date}
                            </span>
                        </div>
                    `;
                    lastDate = msg.date;
                }

                if (msg.from_me) {
                    // Outgoing Message Bubble (Right Side - Authentic WhatsApp Green)
                    const isImage = msg.message_type === 'image' || (msg.media_url && /\.(jpg|jpeg|png|webp|gif)$/i.test(msg.media_url));
                    const isDoc = msg.message_type === 'document' || msg.media_filename || (msg.media_url && msg.media_url.toLowerCase().endsWith('.pdf'));

                    const showCaption = msg.message_text && 
                        msg.message_text !== '📷 Photo' && 
                        msg.message_text !== msg.media_filename && 
                        msg.message_text !== `📄 ${msg.media_filename}`;

                    html += `
                        <div class="flex flex-col items-end mb-3">
                            <div class="text-[10px] font-bold text-emerald-800 mb-0.5 pr-1 flex items-center gap-1">
                                <span>📤 You (Shop Admin)</span>
                            </div>
                            <div class="max-w-[85%] sm:max-w-[75%] bg-[#dcf8c6] text-slate-900 border border-emerald-300/60 rounded-2xl rounded-tr-xs p-3 shadow-sm space-y-1">
                                ${isImage && msg.media_url ? `
                                    <div class="rounded-xl overflow-hidden border border-emerald-400/40 mb-1.5 bg-black/5">
                                        <a href="${msg.media_url}" target="_blank" rel="noopener noreferrer" class="group relative block cursor-pointer" title="Click to view full photo">
                                            <img src="${msg.media_url}" alt="WhatsApp Photo" class="max-h-72 w-full object-cover rounded-xl transition duration-200 group-hover:scale-[1.01]" loading="lazy" />
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/15 transition-colors flex items-center justify-center">
                                                <span class="opacity-0 group-hover:opacity-100 bg-black/70 text-white text-[10px] px-2.5 py-1 rounded-full backdrop-blur-xs transition-opacity flex items-center gap-1 font-semibold shadow-md">
                                                    <i class="fa-solid fa-up-right-and-down-left-from-center text-[9px]"></i> View Full
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                ` : ''}

                                ${isDoc ? `
                                    <a href="${msg.media_url || '#'}" ${msg.media_url ? 'target="_blank"' : ''} class="flex items-center gap-2 p-2 bg-emerald-700/10 hover:bg-emerald-700/20 rounded-xl mb-1.5 border border-emerald-600/20 text-xs transition">
                                        <i class="fa-solid fa-file-pdf text-rose-600 text-lg"></i>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-[11px] text-slate-900 truncate">${msg.media_filename || 'PDF Document'}</div>
                                            <div class="text-[9px] text-slate-500 uppercase font-bold flex items-center gap-1">
                                                <span>PDF Document</span>
                                                ${msg.media_url ? '<i class="fa-solid fa-arrow-down text-[8px] text-emerald-800"></i>' : ''}
                                            </div>
                                        </div>
                                    </a>
                                ` : ''}

                                ${showCaption ? `
                                    <div class="text-xs font-normal leading-relaxed break-words text-slate-900">
                                        ${formatWhatsAppMarkdown(msg.message_text)}
                                    </div>
                                ` : (!isImage && !isDoc ? `
                                    <div class="text-xs font-normal leading-relaxed break-words text-slate-900">
                                        ${formatWhatsAppMarkdown(msg.message_text)}
                                    </div>
                                ` : '')}

                                <div class="flex items-center justify-end gap-1 text-[10px] text-slate-500 pt-0.5 select-none font-medium">
                                    <span>${msg.time}</span>
                                    <span class="text-sky-600 text-[11px]" title="Delivered"><i class="fa-solid fa-check-double"></i></span>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Incoming Message Bubble (Left Side - Clean White with Emerald Accent)
                    const isImage = msg.message_type === 'image' || (msg.media_url && /\.(jpg|jpeg|png|webp|gif)$/i.test(msg.media_url));
                    const isDoc = msg.message_type === 'document' || (msg.media_filename && msg.media_filename.toLowerCase().endsWith('.pdf'));
                    const isAudio = msg.message_type === 'audio' || (msg.media_url && /\.(mp3|ogg|wav|m4a|aac)$/i.test(msg.media_url));

                    const showCaption = msg.message_text && 
                        msg.message_text !== '📷 Photo' && 
                        msg.message_text !== msg.media_filename && 
                        msg.message_text !== `📄 ${msg.media_filename}`;

                    html += `
                        <div class="flex flex-col items-start mb-3">
                            <div class="text-[10px] font-black text-emerald-700 mb-0.5 pl-1 flex items-center gap-1">
                                <span>📥 Customer (${customer.name || msg.sender_name || 'Customer'})</span>
                            </div>
                            <div class="max-w-[85%] sm:max-w-[75%] bg-white text-slate-900 border-l-4 border-l-emerald-500 border border-slate-200 rounded-2xl rounded-tl-xs p-3 shadow-sm space-y-1">
                                ${isImage && msg.media_url ? `
                                    <div class="rounded-xl overflow-hidden border border-slate-200/80 mb-1.5 bg-slate-50">
                                        <a href="${msg.media_url}" target="_blank" rel="noopener noreferrer" class="group relative block cursor-pointer" title="Click to view full photo">
                                            <img src="${msg.media_url}" alt="WhatsApp Photo" class="max-h-72 w-full object-cover rounded-xl transition duration-200 group-hover:scale-[1.01]" loading="lazy" />
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/15 transition-colors flex items-center justify-center">
                                                <span class="opacity-0 group-hover:opacity-100 bg-black/70 text-white text-[10px] px-2.5 py-1 rounded-full backdrop-blur-xs transition-opacity flex items-center gap-1 font-semibold shadow-md">
                                                    <i class="fa-solid fa-up-right-and-down-left-from-center text-[9px]"></i> View Full
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                ` : ''}

                                ${isDoc ? `
                                    <a href="${msg.media_url || '#'}" ${msg.media_url ? 'target="_blank"' : ''} class="flex items-center gap-2.5 p-2 bg-slate-50 hover:bg-slate-100 rounded-xl mb-1.5 border border-slate-200 text-xs transition">
                                        <i class="fa-solid fa-file-pdf text-rose-600 text-xl"></i>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-[11px] text-slate-900 truncate">${msg.media_filename || 'Document.pdf'}</div>
                                            <div class="text-[9px] text-slate-500 uppercase font-bold flex items-center gap-1">
                                                <span>PDF Document</span>
                                                ${msg.media_url ? '<i class="fa-solid fa-arrow-down text-[8px] text-emerald-600"></i>' : ''}
                                            </div>
                                        </div>
                                    </a>
                                ` : ''}

                                ${isAudio && msg.media_url ? `
                                    <audio controls class="w-full my-1 rounded">
                                        <source src="${msg.media_url}">
                                    </audio>
                                ` : ''}

                                ${showCaption ? `
                                    <div class="text-xs font-normal leading-relaxed break-words text-slate-900">
                                        ${formatWhatsAppMarkdown(msg.message_text)}
                                    </div>
                                ` : (!isImage && !isDoc && !isAudio ? `
                                    <div class="text-xs font-normal leading-relaxed break-words text-slate-900">
                                        ${formatWhatsAppMarkdown(msg.message_text)}
                                    </div>
                                ` : '')}

                                <div class="flex items-center justify-end text-[10px] text-slate-400 pt-0.5 select-none font-medium">
                                    <span>${msg.time}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            if (messages.length === 0) {
                html = `
                    <div class="p-8 text-center text-slate-400 space-y-2">
                        <i class="fa-regular fa-comment-dots text-3xl text-slate-300"></i>
                        <p class="text-xs font-bold">No messages in this chat yet.</p>
                        <p class="text-[11px]">Type below to send a direct WhatsApp message to ${customer.name}.</p>
                    </div>
                `;
            }

            stream.innerHTML = html;

            // Always scroll to bottom so latest messages and customer replies are immediately visible!
            scrollToBottom();

        } catch (e) {
            console.error('Failed to load chat messages:', e);
        }
    }

    // Send WhatsApp Chat Message
    async function handleSendChatMessage(e) {
        if (e) e.preventDefault();
        if (isSending || !activePhone) return;

        const input = document.getElementById('chatInputText');
        const text = input.value.trim();
        if (!text) return;

        isSending = true;
        const btn = document.getElementById('chatSendBtn');
        const origBtnContent = btn.innerHTML;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Sending...</span>`;
        btn.disabled = true;

        // Optimistically render bubble
        const stream = document.getElementById('chatMessagesStream');
        const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const tempBubble = document.createElement('div');
        tempBubble.className = "flex flex-col items-end mb-3 opacity-70";
        tempBubble.innerHTML = `
            <div class="text-[10px] font-bold text-emerald-800 mb-0.5 pr-1">📤 You (Shop Admin)</div>
            <div class="max-w-[85%] sm:max-w-[75%] bg-[#dcf8c6] text-slate-900 border border-emerald-300/60 rounded-2xl rounded-tr-xs p-3 shadow-sm space-y-1">
                <div class="text-xs font-normal leading-relaxed break-words text-slate-900">
                    ${formatWhatsAppMarkdown(text)}
                </div>
                <div class="flex items-center justify-end gap-1 text-[10px] text-slate-500 pt-0.5 font-medium">
                    <span>${nowTime}</span>
                    <span class="text-slate-400"><i class="fa-regular fa-clock"></i></span>
                </div>
            </div>
        `;
        stream.appendChild(tempBubble);
        scrollToBottom();
        input.value = '';

        try {
            const res = await fetch(`{{ route('admin.whatsapp.chat.send') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: activePhone,
                    message: text
                })
            });

            const data = await res.json();

            if (data.success) {
                tempBubble.classList.remove('opacity-70');
                const tick = tempBubble.querySelector('.fa-clock');
                if (tick) {
                    tick.className = 'fa-solid fa-check-double text-sky-600';
                }

                // Refresh background conversations list
                pollBackgroundChats();
            } else {
                tempBubble.classList.add('border-rose-300', 'bg-rose-50');
                alert(data.error || 'Failed to deliver message via WhatsApp.');
            }
        } catch (err) {
            alert('Network error while sending WhatsApp message.');
        } finally {
            isSending = false;
            btn.innerHTML = origBtnContent;
            btn.disabled = false;
            input.focus();
        }
    }

    // Background poller for live customer messages and chat list updates
    async function pollBackgroundChats() {
        try {
            // 1. If an active conversation is open, smoothly poll it for instant incoming customer replies
            if (activePhone) {
                refreshCurrentChat(false);
            }

            // 2. Fetch updated chats list
            const res = await fetch(`{{ route('admin.whatsapp.chats') }}`);
            const data = await res.json();

            if (data.success && data.chats) {
                // Update Unread Counter on Tab
                const unreadTab = document.getElementById('unreadTabCount');
                if (unreadTab) {
                    if (data.total_unread > 0) {
                        unreadTab.innerText = data.total_unread;
                        unreadTab.classList.remove('hidden');
                    } else {
                        unreadTab.classList.add('hidden');
                    }
                }

                // Update Left Sidebar snippets, times, and unread badges in real-time
                data.chats.forEach(chat => {
                    const item = document.getElementById('chat-item-' + chat.phone);
                    if (item) {
                        item.setAttribute('data-unread', chat.unread_count);

                        const snippetText = item.querySelector('.chat-snippet-text');
                        if (snippetText && chat.last_message?.text) {
                            snippetText.innerText = chat.last_message.text.substring(0, 36);
                        }

                        const timeText = item.querySelector('.chat-time-text');
                        if (timeText && (chat.last_message?.time || chat.time_formatted)) {
                            timeText.innerText = chat.last_message.time || chat.time_formatted;
                        }

                        // Update or clear unread badge
                        let badge = item.querySelector('.unread-badge');
                        if (chat.unread_count > 0 && chat.phone !== activePhone) {
                            if (!badge) {
                                badge = document.createElement('span');
                                badge.className = 'unread-badge inline-flex items-center justify-center min-w-[18px] h-[18px] bg-emerald-500 text-white text-[10px] font-black rounded-full px-1 shrink-0 shadow-sm animate-bounce';
                                item.querySelector('.chat-snippet-row')?.appendChild(badge);
                            }
                            badge.innerText = chat.unread_count;
                        } else if (badge && chat.phone === activePhone) {
                            badge.remove();
                        }
                    }
                });
            }
        } catch (e) {}
    }

    // Start Real-Time background poller every 3.5 seconds
    pollInterval = setInterval(pollBackgroundChats, 3500);

    // Auto-open first chat if on desktop
    document.addEventListener('DOMContentLoaded', () => {
        const firstChat = document.querySelector('.chat-conversation-item');
        if (firstChat && window.innerWidth >= 1024) {
            const phone = firstChat.getAttribute('data-phone');
            if (phone) openChat(phone);
        }
    });
</script>
@endif

@endsection
