@if ($invoices->count() == 0)

    <div style="padding:40px;text-align:center;">
        <p class="text-muted">No invoices found</p>
    </div>

@else

    <div class="tbl-wrap customer-invoice-table-wrap">

        <table class="tbl customer-invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($invoices as $key => $invoice)

                    @php
                        $due = max(0, $invoice->grand_total - $invoice->paid_amount);
                    @endphp

                    <tr>
                        <td>{{ $invoices->firstItem() + $key }}</td>
                        <td><strong>{{ $invoice->invoice_no }}</strong></td>

                        <td>
                            {{ $invoice->invoice_date
                                ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y')
                                : '-' }}
                        </td>

                        <td>৳{{ number_format($invoice->grand_total, 2) }}</td>
                        <td>৳{{ number_format($invoice->paid_amount, 2) }}</td>

                        <td>
                            @if($due > 0)
                                <span class="badge badge-warning">
                                    ৳{{ number_format($due, 2) }}
                                </span>
                            @else
                                <span class="badge badge-teal">Paid</span>
                            @endif
                        </td>

                        <td>
                            @if ($invoice->payment_status === 'paid')
                                <span class="badge badge-teal">Paid</span>
                            @elseif($invoice->payment_status === 'partial')
                                <span class="badge badge-warning">Partial</span>
                            @else
                                <span class="badge badge-danger">Due</span>
                            @endif
                        </td>

                        <td>
                         @can('invoice.detail.view')
                            <a href="{{ route('invoices.show', $invoice->id) }}?from=customer&customer_id={{ $invoice->customer_id }}"
                               class="btn btn-ghost btn-icon">
                                👁
                            </a>
                            @endcan
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>

    </div>

    {{-- ⚠️ AJAX PAGINATION FIX --}}
    <div class="pagination-wrapper mt-3">
        {{ $invoices->links() }}
    </div>

@endif
