@extends('app')

@section('title', 'Create Role')

@section('content')

<div class="page-layout">

    <div class="card card-full">

        <div class="card-header">
            <div>
                <h1 class="page-title">Create Role</h1>
                <p class="text-muted mt-4">Create a role and assign its access permissions</p>
            </div>

            <a href="{{ route('roles.index') }}" class="btn btn-ghost">
                &larr; Back
            </a>
        </div>

        <div class="divider"></div>

        <form action="{{ route('roles.store') }}"
              method="POST">

            @csrf

            <div class="grid-2">
                <div class="field">
                    <label>Role Name</label>

                    <input type="text"
                           name="name"
                           class="no-icon"
                           value="{{ old('name') }}"
                           required>
                </div>
            </div>

            <div class="mt-6">
                @include('roles.partials.permissions', [
                    'selectedPermissions' => old('permissions', []),
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
