@extends('app')

@section('content')
<div class="page-layout">

    <div class="card card-full">

        <div class="card-header">
            <div>
                <h1 class="page-title">Invoice Templates</h1>
                <p class="text-muted">Manage your invoice print layouts</p>
            </div>
            {{-- <a href="{{ route('invoice-templates.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Add Template
            </a> --}}
        </div>

        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>View name</th>
                        <th style="text-align:center">Preview</th>
                        <th style="text-align:center">Default</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoiceTemplates as $template)
                    <tr data-template-id="{{ $template->id }}">
                        <td style="color:var(--text-muted)">{{ $template->id }}</td>

                        <td style="font-weight:600">{{ $template->name }}</td>

                        <td>
                            <code style="font-size:.78rem;background:var(--surface-2);border:1px solid var(--border);padding:3px 9px;border-radius:var(--radius-sm);color:var(--teal-mid);">
                                {{ $template->view_name }}
                            </code>
                        </td>

                        <td style="text-align:center">
                            @if ($template->preview_image)
                                <a href="{{ asset('storage/' . $template->preview_image) }}" target="_blank">
                                    <i class="ti ti-photo" style="font-size:18px;color:var(--teal-bright);"></i>
                                </a>
                            @else
                                <i class="ti ti-photo-off" style="font-size:18px;color:#d1d5db;"></i>
                            @endif
                        </td>

                        <td style="text-align:center">
                            <label class="toggle-switch">
                                <input type="checkbox"
                                       class="toggle-default"
                                       data-id="{{ $template->id }}"
                                       {{ $template->is_default ? 'checked' : '' }}
                                       {{ $template->is_default ? 'disabled' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>

                        <td style="text-align:center">
                            <label class="toggle-switch">
                                <input type="checkbox"
                                       class="toggle-status"
                                       data-id="{{ $template->id }}"
                                       {{ $template->status ? 'checked' : '' }}
                                       {{ $template->is_default ? 'disabled' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>

                        <td style="text-align:center">
                            <div class="flex items-center gap-2" style="justify-content:center">
                                <a href="{{ route('invoice-templates.view', $template->id) }}" class="btn-icon" title="View">
                                    <i class="ti ti-eye" style="font-size:15px;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                            <i class="ti ti-file-invoice" style="font-size:2.5rem;display:block;margin-bottom:10px;opacity:.35;color:var(--teal-bright);"></i>
                            No templates found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: #10b981;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }

    input:disabled + .toggle-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-left: 4px solid;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    .toast-success {
        border-left-color: #10b981;
    }

    .toast-error {
        border-left-color: #ef4444;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // CSRF Token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Helper function to show toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="ti ti-${type === 'success' ? 'check-circle' : 'alert-circle'}" style="font-size: 20px;"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Handle Default Toggle
    document.querySelectorAll('.toggle-default').forEach(toggle => {
        toggle.addEventListener('change', function(e) {
            const templateId = this.dataset.id;
            const wasChecked = this.checked;

            // Optimistically update UI
            this.checked = wasChecked;

            fetch(`{{ url('invoice-templates') }}/${templateId}/toggle-default`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');

                    // Update UI - uncheck all other default toggles
                    if (data.is_default) {
                        document.querySelectorAll('.toggle-default').forEach(t => {
                            if (t !== toggle) {
                                t.checked = false;
                                t.disabled = false;
                            } else {
                                t.checked = true;
                                t.disabled = true;
                            }
                        });

                        // Enable status toggle for the new default (but keep it disabled)
                        document.querySelectorAll('.toggle-status').forEach(t => {
                            const row = t.closest('tr');
                            const id = row?.dataset.templateId;
                            if (id == templateId) {
                                t.disabled = true;
                                if (!t.checked) {
                                    // Optionally show warning
                                    showToast('Default template cannot be disabled', 'error');
                                }
                            } else {
                                t.disabled = false;
                            }
                        });
                    }
                } else {
                    showToast(data.message, 'error');
                    // Revert the toggle
                    toggle.checked = !wasChecked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
                toggle.checked = !wasChecked;
            });
        });
    });

    // Handle Status Toggle
    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function(e) {
            const templateId = this.dataset.id;
            const wasChecked = this.checked;

            // Optimistically update UI
            this.checked = wasChecked;

            fetch(`{{ url('invoice-templates') }}/${templateId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Update the toggle state
                    toggle.checked = data.status;
                } else {
                    showToast(data.message, 'error');
                    toggle.checked = wasChecked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
                toggle.checked = wasChecked;
            });
        });
    });
</script>
@endpush
