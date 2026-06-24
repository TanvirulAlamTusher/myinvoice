@php
    $invoiceItems = old('items');

    if (!$invoiceItems) {
        $invoiceItems =
            $invoice->items
                ?->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'purchase_price' => $item->purchase_price,
                        'unit_price' => $item->unit_price,
                    ];
                })
                ->values()
                ->all() ?:
            [];
    }

    $productPayload = $products
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->productUnit?->name,
                'purchase_price' => (float) $product->purchase_price,
                'sale_price' => (float) $product->sale_price,
                'stock' => (float) $product->stock,
                'alert_stock' => (float) $product->alert_stock,
                'image' => $product->image
                    ? asset('storage/' . $product->image)
                    : asset('images/default_product_image.png'),
            ];
        })
        ->values();

    $customerPayload = $customers
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'business_name' => $customer->business_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
            ];
        })
        ->values();
@endphp

<div class="inv-wrap">

    {{-- ══════════ LEFT: PRODUCT SIDEBAR ══════════ --}}
    <aside class="inv-sidebar">

        <div class="inv-sidebar-head">
            <h4>Products</h4>

            <div class="inv-search">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="productSearch" placeholder="Search by name or SKU…">
            </div>
        </div>

        <div class="inv-product-list" id="productList"></div>

    </aside>

    {{-- ══════════ RIGHT: INVOICE FORM ══════════ --}}
    <main class="inv-main">
        <div class="inv-card">

            {{-- Header --}}
            <div class="inv-card-header">
                <div>
                    <h1>{{ $title }}</h1>
                    <p>{{ $subtitle }}</p>
                </div>

                <div class="inv-header-actions">
                    <a href="{{ route('invoices.index') }}" class="btn btn-ghost">← Back</a>
                    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                </div>
            </div>

            <hr class="inv-divider">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="inv-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Two-column form --}}
            <div class="inv-form-grid">

                {{-- LEFT col --}}
                <div class="inv-form-col">

                    {{-- Invoice Details --}}
                    <div class="inv-section">
                        <p class="inv-section-title">Invoice Details</p>

                        <div class="inv-grid-3">

                            <div class="inv-field">
                                <label>Invoice No <span style="color:var(--danger)">*</span></label>
                                <input type="text" name="invoice_no"
                                    value="{{ old('invoice_no', $invoice->invoice_no) }}" required
                                    placeholder="INV-0001">
                            </div>

                            <div class="inv-field">
                                <label>Invoice Date <span style="color:var(--danger)">*</span></label>
                                <input type="datetime-local" name="invoice_date"
                                    value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                                    required>
                            </div>

                            <div class="inv-field">
                                <label>Status <span style="color:var(--danger)">*</span></label>
                                <select name="status" required>
                                    <option value="completed"
                                        {{ old('status', $invoice->status ?? 'completed') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="draft"
                                        {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Invoice Items --}}
                    <div class="inv-section">
                        <div class="inv-section-bar"
                            style="margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                            <p class="inv-section-title">Invoice Items</p>
                            <button type="button" class="btn-add-row" id="addInvoiceItem">+ Add Row</button>
                        </div>

                        <div class="inv-table-wrap">
                            <table class="inv-table">
                                <thead>
                                    <tr>
                                        <th class="col-product">Product</th>
                                        <th class="col-qty">Qty</th>
                                        <th class="col-unit">Unit</th>

                                        <th class="col-price">Unit Price</th>
                                        <th class="col-total">Total</th>
                                        <th class="col-purchase-price">P Price(per unit)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceItemsBody">
                                    {{-- rows injected by JS --}}
                                </tbody>
                            </table>

                            {{-- Empty state shown when no rows exist --}}
                            <div id="invoiceItemsEmpty"
                                style="
                                display:none;
                                text-align:center;
                                padding:28px 16px;
                                color:var(--text-muted);
                                font-size:.85rem;
                                border-top:1px solid var(--border);
                            ">
                                No items yet — click <strong>+ Add Row</strong> or select a product from the sidebar.
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT col --}}
                <div class="inv-form-col">

                    {{-- Customer --}}
                    <div class="inv-section">
                        <p class="inv-section-title">Customer</p>

                        <div class="inv-field">
                            <label>Saved Customer</label>

                            <div class="customer-search-wrap">

                                <input type="text" id="customerSearch" class="customer-search-input"
                                    placeholder="Search customer by name or phone..." autocomplete="new-password"
                                    autocorrect="off" autocapitalize="off" spellcheck="false">

                                <input type="hidden" name="customer_id" id="customerSelect"
                                    value="{{ old('customer_id', $invoice->customer_id) }}">

                                <div class="customer-dropdown" id="customerDropdown">

                                    <div class="customer-dropdown-item active" data-id="">
                                        Walk-in / Manual Customer
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="inv-field">
                            <label>Full Name</label>
                            <input type="text" name="customer_name" id="customerName"
                                value="{{ old('customer_name', $invoice->customer_name) }}"
                                placeholder="Customer name">
                        </div>

                        <div class="inv-field">
                            <label>Business Name</label>
                            <input type="text" name="customer_business_name" id="customerBusinessName"
                                value="{{ old('customer_business_name', $invoice->customer_business_name) }}"
                                placeholder="Business / Company">
                        </div>

                        <div class="inv-grid-2">
                            <div class="inv-field">
                                <label>Phone</label>
                                <input type="text" name="customer_phone" id="customerPhone"
                                    value="{{ old('customer_phone', $invoice->customer_phone) }}"
                                    placeholder="+880…">
                            </div>

                            <div class="inv-field">
                                <label>Email</label>
                                <input type="email" name="customer_email" id="customerEmail"
                                    value="{{ old('customer_email', $invoice->customer_email) }}"
                                    placeholder="email@…">
                            </div>
                        </div>

                        <div class="inv-field">
                            <label>Address</label>
                            <textarea name="customer_address" id="customerAddress" rows="2" placeholder="Street, city…">{{ old('customer_address', $invoice->customer_address) }}</textarea>
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div class="inv-section">
                        <p class="inv-section-title">Payment</p>

                        <div class="inv-grid-2">
                            <div class="inv-field">
                                <label>Discount (৳)</label>
                                <input type="number" min="0" name="discount_amount" id="discountAmount"
                                    value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}"
                                    placeholder="0">
                            </div>

                            <div class="inv-field">
                                <label>Tax (৳)</label>
                                <input type="number" min="0" name="tax_amount" id="taxAmount"
                                    value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}" placeholder="0">
                            </div>
                        </div>

                        <div class="inv-grid-2">
                            <div class="inv-field">
                                <label>Paid Amount (৳)</label>
                                <input type="number" min="0" name="paid_amount" id="paidAmount"
                                    value="{{ old('paid_amount', $invoice->paid_amount ?? 0) }}" placeholder="0">
                            </div>

                            <div class="inv-field">
                                <label>Payment Method <span style="color:var(--danger)">*</span></label>
                                <select name="payment_method" required>
                                    @foreach (['cash' => 'Cash', 'bank' => 'Bank', 'cheque' => 'Cheque', 'mobile_banking' => 'Mobile Banking', 'credit' => 'Credit'] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('payment_method', $invoice->payment_method ?? 'cash') === $value ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="inv-summary">
                            <div class="inv-summary-row">
                                <span>Subtotal</span>
                                <strong id="summarySubTotal">0.00</strong>
                            </div>
                            <div class="inv-summary-row">
                                <span>Discount</span>
                                <strong id="summaryDiscount">0.00</strong>
                            </div>
                            <div class="inv-summary-row">
                                <span>Tax</span>
                                <strong id="summaryTax">0.00</strong>
                            </div>
                            <div class="inv-summary-row inv-summary-total">
                                <span>Grand Total</span>
                                <strong id="summaryGrandTotal">0.00</strong>
                            </div>
                            <div class="inv-summary-row">
                                <span>Paid</span>
                                <strong id="summaryPaid">0.00</strong>
                            </div>
                            <div class="inv-summary-row" style="background:var(--danger-light);">
                                <span style="color:var(--danger);font-weight:600;">Due</span>
                                <strong id="summaryDue" style="color:var(--danger);">0.00</strong>
                            </div>
                        </div>

                        <div class="inv-field" style="margin-top:14px;">
                            <label>Note</label>
                            <textarea name="note" rows="3" placeholder="Additional notes…">{{ old('note', $invoice->note) }}</textarea>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>

</div>

{{-- Row template --}}
<template id="invoiceItemTemplate">
    <tr>
        <td class="col-product">
            <input type="text" class="item-product-name" placeholder="Product name">
            <input type="hidden" class="item-product-id">
            <input type="hidden" class="item-product-sku">
        </td>
        <td class="col-qty">
            <input type="number" class="item-quantity" value="1" min="1">
        </td>
        <td class="col-unit">
            <input type="text" class="item-unit" placeholder="pcs">
        </td>

        <td class="col-price">
            <input type="number" min="0" class="item-unit-price" value="0">
        </td>
        <td class="col-total">
            <span class="item-subtotal">0.00</span>
        </td>
         <td class="col-purchase-price">
            <input type="number" min="0" class="item-purchase-price" value="0">
        </td>
        <td>
            <button type="button" class="btn-danger-sm remove-item" title="Remove row">×</button>
        </td>
    </tr>
</template>

@push('scripts')
    <script>
        const invoiceProducts = @json($productPayload);
        const invoiceCustomers = @json($customerPayload);
        const initialInvoiceItems = @json($invoiceItems); // [] on create, real items on edit
        let selectedCustomerId = "{{ old('customer_id', $invoice->customer_id) }}";
        const itemBody = document.getElementById('invoiceItemsBody');
        const emptyNote = document.getElementById('invoiceItemsEmpty');

        function money(v) {
            return Number(v || 0).toFixed(2);
        }

        /* show/hide the "no items" placeholder */
        function syncEmptyState() {
            const hasRows = itemBody.querySelectorAll('tr').length > 0;
            emptyNote.style.display = hasRows ? 'none' : 'block';
        }

        function setRowNames(row, index) {
            row.querySelector('.item-product-id').name = `items[${index}][product_id]`;
            row.querySelector('.item-product-name').name = `items[${index}][product_name]`;
            row.querySelector('.item-product-sku').name = `items[${index}][product_sku]`;
            row.querySelector('.item-purchase-price').name = `items[${index}][purchase_price]`;
            row.querySelector('.item-quantity').name = `items[${index}][quantity]`;
            row.querySelector('.item-unit').name = `items[${index}][unit]`;
            row.querySelector('.item-unit-price').name = `items[${index}][unit_price]`;
        }

        function reindexRows() {
            itemBody.querySelectorAll('tr').forEach(setRowNames);
        }

        function addItem(data = {}) {
            const row = document.getElementById('invoiceItemTemplate')
                .content.firstElementChild.cloneNode(true);

            row.querySelector('.item-product-id').value = data.product_id || '';
            row.querySelector('.item-product-name').value = data.product_name || '';
            row.querySelector('.item-product-sku').value = data.product_sku || '';
            row.querySelector('.item-purchase-price').value = data.purchase_price || 0;
            row.querySelector('.item-quantity').value = data.quantity || 1;
            row.querySelector('.item-unit').value = data.unit || '';
            row.querySelector('.item-unit-price').value = data.unit_price || 0;

            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            row.querySelector('.remove-item').addEventListener('click', function() {
                row.remove();
                reindexRows();
                calculateTotals();
                syncEmptyState();
            });

            itemBody.appendChild(row);
            reindexRows();
            calculateTotals();
            syncEmptyState();
        }

        function addProductToInvoice(product) {
            const existingRow = Array.from(itemBody.querySelectorAll('tr')).find(row =>
                String(row.querySelector('.item-product-id').value) === String(product.id)
            );

            if (existingRow) {
                const quantityInput = existingRow.querySelector('.item-quantity');
                quantityInput.value = Number(quantityInput.value || 0) + 1;
                calculateTotals();
                return;
            }

            addItem({
                product_id: product.id,
                product_name: product.name,
                product_sku: product.sku,
                quantity: 1,
                unit: product.unit,
                purchase_price: product.purchase_price,
                unit_price: product.sale_price,
            });
        }

        function calculateTotals() {
            let subTotal = 0;

            itemBody.querySelectorAll('tr').forEach(row => {
                const qty = Number(row.querySelector('.item-quantity').value || 0);
                const price = Number(row.querySelector('.item-unit-price').value || 0);
                const rowTotal = qty * price;
                row.querySelector('.item-subtotal').textContent = money(rowTotal);
                subTotal += rowTotal;
            });

            const discount = Number(document.getElementById('discountAmount').value || 0);
            const tax = Number(document.getElementById('taxAmount').value || 0);
            const paid = Number(document.getElementById('paidAmount').value || 0);
            const grandTotal = Math.max(0, subTotal - discount + tax);
            const due = Math.max(0, grandTotal - paid);

            document.getElementById('summarySubTotal').textContent = money(subTotal);
            document.getElementById('summaryDiscount').textContent = money(discount);
            document.getElementById('summaryTax').textContent = money(tax);
            document.getElementById('summaryGrandTotal').textContent = money(grandTotal);
            document.getElementById('summaryPaid').textContent = money(paid);
            document.getElementById('summaryDue').textContent = money(due);
        }

        function renderProducts(products) {
            const container = document.getElementById('productList');
            container.innerHTML = '';

            if (!products.length) {
                container.innerHTML =
                    '<p style="font-size:13px;color:var(--text-muted);text-align:center;padding:20px 0;">No products found.</p>';
                return;
            }

            products.forEach(product => {
                const div = document.createElement('div');
                div.className = 'product-card';
                div.innerHTML = `
            <div class="product-card-img">
                <img src="${product.image}" alt="${product.name}" loading="lazy">
            </div>
     <div class="product-card-info">
    <div class="product-card-name">${product.name}</div>

    <div class="product-card-meta">
        <span class="
            ${product.stock <= 0
                ? 'stock-danger'
                : product.stock <= product.alert_stock
                    ? 'stock-warning'
                    : 'stock-normal'}
        ">
            Stock: ${product.stock}
        </span>
    </div>
</div>
</div>
            <button type="button" class="product-card-add" title="Add to invoice">+</button>
        `;
                div.addEventListener('click', () => addProductToInvoice(product));
                container.appendChild(div);
            });
        }

        // Search
        document.getElementById('productSearch').addEventListener('input', function() {
            const kw = this.value.toLowerCase();
            renderProducts(invoiceProducts.filter(p =>
                p.name.toLowerCase().includes(kw) ||
                (p.sku || '').toLowerCase().includes(kw)
            ));
        });

        // Add row button
        document.getElementById('addInvoiceItem').addEventListener('click', () => addItem());

        // Payment inputs → recalculate
        ['discountAmount', 'taxAmount', 'paidAmount'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateTotals);
        });

        // Customer select autofill
        const customerSearch = document.getElementById('customerSearch');
        const customerDropdown = document.getElementById('customerDropdown');
        const customerSelect = document.getElementById('customerSelect');

        function fillCustomer(c = null) {

            if (!c) {
                document.getElementById('customerName').value = '';
                document.getElementById('customerBusinessName').value = '';
                document.getElementById('customerPhone').value = '';
                document.getElementById('customerEmail').value = '';
                document.getElementById('customerAddress').value = '';
                return;
            }

            document.getElementById('customerName').value = c.name || '';
            document.getElementById('customerBusinessName').value = c.business_name || '';
            document.getElementById('customerPhone').value = c.phone || '';
            document.getElementById('customerEmail').value = c.email || '';
            document.getElementById('customerAddress').value = c.address || '';
        }

        function renderCustomerDropdown(customers = []) {

            customerDropdown.innerHTML = '';

            // Walk in option
            const walkin = document.createElement('div');
            walkin.className = 'customer-dropdown-item';
            walkin.textContent = 'Walk-in / Manual Customer';

            walkin.addEventListener('click', function() {

                customerSelect.value = '';
                selectedCustomerId = '';

                customerSearch.value = '';
                customerDropdown.style.display = 'none';

                fillCustomer(null);
            });

            customerDropdown.appendChild(walkin);

            if (!customers.length) {

                const empty = document.createElement('div');
                empty.className = 'customer-dropdown-empty';
                empty.textContent = 'No customer found';

                customerDropdown.appendChild(empty);

                return;
            }

            customers.forEach(c => {

                const item = document.createElement('div');
                item.className = 'customer-dropdown-item';

                item.innerHTML = `
            <div class="customer-name">${c.name}</div>
            <div class="customer-phone">${c.business_name || ''}</div>
        `;

                item.addEventListener('click', function() {

                    selectedCustomerId = c.id;
                    customerSelect.value = c.id;

                    customerSearch.value = `${c.name} (${c.phone || ''})`;

                    customerDropdown.style.display = 'none';

                    fillCustomer(c);
                });

                customerDropdown.appendChild(item);
            });
        }

        customerSearch.addEventListener('focus', function() {
            renderCustomerDropdown(invoiceCustomers);
            customerDropdown.style.display = 'block';
        });

        customerSearch.addEventListener('input', function() {

            const keyword = this.value.toLowerCase();

            const filtered = invoiceCustomers.filter(c =>
                (c.name || '').toLowerCase().includes(keyword) ||
                (c.phone || '').toLowerCase().includes(keyword) ||
                (c.business_name || '').toLowerCase().includes(keyword)
            );

            renderCustomerDropdown(filtered);

            customerDropdown.style.display = 'block';
        });

        document.addEventListener('click', function(e) {

            if (!e.target.closest('.customer-search-wrap')) {
                customerDropdown.style.display = 'none';
            }
        });

        // preload selected customer
        if (selectedCustomerId) {

            const customer = invoiceCustomers.find(c =>
                String(c.id) === String(selectedCustomerId)
            );

            if (customer) {

                customerSearch.value =
                    `${customer.name} (${customer.phone || ''})`;

                fillCustomer(customer);
            }
        }
        // ── Init ──
        // On create: initialInvoiceItems is [] → no rows added, empty state shows
        // On edit:   initialInvoiceItems has real rows → they are restored
        initialInvoiceItems.forEach(item => addItem(item));
        syncEmptyState(); // ensure correct state on first paint

        renderProducts(invoiceProducts);
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}" />
@endpush
