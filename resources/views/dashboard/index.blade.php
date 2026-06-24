@extends('app')

@section('title', 'Dashboard')

@section('content')
    @php
        $summaryCards = [
            ['label' => 'Total Sales', 'value' => number_format($totalSales, 2), 'meta' => 'Completed invoices', 'tone' => 'teal'],
            ['label' => 'Paid Amount', 'value' => number_format($paidAmount, 2), 'meta' => 'Received payments', 'tone' => 'green'],
            ['label' => 'Due Amount', 'value' => number_format($dueAmount, 2), 'meta' => 'Pending collection', 'tone' => 'amber'],
            ['label' => 'Today Sales', 'value' => number_format($todaySales, 2), 'meta' => now()->format('M d, Y'), 'tone' => 'blue'],
            ['label' => 'Month Sales', 'value' => number_format($monthSales, 2), 'meta' => now()->format('F Y'), 'tone' => 'violet'],
            ['label' => 'Stock Value', 'value' => number_format($stockValue, 2), 'meta' => 'Purchase value', 'tone' => 'slate'],
        ];

        $quickStats = [
            ['label' => 'Invoices', 'value' => $invoiceCount],
            ['label' => 'Customers', 'value' => $customerCount],
            ['label' => 'Products', 'value' => $productCount],
            ['label' => 'Low Stock', 'value' => $lowStockCount],
            ['label' => 'Returns', 'value' => number_format($returnAmount, 2)],
        ];

        $paymentLabels = ['paid' => 'Paid', 'partial' => 'Partial', 'due' => 'Due'];
        $paymentTotalCount = max(1, $paymentStatus->sum('count'));
        $trendPoints = $salesTrend->map(function ($item, $index) use ($salesTrend, $salesTrendMax) {
            $x = $salesTrend->count() > 1 ? ($index / ($salesTrend->count() - 1)) * 100 : 50;
            $y = 100 - (($item['total'] / $salesTrendMax) * 82) - 8;

            return round($x, 2) . ',' . round($y, 2);
        })->implode(' ');
    @endphp

    <div class="page-layout dashboard-page">
        <div class="dashboard-heading">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="text-muted mt-4">Sales, inventory, payments, and alerts in one place</p>
            </div>

            <div class="dashboard-actions">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">New Invoice</a>
                <a href="{{ route('products.index', ['stock' => 'low']) }}" class="btn btn-ghost">Low Stock</a>
            </div>
        </div>
 @can('dashboard.sales.statistics.view')
         <div class="divider"></div>
        <div class="dashboard-summary-grid">
            @foreach ($summaryCards as $card)
                <div class="dashboard-card summary-card summary-{{ $card['tone'] }}">
                    <div class="summary-topline">
                        <span>{{ $card['label'] }}</span>
                        <span class="summary-dot"></span>
                    </div>
                    <div class="summary-value">{{ $card['value'] }}</div>
                    <div class="summary-meta">{{ $card['meta'] }}</div>
                </div>
            @endforeach
        </div>
@endcan
        <div class="quick-stat-grid">
            @foreach ($quickStats as $stat)
                <div class="dashboard-card quick-stat">
                    <span>{{ $stat['label'] }}</span>
                    <strong>{{ $stat['value'] }}</strong>
                </div>
            @endforeach
        </div>

        <div class="dashboard-grid">
            <section class="dashboard-card chart-card chart-card-large">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Sales Graph</h2>
                        <p>Last 7 days completed invoice total</p>
                    </div>
                    <strong>{{ number_format($salesTrend->sum('total'), 2) }}</strong>
                </div>

                <div class="line-chart-wrap">
                    <svg class="line-chart" viewBox="0 0 100 100" preserveAspectRatio="none" role="img" aria-label="Sales graph">
                        <polyline points="{{ $trendPoints }}" fill="none" stroke="#0d9488" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="{{ $trendPoints }} 100,100 0,100" fill="rgba(13, 148, 136, .10)" stroke="none" />
                    </svg>

                    <div class="chart-labels">
                        @foreach ($salesTrend as $item)
                            <div>
                                <strong>{{ number_format($item['total'], 0) }}</strong>
                                <span>{{ $item['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="dashboard-card chart-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Payment Status</h2>
                        <p>Completed invoice count</p>
                    </div>
                </div>

                <div class="payment-bars">
                    @foreach ($paymentLabels as $status => $label)
                        @php
                            $count = (int) ($paymentStatus[$status]->count ?? 0);
                            $amount = (float) ($paymentStatus[$status]->total ?? 0);
                            $width = ($count / $paymentTotalCount) * 100;
                        @endphp

                        <div class="payment-row payment-{{ $status }}">
                            <div class="payment-row-top">
                                <span>{{ $label }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                            <div class="progress-track">
                                <span style="width: {{ $width }}%"></span>
                            </div>
                            <small>{{ number_format($amount, 2) }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="dashboard-grid">
            <section class="dashboard-card chart-card chart-card-large">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Top Products Bar</h2>
                        <p>Best selling products by amount</p>
                    </div>
                </div>

                <div class="bar-chart">
                    @forelse ($topProducts as $product)
                        @php($width = ((float) $product->total / $topProductsMax) * 100)
                        <div class="bar-row">
                            <div class="bar-info">
                                <span>{{ $product->product_name }}</span>
                                <small>{{ number_format($product->quantity, 2) }} sold</small>
                            </div>
                            <div class="bar-track">
                                <span style="width: {{ $width }}%"></span>
                            </div>
                            <strong>{{ number_format($product->total, 2) }}</strong>
                        </div>
                    @empty
                        <div class="empty-dashboard-state">No product sales yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="dashboard-card alert-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Low Stock</h2>
                        <p>Products at or below alert stock</p>
                    </div>
                    <strong>{{ $lowStockCount }}</strong>
                </div>

                <div class="low-stock-list">
                    @forelse ($lowStockProducts as $product)
                        <a href="{{ route('products.show', $product->id) }}" class="low-stock-item">
                            <div>
                                <strong>{{ $product->brand?->name ? $product->brand?->name." - " : '' }}{{ $product->name }}</strong>
                            </div>
                            <div class="stock-pill">
                                {{ number_format($product->stock, 2) }} {{ $product->productUnit?->name }}
                            </div>
                        </a>
                    @empty
                        <div class="empty-dashboard-state">All products are above alert stock.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h2>Recent Invoices</h2>
                    <p>Latest invoice activity</p>
                </div>
                <a href="{{ route('invoices.index') }}" class="btn btn-ghost">View All</a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                             <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Payment</th>
                             <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentInvoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}">{{ $invoice->invoice_no }}
                                      <br>  <span class="text-muted">{{ $invoice->invoice_date?->format('d M Y g:i A') }}</span>
                                    </a>
                                </td>
                                <td>{{ $invoice->customer_name ?? $invoice->customer?->name ?? 'Walk-in Customer' }}
                                     <br>  <span class="text-muted">{{ $invoice->customer_business_name ?? 'N/A' }}</span>
                                </td>

                                <td>{{ number_format($invoice->grand_total, 2) }}</td>
                                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td>{{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}</td>
                                <td><span class="status-badge payment-{{ $invoice->payment_status }}">{{ ucfirst($invoice->payment_status) }}</span></td>
                                <td><span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-dashboard-state">No invoices found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-page {
            display: flex;
            flex-direction: column;
            gap: clamp(14px, 1.35vw, 22px);
            width: 100%;
            max-width: 1680px;
            padding: clamp(18px, 2vw, 32px);
        }

        .dashboard-heading,
        .dashboard-actions,
        .dashboard-card-header,
        .summary-topline,
        .payment-row-top,
        .bar-row,
        .low-stock-item,
        .quick-stat {
            display: flex;
            align-items: center;
        }

        .dashboard-heading {
            justify-content: space-between;
            gap: 16px;
        }

        .dashboard-actions {
            gap: 10px;
            flex-wrap: wrap;
        }

        .dashboard-summary-grid,
        .quick-stat-grid,
        .dashboard-grid {
            display: grid;
            gap: clamp(12px, 1.1vw, 18px);
        }

        .dashboard-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .quick-stat-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: minmax(0, 1.75fr) minmax(290px, .82fr);
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: clamp(16px, 1.45vw, 22px);
            box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
            min-width: 0;
        }

        .summary-card {
            overflow: hidden;
            position: relative;
        }

        .summary-card:before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--card-accent, #0d9488);
        }

        .summary-topline {
            justify-content: space-between;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .summary-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--card-accent, #0d9488);
        }

        .summary-value {
            margin-top: 12px;
            font-size: clamp(22px, 2.2vw, 34px);
            font-weight: 800;
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        .summary-meta,
        .dashboard-card-header p,
        .bar-info small,
        .low-stock-item span,
        .payment-row small,
        .quick-stat span {
            color: var(--muted);
            font-size: 13px;
        }

        .summary-meta {
            margin-top: 8px;
        }

        .summary-teal { --card-accent: #0d9488; }
        .summary-green { --card-accent: #16a34a; }
        .summary-amber { --card-accent: #d97706; }
        .summary-blue { --card-accent: #2563eb; }
        .summary-violet { --card-accent: #7c3aed; }
        .summary-slate { --card-accent: #475569; }

        .quick-stat {
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
        }

        .quick-stat strong {
            font-size: 22px;
        }

        .dashboard-card-header {
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .dashboard-card-header h2 {
            font-size: 18px;
            line-height: 1.2;
        }

        .dashboard-card-header strong {
            font-size: 20px;
            color: #0d9488;
            white-space: nowrap;
        }

        .line-chart-wrap {
            min-height: 280px;
        }

        .line-chart {
            width: 100%;
            height: clamp(210px, 20vw, 280px);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background:
                linear-gradient(to top, rgba(226, 232, 240, .7) 1px, transparent 1px) 0 0 / 100% 25%,
                #f8fafc;
        }

        .chart-labels {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            margin-top: 12px;
        }

        .chart-labels div {
            min-width: 0;
            text-align: center;
        }

        .chart-labels strong,
        .chart-labels span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chart-labels strong {
            font-size: 12px;
        }

        .chart-labels span {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .payment-bars,
        .bar-chart,
        .low-stock-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .payment-row-top {
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .progress-track,
        .bar-track {
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: #e2e8f0;
        }

        .progress-track span,
        .bar-track span {
            display: block;
            height: 100%;
            min-width: 4px;
            border-radius: inherit;
            background: #0d9488;
        }

        .payment-partial .progress-track span { background: #d97706; }
        .payment-due .progress-track span { background: #dc2626; }

        .bar-row {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .bar-info {
            width: 190px;
            min-width: 0;
        }

        .bar-info span,
        .low-stock-item strong {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .bar-track {
            flex: 1;
            height: 14px;
        }

        .bar-row > strong {
            width: 110px;
            text-align: right;
            font-size: 13px;
        }

        .low-stock-item {
            justify-content: space-between;
            gap: 12px;
            min-height: 58px;
            padding: 12px;
            color: var(--text);
            text-decoration: none;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            background: #fff7ed;
        }

        .low-stock-item > div:first-child {
            min-width: 0;
        }

        .stock-pill,
        .status-badge {
            flex: 0 0 auto;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .stock-pill {
            padding: 7px 10px;
            color: #991b1b;
            background: #fee2e2;
        }

        .dashboard-table-wrap {
            overflow-x: auto;
        }

        .dashboard-table {
            min-width: 780px;
        }

        .dashboard-table a {
            color: #0d9488;
            font-weight: 700;
            text-decoration: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            background: #e2e8f0;
            color: #334155;
        }

        .status-completed,
        .payment-paid {
            color: #166534;
            background: #dcfce7;
        }

        .status-draft,
        .payment-partial {
            color: #92400e;
            background: #fef3c7;
        }

        .status-cancelled,
        .payment-due {
            color: #991b1b;
            background: #fee2e2;
        }

        .empty-dashboard-state {
            padding: 24px;
            color: var(--muted);
            text-align: center;
            border: 1px dashed var(--border);
            border-radius: 8px;
            background: #f8fafc;
        }

        @media (min-width: 1500px) {
            .dashboard-summary-grid {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }

            .quick-stat-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }

        @media (max-width: 1320px) {
            .dashboard-page {
                padding: 22px 18px;
            }

            .dashboard-grid {
                grid-template-columns: minmax(0, 1.55fr) minmax(280px, .9fr);
            }

            .dashboard-card {
                padding: 18px;
            }
        }

        @media (max-width: 1180px) {
            .dashboard-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .quick-stat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 901px) and (max-width: 1180px) {
            .dashboard-page {
                padding: 20px 16px;
            }

            .dashboard-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .quick-stat-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }

            .summary-value {
                font-size: 24px;
            }

            .quick-stat {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }
        }

        @media (max-width: 700px) {
            .dashboard-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-summary-grid,
            .quick-stat-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-card {
                padding: 16px;
            }

            .chart-labels {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .bar-row {
                align-items: stretch;
                flex-direction: column;
            }

            .bar-info,
            .bar-row > strong {
                width: 100%;
                text-align: left;
            }
        }
    </style>
@endpush
