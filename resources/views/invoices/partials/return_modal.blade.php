    {{-- ================================================================
         RETURN MODAL
         ================================================================ --}}
    <div id="returnModal" style="display:none; position:fixed; inset:0; z-index:9999; overflow-y:auto;">

        {{-- Backdrop --}}
        <div onclick="hideReturnModal()"
             style="position:fixed; inset:0; background:rgba(0,0,0,.5); backdrop-filter:blur(2px);"></div>

        {{-- Dialog --}}
        <div style="position:relative; z-index:10000; max-width:780px; width:calc(100% - 2rem);
                    margin:3rem auto; background:var(--card-bg,#fff);
                    border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,.2); overflow:hidden;">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:center;
                        padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color,#e5e7eb);">
                <div>
                    <h3 style="margin:0; font-size:1.1rem;">↩ Return Items</h3>
                    <p class="text-muted" style="margin:2px 0 0; font-size:.85rem;">
                        Invoice: <strong>{{ $invoice->invoice_no }}</strong>
                        @if ($productReturn)
                            &nbsp;·&nbsp;
                            <span style="color:#f59e0b;">Editing existing return — quantities will be updated</span>
                        @endif
                    </p>
                </div>
                <button onclick="hideReturnModal()"
                        style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280; line-height:1;">×</button>
            </div>

            {{-- Body --}}
            <div style="padding:1.5rem;">

                <h4 style="margin:0 0 .75rem; font-size:.9rem; text-transform:uppercase; letter-spacing:.05em; color:#6b7280;">
                    Select Items & Quantities
                </h4>

                <div class="tbl-wrap" style="margin-bottom:1.25rem;">
                    <table class="tbl" style="font-size:.875rem;">
                        <thead>
                            <tr>
                                <th style="width:32px;">
                                    <input type="checkbox" id="returnSelectAll" onchange="toggleAllReturnItems(this)">
                                </th>
                                <th>Product</th>
                                <th>Sold Qty</th>
                                <th>Return Qty</th>
                                <th>Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                @php
                                    // Previously returned qty for THIS item in the single return record
                                    $prevReturnedQty = $productReturn
                                        ? $productReturn->items->where('invoice_item_id', $item->id)->sum('quantity')
                                        : 0;
                                @endphp
                                <tr data-item-id="{{ $item->id }}"
                                    data-unit-price="{{ $item->unit_price }}"
                                    data-max="{{ $item->quantity }}">
                                    <td>
                                        <input type="checkbox"
                                               class="return-item-check"
                                               data-row="{{ $item->id }}"
                                               onchange="onReturnItemToggle(this)"
                                               {{ $prevReturnedQty > 0 ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if ($item->product_sku)
                                            <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                    <td>
                                        <input type="number"
                                               id="returnQty_{{ $item->id }}"
                                               class="return-qty-input"
                                               data-row="{{ $item->id }}"
                                               min="0"
                                               max="{{ $item->quantity }}"
                                               step="0.01"
                                               value="{{ $prevReturnedQty > 0 ? $prevReturnedQty : min(1, $item->quantity) }}"
                                               style="width:80px; padding:4px 8px; border:1px solid #d1d5db;
                                                      border-radius:6px; font-size:.85rem;"
                                               oninput="recalcReturnTotal()">
                                        <br><small class="text-muted">Max: {{ number_format($item->quantity, 2) }}</small>
                                    </td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Meta fields --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:.85rem; font-weight:600; margin-bottom:4px;">Reason</label>
                        <select id="returnReason"
                                style="width:100%; padding:8px 10px; border:1px solid #d1d5db;
                                       border-radius:6px; font-size:.875rem; background:var(--card-bg,#fff);">
                            <option value="">— Select reason —</option>
                            <option value="defective"         {{ ($productReturn?->reason === 'defective')         ? 'selected' : '' }}>Defective / Damaged</option>
                            <option value="wrong_item"        {{ ($productReturn?->reason === 'wrong_item')        ? 'selected' : '' }}>Wrong Item</option>
                            <option value="customer_request"  {{ ($productReturn?->reason === 'customer_request')  ? 'selected' : '' }}>Customer Request</option>
                            <option value="other"             {{ ($productReturn?->reason === 'other')             ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:.85rem; font-weight:600; margin-bottom:4px;">Return Method</label>
                        <select id="returnMethod"
                                style="width:100%; padding:8px 10px; border:1px solid #d1d5db;
                                       border-radius:6px; font-size:.875rem; background:var(--card-bg,#fff);">
                            <option value="">— Select method —</option>
                            <option value="refund"       {{ ($productReturn?->return_method === 'refund')       ? 'selected' : '' }}>Refund</option>
                            <option value="exchange"     {{ ($productReturn?->return_method === 'exchange')     ? 'selected' : '' }}>Exchange</option>
                            <option value="store_credit" {{ ($productReturn?->return_method === 'store_credit') ? 'selected' : '' }}>Store Credit</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:.85rem; font-weight:600; margin-bottom:4px;">
                        Note <span class="text-muted" style="font-weight:400;">(optional)</span>
                    </label>
                    <textarea id="returnNote" rows="2"
                              placeholder="Any additional notes..."
                              style="width:100%; padding:8px 10px; border:1px solid #d1d5db;
                                     border-radius:6px; font-size:.875rem; resize:vertical;
                                     font-family:inherit; box-sizing:border-box;">{{ $productReturn?->note }}</textarea>
                </div>

                {{-- Total --}}
                <div style="display:flex; justify-content:flex-end; align-items:center; gap:1rem;
                            padding:.75rem 1rem; border-radius:8px; background:#f9fafb; margin-bottom:1rem;">
                    <span style="font-size:.9rem; color:#6b7280;">Total Return Amount:</span>
                    <strong id="returnTotalDisplay" style="font-size:1.2rem; color:#dc2626;">0.00</strong>
                </div>

                {{-- Error --}}
                <div id="returnErrorMsg"
                     style="display:none; padding:10px 14px; background:#fef2f2; color:#dc2626;
                            border:1px solid #fecaca; border-radius:6px; font-size:.875rem; margin-bottom:1rem;">
                </div>

            </div>{{-- end body --}}

            {{-- Footer --}}
            <div style="display:flex; justify-content:flex-end; gap:.75rem;
                        padding:1rem 1.5rem; border-top:1px solid var(--border-color,#e5e7eb);">
                <button onclick="hideReturnModal()" class="btn btn-ghost">Cancel</button>
                <button onclick="submitReturn()" id="submitReturnBtn" class="btn btn-danger">
                    ↩ {{ $productReturn ? 'Update Return' : 'Submit Return' }}
                </button>
            </div>

        </div>
    </div>
