@if ($invoices->isEmpty())
    <div style="text-align:center; padding:60px 20px; min-height:320px; display:grid; place-content:center;">
        <p class="text-muted">
            {{ request('search') || request('status') || request('payment_status') ? 'No invoices matched your filters' : 'No invoices found' }}
        </p>

        @unless (request('search') || request('status') || request('payment_status'))
            @can('invoice.create')
                <a href="{{ route('invoices.create') }}" class="btn btn-ghost mt-4">Create First Invoice</a>
            @endcan
        @endunless
    </div>
@else
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $key => $invoice)
                    @php
                        $due = max(0, $invoice->grand_total - $invoice->paid_amount);
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $invoices->firstItem() + $key }}</td>
                        <td>
                            @can('invoice.detail.view')
                                <a href="{{ route('invoices.show', $invoice) }}?from=index">
                                @endcan
                                <strong>{{ $invoice->invoice_no }}</strong><br>
                                <small class="text-muted">
                                    {{ $invoice->invoice_date?->format('d M Y h:i A') }}
                                </small>
                            </a>
                        </td>
                        <td>
                            <strong>{{ $invoice->customer_name ?? 'Walk-in Customer' }}</strong>
                            @if ($invoice->customer_phone)
                                <br><small class="text-muted">{{ $invoice->customer_phone }}</small>
                            @endif
                        </td>
                        <td><strong>{{ number_format($invoice->grand_total, 2) }}</strong></td>
                        <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td>{{ number_format($due, 2) }}</td>
                        <td>
                            @if ($invoice->status === 'completed')
                                <span class="badge badge-teal">Completed</span>
                            @elseif($invoice->status === 'draft')
                                <span class="badge badge-warning">Draft</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
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
                            <div class="flex gap-2 align-items-center">

                                {{-- Print Button Outside --}}
                                @can('invoice.print')
                                    <a href="javascript:void(0)" onclick="printInvoice({{ $invoice->id }})"
                                        class="btn btn-warning btn-icon" aria-label="Print {{ $invoice->invoice_no }}">🖨️
                                    </a>
                                @endcan
                                <div class="action-dropdown">

                                    <button type="button" class="btn btn-primary btn-icon action-toggle">
                                        🗂️
                                    </button>

                                    <div class="action-menu">

                                        <a href="{{ route('invoices.pdf', $invoice) }}" class="action-menu-item">
                                            ⬇️ Download PDF
                                        </a>

                                        <button type="button" class="action-menu-item"
                                            onclick="sharePdf({{ $invoice->id }}, '{{ $invoice->invoice_no }}')">
                                            🔗 Share PDF
                                        </button>

                                    </div>

                                </div>
                                <div id="printContainer" style="display:none;"></div>

                                {{-- 3 Dot Dropdown --}}
                                <div class="action-dropdown">

                                    <button type="button" class="btn btn-ghost btn-icon action-toggle">

                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">

                                            <circle cx="12" cy="5" r="1"></circle>
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="12" cy="19" r="1"></circle>

                                        </svg>
                                    </button>

                                    <div class="action-menu">
                                        @can('invoice.detail.view')
                                            <a href="{{ route('invoices.show', $invoice) }}?from=index"
                                                class="action-menu-item">

                                                👁 View

                                            </a>
                                        @endcan

                                        @can('invoice.edit')
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="action-menu-item">

                                                ✏️ Edit

                                            </a>
                                        @endcan

                                        @can('invoice.delete')
                                            <button type="button" class="action-menu-item action-delete"
                                                onclick='openDeleteModal(@json(route('invoices.destroy', $invoice)), @json($invoice->invoice_no))'>

                                                🗑 Delete

                                            </button>
                                        @endcan

                                    </div>

                                </div>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:15px; display:flex; justify-content:center;">
        {{ $invoices->links() }}
    </div>
@endif
<script>
    async function printInvoice(invoiceId) {
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;

        // Show loading
        btn.innerHTML = '⏳';
        btn.disabled = true;

        try {
            const response = await fetch(`{{ url('invoices') }}/${invoiceId}/print`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const html = await response.text();

            // Create hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            const iframeDoc = iframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(html);
            iframeDoc.close();

            // Trigger print
            iframe.contentWindow.print();

            // Clean up
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);

        } catch (error) {
            console.error('Print failed:', error);
            alert('Failed to print. Please try again.');
        } finally {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    }


   async function sharePdf(invoiceId, invoiceNo) {

    const response = await fetch(
        `/invoices/${invoiceId}/share-pdf`
    );

    const blob = await response.blob();

    const file = new File(
        [blob],
        `${invoiceNo}.pdf`,
        {
            type: 'application/pdf'
        }
    );

    await navigator.share({
        files: [file],
        title: `Invoice ${invoiceNo}`
    });
}
</script>
