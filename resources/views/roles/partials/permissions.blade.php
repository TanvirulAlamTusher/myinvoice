@php
    $selectedPermissions = collect($selectedPermissions ?? [])->all();

    $permissionGroups = $permissions->groupBy(function ($permission) {
        $parts = explode('.', $permission->name);

        return count($parts) > 1
            ? implode(' ', array_slice($parts, 0, -1))
            : $permission->name;
    });

    $formatLabel = fn ($value) => str($value)->replace(['.', '_', '-'], ' ')->title();
@endphp

<div class="permission-panel">
    <div class="permission-panel-head">
        <h3 class="section-title">Permissions</h3>

        <span class="badge badge-teal">
            {{ $permissions->count() }} Total
        </span>
    </div>

    <div class="permission-table-wrap">
        <table class="permission-table">
            <tbody>
                @foreach($permissionGroups as $group => $groupPermissions)
                    <tr>
                        <th>
                            <span>{{ $formatLabel($group) }}</span>
                            <small>{{ $groupPermissions->count() }} permissions</small>
                        </th>

                        <td>
                            <div class="permission-actions">
                                @foreach($groupPermissions as $permission)
                                    @php
                                        $parts = explode('.', $permission->name);
                                        $action = count($parts) > 1 ? end($parts) : $permission->name;
                                    @endphp

                                    <label class="permission-chip" title="{{ $permission->name }}">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               @checked(in_array($permission->name, $selectedPermissions, true))>

                                        <span>{{ $formatLabel($action) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@once
    @push('styles')
        <style>
            .permission-panel {
                border: 1px solid var(--border);
                border-radius: var(--radius-xl);
                background: var(--surface-1);
                overflow: hidden;
            }

            .permission-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 16px 18px;
                background: var(--surface-2);
                border-bottom: 1px solid var(--border);
            }

            .permission-panel-head .section-title {
                margin-bottom: 0;
            }

            .permission-table-wrap {
                width: 100%;
                overflow-x: auto;
            }

            .permission-table {
                width: 100%;
                border-collapse: collapse;
            }

            .permission-table tr + tr {
                border-top: 1px solid var(--border);
            }

            .permission-table th,
            .permission-table td {
                padding: 12px 16px;
                vertical-align: middle;
            }

            .permission-table th {
                width: 220px;
                background: #fbfffe;
                text-align: left;
            }

            .permission-table th span {
                display: block;
                color: var(--text-main);
                font-size: .86rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .permission-table th small {
                display: block;
                margin-top: 3px;
                color: var(--text-muted);
                font-size: .7rem;
                font-weight: 500;
            }

            .permission-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .permission-chip {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                min-height: 34px;
                padding: 7px 10px;
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                background: var(--surface-1);
                color: var(--text-main);
                cursor: pointer;
                transition: background .15s ease, border-color .15s ease, color .15s ease;
            }

            .permission-chip:hover,
            .permission-chip:has(input:checked) {
                background: var(--teal-pale);
                border-color: rgba(13,148,136,.35);
                color: var(--teal-mid);
            }

            .permission-chip input {
                width: 14px;
                height: 14px;
            }

            .permission-chip span {
                font-size: .76rem;
                font-weight: 700;
                line-height: 1;
                white-space: nowrap;
            }

            @media (max-width: 700px) {
                .permission-panel-head {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .permission-table,
                .permission-table tbody,
                .permission-table tr,
                .permission-table th,
                .permission-table td {
                    display: block;
                    width: 100%;
                }

                .permission-table td {
                    padding-top: 0;
                }

                .permission-actions {
                    gap: 6px;
                }
            }
        </style>
    @endpush
@endonce
