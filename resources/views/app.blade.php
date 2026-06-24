<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>{{ $title ?? 'Dashboard' }}</title>
       @php
        $business = \App\Models\BusinessSetting::first();
    @endphp

    @if ($business && $business->favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $business->favicon) }}">
    @endif

    {{-- ================= CSS ================= --}}
    @vite(['resources/css/app.css', 'resources/css/teal.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @stack('styles')

    {{-- ================= CSRF (IMPORTANT FOR AXIOS) ================= --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

    <div class="layout" id="layout">

        <!-- ================= SIDEBAR ================= -->
        <div id="sidebar">
            @include('components.sidebar')
        </div>

        <button class="sidebar-backdrop" type="button" aria-label="Close sidebar"></button>

        <!-- ================= MAIN ================= -->
        <main class="main">

            @include('components.header')

            @yield('content')

        </main>

    </div>

    {{-- ================= DELETE MODAL ================= --}}
    @include('components.delete_modal')

    {{-- ================= TOAST CONTAINER ================= --}}
    <div id="toast-container"></div>

    {{-- ================= AXIOS (LOCAL - OFFLINE SAFE) ================= --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>

    <script>
        // ================= AXIOS DEFAULT CONFIG =================
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').content;
    </script>

    {{-- ================= TOAST MESSAGE HANDLER ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('success'))
                toast(@json(session('success')), 'success');
            @endif

            @if (session('error'))
                toast(@json(session('error')), 'error');
            @endif

            @if (session('warning'))
                toast(@json(session('warning')), 'warning');
            @endif

        });
    </script>

    {{-- ================= GLOBAL APP JS ================= --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- ================= PAGE SCRIPTS ================= --}}
    @stack('scripts')

    <script>
        document.addEventListener('click', function(e) {

            const toggle = e.target.closest('.action-toggle');

            // TOGGLE MENU
            if (toggle) {

                const dropdown = toggle.closest('.action-dropdown');

                // close others
                document.querySelectorAll('.action-dropdown').forEach(item => {
                    if (item !== dropdown) {
                        item.classList.remove('active');
                    }
                });

                dropdown.classList.toggle('active');

                return;
            }

            // CLOSE ALL WHEN CLICK OUTSIDE
            document.querySelectorAll('.action-dropdown').forEach(item => {
                item.classList.remove('active');
            });

        });
    </script>

</body>

</html>
