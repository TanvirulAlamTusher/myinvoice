@extends('app')

@section('title', 'Roles')

@section('content')

    <div class="page-layout">

        <div class="card">

            <div class="card-header">
                <h1 class="page-title">Roles</h1>
                @can('role.permission.create')
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        + Add Role
                    </a>
                @endcan
            </div>

            <div class="divider"></div>

            <div class="tbl-wrap">

                <table class="tbl">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role</th>
                            <th>Permissions</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($roles as $key => $role)

                            <tr>

                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <strong>
                                        {{ ucfirst($role->name) }}
                                    </strong>
                                </td>

                                <td>

                                    <div class="flex flex-wrap gap-2">

                                        @foreach ($role->permissions as $permission)
                                            <span class="badge badge-primary">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach

                                    </div>

                                </td>

                                <td>

                                    <div class="flex gap-2">
                                        @can('role.permission.edit')
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                        @endcan

                                        <form action="{{ route('roles.destroy', $role) }}" method="POST">

                                            @csrf
                                            @method('DELETE')
                                            @can('role.permission.delete')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete this role?')">
                                                    Delete
                                                </button>
                                            @endcan

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center">
                                    No roles found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
