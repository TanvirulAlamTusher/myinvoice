@extends('app')

@section('title', 'Products')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Products</h1>
                    <p class="text-muted mt-4">Manage your inventory products</p>
                </div>

                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    ➕ Add Product
                </a>

            </div>

            <div class="divider"></div>

            {{-- ================= STOCK STATS ================= --}}
            @can('product.statistics.view')
                <div class="stats-grid">

                    <div class="stat-box">
                        <div class="text-muted">Total Stock</div>
                        <div class="stat-value">{{ $totalStock }}</div>
                    </div>

                    <div class="stat-box">
                        <div class="text-muted">Stock Value (Cost)</div>
                        <div class="stat-value">
                            {{ number_format($totalPurchaseValue, 2) }}
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="text-muted">Stock Value (Selling)</div>
                        <div class="stat-value text-teal">
                            {{ number_format($totalSellingValue, 2) }}
                        </div>
                    </div>

                </div>
            @endcan

            {{-- ================= FILTER ================= --}}
            <form id="filterForm" class="filter-form product-filter">

                {{-- SEARCH --}}
                <div class="search-box">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Search name or SKU..." class="form-input filter-input">
                </div>

                {{-- CATEGORY --}}
                <select name="category" class="form-input filter-input">
                    <option value="">All Category</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                {{-- BRAND --}}
                <select name="brand" class="form-input filter-input">
                    <option value="">All Brand</option>
                    <option value="none" {{ request('brand') == 'none' ? 'selected' : '' }}>
                        Non Brand
                    </option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>

                {{-- STOCK --}}
                <select name="stock" class="form-input filter-input">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>
                        Low Stock
                    </option>
                </select>

                {{-- STATUS --}}
                <select name="status" class="form-input filter-input">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>

                {{-- RESET --}}
                <button type="button" class="btn btn-danger" id="resetBtn">
                    Reset
                </button>

            </form>

            <div class="divider"></div>

            {{-- ================= TABLE ================= --}}
            <div id="productTable">
                @include('products.partials.table')
            </div>

        </div>

    </div>

@endsection


{{-- ================= AJAX ================= --}}
@push('scripts')
    <script>
        let timer;

        function fetchProducts() {

            let form = document.getElementById('filterForm');
            let formData = new FormData(form);

            axios.get("{{ route('products.index') }}", {
                    params: Object.fromEntries(formData)
                })
                .then(res => {
                    document.getElementById('productTable').innerHTML = res.data.html;
                })
                .catch(err => console.log(err));
        }

        /* ================= LIVE SEARCH ================= */
        document.getElementById('searchInput').addEventListener('input', function() {

            clearTimeout(timer);

            timer = setTimeout(fetchProducts, 400);

        });

        /* ================= FILTER CHANGE ================= */
        document.querySelectorAll('.filter-input').forEach(el => {
            el.addEventListener('change', fetchProducts);
        });

        /* ================= RESET ================= */
        document.getElementById('resetBtn').addEventListener('click', function() {

            document.getElementById('filterForm').reset();
            document.getElementById('searchInput').value = '';

            fetchProducts();
        });
    </script>
@endpush
