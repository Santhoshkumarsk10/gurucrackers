<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Audit Trail Report - {{ $shop->name ?? 'Guru Crackers' }}</title>
    <style>
        @page {
            margin: 24px 24px 30px 24px;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #312e81;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .shop-title {
            font-size: 18px;
            font-weight: bold;
            color: #312e81;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .shop-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }

        .report-title-box {
            text-align: right;
        }

        .report-badge {
            background-color: #312e81;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-meta {
            font-size: 8px;
            color: #475569;
            margin-top: 3px;
        }

        /* Filter Summary Pill Bar */
        .filter-table {
            width: 100%;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 10px;
            padding: 5px 8px;
            font-size: 8px;
        }

        /* KPI Summary Grid */
        .kpi-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            border-radius: 4px;
            text-align: center;
        }

        .kpi-label {
            font-size: 7.5px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            letter-spacing: 0.4px;
        }

        .kpi-value {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Audit Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #334155;
            text-align: left;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 4.5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8px;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-module { background-color: #e0e7ff; color: #3730a3; }
        .badge-created { background-color: #dcfce7; color: #166534; }
        .badge-updated { background-color: #e0f2fe; color: #075985; }
        .badge-deleted { background-color: #ffe4e6; color: #9f1239; }
        .badge-status { background-color: #fef3c7; color: #92400e; }
        .badge-dispatch { background-color: #f3e8ff; color: #6b21a8; }
        .badge-login { background-color: #ede9fe; color: #5b21b6; }
        .badge-whatsapp { background-color: #d1fae5; color: #065f46; }
        .badge-default { background-color: #f1f5f9; color: #475569; }

        .diff-box {
            margin-top: 2px;
            padding: 3px 4px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 7px;
            color: #334155;
            word-break: break-all;
        }

        .diff-old { color: #b91c1c; }
        .diff-new { color: #15803d; font-weight: bold; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7.5px;
            color: #64748b;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div class="shop-title">{{ $shop->name ?: 'GURU CRACKERS' }}</div>
                <div class="shop-sub">
                    Sivakasi Cracker Wholesale Direct Store | Contact: +91 {{ $shop->phone ?: '8779981128' }} | Email: {{ $shop->email ?: 'admin@gurucrackers.com' }}
                </div>
            </td>
            <td style="width: 40%; vertical-align: middle;" class="report-title-box">
                <div class="report-badge">100% Comprehensive Audit Trail</div>
                <div class="report-meta">
                    <strong>Generated:</strong> {{ now()->format('d M Y, h:i:s A') }} (IST)<br>
                    <strong>Operator:</strong> {{ auth()->user()->name ?? 'System Admin' }} ({{ auth()->user()->email ?? 'admin' }})
                </div>
            </td>
        </tr>
    </table>

    <!-- Active Filters Summary -->
    <table class="filter-table">
        <tr>
            <td style="width: 25%;">
                <strong>Timeframe:</strong> {{ ucfirst(str_replace('_', ' ', $datePreset)) }}
                @if(!empty($startDate) && !empty($endDate))
                    ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
                @endif
            </td>
            <td style="width: 20%;">
                <strong>Module:</strong> {{ ucfirst($module) }}
            </td>
            <td style="width: 20%;">
                <strong>Event:</strong> {{ ucfirst($event) }}
            </td>
            <td style="width: 35%;">
                <strong>Search Keyword:</strong> {{ $search ?: 'None (All Records)' }}
            </td>
        </tr>
    </table>

    <!-- KPI Metric Summary Cards -->
    <table class="kpi-table">
        <tr>
            <td style="width: 25%; padding-right: 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Records In Report</div>
                    <div class="kpi-value" style="color: #312e81;">{{ number_format($totalCount) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Order Operations</div>
                    <div class="kpi-value" style="color: #be123c;">{{ $logs->where('module', 'orders')->count() }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">High-Impact Changes</div>
                    <div class="kpi-value" style="color: #b45309;">{{ $logs->whereIn('event', ['deleted', 'status_change', 'dispatched', 'password_change'])->count() }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-box">
                    <div class="kpi-label">Unique Operators</div>
                    <div class="kpi-value" style="color: #047857;">{{ $logs->pluck('user_name')->unique()->count() }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Audit Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">#</th>
                <th style="width: 11%;">Date & Time (IST)</th>
                <th style="width: 14%;">Operator & IP</th>
                <th style="width: 9%;">Module</th>
                <th style="width: 10%;">Event</th>
                <th style="width: 15%;">Record / Target</th>
                <th style="width: 21%;">Activity Description</th>
                <th style="width: 17%;">Values Changed (Diff)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                @php
                    $eventClass = match($log->event) {
                        'created' => 'badge-created',
                        'updated' => 'badge-updated',
                        'deleted' => 'badge-deleted',
                        'status_change' => 'badge-status',
                        'dispatched' => 'badge-dispatch',
                        'login', 'logout', 'password_change' => 'badge-login',
                        'whatsapp_sent' => 'badge-whatsapp',
                        default => 'badge-default'
                    };
                    $hasDiff = !empty($log->old_values) || !empty($log->new_values);
                @endphp
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: bold;">
                        {{ $index + 1 }}
                    </td>
                    <td>
                        <strong>{{ $log->created_at->format('d M Y') }}</strong><br>
                        <span style="color: #64748b;">{{ $log->created_at->format('h:i:s A') }}</span>
                    </td>
                    <td>
                        <strong>{{ $log->user_name ?: 'System / Guest' }}</strong><br>
                        <span style="color: #64748b; font-size: 7px;">IP: {{ $log->ip_address ?: '127.0.0.1' }} ({{ strtoupper($log->user_role) }})</span>
                    </td>
                    <td>
                        <span class="badge badge-module">{{ strtoupper($log->module) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $eventClass }}">{{ strtoupper(str_replace('_', ' ', $log->event)) }}</span>
                    </td>
                    <td>
                        <strong>{{ $log->record_name ?: 'System Setting' }}</strong>
                        @if($log->auditable_type)
                            <br><span style="color: #94a3b8; font-size: 7px;">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $log->summary }}
                    </td>
                    <td>
                        @if($hasDiff)
                            @if(!empty($log->old_values))
                                <div class="diff-box">
                                    <span class="diff-old">OLD:</span> {{ Str::limit(json_encode($log->old_values, JSON_UNESCAPED_UNICODE), 75) }}
                                </div>
                            @endif
                            @if(!empty($log->new_values))
                                <div class="diff-box">
                                    <span class="diff-new">NEW:</span> {{ Str::limit(json_encode($log->new_values, JSON_UNESCAPED_UNICODE), 75) }}
                                </div>
                            @endif
                        @else
                            <span style="color: #94a3b8; font-style: italic;">No attribute diff</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #64748b;">
                        No audit records found matching the specified criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <strong>Confidential & Proprietary</strong> — Guru Crackers Sivakasi Internal Application Audit Trail. Not for public distribution.
                </td>
                <td style="width: 30%; text-align: right;">
                    Page <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
