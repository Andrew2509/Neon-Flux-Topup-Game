<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', get_setting('site_name', 'Neon Core') . ' — ' . get_setting('site_tagline', 'Dashboard'))</title>
    
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    {{-- Custom CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
        }
        .content-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        /* Color Constants for Tailwind 4 integration if needed */
        :root {
            --corp-navy: #1e293b;
            --corp-sidebar: #0f172a;
            --corp-accent: #2563eb;
            --corp-border: #e2e8f0;
            --corp-muted: #64748b;
            --corp-bg: #f8fafc;
        }

        /* Hide scrollbar for clean look */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Utility classes to match User Template */
        .bg-corp-sidebar { background-color: var(--corp-sidebar); }
        .bg-corp-accent { background-color: var(--corp-accent); }
        .bg-corp-bg { background-color: var(--corp-bg); }
        .border-corp-border { border-color: var(--corp-border); }
        .text-corp-navy { color: var(--corp-navy); }
        .text-corp-accent { color: var(--corp-accent); }
        .text-corp-muted { color: var(--corp-muted); }
    </style>

    @stack('styles')
</head>
<body class="h-full overflow-hidden flex font-sans selection:bg-blue-100 selection:text-corp-navy">
    {{-- Sidebar --}}
    @include('desktop.partials.user_sidebar')

    {{-- Main Content Wrapper --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Top Navigation Bar --}}
        {{-- Top Navigation Bar --}}
        <header class="h-16 flex items-center justify-between px-8 border-b border-corp-border bg-white shadow-sm z-10">
            {{-- Left: Search Bar Placeholder & Mobile Toggle --}}
            <div class="flex items-center flex-1 max-w-xl">
                <div class="relative w-full max-w-md hidden md:block">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-corp-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                    <input type="text" placeholder="Cari transaksi atau bantuan..." 
                           class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-xs text-corp-navy placeholder-corp-muted focus:outline-none focus:ring-1 focus:ring-corp-accent focus:bg-white transition-all">
                </div>
                {{-- Mobile Title --}}
                <div class="md:hidden">
                    <h1 class="text-sm font-bold text-corp-navy uppercase">@yield('page_title', 'Portal')</h1>
                </div>
            </div>
            
            {{-- Right: Notifications & User Info --}}
            <div class="flex items-center space-x-4">
                {{-- Back to Topup Button --}}
                <a href="{{ url('/') }}" class="hidden lg:flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-corp-navy transition-all shadow-md shadow-slate-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>TOP-UP CENTER</span>
                </a>

                {{-- Balance Badge --}}
                <div class="hidden sm:flex items-center bg-blue-50 border border-blue-100 rounded-lg px-3 py-1.5">
                    <span class="text-[10px] font-bold text-corp-accent uppercase tracking-wider mr-2">Saldo:</span>
                    <span class="text-xs font-bold text-corp-navy">Rp{{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                </div>

                {{-- Notification Bell --}}
                <button class="relative p-2 text-corp-muted hover:text-corp-accent transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <div class="h-6 w-px bg-corp-border hidden sm:block"></div>

                {{-- User Info Block --}}
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden lg:block">
                        <p class="text-xs font-bold text-corp-navy leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-corp-muted uppercase font-semibold tracking-wider mt-1">{{ Auth::user()->role }} Member</p>
                    </div>
                    <div class="w-9 h-9 rounded-full border-2 border-slate-100 p-0.5 bg-white shadow-sm overflow-hidden">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2563eb&color=fff' }}" 
                             class="w-full h-full rounded-full object-cover">
                    </div>
                </div>
            </div>
        </header>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto p-8 space-y-8 no-scrollbar">
            @yield('content')

            {{-- Footer --}}
            <footer class="pt-8 border-t border-corp-border flex flex-col md:flex-row items-center justify-between text-[10px] text-corp-muted uppercase tracking-widest gap-4 font-medium mt-auto">
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <p>© {{ date('Y') }} {{ strtoupper(get_setting('site_name', 'NEON CORE')) }}. SEMUA HAK DILINDUNGI.</p>
                    <div class="flex space-x-2">
                        <span class="px-2 py-0.5 border border-corp-border rounded bg-white">V4.0.21-STABLE</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-corp-accent border border-blue-100 rounded">SISTEM TERPROTEKSI</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 bg-white border border-corp-border px-3 py-1.5 rounded-full shadow-sm">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    <span>Latensi Server: <span class="text-corp-navy font-bold">12ms</span></span>
                </div>
            </footer>
        </div>
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#2563eb',
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#2563eb',
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
