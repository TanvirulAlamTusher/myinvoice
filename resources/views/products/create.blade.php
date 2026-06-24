@extends('app')

@section('title', 'Create Product')

@section('content')

<div class="page-layout">

    <form method="POST"
          action="{{ route('products.store') }}"
          enctype="multipart/form-data">

        @csrf

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Create Product</h1>

                    <p class="text-muted mt-4">
                        Add a new product to inventory
                    </p>
                </div>

                <div class="flex gap-2">

                    <a href="{{ route('products.index') }}"
                       class="btn btn-ghost">← Back </a>

                    <button type="submit" class="btn btn-primary">

                        Save Product

                    </button>

                </div>

            </div>

            <div class="divider"></div>

            {{-- ================= FORM GRID ================= --}}
            <div class="product-grid">

                {{-- ================= LEFT ================= --}}
                <div>

                    {{-- BASIC INFO --}}
                    <div class="card-section">

                        <h3 class="section-title">
                            Basic Information
                        </h3>

                        {{-- NAME --}}
                        <div class="field">

                            <label>Product Name *</label>

                            <input type="text"
                                   name="name"
                                   class="no-icon"
                                   value="{{ old('name') }}"
                                   required>

                            @error('name')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        {{-- SKU + BARCODE --}}
                        <div class="grid-2">

                            <div class="field">

                                <label>SKU</label>

                                <input type="text"
                                       name="sku"
                                       class="no-icon"
                                       value="{{ old('sku') }}">

                            </div>

                            <div class="field">

                                <label>Barcode</label>

                                <input type="text"
                                       name="barcode"
                                       class="no-icon"
                                       value="{{ old('barcode') }}">

                            </div>

                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="field">

                            <label>Description</label>

                            <textarea name="description"
                                      class="no-icon"
                                      rows="5">{{ old('description') }}</textarea>

                        </div>

                    </div>

                    {{-- INVENTORY --}}
                    <div class="card-section mt-5">

                        <h3 class="section-title">
                            Inventory
                        </h3>

                        <div class="grid-3">

                            {{-- STOCK --}}
                            <div class="field">

                                <label>Current Stock *</label>

                                <input type="number"

                                       name="stock"
                                       class="no-icon"
                                       value="{{ old('stock', 0) }}"
                                       required>

                            </div>

                            {{-- ALERT STOCK --}}
                            <div class="field">

                                <label>Alert Stock *</label>

                                <input type="number"
                                      
                                       name="alert_stock"
                                       class="no-icon"
                                       value="{{ old('alert_stock', 5) }}"
                                       required>

                            </div>

                            {{-- WEIGHT --}}
                            <div class="field">

                                <label>Weight (kg)</label>

                                <input type="number"
                                       step="0.01"
                                       name="weight"
                                       class="no-icon"
                                       value="{{ old('weight') }}">

                            </div>

                        </div>

                    </div>

                    {{-- PRICING --}}
                    <div class="card-section mt-5">

                        <h3 class="section-title">
                            Pricing
                        </h3>

                        <div class="grid-2">

                            {{-- PURCHASE --}}
                            <div class="field">

                                <label>Purchase Price</label>

                                <input type="number"

                                       name="purchase_price"
                                       class="no-icon"
                                       value="{{ old('purchase_price') }}"
                                       required>

                            </div>

                            {{-- SALE --}}
                            <div class="field">

                                <label>Default Sale Price *</label>

                                <input type="number"

                                       name="sale_price"
                                       class="no-icon"
                                       value="{{ old('sale_price') }}"
                                       required>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ================= RIGHT ================= --}}
                <div>

                    {{-- IMAGE --}}
                    <div class="card-section">

                        <h3 class="section-title">
                            Product Image
                        </h3>

                        <div class="image-upload-box">

                            <img id="previewImage"
                                 src="{{ asset('no-image.png') }}"
                                 alt="preview">

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

                        <h3 class="section-title">
                            Product Details
                        </h3>

                        {{-- CATEGORY --}}
                        <div class="field">

                            <label>Category</label>

                            <select name="category_id">

                                <option value="">
                                    Select Category
                                </option>

                                @foreach($categories as $category)

                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>

                                        {{ $category->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- BRAND --}}
                        <div class="field">

                            <label>Brand</label>

                            <select name="brand_id">

                                <option value="">
                                    Select Brand
                                </option>

                                @foreach($brands as $brand)

                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>

                                        {{ $brand->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- UNIT --}}
                        <div class="field">

                            <label>Unit</label>

                            <select name="product_unit_id">

                                <option value="">
                                    Select Unit
                                </option>

                                @foreach($units as $unit)

                                    <option value="{{ $unit->id }}"
                                        {{ old('product_unit_id') == $unit->id ? 'selected' : '' }}>

                                        {{ $unit->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- STATUS --}}
                        <label class="remember mt-4">

                            <input type="checkbox"
                                   name="is_active"
                                   checked>

                            <span>Active Product</span>

                        </label>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

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
