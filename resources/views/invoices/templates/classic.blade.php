<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #0f2a28;
            background: #fff;
            padding: 12mm 14mm 10mm;
        }

        /* ── Watermark ─────────────────────── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            width: 360px;
            height: 360px;
            object-fit: contain;
            opacity: 0.042;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Wrapper ───────────────────────── */
        .invoice-wrapper {
            max-width: 780px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Accent bar ────────────────────── */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #0d3d3a, #0d9488, #0f5c57);
            border-radius: 3px 3px 0 0;
        }

        /* ── Card ──────────────────────────── */
        .invoice-card {
            border: 1px solid #cceae5;
            border-top: none;
            padding: 14px 20px 0;
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

        /* ── Header ────────────────────────── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .company-left {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            border: 1px solid #cceae5;
            border-radius: 8px;
            padding: 4px;
            background: #f0fdfa;
            flex-shrink: 0;
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

        .invoice-badge-wrap {
            text-align: right;
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
            border-radius: 99px;
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

        .status-default {
            background: #f0fdfa;
            color: #0f5c57;
            border: 1px solid #cceae5;
        }

        /* ── Divider ───────────────────────── */
        .divider {
            height: 1.5px;
            background: linear-gradient(90deg, #0d9488, #cceae5, transparent);
            margin: 2px 0 8px;
        }

        /* ── Bill To + Invoice info row ────── */
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 10px;
        }

        .bill-section {
            background: #f0fdfa;
            border: 1px solid #cceae5;
            border-left: 3px solid #0d9488;
            border-radius: 0 6px 6px 0;
            padding: 8px 12px;
            flex: 1;
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

        /* ── Summary + Footer row ──────────── */
        .bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 8px 0 10px;
            border-top: 1px dashed #cceae5;
            margin-top: 4px;
        }

        /* Terms block (now holds both terms & signature) */
        .terms-block {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
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
        }

        /* Signature inside terms area, under terms text */
        .signature-inside {
            text-align: left;
            margin-top: 6px;
            width: 100%;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
        }

        .signature-inside .sig-placeholder {
            height: 38px;
            border-bottom: 1.5px solid #0d3d3a;
            margin-bottom: 3px;
            width: 160px;
        }

        .signature-inside img {
            width: 140px;
            height: auto;
            max-height: 52px;
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
            width: 230px;
            flex-shrink: 0;
            border-collapse: collapse;
            border: 1px solid #cceae5;
            border-radius: 8px;
            overflow: hidden;
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

        /* Original signature-block removed, replaced by signature-inside */

        /* ── Contact bar ───────────────────── */
        .contact-bar {
            background: #0d3d3a;
            margin: 0 -20px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: #99f6e4;
            letter-spacing: .02em;
        }

        .contact-item .icon {
            width: 12px;
            height: 12px;
            stroke: #0d9488;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        /* ── Print ─────────────────────────── */
        @media print {
            body {
                padding: 10mm 12mm 8mm;
            }

            .invoice-card {
                border: none;
            }

            .watermark {
                position: fixed;
            }
        }
    </style>
</head>

<body>

    {{-- WATERMARK --}}
    @if ($business?->logo)
        <img class="watermark" src="{{ public_path('storage/' . $business->logo) }}" alt="" aria-hidden="true">
    @endif

    <div class="invoice-wrapper">

        <div class="accent-bar"></div>

        <div class="invoice-card">

            {{-- TOP TAGLINE --}}
            @if ($business?->top_tagline)
                <div class="top-tagline">{{ $business->top_tagline }}</div>
            @endif

            {{-- HEADER --}}
            <div class="header">

                <div class="company-left">
                    @if ($business?->logo)
                        <img src="{{ public_path('storage/' . $business->logo) }}" class="logo" alt="Logo">
                    @endif
                    <div class="company-info">
                        <h1>{{ $business?->business_name }}</h1>
                        @if ($business?->tagline)
                            <div class="company-tagline">{{ $business->tagline }}</div>
                        @endif
                        @if ($business?->owner_name)
                            <div class="company-owner">Proprietor: <strong>{{ $business->owner_name }}</strong></div>
                        @endif
                    </div>
                </div>

                <div class="invoice-badge-wrap">
                    <div class="invoice-word">Invoice</div>
                    <div class="invoice-meta">
                        <strong>No:</strong> {{ $invoice->invoice_no }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}<br>
                        <strong>Status:</strong>
                        @php
                            $sc = match (strtolower($invoice->status)) {
                                'paid' => 'status-paid',
                                'due' => 'status-due',
                                'partial' => 'status-partial',
                                default => 'status-default',
                            };
                        @endphp
                        <span class="status-pill {{ $sc }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>

            </div>

            <div class="divider"></div>

            {{-- BILL TO --}}
            <div class="meta-row">
                <div class="bill-section">
                    <div class="section-label">Bill To</div>
                    <div class="customer-name">{{ $invoice->customer_business_name }} - <small>(
                            {{ $invoice->customer_name }} )</small></div>
                    <div class="customer-details">
                        @if ($invoice->customer_phone)
                            Phone: {{ $invoice->customer_phone }}
                        @endif
                        @if ($invoice->customer_address)
                            <br> Address: {{ $invoice->customer_address }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
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
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td style="color:#6b7280;font-size:10.5px">{{ $loop->iteration }}</td>
                            <td><span class="product-name">
                                    @if ($item->product->brand->name ?? false)
                                        {{ $item->product->brand->name }} - {{ $item->product_name }}
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </span></td>
                            <td>{{ $item->quantity }}</td>
                            <td style="color:#6b7280">{{ $item->unit }}</td>
                            <td class="tr">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="tr" style="font-weight:600">
                                {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- BOTTOM ROW: terms (with signature inside) at left, summary table at right --}}
            <div class="bottom-row">

                {{-- Left side: Terms & Conditions + Signature (inline, signature under terms text) --}}
                <div class="terms-block">
                    @if ($business?->terms_conditions)
                        <div class="terms-content">
                            <h4>Terms &amp; Conditions</h4>
                            <p>{{ $business->terms_conditions }}</p>
                        </div>
                    @else
                        {{-- If no terms, still reserve area for signature but no extra heading --}}
                        <div class="terms-content">
                            <h4 style="opacity:0.4;">Terms &amp; Conditions</h4>
                            <p style="color:#e2e8f0; font-style:italic;">—</p>
                        </div>
                    @endif

                    {{-- SIGNATURE placed directly under terms content --}}
                    <div class="signature-inside">
                        @if ($business?->signature)
                            <img src="{{ public_path('storage/' . $business->signature) }}" alt="Signature">
                        @else
                            <div class="sig-placeholder"></div>
                        @endif
                        <div class="sig-label">Authorized Signature</div>
                    </div>
                </div>

                {{-- Right side: Financial summary (subtotal, tax, discount, grand total, paid, due) --}}
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
                        <td>Paid ({{ $invoice->payment_method }})</td>
                        <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due-row">
                        <td>Due</td>
                        <td>
                            {{-- Calculate due amount properly: grand_total - paid_amount --}}
                            {{ number_format(max($invoice->grand_total - $invoice->paid_amount, 0), 2) }}
                        </td>
                    </tr>
                </table>

            </div>

            {{-- CONTACT BAR (unchanged) --}}
            <div class="contact-bar">

                @if ($business?->phone_1)
                    <div class="contact-item">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.31 12 19.79 19.79 0 0 1 1.21 3.18 2 2 0 0 1 3.22 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        {{ $business->phone_1 }}{{ $business->phone_2 ? ', ' . $business->phone_2 : '' }}
                    </div>
                @endif

                @if ($business?->email)
                    <div class="contact-item">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        {{ $business->email }}
                    </div>
                @endif

                @if ($business?->website)
                    <div class="contact-item">
                        <svg class="icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="2" y1="12" x2="22" y2="12" />
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                        </svg>
                        {{ $business->website }}
                    </div>
                @endif

                @if ($business?->address)
                    <div class="contact-item">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ $business->address }}
                    </div>
                @endif

            </div>

        </div>

    </div>

</body>

</html>
