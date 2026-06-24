@extends('app')

@section('title', 'Invoice Details')

@section('content')

    @php
        $totalPurchasePrice = $invoice->items->sum(fn($item) => $item->purchase_price * $item->quantity);
        $productReturn = $invoice->productReturns->first();
        $totalReturned = $productReturn?->total_amount ?? 0;
        $effectivePaid = $invoice->paid_amount + $totalReturned;
        $dueAmount = max(0, $invoice->grand_total - $effectivePaid);
        $profitOrLoss = $invoice->grand_total - $totalReturned - $totalPurchasePrice;
    @endphp

    <div class="page-layout invoice-page-layout">
        <div class="card card-full invoice-print-area">

            {{-- ── CARD HEADER ──────────────────────────────────────── --}}
            <div class="card-header">
                <div>
                    <h1 class="page-title">{{ $invoice->invoice_no }}</h1>
                    <p class="text-muted mt-4">Invoice date: {{ $invoice->invoice_date?->format('d M Y h:i A') }}</p>
                </div>

                <div class="flex gap-2">
                    @php
                        if (request('from') === 'trash') {
                            $backUrl = route('invoices.trash');
                        } elseif (request('from') === 'customer' && request('customer_id')) {
                            $backUrl = route('customers.show', request('customer_id'));
                        } else {
                            $backUrl = route('invoices.index');
                        }
                    @endphp

                    <a href="{{ $backUrl }}" class="btn btn-ghost">
                        ← Back
                    </a>

                    @can('invoice.cancel.return')
                    @if ($productReturn)
                        <a href="javascript:void(0)" onclick="cancelReturn()" class="btn btn-ghost"
                            style="color:#dc2626; border-color:#fecaca;">
                            🗑 Cancel Return
                        </a>
                    @endif
                    @endcan

                     @can('invoice.return')
                    <a href="javascript:void(0)" onclick="showReturnModal()" class="btn btn-danger btn-icon">
                        ↩ {{ $productReturn ? 'Edit Return' : 'Return Items' }}
                    </a>
                 @endcan

                   @can('invoice.print')
                    <a href="javascript:void(0)" onclick="printInvoice({{ $invoice->id }})"
                        class="btn btn-warning btn-icon">
                        🖨️ Print
                    </a>
                    @endcan
                    <div id="printContainer" style="display:none;"></div>
 @can('invoice.edit')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">✏️ Edit</a>
                    @endcan
                </div>
            </div>

            <div class="divider"></div>

            {{-- ── CUSTOMER + INVOICE META ──────────────────────────── --}}
            <div class="invoice-detail-grid">
                <div class="card-section">
                    <h3 class="section-title">Customer</h3>
                    <p><strong>{{ $invoice->customer_name ?? 'Walk-in Customer' }}</strong></p>
                    @if ($invoice->customer_business_name)
                        <p class="text-muted">{{ $invoice->customer_business_name }}</p>
                    @endif
                    <p class="text-muted">Phone: {{ $invoice->customer_phone ?? '-' }}</p>
                    <p class="text-muted">Email: {{ $invoice->customer_email ?? '-' }}</p>
                    <p class="text-muted">Address: {{ $invoice->customer_address ?? '-' }}</p>
                </div>

                <div class="card-section">
                    <h3 class="section-title">Invoice</h3>
                    <div class="invoice-meta-list">
                        <div><span>Status</span><strong>{{ ucfirst($invoice->status) }}</strong></div>
                        <div><span>Payment</span><strong>{{ ucfirst($invoice->payment_status) }}</strong></div>
                        <div>
                            <span>Method</span><strong>{{ ucwords(str_replace('_', ' ', $invoice->payment_method)) }}</strong>
                        </div>
                        <div><span>Created By</span><strong>{{ $invoice->creator->name ?? '-' }}</strong></div>
                        <div><span>Updated By</span><strong>{{ $invoice->updater->name ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>

            {{-- ── ITEMS TABLE ──────────────────────────────────────── --}}
            <div class="card-section mt-6">
                <h3 class="section-title">Items</h3>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Selling Price</th>
                                <th>Purchase Price</th>
                                <th>Returned</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $key => $item)
                                @php $returnedQty = $item->returnItems->sum('quantity'); @endphp
                                <tr>
                                    <td class="text-muted">{{ $key + 1 }}</td>
                                    <td>
                                        @if ($item->product)
                                            <a href="{{ route('products.show', $item->product->id) }}" target="_blank">
                                                <img src="{{ $item->product->image
                                                    ? asset('storage/' . $item->product->image)
                                                    : asset('images/default_product_image.png') }}"
                                                    alt="{{ $item->product_name }}" class="img-thumbnail"
                                                    style="max-width:50px; max-height:50px;">
                                            </a>
                                        @else
                                            <img src="{{ asset('images/default_product_image.png') }}"
                                                alt="{{ $item->product_name }}" class="img-thumbnail"
                                                style="max-width:50px; max-height:50px;">
                                        @endif
                                    </td>

                                    <td>
                                        @if ($item->product)
                                            <a href="{{ route('products.show', $item->product->id) }}" target="_blank">
                                                <strong>{{ $item->product_name }}</strong>
                                            </a>
                                        @else
                                            <strong>{{ $item->product_name }}</strong>
                                            <br>
                                            <small style="color:#f59e0b;">Manual Product</small>
                                        @endif

                                        <br>
                                        <small class="text-muted">
                                            SKU: {{ $item->product_sku ?? '-' }}
                                        </small>
                                    </td>

                                    <td class="text-muted">
                                        {{ $item->product?->brand?->name ?? '-' }}
                                    </td>
                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                    <td>{{ $item->unit ?? '-' }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->purchase_price, 2) }}</td>
                                    <td>
                                        @if ($returnedQty > 0)
                                            <span
                                                style="color:#dc2626; font-weight:600;">{{ number_format($returnedQty, 2) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($item->subtotal, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── FOOTER : NOTE + SUMMARY ──────────────────────────── --}}
            <div class="invoice-show-footer mt-6">

                <div class="card-section">
                    <h3 class="section-title">Note</h3>
                    <p class="text-muted">{{ $invoice->note ?: 'No note added' }}</p>
                </div>

                <div class="card-section">
                    <div class="invoice-summary invoice-summary-show">

                        {{-- BLOCK 1: Subtotal → Grand Total --}}
                        <div>
                            <span>Subtotal</span>
                            <strong>{{ number_format($invoice->sub_total, 2) }}</strong>
                        </div>
                        <div>
                            <span>Discount</span>
                            <strong style="color:#dc2626;">- {{ number_format($invoice->discount_amount, 2) }}</strong>
                        </div>
                        <div>
                            <span>Tax</span>
                            <strong>+ {{ number_format($invoice->tax_amount, 2) }}</strong>
                        </div>
                        <div class="invoice-summary-total">
                            <span>Grand Total</span>
                            <strong>{{ number_format($invoice->grand_total, 2) }}</strong>
                        </div>

                        {{-- BLOCK 2: Grand Total → Due --}}
                        <div>
                            <span>Paid</span>
                            <strong style="color:#16a34a;">- {{ number_format($invoice->paid_amount, 2) }}</strong>
                        </div>
                        @if ($totalReturned > 0)
                            <div>
                                <span>Returned</span>
                                <strong style="color:#dc2626;">- {{ number_format($totalReturned, 2) }}</strong>
                            </div>
                        @endif
                        <div style="border-top:2px solid var(--border-color,#e5e7eb); padding-top:8px; margin-top:4px;">
                            <span><strong>Due</strong></span>
                            <strong style="font-size:1.05rem; {{ $dueAmount > 0 ? 'color:#dc2626;' : 'color:#16a34a;' }}">
                                {{ number_format($dueAmount, 2) }}
                            </strong>
                        </div>

                        {{-- BLOCK 3: Net Sales → Profit/Loss --}}
                        @if ($totalReturned > 0)
                            <div
                                style="border-top:1px dashed var(--border-color,#e5e7eb); margin-top:8px; padding-top:8px;">
                                <span>Net Sales</span>
                                <strong>{{ number_format($invoice->grand_total - $totalReturned, 2) }}</strong>
                            </div>
                            <div>
                                <span>Total Purchase Cost</span>
                                <strong>{{ number_format($totalPurchasePrice, 2) }}</strong>
                            </div>
                        @else
                            <div
                                style="border-top:1px dashed var(--border-color,#e5e7eb); margin-top:8px; padding-top:8px;">
                                <span>Total Purchase Cost</span>
                                <strong>{{ number_format($totalPurchasePrice, 2) }}</strong>
                            </div>
                        @endif
                        <div class="{{ $profitOrLoss >= 0 ? 'text-success' : 'text-danger' }}"
                            style="border-top:2px solid var(--border-color,#e5e7eb); padding-top:8px; margin-top:4px;">
                            <span><strong>{{ $profitOrLoss >= 0 ? 'Profit' : 'Loss' }}</strong></span>
                            <strong style="font-size:1.05rem;">
                                {{ $profitOrLoss >= 0 ? '' : '-' }}{{ number_format(abs($profitOrLoss), 2) }}
                            </strong>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── RETURN RECORD (inside same card) ────────────────── --}}
            @if ($productReturn)
                <div class="divider" style="margin:1.5rem 0;"></div>

                <div class="card-section">
                    <div
                        style="display:flex; justify-content:space-between; align-items:center;
                                margin-bottom:1rem; flex-wrap:wrap; gap:.5rem;">
                        <h3 class="section-title" style="margin:0;">↩ Return Record</h3>
                        <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
                            @if ($productReturn->reason)
                                <span
                                    style="background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:20px; font-size:.75rem;">
                                    {{ ucfirst(str_replace('_', ' ', $productReturn->reason)) }}
                                </span>
                            @endif
                            @if ($productReturn->return_method)
                                <span
                                    style="background:#dbeafe; color:#1e40af; padding:3px 10px; border-radius:20px; font-size:.75rem;">
                                    {{ ucwords(str_replace('_', ' ', $productReturn->return_method)) }}
                                </span>
                            @endif
                            @if ($productReturn->status)
                                <span
                                    style="background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:.75rem;">
                                    {{ ucfirst($productReturn->status) }}
                                </span>
                            @endif
                            <span class="text-muted" style="font-size:.8rem;">
                                {{ $productReturn->return_no }}
                                &nbsp;·&nbsp;
                                {{ \Carbon\Carbon::parse($productReturn->return_date)->format('d M Y h:i A') }}
                                &nbsp;·&nbsp;
                                By: {{ $productReturn->creator->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="tbl-wrap">
                        <table class="tbl" style="font-size:.875rem;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Qty Returned</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productReturn->items as $k => $ri)
                                    <tr>
                                        <td class="text-muted">{{ $k + 1 }}</td>
                                        <td><strong>{{ $ri->product_name }}</strong></td>
                                        <td style="color:#dc2626; font-weight:600;">{{ number_format($ri->quantity, 2) }}
                                        </td>
                                        <td>{{ number_format($ri->unit_price, 2) }}</td>
                                        <td><strong>{{ number_format($ri->subtotal, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div
                        style="display:flex; justify-content:space-between; align-items:flex-start;
                                flex-wrap:wrap; gap:1rem; margin-top:1rem;">
                        <p class="text-muted" style="font-size:.85rem; max-width:60%;">
                            <strong>Note:</strong> {{ $productReturn->note ?: 'No note' }}
                        </p>
                        <div style="text-align:right;">
                            <p class="text-muted" style="font-size:.8rem; margin-bottom:2px;">Total Returned</p>
                            <strong style="font-size:1.1rem; color:#dc2626;">
                                {{ number_format($productReturn->total_amount, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            @endif

        </div>{{-- end .card --}}
    </div>{{-- end .page-layout --}}

    @include('invoices.partials.return_modal')

@endsection

@push('scripts')
    <script>
        /* ── print ──────────────────────────────────────────────── */
        async function printInvoice(invoiceId) {
            const btn = event.currentTarget;
            const orig = btn.innerHTML;
            btn.innerHTML = '⏳';
            btn.disabled = true;
            try {
                const res = await fetch(`{{ url('invoices') }}/${invoiceId}/print`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await res.text();
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                iframe.contentWindow.document.open();
                iframe.contentWindow.document.write(html);
                iframe.contentWindow.document.close();
                iframe.contentWindow.print();
                setTimeout(() => document.body.removeChild(iframe), 1000);
            } catch (e) {
                console.error(e);
                alert('Failed to print. Please try again.');
            } finally {
                btn.innerHTML = orig;
                btn.disabled = false;
            }
        }

        /* ── modal open/close ───────────────────────────────────── */
        function showReturnModal() {
            document.getElementById('returnModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            recalcReturnTotal();
        }

        function hideReturnModal() {
            document.getElementById('returnModal').style.display = 'none';
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') hideReturnModal();
        });

        /* ── select all ─────────────────────────────────────────── */
        function toggleAllReturnItems(masterCb) {
            document.querySelectorAll('.return-item-check').forEach(cb => cb.checked = masterCb.checked);
            recalcReturnTotal();
        }

        function onReturnItemToggle() {
            recalcReturnTotal();
            const all = document.querySelectorAll('.return-item-check');
            document.getElementById('returnSelectAll').checked = [...all].every(c => c.checked);
        }

        /* ── recalc total ───────────────────────────────────────── */
        function recalcReturnTotal() {
            let total = 0;
            document.querySelectorAll('.return-item-check:checked').forEach(cb => {
                const qty = parseFloat(document.getElementById('returnQty_' + cb.dataset.row)?.value) || 0;
                const price = parseFloat(cb.closest('tr').dataset.unitPrice) || 0;
                total += price * qty;
            });
            document.getElementById('returnTotalDisplay').textContent = total.toFixed(2);
        }

        /* ── submit return ──────────────────────────────────────── */
        async function submitReturn() {
            const errorDiv = document.getElementById('returnErrorMsg');
            errorDiv.style.display = 'none';

            const items = [];
            document.querySelectorAll('.return-item-check:checked').forEach(cb => {
                const row = cb.dataset.row;
                const tr = cb.closest('tr');
                const qty = parseFloat(document.getElementById('returnQty_' + row)?.value) || 0;
                const price = parseFloat(tr.dataset.unitPrice) || 0;
                const max = parseFloat(tr.dataset.max) || 0;
                if (qty > 0) items.push({
                    invoice_item_id: row,
                    quantity: qty,
                    unit_price: price,
                    subtotal: +(qty * price).toFixed(2),
                    max
                });
            });

            if (!items.length) {
                showReturnError('Please select at least one item to return.');
                return;
            }
            const overQty = items.find(it => it.quantity > it.max);
            if (overQty) {
                showReturnError(`Return quantity exceeds sold quantity (max: ${overQty.max}).`);
                return;
            }

            const btn = document.getElementById('submitReturnBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Saving...';

            try {
                const res = await fetch('{{ route('invoices.returns.store', $invoice) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        items,
                        reason: document.getElementById('returnReason').value,
                        return_method: document.getElementById('returnMethod').value,
                        note: document.getElementById('returnNote').value.trim(),
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    showReturnError(data.message ?? 'Something went wrong.');
                    return;
                }
                window.location.reload();
            } catch (e) {
                console.error(e);
                showReturnError('Network error. Please try again.');
            } finally {
                btn.disabled = false;
                btn.textContent = '{{ $productReturn ? '↩ Update Return' : '↩ Submit Return' }}';
            }
        }

        /* ── cancel return ──────────────────────────────────────── */
        async function cancelReturn() {
            if (!confirm('Cancel this return? This will reverse all stock changes and delete the return record.'))
                return;

            try {
                const res = await fetch('{{ route('invoices.returns.destroy', $invoice) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                if (!res.ok) {
                    alert(data.message ?? 'Something went wrong.');
                    return;
                }
                window.location.reload();
            } catch (e) {
                console.error(e);
                alert('Network error. Please try again.');
            }
        }

        function showReturnError(msg) {
            const el = document.getElementById('returnErrorMsg');
            el.textContent = msg;
            el.style.display = 'block';
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    </script>
@endpush
