@include('products.partials.stockModal')
<div class="tbl-wrap">

    <table class="tbl">

        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($products as $key => $product)
                @php
                    $lowStock = $product->stock <= $product->alert_stock;
                @endphp

                <tr>

                    <td class="text-muted">
                        {{ $products->firstItem() + $key }}
                    </td>

                    <td>
                        <a href="{{ route('products.show', $product->id) }}">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default_product_image.png') }}"
                                style="width:50px;height:50px;border-radius:12px;object-fit:cover;border:1px solid var(--border);">
                        </a>
                    </td>

                    <td>
                        <a href="{{ route('products.show', $product->id) }}" class="text-teal">
                            <strong>{{ $product->name }}</strong><br>
                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                        </a>
                    </td>

                    <td>{{ $product->brand->name ?? '—' }}</td>
                    <td>{{ $product->category->name ?? '—' }}</td>

                    <td id="stock-{{ $product->id }}">
                        @if ($lowStock)
                            <span class="badge badge-danger">{{ $product->stock }}</span>
                        @else
                            <span class="badge badge-teal">{{ $product->stock }}</span>
                        @endif
                    </td>

                    <td>
                        <small>Buy: {{ number_format($product->purchase_price, 2) }}</small><br>
                        <strong>Sell: {{ number_format($product->sale_price, 2) }}</strong>
                    </td>

                    <td>
                        @if ($product->is_active)
                            <span class="badge badge-teal">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>

                    <td>
                        <div class="flex gap-2">

                            {{-- + STOCK (OUTSIDE MENU) --}}
                            @can('product.stock.increment')
                            <button type="button" class="btn btn-ghost btn-icon"
                                onclick="openStockModal(
            '{{ route('products.increaseStock', $product->id) }}',
            '{{ $product->name }}',
            '{{ $product->id }}'
        )">
                                + Stock
                            </button>
                            @endcan
                            {{-- VIEW (optional outside OR inside menu — here inside menu) --}}

                            {{-- 3 DOT MENU --}}
                            <div class="action-dropdown">

                                <button type="button" class="btn btn-ghost btn-icon action-toggle">
                                    ⋮
                                </button>

                                <div class="action-menu">

                                    <a href="{{ route('products.show', $product->id) }}" class="action-menu-item">
                                        👁️ View
                                    </a>

                                    <a href="{{ route('products.edit', $product->id) }}" class="action-menu-item">
                                        ✏️ Edit
                                    </a>

                                    <button type="button" class="action-menu-item action-delete"
                                        onclick="openDeleteModal(
                    '{{ route('products.destroy', $product->id) }}',
                    '{{ $product->name }}'
                )">
                                        🗑️ Delete
                                    </button>

                                </div>

                            </div>

                        </div>
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:20px;">
                        No products found
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>
</div>

{{-- pagination --}}
<div style="margin-top: 15px; display:flex; justify-content:center;">
    {{ $products->links() }}
</div>
