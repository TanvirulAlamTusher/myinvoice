@extends('app')

@section('title', 'Customers')

@section('content')

<div class="page-layout customer-page-layout">

    <div class="card card-full customer-card">

        {{-- ================= HEADER ================= --}}
        <div class="card-header">

            <div>
                <h1 class="page-title">Customers</h1>
                <p class="text-muted mt-4">Manage invoice customer records</p>
            </div>
@can('customer.create')
            <button class="btn btn-primary" type="button" onclick="openModal('createModal')">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Customer
            </button>
            @endcan

        </div>

        <div class="divider"></div>

        {{-- ================= FLASH / ERRORS ================= --}}
        @if ($errors->any())
            <div class="alert alert-error mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= CUSTOMER STATS ================= --}}
        <div class="stats-grid">

            <div class="stat-box">
                <div class="text-muted">Total Customers</div>
                <div class="stat-value">{{ $totalCustomers }}</div>
            </div>

            <div class="stat-box">
                <div class="text-muted">Active Customers</div>
                <div class="stat-value text-teal">{{ $totalActiveCustomers }}</div>
            </div>

            <div class="stat-box">
                <div class="text-muted">Inactive Customers</div>
                <div class="stat-value">{{ $totalInactiveCustomers }}</div>
            </div>

        </div>

        {{-- ================= SEARCH ================= --}}
        <form id="customerSearchForm" class="filter-form product-filter">
            <div class="search-box">
                <input type="text"
                       id="customerSearchInput"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search name, phone, email..."
                       class="form-input filter-input"
                       autocomplete="off">
            </div>

            <select name="status" class="form-input filter-input" id="customerStatusFilter">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="button" class="btn btn-danger" id="customerSearchReset">
                Reset
            </button>
        </form>

        <div class="divider"></div>

        {{-- ================= TABLE ================= --}}
        <div id="customerTable">
            @include('customers.partials.table')
        </div>

    </div>

</div>

@include('customers.modal.create')
@include('customers.modal.edit')


@endsection


{{-- ================= SCRIPT ================= --}}
@push('scripts')
<script>
function openModal(id){
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id).classList.add('hidden');
}

function editCustomer(button){
    const data = button.dataset;
    const form = document.getElementById('editForm');

    form.action = data.updateUrl;
    document.getElementById('edit_name').value = data.name || '';
    document.getElementById('edit_business_name').value = data.businessName || '';
    document.getElementById('edit_phone').value = data.phone || '';
    document.getElementById('edit_alternative_phone').value = data.alternativePhone || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_address').value = data.address || '';
    document.getElementById('edit_is_active').checked = data.isActive === '1';

    openModal('editModal');
}

let customerSearchTimer;

function fetchCustomers(url = "{{ route('customers.index') }}") {
    const form = document.getElementById('customerSearchForm');
    const formData = new FormData(form);

    axios.get(url, {
        params: Object.fromEntries(formData)
    })
    .then(res => {
        document.getElementById('customerTable').innerHTML = res.data.html;
    })
    .catch(() => {
        toast('Failed to search customers', 'error');
    });
}

document.getElementById('customerSearchInput').addEventListener('input', function () {
    clearTimeout(customerSearchTimer);
    customerSearchTimer = setTimeout(() => fetchCustomers(), 300);
});

document.getElementById('customerStatusFilter').addEventListener('change', function () {
    fetchCustomers();
});

document.getElementById('customerSearchReset').addEventListener('click', function () {
    document.getElementById('customerSearchForm').reset();
    document.getElementById('customerSearchInput').value = '';
    fetchCustomers();
});

document.getElementById('customerTable').addEventListener('click', function (event) {
    const link = event.target.closest('.pagination a');

    if (!link) {
        return;
    }

    event.preventDefault();
    fetchCustomers(link.href);
});
</script>
@endpush
