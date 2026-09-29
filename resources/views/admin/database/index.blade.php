@extends('layouts.app')

@section('title', 'Database Manager - Admin')

@section('content')
<div class="mx-auto space-y-5 pb-12">

    {{-- ═══════════════════ HEADER BANNER ═══════════════════ --}}
    <div class="relative bg-gradient-to-r from-slate-900 via-red-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl overflow-hidden border border-red-900/40">
        <div class="absolute -right-10 -bottom-8 w-72 h-72 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/4 -top-16 w-52 h-52 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-500/20 text-red-300 border border-red-500/30 tracking-wide uppercase mb-3">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                    Danger Zone — Admin Only
                </span>
                <h1 class="text-2xl sm:text-3xl font-black font-heading tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-database text-red-400"></i>
                    <span>Database Manager</span>
                </h1>
                <p class="text-sm text-slate-400 mt-1 max-w-xl">
                    Backup your full database, clear specific tables, and email backup files. Every clear action auto-creates a backup first for safety.
                </p>
            </div>
            <div class="flex flex-col items-start md:items-end gap-2 shrink-0">
                <a href="{{ route('admin.audit_logs.index') }}?module=database"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                    <span>View DB Action Logs</span>
                </a>
                <p class="text-[11px] text-slate-500 font-mono">DB: {{ config('database.connections.mysql.database') }}</p>
            </div>
        </div>
    </div>



    {{-- ═══════════════════ MAIN GRID ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ───── LEFT COLUMN: Table List & Quick Actions ───── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ── TABLE OVERVIEW CARD ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center text-sm">
                            <i class="fa-solid fa-table"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-slate-900 text-sm">Database Tables</h2>
                            <p class="text-[11px] text-slate-500">{{ count($tables) }} tables • Select to clear</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleSelectAll()"
                        class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                        Select All Clearable
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($tables as $tname => $tmeta)
                        <div class="flex items-center px-5 py-3.5 hover:bg-slate-50/60 transition-colors gap-3">
                            {{-- Checkbox --}}
                            <div class="shrink-0">
                                @if ($tmeta['clearable'])
                                    <input type="checkbox"
                                        id="chk_{{ $tname }}"
                                        name="table_check"
                                        value="{{ $tname }}"
                                        class="clearable-table-checkbox w-4 h-4 accent-rose-600 cursor-pointer rounded"
                                        onchange="updateClearButton()">
                                @else
                                    <div class="w-4 h-4 rounded border border-slate-200 bg-slate-100 flex items-center justify-center"
                                        title="Protected — cannot be cleared">
                                        <i class="fa-solid fa-lock text-[8px] text-slate-400"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Table info --}}
                            <label for="{{ $tmeta['clearable'] ? 'chk_'.$tname : '' }}"
                                class="flex-1 flex items-center justify-between {{ $tmeta['clearable'] ? 'cursor-pointer' : '' }} gap-3">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">{{ $tmeta['label'] }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $tname }}</div>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <span class="text-xs font-black {{ $tmeta['count'] > 0 ? 'text-slate-800' : 'text-slate-400' }}">
                                        {{ number_format($tmeta['count']) }}
                                        <span class="text-[10px] font-medium text-slate-500 ml-0.5">rows</span>
                                    </span>
                                    @if ($tmeta['protected'])
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                            Protected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            Clearable
                                        </span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                {{-- Clear Selected Footer --}}
                <div class="px-5 py-4 bg-rose-50/60 border-t border-rose-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-rose-800">Clear Selected Tables</p>
                            <p class="text-[11px] text-rose-600">A backup will be auto-created before clearing.</p>
                        </div>
                        <button type="button" id="clearSelectedBtn" onclick="openClearSelectedModal()"
                            disabled
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-trash-can"></i>
                            <span id="clearSelectedBtnLabel">Clear Selected</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── BACKUP FILES LIST ── --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center text-sm">
                            <i class="fa-solid fa-file-zipper"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-slate-900 text-sm">Saved Backups</h2>
                            <p class="text-[11px] text-slate-500">{{ count($backupFiles) }} backup(s) on server</p>
                        </div>
                    </div>
                </div>

                @if (count($backupFiles) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach ($backupFiles as $bf)
                            <div class="flex items-center px-5 py-3.5 hover:bg-slate-50/60 transition-colors gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-code text-slate-600 text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-900 text-xs truncate">{{ $bf['name'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $bf['size_display'] }} • {{ $bf['modified_fmt'] }}</div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    {{-- Download --}}
                                    <a href="{{ route('admin.database.backup.download', $bf['name']) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 text-[11px] font-bold transition-all border border-slate-200">
                                        <i class="fa-solid fa-download text-[10px]"></i>
                                        <span>Download</span>
                                    </a>
                                    {{-- Email this backup --}}
                                    <button type="button" onclick="openEmailModal('{{ $bf['name'] }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 text-[11px] font-bold transition-all border border-slate-200 cursor-pointer">
                                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                        <span>Email</span>
                                    </button>
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.database.backup.delete', $bf['name']) }}"
                                        onsubmit="return confirm('Delete this backup file: {{ $bf['name'] }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-700 text-[11px] font-bold transition-all border border-slate-200 cursor-pointer">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2 block"></i>
                        <p class="text-xs font-bold text-slate-600">No backups yet</p>
                        <p class="text-[11px] mt-0.5">Create your first backup from the panel on the right.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- ───── RIGHT COLUMN: Action Panels ───── --}}
        <div class="space-y-5">

            {{-- ── BACKUP NOW ── --}}
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-emerald-900 text-sm">Full DB Backup</h3>
                            <p class="text-[11px] text-emerald-700">Export complete MySQL dump</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <ul class="text-[11px] text-slate-600 space-y-1.5">
                        <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-500 text-[10px]"></i> All {{ count($tables) }} tables included</li>
                        <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-500 text-[10px]"></i> Saved to server storage</li>
                        <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-500 text-[10px]"></i> Download or email anytime</li>
                    </ul>
                    <form method="POST" action="{{ route('admin.database.backup') }}">
                        @csrf
                        <input type="hidden" name="download" value="0">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Create Full Backup Now</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.database.backup') }}">
                        @csrf
                        <input type="hidden" name="download" value="1">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs transition-all cursor-pointer border border-slate-200">
                            <i class="fa-solid fa-download text-[11px]"></i>
                            <span>Backup & Download Directly</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── EMAIL BACKUP ── --}}
            <div class="bg-white rounded-2xl border border-blue-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shadow-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-blue-900 text-sm">Email Backup</h3>
                            <p class="text-[11px] text-blue-700">Send backup to any email address</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.database.email_backup') }}" class="p-5 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Send To Email</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="email" name="email" value="{{ $adminEmail }}" required
                                placeholder="admin@gurucrackers.com"
                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-medium transition-all">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Creates a fresh backup then emails it as attachment.</p>
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Backup & Send Email</span>
                    </button>
                </form>
            </div>

            {{-- ── CLEAR ALL DATA ── --}}
            <div class="bg-white rounded-2xl border-2 border-rose-300 shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-rose-50 to-red-50 border-b border-rose-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-base shadow-sm">
                            <i class="fa-solid fa-skull-crossbones"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-rose-900 text-sm">Clear ALL Data</h3>
                            <p class="text-[11px] text-rose-700">Wipes all clearable tables (backup first!)</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-[11px] text-rose-800 space-y-1">
                        <div class="font-black flex items-center gap-1.5 text-rose-900">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Irreversible Action!
                        </div>
                        <div>This will clear: orders, products, categories, messages, audit logs, banners, sessions.</div>
                        <div class="font-bold">Protected tables (users, shops, migrations) are never cleared.</div>
                    </div>
                    <button type="button" onclick="openClearAllModal()"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Clear All Data (With Backup)</span>
                    </button>
                </div>
            </div>

            {{-- ── DB STATS ── --}}
            <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-xs p-5 text-slate-300">
                <h3 class="font-black text-white text-sm mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-slate-400"></i>
                    Quick Stats
                </h3>
                <div class="space-y-2">
                    @php
                        $totalRows = collect($tables)->sum('count');
                        $totalClearable = collect($tables)->where('clearable', true)->sum('count');
                    @endphp
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Total Tables</span>
                        <span class="font-bold text-white">{{ count($tables) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Total Rows</span>
                        <span class="font-bold text-white">{{ number_format($totalRows) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Clearable Rows</span>
                        <span class="font-bold text-rose-400">{{ number_format($totalClearable) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Saved Backups</span>
                        <span class="font-bold text-emerald-400">{{ count($backupFiles) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Database</span>
                        <span class="font-mono font-bold text-sky-400 text-[11px]">{{ config('database.connections.mysql.database') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ═══════════════════ CLEAR SELECTED MODAL ═══════════════════ --}}
<div id="clearSelectedModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-rose-200 overflow-hidden">
        <div class="bg-rose-600 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white text-lg">
                    <i class="fa-solid fa-trash-can"></i>
                </span>
                <div>
                    <h3 class="font-black text-white text-base">Clear Selected Tables</h3>
                    <p class="text-rose-200 text-xs">A backup is created automatically first.</p>
                </div>
            </div>
            <button onclick="closeClearSelectedModal()" class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center text-sm cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.database.clear_tables') }}" class="p-5 space-y-4">
            @csrf
            <div id="selectedTablesSummary" class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-xs text-rose-900 font-medium"></div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 font-medium flex items-start gap-2">
                <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5 shrink-0"></i>
                <span>Auto-backup will be created before any data is removed. You can find it in the Saved Backups section after the operation.</span>
            </div>

            {{-- Hidden table inputs (populated by JS) --}}
            <div id="hiddenTableInputs"></div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Backup Confirmed <span class="text-rose-600">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="backup_confirmed" id="backupConfirmed" value="1" required class="w-4 h-4 accent-rose-600 cursor-pointer">
                    <label for="backupConfirmed" class="text-xs text-slate-700 font-medium cursor-pointer">
                        I understand a backup will be created before clearing.
                    </label>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Type <code class="bg-rose-100 text-rose-700 px-1 py-0.5 rounded font-mono">DELETE</code> to confirm <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="confirm_text" required autocomplete="off"
                    placeholder="Type DELETE in caps"
                    class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/30 font-mono font-bold tracking-widest uppercase">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="button" onclick="closeClearSelectedModal()"
                    class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i>
                    Clear Tables
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════ CLEAR ALL MODAL ═══════════════════ --}}
<div id="clearAllModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-rose-300 overflow-hidden">
        <div class="bg-gradient-to-r from-rose-700 to-red-700 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white text-xl">
                    <i class="fa-solid fa-skull-crossbones"></i>
                </span>
                <div>
                    <h3 class="font-black text-white text-base">Clear ALL Database Data</h3>
                    <p class="text-rose-200 text-xs">This is a very destructive action. Auto-backup first.</p>
                </div>
            </div>
            <button onclick="closeClearAllModal()" class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center text-sm cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.database.clear_all') }}" class="p-5 space-y-4">
            @csrf
            <div class="bg-rose-50 border-2 border-rose-300 rounded-xl p-4 text-xs text-rose-900 space-y-2">
                <div class="font-black text-sm text-rose-800 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    This will wipe ALL clearable tables!
                </div>
                <div>Tables affected: <strong>orders, order_items, products, categories, banners, whatsapp_messages, audit_logs, sessions, cache</strong></div>
                <div class="font-bold text-emerald-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved"></i>
                    Protected (NOT cleared): users, shops, migrations
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 font-medium flex items-start gap-2">
                <i class="fa-solid fa-floppy-disk text-amber-600 mt-0.5 shrink-0"></i>
                <span>A complete database backup will be automatically created before any data is deleted. You can restore from it anytime.</span>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Type exactly: <code class="bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-mono text-[11px]">CLEAR ALL DATA</code> to confirm
                </label>
                <input type="text" name="confirm_text" required autocomplete="off"
                    placeholder="CLEAR ALL DATA"
                    class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-rose-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/30 font-mono font-black tracking-widest uppercase text-center">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="button" onclick="closeClearAllModal()"
                    class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer">
                    Cancel — Go Back
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-gradient-to-r from-rose-700 to-red-700 hover:from-rose-800 hover:to-red-800 text-white font-bold text-xs shadow-md cursor-pointer">
                    <i class="fa-solid fa-skull-crossbones"></i>
                    Clear Everything
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════ EMAIL BACKUP MODAL ═══════════════════ --}}
<div id="emailBackupModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-blue-200 overflow-hidden">
        <div class="bg-blue-600 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white text-xl">
                    <i class="fa-solid fa-paper-plane"></i>
                </span>
                <div>
                    <h3 class="font-black text-white text-base">Email Backup File</h3>
                    <p class="text-blue-200 text-xs" id="emailBackupFilename">—</p>
                </div>
            </div>
            <button onclick="closeEmailModal()" class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center text-sm cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.database.email_backup') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="filename" id="emailModalFilename">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Send Backup To (Registered Admin)</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="email" name="email" value="{{ $adminEmail }}" readonly
                        class="w-full pl-8 pr-3 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-700 font-medium cursor-not-allowed">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">For security, database dumps are sent strictly to your registered administrator email address.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="closeEmailModal()"
                    class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md cursor-pointer">
                    <i class="fa-solid fa-paper-plane"></i>
                    Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ─── SELECT ALL ───
    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.clearable-table-checkbox');
        const allChecked = [...checkboxes].every(c => c.checked);
        checkboxes.forEach(c => c.checked = !allChecked);
        updateClearButton();
    }

    // ─── UPDATE CLEAR BUTTON ───
    function updateClearButton() {
        const checked = document.querySelectorAll('.clearable-table-checkbox:checked');
        const btn = document.getElementById('clearSelectedBtn');
        const label = document.getElementById('clearSelectedBtnLabel');
        if (checked.length > 0) {
            btn.disabled = false;
            label.textContent = 'Clear ' + checked.length + ' Table(s)';
        } else {
            btn.disabled = true;
            label.textContent = 'Clear Selected';
        }
    }

    // ─── CLEAR SELECTED MODAL ───
    function openClearSelectedModal() {
        const checked = [...document.querySelectorAll('.clearable-table-checkbox:checked')];
        if (checked.length === 0) return;

        const names = checked.map(c => c.value);

        // Populate summary
        document.getElementById('selectedTablesSummary').innerHTML =
            `<strong>Tables to clear (${names.length}):</strong><br>` +
            names.map(n => `<code class="bg-rose-200 text-rose-900 px-1 py-0.5 rounded text-[10px] mr-1 font-mono">${n}</code>`).join('');

        // Populate hidden inputs
        const container = document.getElementById('hiddenTableInputs');
        container.innerHTML = names.map(n =>
            `<input type="hidden" name="tables[]" value="${n}">`
        ).join('');

        document.getElementById('clearSelectedModal').classList.remove('hidden');
    }

    function closeClearSelectedModal() {
        document.getElementById('clearSelectedModal').classList.add('hidden');
    }

    // ─── CLEAR ALL MODAL ───
    function openClearAllModal() {
        document.getElementById('clearAllModal').classList.remove('hidden');
    }

    function closeClearAllModal() {
        document.getElementById('clearAllModal').classList.add('hidden');
    }

    // ─── EMAIL BACKUP MODAL ───
    function openEmailModal(filename) {
        document.getElementById('emailModalFilename').value = filename;
        document.getElementById('emailBackupFilename').textContent = filename;
        document.getElementById('emailBackupModal').classList.remove('hidden');
    }

    function closeEmailModal() {
        document.getElementById('emailBackupModal').classList.add('hidden');
    }

    // Close modals on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeClearSelectedModal();
            closeClearAllModal();
            closeEmailModal();
        }
    });
</script>
@endsection
