@extends('app')

@section('title', 'Edit Product')

@section('content')

<div class="page-layout product-edit-page">

    <form method="POST"
          action="{{ route('products.update', $product->id) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Edit Product</h1>
                    <p class="text-muted mt-4">
                        Update product information
                    </p>
                </div>

                <div class="flex gap-2 product-edit-actions">

                    <a href="{{ route('products.index') }}"
                       class="btn btn-ghost">← Back</a>

                    <button type="submit"
                            class="btn btn-primary">

                        Update Product

                    </button>

                </div>

            </div>

            <div class="divider"></div>

            {{-- ================= GRID ================= --}}
            <div class="product-grid">

                {{-- ================= LEFT ================= --}}
                <div class="product-edit-main">

                    {{-- BASIC INFO --}}
                    <div class="card-section">

                        <h3 class="section-title">Basic Information</h3>

                        {{-- NAME --}}
                        <div class="field">
                            <label>Product Name *</label>
                            <input type="text"
                                   name="name"
                                   class="no-icon"
                                   value="{{ old('name', $product->name) }}"
                                   required>
                        </div>

                        {{-- SKU + BARCODE --}}
                        <div class="grid-2">

                            <div class="field">
                                <label>SKU</label>
                                <input type="text"
                                       name="sku"
                                       class="no-icon"
                                       value="{{ old('sku', $product->sku) }}">
                            </div>

                            <div class="field">
                                <label>Barcode</label>
                                <input type="text"
                                       name="barcode"
                                       class="no-icon"
                                       value="{{ old('barcode', $product->barcode) }}">
                            </div>

                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="field">
                            <label>Description</label>
                            <textarea name="description"
                                      class="no-icon"
                                      rows="5">{{ old('description', $product->description) }}</textarea>
                        </div>

                    </div>

                    {{-- INVENTORY --}}
                    <div class="card-section mt-5">

                        <h3 class="section-title">Inventory</h3>

                        <div class="grid-3">

                            <div class="field">
                                <label>Stock</label>
                                <input type="number"
                                     
                                       name="stock"
                                       class="no-icon"
                                       value="{{ old('stock', $product->stock) }}">
                            </div>

                            <div class="field">
                                <label>Alert Stock</label>
                                <input type="number"

                                       name="alert_stock"
                                       class="no-icon"
                                       value="{{ old('alert_stock', $product->alert_stock) }}">
                            </div>

                            <div class="field">
                                <label>Weight (kg)</label>
                                <input type="number"
                                       step="0.01"
                                       name="weight"
                                       class="no-icon"
                                       value="{{ old('weight', $product->weight) }}">
                            </div>

                        </div>

                    </div>

                    {{-- PRICING --}}
                    <div class="card-section mt-5">

                        <h3 class="section-title">Pricing</h3>

                        <div class="grid-2">

                            <div class="field">
                                <label>Purchase Price</label>
                                <input type="number"

                                       name="purchase_price"
                                       class="no-icon"
                                       value="{{ old('purchase_price', $product->purchase_price) }}"
                                       required>
                            </div>

                            <div class="field">
                                <label>Sale Price *</label>
                                <input type="number"

                                       name="sale_price"
                                       class="no-icon"
                                       value="{{ old('sale_price', $product->sale_price) }}"
                                       required>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="product-edit-side">

                    {{-- IMAGE --}}
                    <div class="card-section">

                        <h3 class="section-title">Product Image</h3>

                        <div class="image-upload-box">

                            <img id="previewImage"
                                 src="{{ $product->image ? asset('storage/'.$product->image) : asset('no-image.png') }}"
                                 alt="product image">

                        </div>

                        <div class="field mt-4">

                            <input type="file"
                                   name="image"
                                   id="imageInput"
                                   accept="image/*"
                                   class="no-icon">

                        </div>

                    </div>

                    {{-- RELATIONS --}}
                    <div class="card-section mt-5">

                        <h3 class="section-title">Product Details</h3>

                        {{-- CATEGORY --}}
                        <div class="field">

                            <label>Category</label>

                            <select name="category_id">

                                <option value="">Select Category</option>

                                @foreach($categories as $category)

                                    <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>

                                        {{ $category->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- BRAND --}}
                        <div class="field">

                            <label>Brand</label>

                            <select name="brand_id">

                                <option value="">Select Brand</option>

                                @foreach($brands as $brand)

                                    <option value="{{ $brand->id }}"
                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>

                                        {{ $brand->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- UNIT --}}
                        <div class="field">

                            <label>Unit</label>

                            <select name="product_unit_id">

                                <option value="">Select Unit</option>

                                @foreach($units as $unit)

                                    <option value="{{ $unit->id }}"
                                        {{ $product->product_unit_id == $unit->id ? 'selected' : '' }}>

                                        {{ $unit->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- STATUS --}}
                        <label class="remember mt-4">

                            <input type="checkbox"
                                   name="is_active"
                                   {{ $product->is_active ? 'checked' : '' }}>

                            <span>Active Product</span>

                        </label>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

{{-- ================= STYLE ================= --}}
<style>

.product-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:24px;
}

.card-section{
    background:var(--surface-1);
    border:1px solid var(--border);
    border-radius:var(--radius-xl);
    padding:24px;
}

.section-title{
    margin-bottom:20px;
    font-size:1rem;
    font-weight:700;
}

.grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

.grid-3{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
}

.image-upload-box{
    width:100%;
    height:260px;
    border:2px dashed var(--border);
    border-radius:var(--radius-xl);
    overflow:hidden;
    background:var(--surface-2);
    display:flex;
    align-items:center;
    justify-content:center;
}

.image-upload-box img{
    width:100%;
    height:100%;
    object-fit:contain;
}

@media(max-width:992px){
    .product-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:768px){
    .product-edit-page{
        padding-inline:10px;
    }

    .product-edit-page .card{
        border-radius:14px;
        padding:16px;
    }

    .product-edit-page .card-header{
        gap:14px;
    }

    .product-edit-page .card-header > div:first-child{
        min-width:0;
    }

    .product-edit-actions{
        width:100%;
        align-items:stretch;
    }

    .product-edit-actions .btn{
        flex:1;
        min-width:0;
        min-height:42px;
    }

    .product-grid{
        gap:16px;
    }

    .product-edit-main,
    .product-edit-side{
        display:grid;
        gap:16px;
    }

    .card-section{
        padding:16px;
        border-radius:14px;
    }

    .section-title{
        margin-bottom:14px;
    }

    .grid-2,
    .grid-3{
        grid-template-columns:1fr;
        gap:10px;
    }

    .field{
        margin-bottom:14px;
    }

    .field input,
    .field select,
    .field textarea{
        min-height:46px;
        font-size:16px;
    }

    .field textarea{
        min-height:120px;
    }

    .image-upload-box{
        height:auto;
        min-height:180px;
        aspect-ratio:4 / 3;
        border-radius:14px;
    }

    .image-upload-box img{
        padding:8px;
    }

    input[type="file"].no-icon{
        width:100%;
        min-height:46px;
        padding:10px;
        border:1.5px solid var(--border);
        border-radius:var(--radius-md);
        background:var(--surface-1);
    }

    .remember{
        min-height:44px;
        align-items:center;
        padding:10px 0;
    }
}

@media(max-width:420px){
    .product-edit-actions{
        flex-direction:column;
    }
}

</style>

{{-- ================= IMAGE PREVIEW ================= --}}
<script>

document.getElementById('imageInput')
.addEventListener('change', function(e){

    const file = e.target.files[0];

    if(file){
        document.getElementById('previewImage').src =
            URL.createObjectURL(file);
    }

});

</script>

@endsection
