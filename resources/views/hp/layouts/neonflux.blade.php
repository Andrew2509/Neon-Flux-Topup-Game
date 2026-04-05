<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Neon Flux Mobile')</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

    @include('partials.vite-safe')

    <script>
        (function() {
            // Force Light Mode for Mobile
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        })();
    </script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
        }


        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }


        .glass-panel-mobile {
            background: var(--surface-color);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }


        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Safe Area Padding for modern phones */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>

    @stack('styles')
</head>
<body class="font-body min-h-screen pb-24 bg-slate-50 text-slate-900 transition-colors duration-300 antialiased"> {{-- pb-24 for bottom nav --}}

    {{-- Top Navbar Mobile --}}
    @include('hp.partials.navbar')

    {{-- Content --}}
    <div class="max-w-5xl mx-auto px-4 md:px-8 pt-20">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('hp.partials.footer')

    {{-- Bottom Navigation --}}
    @include('hp.partials.bottom-nav')

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Session Alerts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                });
            @endif
        });
    </script>
</body>
</html>
