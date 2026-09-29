<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Packing Checklist #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.3;
        }
        /* Fixed Background Watermark on every page */
        .watermark-container {
            position: fixed;
            top: 28%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: 0.08;
            z-index: -1000;
        }
        .watermark-logo {
            max-width: 300px;
            max-height: 250px;
            display: inline-block;
            margin: 0 auto;
        }
        .watermark-text {
            font-size: 26px;
            font-weight: bold;
            color: #000000;
            margin-top: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .watermark-sub {
            font-size: 11px;
            font-weight: bold;
            color: #333333;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #9f1239;
        }
        .header-subtitle {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 2px 0 0 0;
            color: #333;
        }
        .meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .meta-box td {
            padding: 5px 8px;
            vertical-align: top;
            font-size: 10.5px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 90px;
        }
        .meta-val {
            font-weight: bold;
            color: #0f172a;
        }
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .checklist-table th {
            background: #1e293b;
            color: #ffffff;
            padding: 6px 6px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #1e293b;
        }
        .checklist-table td {
            padding: 6px 6px;
            border: 1px solid #94a3b8;
            font-size: 10.5px;
        }
        .checklist-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .checkbox-cell {
            width: 32px;
            text-align: center;
        }
        .tick-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #000000;
            border-radius: 2px;
            background: #ffffff;
        }
        .sno-cell {
            width: 25px;
            text-align: center;
            font-weight: bold;
            color: #64748b;
        }
        .qty-cell {
            width: 75px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            background: #f1f5f9;
        }
        .product-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .tamil-name {
            font-size: 9.5px;
            color: #475569;
            margin-top: 1px;
        }
        .category-badge {
            font-size: 9px;
            color: #334155;
            text-transform: uppercase;
        }
        .unit-cell {
            width: 55px;
            text-align: center;
            font-size: 10px;
            color: #475569;
        }
        .sign-cell {
            width: 70px;
            text-align: center;
            color: #cbd5e1;
        }
        .summary-box {
            margin-top: 12px;
            border: 1.5px solid #000;
            padding: 8px 12px;
            background: #fff;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 3px 0;
            font-size: 11px;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .sign-table td {
            width: 33.33%;
            padding-top: 30px;
            border-top: 1px dashed #64748b;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Background Watermark -->
    <div class="watermark-container">
        @if (!empty($shop->logo_base64))
            <img src="{{ $shop->logo_base64 }}" class="watermark-logo" alt="Watermark" />
        @endif
        <div class="watermark-text">{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</div>
        <div class="watermark-sub">SIVAKASI &middot; DIRECT FACTORY FIREWORKS</div>
    </div>

    <!-- Header -->
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                @if (!empty($shop->logo_base64))
                    <td style="width: 65px; vertical-align: middle; padding-right: 10px;">
                        <img src="{{ $shop->logo_base64 }}" style="max-width: 60px; max-height: 60px; display: block;" alt="{{ $shop->name ?? 'Logo' }}" />
                    </td>
                @endif
                <td style="vertical-align: middle;">
                    <h1 class="header-title">{{ $shop->name ?? 'GURU CRACKERS' }} - SIVAKASI</h1>
                    <p class="header-subtitle">WAREHOUSE DISPATCH & PACKING CHECKLIST</p>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <span style="display: inline-block; padding: 4px 10px; background: #e11d48; color: #fff; font-weight: bold; font-size: 12px; border-radius: 4px;">
                        ORDER #{{ $order->order_number }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Order & Customer Meta Box -->
    <table class="meta-box">
        <tr>
            <td class="meta-label">Customer:</td>
            <td class="meta-val">{{ $order->name }}</td>
            <td class="meta-label">Order Date:</td>
            <td class="meta-val">{{ $order->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Primary Mobile:</td>
            <td class="meta-val">{{ $order->phone1 }}</td>
            <td class="meta-label">Alt Mobile:</td>
            <td class="meta-val">{{ $order->phone2 ?: '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Destination:</td>
            <td class="meta-val" style="color: #b91c1c; font-size: 11.5px;">{{ strtoupper($order->city) }} - {{ $order->pincode }}</td>
            <td class="meta-label">Payment:</td>
            <td class="meta-val">{{ strtoupper($order->payment_status ?: 'PENDING') }} (₹{{ number_format($order->total_amount, 2) }})</td>
        </tr>
        <tr>
            <td class="meta-label">Delivery Address:</td>
            <td colspan="3" class="meta-val" style="font-weight: normal; font-size: 10px;">
                {{ $order->delivery_address }}, {{ $order->city }} - {{ $order->pincode }}
            </td>
        </tr>
    </table>

    <!-- Packing Checklist Items Table -->
    <table class="checklist-table">
        <thead>
            <tr>
                <th class="checkbox-cell">Tick</th>
                <th class="sno-cell">#</th>
                <th style="text-align: left;">Product Name & Description</th>
                <th style="width: 80px; text-align: left;">Category</th>
                <th class="unit-cell">Unit</th>
                <th class="qty-cell">Qty to Pack</th>
                <th class="sign-cell">Worker Check</th>
            </tr>
        </thead>
        <tbody>
            @php $totalUnits = 0; @endphp
            @foreach ($order->items as $i => $item)
                @php 
                    $totalUnits += $item->quantity;
                    $product = $item->product;
                @endphp
                <tr>
                    <td class="checkbox-cell">
                        <span class="tick-box"></span>
                    </td>
                    <td class="sno-cell">{{ $i + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                    </td>
                    <td>
                        <span class="category-badge">
                            {{ $product?->category?->name ?? 'Crackers' }}
                        </span>
                    </td>
                    <td class="unit-cell">
                        {{ $product?->unit ?? 'Pkt/Box' }}
                    </td>
                    <td class="qty-cell">
                        {{ $item->quantity }}
                    </td>
                    <td class="sign-cell">
                        [ &nbsp;&nbsp;&nbsp;&nbsp; ]
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Order Summary & Sign-off Box -->
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td><strong>Total Varieties:</strong> {{ $order->items->count() }} items</td>
                <td style="text-align: center; font-size: 12px;"><strong>TOTAL BOXES TO PACK:</strong> <span style="font-size: 14px; color: #b91c1c; font-weight: bold;">{{ number_format($totalUnits) }}</span> Units</td>
                <td style="text-align: right;"><strong>Total Order Value:</strong> ₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Signatures & Lorry Details -->
        <table class="sign-table">
            <tr>
                <td>
                    <strong>PACKED BY</strong><br>
                    <span style="font-size: 9px; color: #64748b;">Staff Signature & Date</span>
                </td>
                <td>
                    <strong>CHECKED BY</strong><br>
                    <span style="font-size: 9px; color: #64748b;">Verifier Signature</span>
                </td>
                <td>
                    <strong>DISPATCH LR / TRANSPORT</strong><br>
                    <span style="font-size: 9px; color: #64748b;">Lorry Service & LR No.</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ $shop->name ?? 'Guru Crackers' }} &middot; Factory Direct Sivakasi Crackers &middot; Phone: {{ $shop->phone ?? '+91 9789874381' }}@if (!empty($shop->secondary_phone)) / {{ $shop->secondary_phone }}@endif &middot; Generated: {{ now()->format('d M Y, h:i A') }}
    </div>

</body>
</html>
