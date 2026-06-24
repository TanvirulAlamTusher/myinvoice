@extends('app')

@section('title', 'Trash Invoices')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            <div class="card-header">

                <div>
                    <h1 class="page-title">Trash Invoices</h1>
                    <p class="text-muted mt-4">Restore or permanently delete invoices</p>
                </div>

                <div class="flex gap-2">

                    <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                        Back To Invoices
                    </a>

                    @if ($invoices->count())
                    @can('trash.delete.all')
                        <button type="button" class="btn btn-danger" onclick="openDeleteAllModal()">
                            Delete All
                        </button>
                        @endcan
                    @endif

                </div>

            </div>

            <div class="divider"></div>

            {{-- SEARCH --}}
            <form method="GET" class="filter-form">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search invoice..." class="form-input">
                </div>
                <button class="btn btn-primary">Search</button>
            </form>

            <div class="divider"></div>

            {{-- BULK DELETE FORM (wraps only the table checkboxes, NOT the restore buttons) --}}
            <form id="bulkDeleteForm"
                  action="{{ route('invoices.forceDeleteSelected') }}"
                  method="POST">

                @csrf
                @method('DELETE')

                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        @can('trash.delete')
                        <button type="button" class="btn btn-danger" id="bulkDeleteBtn">
                            Delete Selected
                        </button>
                        @endcan
                    </div>
                </div>

                <div class="tbl-wrap">
                    <table class="tbl">

                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="checkAll"></th>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($invoices as $key => $invoice)
                                <tr>

                                    <td>
                                        <input type="checkbox" name="ids[]"
                                               value="{{ $invoice->id }}" class="row-checkbox">
                                    </td>

                                    <td>{{ $invoices->firstItem() + $key }}</td>

                                    <td>
                                        <strong>{{ $invoice->invoice_no }}</strong>
                                        <br>
                                        <small>{{ $invoice->invoice_date?->format('d M Y h:i A') }}</small>
                                    </td>

                                    <td>{{ $invoice->customer_name }}</td>

                                    <td>{{ number_format($invoice->grand_total, 2) }}</td>

                                    <td>{{ $invoice->deleted_at?->diffForHumans() }}</td>

                                    <td>
                                        <div class="flex gap-2 flex-wrap">

                                            {{-- SHOW --}}
                                            @can('invoice.detail.view')
                                            <a href="{{ route('invoices.show', $invoice->id) }}?from=trash"
                                               class="btn btn-ghost btn-icon">
                                                👁️
                                            </a>
                                            @endcan

                                            {{-- RESTORE: triggers the standalone form outside the table --}}
                                            @can('trash.restore')
                                            <button type="button"
                                                    class="btn btn-primary btn-sm"
                                                    onclick="restoreInvoice({{ $invoice->id }})">
                                                Restore
                                            </button>
                                            @endcan

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:30px;">
                                        No deleted invoices found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </form>
            {{-- END bulkDeleteForm --}}

            {{-- ✅ STANDALONE RESTORE FORM — lives outside bulkDeleteForm --}}
            <form id="restoreForm" method="POST" style="display:none;">
                @csrf
            </form>

            <div style="margin-top:20px;">
                {{ $invoices->links() }}
            </div>

        </div>

    </div>

@endsection

@push('scripts')
<script>

    // ── SELECT ALL ────────────────────────────────────────────────────
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // ── RESTORE (standalone form, never nested) ───────────────────────
    function restoreInvoice(id) {
        if (!confirm('Restore this invoice?')) return;
        const form = document.getElementById('restoreForm');
        form.action = `/invoices/${id}/restore`;
        form.submit();
    }

    // ── BULK DELETE MODAL ─────────────────────────────────────────────
    document.getElementById('bulkDeleteBtn')?.addEventListener('click', function () {

        const checked = document.querySelectorAll('.row-checkbox:checked');

        if (checked.length === 0) {
            toast('Please select at least one invoice', 'error');
            return;
        }

        document.getElementById('deleteItemName').textContent =
            `${checked.length} selected invoice(s)`;

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = document.getElementById('bulkDeleteForm').action;

        deleteForm.querySelectorAll('.temp-delete-input').forEach(el => el.remove());

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = cb.value;
            input.classList.add('temp-delete-input');
            deleteForm.appendChild(input);
        });

        document.getElementById('deleteModal').classList.remove('hidden');
    });

    // ── DELETE ALL MODAL ──────────────────────────────────────────────
    function openDeleteAllModal() {

        const total = {{ $invoices->count() }};

        if (total === 0) {
            toast('No invoices to delete', 'error');
            return;
        }

        document.getElementById('deleteItemName').textContent =
            `ALL ${total} trashed invoices`;

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = "{{ route('invoices.forceDeleteAll') }}";

        deleteForm.querySelectorAll('.temp-delete-input').forEach(el => el.remove());

        document.getElementById('deleteModal').classList.remove('hidden');
    }

</script>
@endpush
