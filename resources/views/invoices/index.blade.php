@extends('app')

@section('title', 'Invoices')

@section('content')

    <div class="page-layout invoice-page-layout">

        <div class="card card-full">

            <div class="card-header">
                <div>
                    <h1 class="page-title">Invoices</h1>
                    <p class="text-muted mt-4">Create invoices, track payments, and manage stock movement</p>
                </div>


                @can('invoice.create')
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                        Add Invoice
                    </a>
                @endcan
            </div>

            <div class="divider"></div>
            @can('invoice.statistics.view')
                <div class="stats-grid invoice-stats-grid">
                    <div class="stat-box">
                        <div class="text-muted">Total Invoices</div>
                        <div class="stat-value">{{ $totalInvoices }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="text-muted">Completed Sales</div>
                        <div class="stat-value text-teal">{{ number_format($completedTotal, 2) }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="text-muted">Paid Amount</div>
                        <div class="stat-value">{{ number_format($paidTotal, 2) }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="text-muted">Due Amount</div>
                        <div class="stat-value">{{ number_format($dueTotal, 2) }}</div>
                    </div>
                </div>
            @endcan

            <form id="invoiceFilterForm" class="filter-form product-filter">
                <div class="search-box">
                    <input type="text" id="invoiceSearchInput" name="search" value="{{ request('search') }}"
                        placeholder="Search invoice or customer..." class="form-input filter-input" autocomplete="off">
                </div>

                <select name="status" class="form-input filter-input">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="payment_status" class="form-input filter-input">
                    <option value="">All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial
                    </option>
                    <option value="due" {{ request('payment_status') === 'due' ? 'selected' : '' }}>Due</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input filter-input">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input filter-input">

                <button type="button" class="btn btn-danger" id="invoiceResetBtn">Reset</button>
            </form>

            <div class="divider"></div>

            <div id="invoiceTable">
                @include('invoices.partials.table')
            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        let invoiceSearchTimer;

        function fetchInvoices(url = "{{ route('invoices.index') }}") {
            const form = document.getElementById('invoiceFilterForm');
            const formData = new FormData(form);

            axios.get(url, {
                params: Object.fromEntries(formData)
            }).then(res => {
                document.getElementById('invoiceTable').innerHTML = res.data.html;
            }).catch(() => {
                toast('Failed to load invoices', 'error');
            });
        }

        document.getElementById('invoiceSearchInput').addEventListener('input', function() {
            clearTimeout(invoiceSearchTimer);
            invoiceSearchTimer = setTimeout(() => fetchInvoices(), 300);
        });

        document.querySelectorAll('#invoiceFilterForm .filter-input').forEach(el => {
            el.addEventListener('change', () => fetchInvoices());
        });

        document.getElementById('invoiceResetBtn').addEventListener('click', function() {
            document.getElementById('invoiceFilterForm').reset();
            document.getElementById('invoiceSearchInput').value = '';
            fetchInvoices();
        });

        document.getElementById('invoiceTable').addEventListener('click', function(event) {
            const link = event.target.closest('.pagination a');

            if (!link) {
                return;
            }

            event.preventDefault();
            fetchInvoices(link.href);
        });
    </script>
@endpush
