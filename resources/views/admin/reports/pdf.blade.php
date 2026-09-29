<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ $shop->shop_name ?? 'Guru Crackers' }}</title>
    <style>
        @page {
            margin: 28px 28px 35px 28px;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #be123c;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .shop-title {
            font-size: 20px;
            font-weight: bold;
            color: #be123c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .shop-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        .report-title-box {
            text-align: right;
        }

        .report-badge {
            background-color: #be123c;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
        }

        .report-dates {
            font-size: 9px;
            color: #475569;
            margin-top: 4px;
            font-weight: bold;
        }

        /* KPI Summary Grid */
        .kpi-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            border-radius: 4px;
            text-align: center;
        }

        .kpi-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .data-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 8.5px;
            text-transform: uppercase;
            font-weight: bold;
            color: #334155;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            font-size: 9px;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-rose { color: #be123c; }
        .text-emerald { color: #047857; }
        .text-amber { color: #b45309; }

        .status-badge {
            font-size: 7.5px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-dispatched { background-color: #e0e7ff; color: #3730a3; }
        .status-confirmed { background-color: #dbeafe; color: #1e40af; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="shop-title">{{ $shop->shop_name ?? 'Guru Crackers Sivakasi' }}</div>
                <div class="shop-sub">
                    Direct Sivakasi Wholesale & Retail Online Bookings<br>
                    Phone: {{ $shop->phone ?? 'Contact Store' }} | {{ $shop->address ?? 'Sivakasi, Tamil Nadu' }}
                </div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="report-title-box">
                <div class="report-badge">Sales & Bookings Statement</div>
                <div class="report-dates">Period: {{ $startStr }} - {{ $endStr }}</div>
                <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">
                    Generated on: {{ now()->format('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- KPI Summary Grid -->
    <table class="kpi-table">
        <tr>
            <td style="width: 20%; padding-right: 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Total Bookings</div>
                    <div class="kpi-value text-rose">{{ number_format($totalOrders) }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Gross Revenue</div>
                    <div class="kpi-value">₹{{ number_format($totalRevenue, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Paid / Confirmed</div>
                    <div class="kpi-value text-emerald">₹{{ number_format($paidRevenue, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Pending Amount</div>
                    <div class="kpi-value text-amber">₹{{ number_format($pendingRevenue, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%; padding-left: 5px;">
                <div class="kpi-box">
                    <div class="kpi-label">Cracker Units Sold</div>
                    <div class="kpi-value">{{ number_format($totalItemsSold) }} pkts</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Top Products Summary (if any) -->
    @if($topProducts->isNotEmpty())
        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #475569;">
            Top 5 Best-Selling Crackers in Period
        </div>
        <table class="data-table" style="margin-bottom: 12px;">
            <thead>
                <tr>
                    <th style="width: 8%;">Rank</th>
                    <th style="width: 52%;">Cracker Product Name</th>
                    <th style="width: 20%; text-align: center;">Total Units / Boxes Sold</th>
                    <th style="width: 20%; text-align: right;">Sales Revenue (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $i => $tp)
                    <tr>
                        <td class="text-center font-bold">#{{ $i + 1 }}</td>
                        <td class="font-bold">{{ $tp->product_name }}</td>
                        <td class="text-center">{{ number_format($tp->total_qty) }}</td>
                        <td class="text-right font-bold">₹{{ number_format($tp->total_sales, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Orders List -->
    <div style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #475569;">
        Itemized Orders Detail ({{ count($orders) }} records)
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Order #</th>
                <th style="width: 13%;">Date & Time</th>
                <th style="width: 24%;">Customer & Phone</th>
                <th style="width: 15%;">City & State</th>
                <th style="width: 6%; text-align: center;">Items</th>
                <th style="width: 12%; text-align: right;">Amount (₹)</th>
                <th style="width: 15%; text-align: center;">Status / LR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $ord)
                <tr>
                    <td class="font-bold">{{ $ord->order_number }}</td>
                    <td>
                        <span style="font-weight: bold;">{{ $ord->created_at ? $ord->created_at->format('d M Y') : 'N/A' }}</span><br>
                        <span style="font-size: 7.5px; color: #64748b;">{{ $ord->created_at ? $ord->created_at->format('h:i A') : '' }}</span>
                    </td>
                    <td>
                        <span class="font-bold">{{ $ord->name }}</span><br>
                        <span style="font-size: 7.5px; color: #475569; font-family: monospace;">{{ $ord->phone1 }}</span>
                    </td>
                    <td>{{ $ord->city ?: 'N/A' }}<br><span style="font-size: 7.5px; color: #64748b;">{{ $ord->state ?? 'Tamil Nadu' }}</span></td>
                    <td class="text-center">{{ $ord->items_count }}</td>
                    <td class="text-right font-bold">₹{{ number_format((float) $ord->total_amount, 2) }}</td>
                    <td class="text-center">
                        @php
                            $st = $ord->payment_status ?: 'pending';
                        @endphp
                        <span class="status-badge status-{{ $st }}">{{ strtoupper($st) }}</span>
                        @if($ord->lr_number)
                            <div style="font-size: 7.5px; color: #475569; margin-top: 2px;">
                                LR: {{ $ord->lr_number }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #94a3b8;">
                        No orders recorded for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL ({{ count($orders) }} Orders):</td>
                <td class="text-right font-bold text-rose">₹{{ number_format($totalRevenue, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Computer Generated Statement | {{ $shop->shop_name ?? 'Guru Crackers' }} Sivakasi | Confidential Admin Report
    </div>

</body>
</html>
