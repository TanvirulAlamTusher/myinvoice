@extends('app')

@section('title', 'Update Role')

@section('content')

<div class="page-layout">

    <div class="card card-full">

        <div class="card-header">
            <div>
                <h1 class="page-title">Update Role</h1>
                <p class="text-muted mt-4">Update role details and permission access</p>
            </div>

            <a href="{{ route('roles.index') }}" class="btn btn-ghost">
                &larr; Back
            </a>
        </div>

        <div class="divider"></div>

        <form action="{{ route('roles.update', $role) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="grid-2">
                <div class="field">
                    <label>Role Name</label>

                    <input value="{{ old('name', $role->name) }}"
                           type="text"
                           name="name"
                           class="no-icon"
                           required>
                </div>
            </div>

            <div class="mt-6">
                @include('roles.partials.permissions', [
                    'selectedPermissions' => old('permissions', $role->permissions->pluck('name')->toArray()),
                ])
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="btn btn-primary">
                    Save Role
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
