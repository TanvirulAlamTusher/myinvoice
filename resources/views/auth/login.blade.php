<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />

</head>

<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="card">

        <!-- Logo -->
        <div class="logo-wrap">
            <div class="logo-ring">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <h1 class="card-title">Welcome back</h1>
        <p class="card-subtitle">Sign in to your dashboard</p>

        <div class="divider"></div>

        <!-- Errors -->
        @if ($errors->any())
        <div class="error-block">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
            <div class="success-block">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email / Phone -->
            <div class="field">
                <label for="login-input">Email / Phone</label>
                <div class="input-wrap">
                    <input
                        id="login-input"
                        type="text"
                        name="login"
                        placeholder="you@example.com"
                        value="{{ old('login') }}"
                        required
                        autocomplete="username"
                    >
                    <svg class="input-icon" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m2 7 10 7 10-7"/>
                    </svg>
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label for="password-input">Password</label>
                <div class="input-wrap">
                    <input
                        id="password-input"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                    <svg class="input-icon" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="meta-row">
                <label class="remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
                <button type="button" class="forgot reset-toggle" id="resetToggle">Reset password</button>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login">
                Sign In
            </button>

        </form>

        <form method="POST" action="{{ route('password.reset') }}" class="reset-form" id="resetForm">
            @csrf

            <div class="reset-divider">
                <span>Reset password</span>
            </div>

            <div class="field">
                <label for="reset-login-input">Email / Phone</label>
                <div class="input-wrap">
                    <input
                        id="reset-login-input"
                        type="text"
                        name="login"
                        placeholder="Email or phone number"
                        value="{{ old('login') }}"
                        autocomplete="username"
                    >
                    <svg class="input-icon" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m2 7 10 7 10-7"/>
                    </svg>
                </div>
            </div>

            <button type="submit" class="btn-login reset-submit">
                Reset
            </button>
        </form>

        <!-- Footer -->
        {{-- <p class="card-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </p> --}}

    </div>

    <script>
        const resetToggle = document.getElementById('resetToggle');
        const resetForm = document.getElementById('resetForm');
        const resetInput = document.getElementById('reset-login-input');

        resetToggle.addEventListener('click', function () {
            resetForm.classList.toggle('active');

            if (resetForm.classList.contains('active')) {
                resetInput.focus();
            }
        });
    </script>

</body>
</html>
