{{-- ================= EMPTY ================= --}}
@if ($customers->isEmpty())

    <div style="text-align:center; padding:60px 20px; min-height:360px; display:grid; place-content:center;">
        <p class="text-muted">
            {{ request('search') || request('status') ? 'No customers matched your filters' : 'No customers found' }}
        </p>

        @unless (request('search') || request('status'))
        @can('customer.create')
            <button class="btn btn-ghost mt-4" type="button" onclick="openModal('createModal')">
                Create First Customer
            </button>
            @endcan
        @endunless
    </div>
@else
    {{-- ================= TABLE ================= --}}
    <div class="tbl-wrap">

        <table class="tbl">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($customers as $key => $customer)
                    <tr id="row-{{ $customer->id }}">

                        <td class="text-muted">{{ $customers->firstItem() + $key }}</td>

                        <td>
                        @can('customer.detail.view')

                <a href="{{ route('customers.show', $customer->id) }}" >
                             @endcan 
                            <strong>{{ $customer->name }}</strong>
                          </a>
                            @if ($customer->business_name)
                                <br>
                                <small class="text-muted">{{ $customer->business_name }}</small>
                            @endif
                        </td>

                        <td>
                            {{ $customer->phone ?? '-' }}
                            @if ($customer->alternative_phone)
                                <br>
                                <small class="text-muted">{{ $customer->alternative_phone }}</small>
                            @endif
                        </td>

                        <td class="text-muted">{{ $customer->email ?? '-' }}</td>

                        <td class="text-muted" style="max-width:280px; line-height:1.45;">
                            {{ $customer->address ?? '-' }}
                        </td>

                        <td>
                            @if ($customer->is_active)
                                <span class="badge badge-teal">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>

                        <td class="text-muted">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>

                       <td>

    <div class="action-dropdown">

        {{-- 3 DOT BUTTON --}}
        <button type="button"
                class="btn btn-ghost btn-icon action-toggle"
                aria-label="Actions for {{ $customer->name }}">

            <svg viewBox="0 0 24 24"
                 width="18"
                 height="18"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">

                <circle cx="12" cy="5" r="1"></circle>
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="12" cy="19" r="1"></circle>

            </svg>

        </button>

        {{-- DROPDOWN MENU --}}
        <div class="action-menu">

            {{-- VIEW --}}
            @can('customer.detail.view')
            <a href="{{ route('customers.show', $customer->id) }}"
               class="action-menu-item">

                <svg viewBox="0 0 24 24"
                     width="18"
                     height="18"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>

                </svg>

                <span>View</span>

            </a>
            @endcan

            {{-- EDIT --}}
            @can('customer.edit')
            <button type="button"
                    class="action-menu-item"
                    data-update-url="{{ route('customers.update', $customer->id) }}"
                    data-name="{{ $customer->name }}"
                    data-business-name="{{ $customer->business_name }}"
                    data-phone="{{ $customer->phone }}"
                    data-alternative-phone="{{ $customer->alternative_phone }}"
                    data-email="{{ $customer->email }}"
                    data-address="{{ $customer->address }}"
                    data-is-active="{{ $customer->is_active ? 1 : 0 }}"
                    onclick="editCustomer(this)">

                <svg viewBox="0 0 24 24"
                     width="18"
                     height="18"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>

                </svg>

                <span>Edit</span>

            </button>
            @endcan

            {{-- DELETE --}}
            @can('customer.delete')
            <button type="button"
                    class="action-menu-item action-delete"
                    onclick='openDeleteModal(@json(route('customers.destroy', $customer->id)), @json($customer->name))'>

                <svg viewBox="0 0 24 24"
                     width="18"
                     height="18"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>

                </svg>

                <span>Delete</span>

            </button>
            @endcan

        </div>

    </div>

</td>
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <div style="margin-top:15px; display:flex; justify-content:center;">
        {{ $customers->links() }}
    </div>

@endif
