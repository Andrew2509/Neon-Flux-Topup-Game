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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <style>
        :root {
            --bg-color: #050510;
            --surface-color: #0a0a1a;
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
        }


        body {
            background-color: #050510;
            background-image:
                radial-gradient(circle at 15% 50%, rgba(0, 240, 255, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(255, 0, 153, 0.08), transparent 25%),
                linear-gradient(rgba(18, 16, 35, 0.5) 2px, transparent 2px),
                linear-gradient(90deg, rgba(18, 16, 35, 0.5) 2px, transparent 2px);
            background-size: 100% 100%, 100% 100%, 50px 50px, 50px 50px;
            color: var(--text-main);
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }


        .glass-panel-mobile {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.3);
        }


        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Safe Area Padding for modern phones */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>

    @stack('styles')
</head>
<body class="font-body min-h-screen pb-24 text-slate-100 antialiased"> {{-- pb-24 for bottom nav --}}

    {{-- Top Navbar Mobile --}}
    @include('tablet.partials.navbar')

    {{-- Content --}}
    <div class="max-w-5xl mx-auto px-4 md:px-8 pt-20 relative z-0">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('hp.partials.footer')

    {{-- Pagination removed for Tablet to show all operators at once --}}
    @include('tablet.partials.bottom-nav')

    {{-- Scripts --}}
    <link rel="stylesheet" href="{{ asset('css/neonflux/base.css') }}?v={{ time() }}" />
    <script src="{{ asset('js/neonflux/theme-toggle.js') }}"></script>
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
