<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <style>
        /* ── Reset & Base ─────────────────────── */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #0f2a28;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* ── Wrapper ───────────────────────── */
        .invoice-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* ── Accent bar ────────────────────── */
        .accent-bar {
            height: 4px;
            background: #0d3d3a;
        }

        /* ── Card ──────────────────────────── */
        .invoice-card {
            border: 1px solid #cceae5;
            border-top: none;
            padding: 14px 20px 0 20px;
            background: #fff;
        }

        /* ── Top tagline ───────────────────── */
        .top-tagline {
            text-align: center;
            font-size: 9px;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #0d9488;
            font-weight: 700;
            padding: 5px 0 8px;
            border-bottom: 1px dashed #cceae5;
            margin-bottom: 10px;
        }

        /* ── Header Table ───────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .company-cell {
            width: 65%;
        }

        .badge-cell {
            width: 35%;
            text-align: right;
        }

        .logo-img {
            width: 56px;
            height: 56px;
            border: 1px solid #cceae5;
            padding: 4px;
            background: #f0fdfa;
            float: left;
            margin-right: 10px;
        }

        .company-info h1 {
            font-size: 18px;
            font-weight: 800;
            color: #0d3d3a;
            letter-spacing: -.01em;
            line-height: 1.15;
            margin-bottom: 1px;
        }

        .company-tagline {
            font-size: 10px;
            color: #0d9488;
            letter-spacing: .04em;
            margin-bottom: 2px;
        }

        .company-owner {
            font-size: 10.5px;
            color: #0f5c57;
        }

        .invoice-word {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #0d9488;
            line-height: 1;
            margin-bottom: 6px;
        }

        .invoice-meta {
            font-size: 11px;
            line-height: 1.85;
            color: #0f2a28;
        }

        .invoice-meta strong {
            color: #0d3d3a;
        }

        .status-pill {
            display: inline-block;
            padding: 1px 9px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-due {
            background: #ffe4e6;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .status-partial {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-default {
            background: #f0fdfa;
            color: #0f5c57;
            border: 1px solid #cceae5;
        }

        /* ── Divider ───────────────────────── */
        .divider {
            height: 1.5px;
            background: #0d9488;
            margin: 2px 0 8px;
        }

        /* ── Bill To ────────────────────────── */
        .bill-section {
            background: #f0fdfa;
            border: 1px solid #cceae5;
            border-left: 3px solid #0d9488;
            padding: 8px 12px;
            margin-bottom: 10px;
        }

        .section-label {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #0d9488;
            margin-bottom: 3px;
        }

        .customer-name {
            font-size: 13px;
            font-weight: 700;
            color: #0d3d3a;
            margin-bottom: 1px;
        }

        .customer-details {
            font-size: 10.5px;
            line-height: 1.65;
            color: #4b5563;
        }

        /* ── Items table ───────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .items-table thead tr {
            background: #0d3d3a;
        }

        .items-table th {
            padding: 7px 10px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #cceae5;
            text-align: left;
        }

        .items-table th.tr {
            text-align: right;
        }

        .items-table td {
            padding: 6px 10px;
            font-size: 11.5px;
            color: #0f2a28;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .items-table td.tr {
            text-align: right;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9fffe;
        }

        .product-name {
            font-weight: 600;
            color: #0d3d3a;
        }

        /* ── Bottom Row Table ───────────────── */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            padding: 8px 0 10px;
            border-top: 1px dashed #cceae5;
            margin-top: 4px;
        }

        .bottom-table td {
            vertical-align: top;
            padding: 8px 0;
        }

        .terms-cell {
            width: 60%;
            padding-right: 20px;
        }

        .summary-cell {
            width: 40%;
            text-align: right;
        }

        /* Terms & Conditions */
        .terms-content {
            margin-bottom: 20px;
        }

        .terms-content h4 {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #0f5c57;
            margin-bottom: 4px;
        }

        .terms-content p {
            font-size: 10px;
            line-height: 1.7;
            color: #6b7280;
            white-space: pre-line;
            margin-bottom: 10px;
        }



        /* Signature with spacing above */
        .signature-inside {
            text-align: left;
            width: 100%;
            padding-top: 15px;
        }

        .signature-inside .sig-placeholder {
            height: 30px;
            border-bottom: 1.5px solid #0d3d3a;
            margin-bottom: 3px;
            width: 140px;
        }

        .signature-inside img {
            width: 120px !important;
            max-width: 120px !important;
            height: auto !important;
            max-height: 40px !important;
            object-fit: contain;
            margin-bottom: 3px;
            display: block;
        }

        .signature-inside .sig-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #0d3d3a;
        }

        /* Summary table */
        .summary-table {
            width: 100%;
            max-width: 230px;
            border-collapse: collapse;
            border: 1px solid #cceae5;
            float: right;
        }

        .summary-table td {
            padding: 5px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-table td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .summary-table .grand td {
            background: #0d3d3a;
            color: #f0fdfa;
            font-weight: 700;
            font-size: 12px;
        }

        .summary-table .paid-row td {
            color: #065f46;
        }

        .summary-table .due-row td {
            color: #be123c;
            font-weight: 700;
        }

        /* ── Contact bar ───────────────────── */
        .contact-bar {
            background: #0d3d3a;
            margin: 0 -20px;
            padding: 10px 20px;
            text-align: center;
        }

        .contact-item {
            display: inline-block;
            margin: 0 12px;
            font-size: 10px;
            color: #99f6e4;
            letter-spacing: .02em;
        }

        .contact-item .icon {
            display: none;
        }

        /* ── MPDF fixes ────────────────────── */
        @page {
            margin: 15px 20px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .accent-bar {
            background: #0d3d3a;
        }

        .divider {
            background: #0d9488;
        }

        .invoice-card {
            border-radius: 0;
        }

        .status-pill {
            border-radius: 0;
        }

        .logo-img {
            border-radius: 0;
        }

        /* Fix for small text */
        small {
            font-size: 85%;
        }
    </style>
</head>

<body>

    <div class="invoice-wrapper">

        <div class="accent-bar"></div>

        <div class="invoice-card">

            {{-- TOP TAGLINE --}}
            @if ($business?->top_tagline)
                <div class="top-tagline">{{ $business->top_tagline }}</div>
            @endif

            {{-- HEADER using TABLE --}}
            <table class="header-table">
                <tr>
                    <td class="company-cell">
                        @if ($business?->logo)
                            <img src="{{ public_path('storage/' . $business->logo) }}" class="logo-img" alt="Logo">
                        @endif
                        <div class="company-info">
                            <h1>{{ $business?->business_name ?? 'Business Name' }}</h1>
                            @if ($business?->tagline)
                                <div class="company-tagline">{{ $business->tagline }}</div>
                            @endif
                            @if ($business?->owner_name)
                                <div class="company-owner">Proprietor: <strong>{{ $business->owner_name }}</strong></div>
                            @endif
                        </div>
                    </td>
                    <td class="badge-cell">
                        <div class="invoice-word">Invoice</div>
                        <div class="invoice-meta">
                            <strong>No:</strong> {{ $invoice->invoice_no }}<br>
                            <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}<br>
                            <strong>Status:</strong>
                            @php
                                $status = strtolower($invoice->status);
                                $sc = match($status) {
                                    'paid' => 'status-paid',
                                    'completed' => 'status-completed',
                                    'due' => 'status-due',
                                    'partial' => 'status-partial',
                                    default => 'status-default',
                                };
                            @endphp
                            <span class="status-pill {{ $sc }}">{{ strtoupper($invoice->status) }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            {{-- BILL TO --}}
            <div class="bill-section">
                <div class="section-label">Bill To</div>
                <div class="customer-name">
                    {{ $invoice->customer_business_name }}
                    @if($invoice->customer_name)
                        - <small>({{ $invoice->customer_name }})</small>
                    @endif
                </div>
                <div class="customer-details">
                    @if ($invoice->customer_phone)
                        Phone: {{ $invoice->customer_phone }}
                    @endif
                    @if ($invoice->customer_address)
                        <br> Address: {{ $invoice->customer_address }}
                    @endif
                </div>
            </div>

            {{-- ITEMS TABLE --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Product</th>
                        <th style="width:56px">Qty</th>
                        <th style="width:56px">Unit</th>
                        <th class="tr" style="width:88px">Price</th>
                        <th class="tr" style="width:96px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoice->items as $item)
                        <tr>
                            <td style="color:#6b7280;font-size:10.5px">{{ $loop->iteration }}</td>
                            <td>
                                <span class="product-name">
                                    @if ($item->product->brand->name ?? false)
                                        {{ $item->product->brand->name }} - {{ $item->product_name }}
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td style="color:#6b7280">{{ $item->unit ?? 'pcs' }}</td>
                            <td class="tr">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="tr" style="font-weight:600">
                                {{ number_format($item->quantity * $item->unit_price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:20px; color:#999;">
                                No items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- BOTTOM ROW using TABLE --}}
            <table class="bottom-table">
                <tr>
                    <td class="terms-cell">
                        {{-- Terms & Conditions --}}
                        <div class="terms-content">
                            <h4>Terms &amp; Conditions</h4>
                            <p>{{ $business?->terms_conditions ?? '—' }}</p>
                        </div>



                        {{-- Signature with space above --}}
                        <div class="signature-inside">
                            @if ($business?->signature)
                                <img src="{{ public_path('storage/' . $business->signature) }}" alt="Signature" style="width:120px; max-height:40px;">
                            @else
                                <div class="sig-placeholder"></div>
                            @endif
                            <div class="sig-label">Authorized Signature</div>
                        </div>
                    </td>
                    <td class="summary-cell">
                        <table class="summary-table">
                            <tr>
                                <td>Subtotal</td>
                                <td>{{ number_format($invoice->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td style="color:#be123c">− {{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td>{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="grand">
                                <td>Grand Total</td>
                                <td>{{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                            <tr class="paid-row">
                                <td>Paid ({{ $invoice->payment_method ?? 'cash' }})</td>
                                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                            </tr>
                            <tr class="due-row">
                                <td>Due</td>
                                <td>
                                    {{ number_format(max($invoice->grand_total - $invoice->paid_amount, 0), 2) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- CONTACT BAR --}}
            <div class="contact-bar">
                @if ($business?->phone_1)
                    <span class="contact-item">📞 {{ $business->phone_1 }}{{ $business->phone_2 ? ', ' . $business->phone_2 : '' }}</span>
                @endif

                @if ($business?->email)
                    <span class="contact-item">✉ {{ $business->email }}</span>
                @endif

                @if ($business?->website)
                    <span class="contact-item">🌐 {{ $business->website }}</span>
                @endif

                @if ($business?->address)
                    <span class="contact-item">📍 {{ $business->address }}</span>
                @endif
            </div>

        </div>

    </div>

</body>

</html>
