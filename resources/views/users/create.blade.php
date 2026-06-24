@extends('app')

@section('title', 'Create User')

@section('content')

    <div class="page-layout">
        <div class="card card-full" style="max-width: 560px; margin: 0 auto;">

            {{-- ── Header ── --}}
            <div class="card-header">
                <div>
                    <h1 class="page-title">Create User</h1>
                    <p class="text-muted mt-4">Fill in the details to add a new account</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-ghost">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Back
                </a>
            </div>

            <div class="divider"></div>

            {{-- ── Errors ── --}}
            @if ($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink:0;margin-top:2px">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Form ── --}}
            <form method="POST" action="{{ route('users.store') }}" class="card-body">
                @csrf

                {{-- Name --}}
                <div class="field">
                    <label for="name">Full Name</label>
                    <div class="input-wrap">
                        <input id="name" type="text" name="name" placeholder="John Doe"
                            value="{{ old('name') }}" autocomplete="name">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                        </svg>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="field">
                    <label for="phone">
                        Phone
                        <span class="req">Required</span>
                    </label>
                    <div class="input-wrap">
                        <input id="phone" type="tel" name="phone" placeholder="01612345678"
                            value="{{ old('phone') }}" required autocomplete="tel">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 16.27z" />
                        </svg>
                    </div>
                </div>

                {{-- Email --}}
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <input id="email" type="email" name="email" placeholder="you@example.com"
                            value="{{ old('email') }}" autocomplete="email">
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
                            <option value="">Select Role</option>

                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Password --}}
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap has-eye">
                        <input id="password" type="password" name="password" placeholder="Min. 4 characters"
                            autocomplete="new-password" oninput="checkStrength(this.value)">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <button type="button" class="btn-eye" onclick="togglePassword()" aria-label="Toggle password">
                            <svg id="eye-icon" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <div class="strength-bar" id="strength-bar" data-level="0">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                </div>


                {{-- Submit --}}
                <div style="margin-top: 8px;">
                    <button type="submit" class="btn btn-primary btn-full">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" y1="8" x2="19" y2="14" />
                            <line x1="22" y1="11" x2="16" y2="11" />
                        </svg>
                        Create User
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.innerHTML = show ?
                `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
               <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
               <line x1="1" y1="1" x2="23" y2="23"/>` :
                `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
               <circle cx="12" cy="12" r="3"/>`;
        }

        function checkStrength(val) {
            const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
            const colors = ['', '#ef4444', '#f97316', '#eab308', '#0d9488'];
            let level = 0;
            if (val.length >= 8) level++;
            if (/[A-Z]/.test(val)) level++;
            if (/[0-9]/.test(val)) level++;
            if (/[^A-Za-z0-9]/.test(val)) level++;
            const bar = document.getElementById('strength-bar');
            const lbl = document.getElementById('strength-label');
            bar.dataset.level = val.length ? level : 0;
            lbl.textContent = val.length ? labels[level] : '';
            lbl.style.color = val.length ? colors[level] : '';
        }
    </script>

@endsection
