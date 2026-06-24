@extends('app')

@section('title', 'Customer Details')

@section('content')

<div class="page-layout customer-page-layout customer-show-page">

    {{-- ================= HEADER ================= --}}
    <div class="card card-full mb-6">

        <div class="card-header">

            <div>
                <h1 class="page-title">{{ $customer->name }}</h1>
                <p class="text-muted mt-4">
                    Customer profile & invoice history
                </p>
            </div>

            <a href="{{ route('customers.index') }}" class="btn btn-ghost">
                ← Back
            </a>

        </div>

        <div class="divider"></div>

        {{-- ================= STATS ================= --}}
        @can( 'customer.statistics.view')
        <div class="stats-grid">

            <div class="stat-box">
                <div class="text-muted">Total Invoices</div>
                <div class="stat-value text-teal">
                    {{ $customer->invoices->count() }}
                </div>
            </div>

            <div class="stat-box">
                <div class="text-muted">Total Purchase</div>
                <div class="stat-value text-teal">
                    ৳{{ number_format($customer->invoices->sum('grand_total'), 2) }}
                </div>
            </div>

            <div class="stat-box">
                <div class="text-muted">Due Amount</div>
                <div class="stat-value">
                    ৳{{ number_format(
                        $customer->invoices->sum(fn ($invoice) =>
                            max(0, $invoice->grand_total - $invoice->paid_amount)
                        ),
                        2
                    ) }}
                </div>
            </div>

        </div>
        @endcan

        {{-- ================= PROFILE (COMPACT) ================= --}}
        <div class="profile-grid">

            {{-- LEFT (compact profile) --}}
            <div>
                <div class="card-section customer-profile-card" style="padding:18px; text-align:center;">

                    <div class="customer-avatar-mark" style="
                        width:70px;
                        height:70px;
                        border-radius:50%;
                        background:rgba(13,148,136,.12);
                        display:grid;
                        place-items:center;
                        margin:0 auto 12px;
                        font-size:1.6rem;
                        font-weight:700;
                        color:var(--teal-bright);
                        text-transform:uppercase;
                    ">
                        {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                    </div>

                    <h3 style="font-size:1rem; margin-bottom:4px;">
                        {{ $customer->name }}
                    </h3>

                    @if($customer->business_name)
                        <p class="text-muted text-sm">
                            {{ $customer->business_name }}
                        </p>
                    @endif

                    <div class="mt-4">
                        @if($customer->is_active)
                            <span class="badge badge-teal">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- RIGHT --}}
            <div>
                <div class="card-section">

                    <div class="section-title">
                        Customer Information
                    </div>

                    <div class="tbl-wrap detail-table-wrap customer-detail-wrap">

                        <table class="tbl detail-table customer-detail-table">
                            <tbody>
                                <tr><th>Name</th><td>{{ $customer->name }}</td></tr>
                                <tr><th>Business</th><td>{{ $customer->business_name ?? '-' }}</td></tr>
                                <tr><th>Phone</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                                <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                                <tr><th>Address</th><td>{{ $customer->address ?? '-' }}</td></tr>
                                <tr><th>Updated</th><td>{{ $customer->updated_at->format('d M Y, h:i A') }}</td></tr>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>

        </div>

    </div>

{{-- ================= INVOICES ================= --}}
<div class="card card-full customer-invoices-card"
     style="
        background: var(--surface-1);
        border: 2px solid var(--border);
        box-shadow: 0 12px 35px rgba(13,148,136,.08);
     ">

    {{-- HEADER + SEARCH (TOGETHER) --}}
    <div class="card-header customer-invoices-header" style="align-items:flex-end;">

        <div>
            <h2 class="page-title">Customer Invoices</h2>
            <p class="text-muted mt-4">
                Invoice history of this customer
            </p>
        </div>

        {{-- SEARCH RIGHT SIDE --}}
        <div class="customer-invoice-search" style="min-width:260px; max-width:320px; width:100%;">

            <div class="input-wrap">


                <input type="text"
                       id="invoiceSearch"
                       class="form-input no-icon"
                       placeholder="Search invoice...">
            </div>

        </div>

    </div>

    <div class="divider"></div>

    {{-- TABLE --}}
    <div id="invoiceTable" class="customer-invoice-table-shell"
         style="
            background:#fff;
            border:1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow:hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,.04);
         ">
        @include('customers.partials.invoice-table', ['invoices' => $invoices])
    </div>

</div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let timer = null;
    const input = document.getElementById('invoiceSearch');

    function loadInvoices(url = null) {

        let search = input.value;

        let fetchUrl = url
            ? url
            : `{{ route('customers.show', $customer->id) }}?search=` + search;

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('invoiceTable').innerHTML = html;
            bindPagination(); // rebind after update
        });
    }

    // SEARCH
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => loadInvoices(), 300);
    });

    // PAGINATION FIX
    function bindPagination() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                loadInvoices(this.href);
            });
        });
    }


    bindPagination();

});
</script>
@endpush

@push('styles')
<style>
.custom-pagination .pagination {
    gap: 6px;
}

.custom-pagination .page-link {
    border-radius: 10px !important;
    border: 1px solid var(--border);
    padding: 6px 12px;
    color: #0f2a2e;
    transition: .2s;
}

.custom-pagination .page-link:hover {
    background: rgba(13,148,136,.1);
    border-color: #0d9488;
    color: #0d9488;
}

.custom-pagination .active .page-link {
    background: #0d9488;
    border-color: #0d9488;
    color: white;
}

@media (max-width: 768px) {
    .customer-show-page {
        padding-inline: 10px;
    }

    .customer-show-page > .card {
        border-radius: 14px;
    }

    .customer-show-page .card-header {
        gap: 14px;
    }

    .customer-profile-card {
        padding: 16px !important;
    }

    .customer-avatar-mark {
        width: 58px !important;
        height: 58px !important;
        font-size: 1.35rem !important;
    }

    .customer-detail-wrap {
        overflow: visible;
    }

    .customer-detail-table,
    .customer-detail-table tbody,
    .customer-detail-table tr,
    .customer-detail-table th,
    .customer-detail-table td {
        display: block;
        width: 100%;
        min-width: 0;
    }

    .customer-detail-table {
        min-width: 0 !important;
    }

    .customer-detail-table tr {
        padding: 10px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .customer-detail-table tr:first-child {
        padding-top: 0;
    }

    .customer-detail-table tr:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .customer-detail-table th,
    .customer-detail-table td {
        padding: 0;
        border: 0;
        text-align: left;
        white-space: normal;
    }

    .customer-detail-table th {
        margin-bottom: 3px;
        color: var(--text-muted);
        font-size: .72rem;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .customer-detail-table td {
        max-width: none !important;
        overflow-wrap: anywhere;
        font-size: .92rem;
    }

    .customer-invoices-card {
        border-width: 1px !important;
    }

    .customer-invoices-header {
        align-items: stretch !important;
    }

    .customer-invoice-search {
        min-width: 0 !important;
        max-width: none !important;
    }

    .customer-invoice-search .form-input {
        min-width: 0;
        width: 100%;
        font-size: 16px;
    }

    .customer-invoice-table-shell {
        border-radius: 14px !important;
        overflow: visible !important;
        box-shadow: none !important;
    }

    .customer-invoice-table-wrap {
        overflow: visible;
    }

    .customer-invoice-table,
    .customer-invoice-table thead,
    .customer-invoice-table tbody,
    .customer-invoice-table tr,
    .customer-invoice-table td {
        display: block;
        width: 100%;
        min-width: 0;
    }

    .customer-invoice-table {
        min-width: 0 !important;
    }

    .customer-invoice-table thead {
        display: none;
    }

    .customer-invoice-table tbody {
        display: grid;
        gap: 12px;
    }

    .customer-invoice-table tbody tr {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
        overflow: hidden;
    }

    .customer-invoice-table tbody tr:hover {
        background: #fff;
    }

    .customer-invoice-table td {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        max-width: none !important;
        padding: 10px 12px;
        border-bottom: 1px solid #eef2f7;
        text-align: right;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .customer-invoice-table td:last-child {
        align-items: center;
        border-bottom: 0;
    }

    .customer-invoice-table td::before {
        flex: 0 0 86px;
        color: var(--text-muted);
        content: "";
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-align: left;
        text-transform: uppercase;
    }

    .customer-invoice-table td:nth-child(1)::before { content: "#"; }
    .customer-invoice-table td:nth-child(2)::before { content: "Invoice"; }
    .customer-invoice-table td:nth-child(3)::before { content: "Date"; }
    .customer-invoice-table td:nth-child(4)::before { content: "Total"; }
    .customer-invoice-table td:nth-child(5)::before { content: "Paid"; }
    .customer-invoice-table td:nth-child(6)::before { content: "Due"; }
    .customer-invoice-table td:nth-child(7)::before { content: "Status"; }
    .customer-invoice-table td:nth-child(8)::before { content: "Action"; }

    .customer-invoice-table .btn-icon {
        width: 40px;
        height: 40px;
        padding: 0;
    }

    .pagination-wrapper {
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .pagination-wrapper .pagination {
        justify-content: flex-start;
        min-width: max-content;
    }
}
</style>
@endpush
