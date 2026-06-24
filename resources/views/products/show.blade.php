@extends('app')

@section('title', 'Product Details')

@section('content')

    <div class="page-layout product-show-page">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">{{ $product->name }}</h1>

                    <p class="text-muted mt-4">
                        Product details overview
                    </p>
                </div>

                <div class="flex gap-2 product-show-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-ghost">
                        ← Back
                    </a>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                        ✏️ Edit
                    </a>



                </div>

            </div>

            <div class="divider"></div>

            {{-- ================= CONTENT ================= --}}
            <div class="product-show-grid" style="display:grid; grid-template-columns: 300px 1fr; gap:24px;">

                {{-- LEFT SIDE IMAGE --}}
                <div class="product-show-media">

                    {{-- PRODUCT IMAGE --}}
                    <div class="product-image-panel"
                        style="
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 16px;
        text-align:center;
    ">

                        <img class="product-show-image" src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default_product_image.png') }}"
                            style="width:100%; max-height:260px; object-fit:cover; border-radius:12px;">

                    </div>

                    {{-- BRAND IMAGE --}}
                    @if ($product->brand && $product->brand->image)
                        <div class="product-brand-panel"
                            style="
            margin-top:12px;
            background: var(--surface-1);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 12px;
            text-align:center;
        ">

                            <p style="margin-bottom:8px; font-size:12px; color:var(--text-muted);">
                                Brand
                            </p>

                            <img src="{{ asset('storage/' . $product->brand->image) }}"
                                style="
                    width:70px;
                    height:70px;
                    object-fit:cover;
                    border-radius:12px;
                    border:1px solid var(--border);
                 ">

                            <p style="margin-top:6px; font-weight:600;">
                                {{ $product->brand->name }}
                            </p>

                        </div>
                    @endif

                    {{-- STATUS BADGE --}}
                    <div class="product-status-panel" style="margin-top:12px; text-align:center;">

                        @if ($product->is_active)
                            <span class="badge badge-teal">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif

                    </div>

                </div>

                {{-- RIGHT SIDE INFO --}}
                <div class="product-show-info">

                    <div class="tbl-wrap product-detail-wrap">

                        <table class="tbl product-detail-table">

                            <tbody>

                                <tr>
                                    <th>Name</th>
                                    <td>{{ $product->name }}</td>
                                </tr>

                                <tr>
                                    <th>SKU</th>
                                    <td>{{ $product->sku ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Barcode</th>
                                    <td>{{ $product->barcode ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Category</th>
                                    <td>{{ $product->category->name ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Brand</th>
                                    <td>{{ $product->brand->name ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Unit</th>
                                    <td>{{ $product->productUnit->name ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Stock</th>
                                    <td>

                                        @if ($product->stock <= $product->alert_stock)
                                            <span class="badge badge-danger">
                                                {{ $product->stock }} (Low Stock)
                                            </span>
                                        @else
                                            <span class="badge badge-teal">
                                                {{ $product->stock }}
                                            </span>
                                        @endif

                                    </td>
                                </tr>

                                <tr>
                                    <th>Alert Stock</th>
                                    <td>{{ $product->alert_stock }}</td>
                                </tr>

                                <tr>
                                    <th>Purchase Price</th>
                                    <td>{{ number_format($product->purchase_price ?? 0, 2) }}</td>
                                </tr>

                                <tr>
                                    <th>Sale Price</th>
                                    <td>{{ number_format($product->sale_price, 2) }}</td>
                                </tr>

                                <tr>
                                    <th>Weight</th>
                                    <td>{{ $product->weight ?? '—' }} kg</td>
                                </tr>

                                <tr>
                                    <th>Description</th>
                                    <td>{{ $product->description ?? '—' }}</td>
                                </tr>

                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $product->created_at->format('d M Y, h:i A') }}</td>
                                </tr>

                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $product->updated_at->format('d M Y, h:i A') }}</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .product-show-page {
        padding-inline: 10px;
    }

    .product-show-page > .card {
        border-radius: 14px;
    }

    .product-show-page .card-header {
        gap: 14px;
    }

    .product-show-actions {
        width: 100%;
        align-items: stretch;
    }

    .product-show-actions .btn {
        flex: 1;
        min-width: 0;
    }

    .product-show-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }

    .product-show-media {
        display: grid;
        gap: 12px;
    }

    .product-image-panel,
    .product-brand-panel {
        border-radius: 14px !important;
    }

    .product-image-panel {
        padding: 12px !important;
    }

    .product-show-image {
        width: 100% !important;
        max-height: none !important;
        aspect-ratio: 4 / 3;
        object-fit: contain !important;
        background: var(--surface-2);
    }

    .product-status-panel {
        margin-top: 0 !important;
    }

    .product-detail-wrap {
        overflow: visible;
    }

    .product-detail-table,
    .product-detail-table tbody,
    .product-detail-table tr,
    .product-detail-table th,
    .product-detail-table td {
        display: block;
        width: 100%;
        min-width: 0;
    }

    .product-detail-table {
        min-width: 0 !important;
    }

    .product-detail-table tr {
        padding: 11px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .product-detail-table tr:first-child {
        padding-top: 0;
    }

    .product-detail-table tr:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .product-detail-table th,
    .product-detail-table td {
        padding: 0;
        border: 0;
        text-align: left;
        white-space: normal;
    }

    .product-detail-table th {
        margin-bottom: 4px;
        color: var(--text-muted);
        font-size: .72rem;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .product-detail-table td {
        max-width: none !important;
        overflow-wrap: anywhere;
        font-size: .92rem;
    }
}

@media (max-width: 420px) {
    .product-show-actions {
        flex-direction: column;
    }
}
</style>
@endpush
