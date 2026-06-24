<header class="header">

    <div class="header-left">

        <!-- Sidebar Toggle -->
        <button id="sidebarToggle" class="menu-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true" aria-controls="sidebar">
            &#9776;
        </button>

        <!-- Title -->
        <div class="header-title">
            <h1>{{ $businessName }}</h1>
            <p>Welcome back to your invoice system</p>
        </div>

    </div>

    <!-- Right Side -->
    <div class="header-right">
        @auth
            @php($user = Auth::user())

            <!-- User Box -->
            <a href="{{ route('profile.index') }}" class="user-box" aria-label="Open profile">
            <div class="avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div class="user-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $user->getRoleNames()->first() ?? 'User' }}</p>
            </div>
            </a>
        @endauth

    </div>

</header>
