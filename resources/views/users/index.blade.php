@extends('app')

@section('title', 'User List')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header flex items-center justify-between">

                <div>
                    <h1 class="page-title">Users</h1>
                    <p class="text-muted mt-4">Manage all registered accounts</p>
                </div>
                @can('user.create')
                    <a href="{{route('users.create')}}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Add User
                    </a>
                @endcan

            </div>

            <div class="divider"></div>

            {{-- ================= FLASH ================= --}}
            @if (session('success'))
                <div class="alert alert-success mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ================= EMPTY ================= --}}
            @if ($users->isEmpty())

                <div class="text-center py-12">
                    <p class="text-muted">No users found.</p>
                    @can('user.create')
                        <a href="/users/create" class="btn btn-ghost mt-6">
                            Add First User
                        </a>
                    @endcan
                </div>
            @else
                {{-- ================= TABLE WRAPPER ================= --}}
                <div class="tbl-wrap" style="overflow-x:auto;">

                    <table class="tbl">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th class="hide-sm">Phone</th>
                                <th class="hide-sm">Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($users as $i => $user)
                                <tr>

                                    {{-- INDEX --}}
                                    <td class="text-muted">{{ $i + 1 }}</td>

                                    {{-- NAME --}}
                                    <td>
                                        <div class="flex items-center gap-3">

                                            <div
                                                style="
                                        width:34px;height:34px;border-radius:50%;
                                        background: rgba(13,148,136,.15);
                                        display:grid;place-items:center;
                                        font-weight:600;
                                        text-transform:uppercase;
                                    ">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>

                                            <span>{{ $user->name }}</span>

                                        </div>
                                    </td>

                                    {{-- PHONE --}}
                                    <td class="hide-sm">{{ $user->phone }}</td>

                                    {{-- EMAIL --}}
                                    <td class="hide-sm text-muted">
                                        {{ $user->email ?? '—' }}
                                    </td>

                                    {{-- ROLE --}}
                                    <td>
                                        @php
                                            $role = strtolower($user->getRoleNames()->first() ?? 'user');

                                            $badge = match ($role) {
                                                'admin' => 'badge-teal',
                                                'manager' => 'badge-warning',
                                                'banned' => 'badge-danger',
                                                default => 'badge-muted',
                                            };
                                        @endphp

                                        <span class="badge {{ $badge }}"> {{ ucfirst($role) }}</span>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td>

                                        <div class="flex items-center gap-2">

                                            {{-- EDIT --}}
                                            @can('user.edit')
                                                <a href="/users/{{ $user->id }}/edit" class="btn btn-ghost btn-icon"
                                                    title="Edit">

                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>

                                                </a>
                                            @endcan

                                            {{-- DELETE --}}

                                            @can('user.delete')
                                                <button type="button" class="btn btn-danger btn-icon"
                                                    onclick="openDeleteModal('{{ route('users.destroy', $user->id) }}', '{{ $user->name }}')">
                                                    <svg viewBox="0 0 24 24">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                    </svg>
                                                </button>
                                            @endcan



                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

@endsection
