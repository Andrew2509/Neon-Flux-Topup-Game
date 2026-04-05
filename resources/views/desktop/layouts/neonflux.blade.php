<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', get_setting('site_name', 'Neon Flux Indonesia') . ' — ' . get_setting('site_tagline', 'Premium Gaming Experience'))</title>
    <meta name="description" content="{{ get_setting('meta_description', 'Platform top up game termurah, tercepat, dan teraman di Indonesia.') }}">
    @if($favicon = get_image_url('site_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        :root {
            --bg-color: #ffffff;
            --surface-color: #ffffff;
            --surface-alt: #f8fafc;
            --text-main: #000000;
            --border-color: rgba(0, 0, 0, 0.05);
            --hero-overlay: #ffffff;
            --grid-color: rgba(0, 0, 0, 0.03);
            --header-h: 80px;
        }

        :root.dark {
            --bg-color: #050510;
            --surface-color: rgba(20, 20, 35, 0.6);
            --surface-alt: #0a0a15;
            --text-main: #ffffff;
            --border-color: rgba(255, 255, 255, 0.05);
            --hero-overlay: #050510;
            --grid-color: rgba(18, 16, 35, 0.8);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .glass-panel {
            background: var(--surface-color);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .dark .glass-panel {
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
    </style>

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/neonflux/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/neonflux/topup.css') }}" />

    @stack('styles')
</head>
<body class="neonflux min-h-screen font-body overflow-x-hidden selection:bg-secondary selection:text-white">

    {{-- Navbar --}}
    @include('desktop.partials.navbar')

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer / Decorations --}}
    @include('desktop.partials.footer')

    {{-- Removed Theme Toggle Script per user request --}}


    {{-- Custom JS --}}
    <script src="{{ asset('js/neonflux/topup.js') }}"></script>

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
