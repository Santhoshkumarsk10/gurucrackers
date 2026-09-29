<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }} - {{ $shop->name ?? 'Guru Crackers' }}</title>
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.35;
            position: relative;
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
            max-width: 320px;
            max-height: 260px;
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

        /* Header Layout Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #9f1239;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-logo-cell {
            width: 75px;
            vertical-align: middle;
            padding-right: 12px;
        }

        .header-logo-img {
            max-width: 70px;
            max-height: 70px;
            display: block;
        }

        .header-info-cell {
            vertical-align: middle;
        }

        .shop-title {
            font-size: 19px;
            font-weight: bold;
            color: #9f1239;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .shop-tagline {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            margin: 2px 0 3px 0;
        }

        .shop-contact {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.3;
        }

        .header-invoice-cell {
            width: 200px;
            vertical-align: middle;
            text-align: right;
        }

        .invoice-pill {
            display: inline-block;
            background: #9f1239;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .invoice-meta-num {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin: 1px 0;
        }

        .invoice-meta-date {
            font-size: 9.5px;
            color: #64748b;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2.5px 7px;
            border-radius: 3px;
            margin-top: 4px;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-dispatched {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #a5b4fc;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Two Column Customer & Order Information Box */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-col {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 7px 10px;
            margin-right: 5px;
        }

        .info-col:last-child .info-card {
            margin-right: 0;
            margin-left: 5px;
        }

        .card-header {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9f1239;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .card-table {
            width: 100%;
            border-collapse: collapse;
        }

        .card-table td {
            padding: 2px 0;
            font-size: 9.5px;
            vertical-align: top;
        }

        .card-label {
            width: 75px;
            color: #64748b;
            font-weight: bold;
        }

        .card-val {
            color: #0f172a;
            font-weight: bold;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .items-table th {
            background: #9f1239;
            color: #ffffff;
            padding: 6px 8px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #9f1239;
        }

        .items-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .items-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .total-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .total-summary-card {
            background: #fff1f2;
            border: 1.5px solid #f43f5e;
            padding: 8px 12px;
            text-align: right;
            border-radius: 6px;
        }

        .grand-total-label {
            font-size: 12px;
            font-weight: bold;
            color: #881337;
            text-transform: uppercase;
        }

        .grand-total-val {
            font-size: 16px;
            font-weight: bold;
            color: #be123c;
            margin-left: 8px;
        }

        /* Payment / Bank Box */
        .notes-box {
            margin-top: 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 9px;
            color: #78350f;
            line-height: 1.35;
        }

        /* Footer */
        .footer {
            margin-top: 18px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            text-align: center;
            color: #64748b;
            font-size: 8.5px;
        }
    </style>
</head>
<body>

    <!-- ==========================================
         BACKGROUND WATERMARK (Repeats on every page)
         ========================================== -->
    <div class="watermark-container">
        @if (!empty($shop->logo_base64))
            <img src="{{ $shop->logo_base64 }}" class="watermark-logo" alt="Watermark" />
        @endif
        <div class="watermark-text">{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</div>
        <div class="watermark-sub">SIVAKASI &middot; DIRECT FACTORY FIREWORKS</div>
    </div>

    <!-- ==========================================
         HEADER WITH SHOP LOGO & INVOICE DETAILS
         ========================================== -->
    <table class="header-table">
        <tr>
            @if (!empty($shop->logo_base64))
                <td class="header-logo-cell">
                    <img src="{{ $shop->logo_base64 }}" class="header-logo-img" alt="{{ $shop->name ?? 'Logo' }}" />
                </td>
            @endif
            <td class="header-info-cell">
                <h1 class="shop-title">{{ strtoupper($shop->name ?? 'GURU CRACKERS') }}</h1>
                <div class="shop-tagline">{{ $shop->tagline ?? 'Sivakasi Direct Factory Green Crackers & Fireworks' }}</div>
                <div class="shop-contact">
                    {{ $shop->address ?? 'Sivakasi' }}, {{ $shop->city ?? 'Sivakasi' }} - {{ $shop->pincode ?? '626123' }}<br>
                    <strong>Phone:</strong> {{ $shop->phone ?? '' }}
                    @if (!empty($shop->secondary_phone)) &middot; <strong>Alt:</strong> {{ $shop->secondary_phone }} @endif
                    @if (!empty($shop->whatsapp_phone)) &middot; <strong>WhatsApp:</strong> {{ $shop->whatsapp_phone }} @endif
                    @if (!empty($shop->email)) &middot; <strong>Email:</strong> {{ $shop->email }} @endif
                </div>
            </td>
            <td class="header-invoice-cell">
                <div class="invoice-pill">Tax Invoice & Packing Slip</div>
                <div class="invoice-meta-num">#{{ $order->order_number }}</div>
                <div class="invoice-meta-date">Date: {{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div>
                    @if ($order->isDispatched())
                        <span class="status-badge status-dispatched">&#10003; DISPATCHED ({{ $order->parcel_service_name ?? 'Transport' }})</span>
                    @elseif ($order->isPaid())
                        <span class="status-badge status-paid">&#10003; PAID & VERIFIED</span>
                    @elseif ($order->payment_status === 'confirmed')
                        <span class="status-badge status-confirmed">&#9679; CONFIRMED</span>
                    @else
                        <span class="status-badge status-pending">&#9203; PAYMENT PENDING</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- ==========================================
         CUSTOMER & ORDER INFORMATION CARDS
         ========================================== -->
    <table class="info-grid">
        <tr>
            <td class="info-col">
                <div class="info-card">
                    <div class="card-header">Customer / Bill To</div>
                    <table class="card-table">
                        <tr>
                            <td class="card-label">Customer:</td>
                            <td class="card-val">{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Phone:</td>
                            <td class="card-val">{{ $order->phone1 }} @if(!empty($order->phone2)) / {{ $order->phone2 }} @endif</td>
                        </tr>
                        <tr>
                            <td class="card-label">Address:</td>
                            <td class="card-val">{{ $order->delivery_address }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Destination:</td>
                            <td class="card-val">{{ $order->city }} - {{ $order->pincode }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="info-col">
                <div class="info-card">
                    <div class="card-header">Order & Dispatch Info</div>
                    <table class="card-table">
                        <tr>
                            <td class="card-label">Order Number:</td>
                            <td class="card-val">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Total Items:</td>
                            <td class="card-val">{{ $order->items->count() }} Products ({{ $order->total_quantity }} Units)</td>
                        </tr>
                        @if ($order->isDispatched())
                            <tr>
                                <td class="card-label">Parcel Service:</td>
                                <td class="card-val">{{ $order->parcel_service_name ?? 'Registered Transport' }}</td>
                            </tr>
                            <tr>
                                <td class="card-label">LR / Waybill #:</td>
                                <td class="card-val" style="color: #4338ca;">{{ $order->lr_number ?? 'N/A' }}</td>
                            </tr>
                        @else
                            <tr>
                                <td class="card-label">Payment Status:</td>
                                <td class="card-val" style="text-transform: uppercase;">{{ $order->payment_status ?: 'Pending' }}</td>
                            </tr>
                            @if (!empty($shop->upi_id))
                                <tr>
                                    <td class="card-label">UPI ID:</td>
                                    <td class="card-val">{{ $shop->upi_id }} ({{ $shop->upi_name ?? $shop->name }})</td>
                                </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- ==========================================
         PRODUCT ITEMS TABLE
         ========================================== -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th class="text-left">Cracker Item Description</th>
                <th style="width: 50px;" class="text-center">Qty</th>
                <th style="width: 80px;" class="text-right">Price (₹)</th>
                <th style="width: 90px;" class="text-right">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $i => $item)
                <tr>
                    <td class="text-center" style="color: #64748b; font-weight: bold;">{{ $i + 1 }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $item->product_name }}</strong>
                    </td>
                    <td class="text-center" style="font-weight: bold; color: #0f172a;">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ==========================================
         TOTAL SUMMARY BLOCK
         ========================================== -->
    <table class="total-section-table">
        <tr>
            <td style="vertical-align: middle; font-size: 10px; color: #64748b;">
                <strong>Item Count:</strong> {{ $order->items->count() }} &nbsp;&middot;&nbsp;
                <strong>Total Quantity:</strong> {{ $order->total_quantity }} Units
            </td>
            <td style="width: 260px;">
                <div class="total-summary-card">
                    <span class="grand-total-label">Grand Total:</span>
                    <span class="grand-total-val">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- ==========================================
         PAYMENT & COMPLIANCE NOTES
         ========================================== -->
    <div class="notes-box">
        <strong>Notice & Legal Compliances:</strong>
        All fireworks are 100% genuine Green Crackers manufactured in Sivakasi following statutory safety & explosive acts.
        Parcels are securely packed and dispatched through registered transport operators.
        @if (!$order->isPaid() && !empty($shop->upi_id))
            <br><strong>UPI Payment:</strong> Pay ₹{{ number_format($order->total_amount, 2) }} to <strong>{{ $shop->upi_id }}</strong> and share transaction screenshot for instant dispatch verification.
        @endif
    </div>

    <!-- ==========================================
         FOOTER
         ========================================== -->
    <div class="footer">
        Generated on {{ now()->format('d M Y, h:i A') }} &middot;
        {{ $shop->name ?? 'Guru Crackers' }} (Sivakasi) &middot;
        This is a computer generated official invoice.
    </div>

</body>
</html>
