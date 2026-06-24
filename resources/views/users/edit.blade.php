@extends('app')

@section('title', 'Edit User')

@section('content')

    <div class="page-layout">

        <div class="card card-sm">

            {{-- HEADER --}}
            <div class="card-header">
                <div>
                    <h1 class="page-title">Edit User</h1>
                    <p class="text-muted mt-4">Update user information</p>
                </div>

                <a href="/users" class="btn btn-ghost">
                    ← Back
                </a>
            </div>

            <div class="divider"></div>

            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="/users/{{ $user->id }}">
                @csrf
                @method('PUT')

                {{-- NAME --}}
                <div class="field">
                    <label>Name</label>
                    <div class="input-wrap">
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Full Name"
                            required>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                        </svg>
                    </div>
                </div>

                {{-- PHONE --}}
                <div class="field">
                    <label>Phone</label>
                    <div class="input-wrap">
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            placeholder="Phone Number" required>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 16.27z" />
                        </svg>
                    </div>
                </div>

                {{-- EMAIL --}}
                <div class="field">
                    <label>Email</label>
                    <div class="input-wrap">
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            placeholder="Email (optional)">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                    </div>
                </div>
                {{-- ROLE --}}
                <div class="field">
                    <label>Role</label>

                    <div class="input-wrap">
                        <select name="role" required>

                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>



                {{-- PASSWORD --}}
                <div class="field">
                    <label>Password <span class="text-muted">(leave blank to keep current)</span></label>

                    <div class="input-wrap has-eye">
                        <input type="password" name="password" placeholder="New Password">

                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                </div>

                {{-- BUTTON --}}
                <button type="submit" class="btn btn-primary btn-full">
                    Update User
                </button>

            </form>

        </div>

    </div>

@endsection
