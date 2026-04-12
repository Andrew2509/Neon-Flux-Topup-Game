<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Admin Panel') — Neon Flux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --color-primary: #2563eb;
            --color-secondary: #ef4444; /* Mapping accent-red to secondary for consistency */
        }
    </style>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .neon-border-blue {
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.2);
        }
        .neon-border-red {
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);
        }
        .sidebar-active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.2) 0%, rgba(37, 99, 235, 0) 100%);
            border-left: 3px solid #2563eb;
        }

        /* Sidebar Transition */
        aside, main {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Collapsed Sidebar State (Desktop) */
        @media (min-width: 1024px) {
            .sidebar-collapsed aside {
                width: 5.5rem !important; /* Adjusted for admin icons */
            }
            .sidebar-collapsed aside .hidden-on-collapse {
                display: none !important;
            }
            .sidebar-collapsed aside .centered-on-collapse {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .sidebar-collapsed aside .pl-12 {
                padding-left: 0 !important;
                justify-content: center !important;
            }
             .sidebar-collapsed aside nav {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }

        /* Mobile Sidebar Behavior */
        @media (max-width: 1023px) {
            aside {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(0);
                width: 280px !important;
                z-index: 40;
            }

            .sidebar-collapsed aside {
                transform: translateX(-100%) !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(4px);
                z-index: 35;
                transition: opacity 0.3s;
            }

            body.sidebar-open .sidebar-overlay {
                display: block;
            }
            
            body:not(.sidebar-collapsed) .sidebar-overlay {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen overflow-x-hidden">
<div class="flex h-screen overflow-hidden">
    <!-- Side Navigation -->
    <aside class="w-72 glass-panel border-r border-slate-200 dark:border-white/5 flex flex-col z-20 overflow-y-auto">
        <div class="p-6 flex items-center gap-3 centered-on-collapse">
            <div class="size-10 rounded-xl bg-gradient-to-br from-primary to-accent-red flex items-center justify-center shadow-lg shadow-primary/20 shrink-0">
                <span class="material-symbols-outlined text-white text-2xl">bolt</span>
            </div>
            <div class="hidden-on-collapse">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Neon Flux</h1>
                <p class="text-[10px] uppercase tracking-widest text-primary font-bold mt-1">Admin Panel</p>
            </div>
        </div>
        <nav class="flex-1 px-4 space-y-1">
            @can('akses-dashboard')
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Dashboard">
                <span class="material-symbols-outlined shrink-0">dashboard</span>
                <span class="text-sm font-semibold hidden-on-collapse">Dashboard</span>
            </a>
            @endcan
            @can('view-visitors')
            <a href="{{ route('admin.visitors') }}" class="{{ request()->routeIs('admin.visitors') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Visitor Tracking">
                <span class="material-symbols-outlined shrink-0">query_stats</span>
                <span class="text-sm font-medium hidden-on-collapse">Visitor Tracking</span>
            </a>
            @endcan
            @can('kelola-pesanan')
            <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Pesanan">
                <span class="material-symbols-outlined shrink-0">shopping_cart</span>
                <span class="text-sm font-medium hidden-on-collapse">Pesanan</span>
            </a>
            @endcan
            @can('manajemen-user-akses')
            <a href="{{ route('admin.management.user.index') }}" class="{{ request()->routeIs('admin.management.user.index') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Manajemen User">
                <span class="material-symbols-outlined shrink-0">group</span>
                <span class="text-sm font-medium hidden-on-collapse">Manajemen User</span>
            </a>
            @endcan
            @can('kelola-deposit')
            <a href="{{ route('admin.deposits') }}" class="{{ request()->routeIs('admin.deposits') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Deposit Member">
                <span class="material-symbols-outlined shrink-0">account_balance_wallet</span>
                <span class="text-sm font-medium hidden-on-collapse">Deposit Member</span>
            </a>
            @endcan
            @can('kelola-kategori')
            <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Kategori">
                <span class="material-symbols-outlined shrink-0">category</span>
                <span class="text-sm font-medium hidden-on-collapse">Kategori</span>
            </a>
            @endcan
            @php
                $isProductActive = request()->routeIs('admin.services') || request()->routeIs('admin.packages');
            @endphp
            @if(Auth::user()->can('kelola-layanan') || Auth::user()->can('kelola-paket') || Auth::user()->can('kelola-tokovoucher') || Auth::user()->can('kelola-logo'))
            <div class="{{ $isProductActive ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" onclick="document.getElementById('product-submenu').classList.toggle('hidden')" title="Produk">
                <span class="material-symbols-outlined shrink-0">inventory_2</span>
                <span class="text-sm font-medium hidden-on-collapse">Produk</span>
                <span class="material-symbols-outlined text-sm ml-auto text-slate-500 transition-transform duration-200 hidden-on-collapse" id="product-chevron">expand_more</span>
            </div>
            <div id="product-submenu" class="pl-12 pr-4 space-y-1 mt-1 {{ $isProductActive ? '' : 'hidden' }}">
                @can('kelola-layanan')
                <a href="{{ route('admin.services') }}" class="{{ request()->routeIs('admin.services') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Layanan">
                    <span class="text-xs font-medium hidden-on-collapse">Layanan</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">list_alt</span>
                </a>
                @endcan
                @can('kelola-paket')
                <a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Paket Layanan">
                    <span class="text-xs font-medium hidden-on-collapse">Paket Layanan</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">package</span>
                </a>
                @endcan
                @can('kelola-tokovoucher')
                <a href="{{ route('admin.tokovoucher.categories') }}" class="{{ request()->routeIs('admin.tokovoucher.categories') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Kategori (TokoVoucher)">
                    <span class="text-xs font-medium hidden-on-collapse">Kategori (TokoVoucher)</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">token</span>
                </a>
                @endcan
                @can('kelola-logo')
                <a href="{{ route('admin.logo-generator.index') }}" class="{{ request()->routeIs('admin.logo-generator.index') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Logo Generator">
                    <span class="text-xs font-medium hidden-on-collapse">Logo Generator</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">app_registration</span>
                </a>
                @endcan
            </div>
            @endif
            @can('kelola-voucher')
            <a href="{{ route('admin.vouchers') }}" class="{{ request()->routeIs('admin.vouchers') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Voucher">
                <span class="material-symbols-outlined shrink-0">confirmation_number</span>
                <span class="text-sm font-medium hidden-on-collapse">Voucher</span>
            </a>
            @endcan
            @can('kelola-slider')
            <a href="{{ route('admin.sliders') }}" class="{{ request()->routeIs('admin.sliders') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Slider dan Banner">
                <span class="material-symbols-outlined shrink-0">view_carousel</span>
                <span class="text-sm font-medium hidden-on-collapse">Slider dan Banner</span>
            </a>
            @endcan
            @can('kelola-pembayaran')
            <a href="{{ route('admin.payments') }}" class="{{ request()->routeIs('admin.payments') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Pembayaran">
                <span class="material-symbols-outlined shrink-0">payments</span>
                <span class="text-sm font-medium hidden-on-collapse">Pembayaran</span>
            </a>
            @endcan
            @php
                $isWithdrawalActive = request()->routeIs('admin.withdrawals.*');
            @endphp
            @can('kelola-penarikan')
            <div class="{{ $isWithdrawalActive ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" onclick="document.getElementById('withdrawal-submenu').classList.toggle('hidden')" title="Penarikan">
                <span class="material-symbols-outlined shrink-0">account_balance</span>
                <span class="text-sm font-medium hidden-on-collapse">Penarikan</span>
                <span class="material-symbols-outlined text-sm ml-auto text-slate-500 transition-transform duration-200 hidden-on-collapse" id="withdrawal-chevron">expand_more</span>
            </div>
            <div id="withdrawal-submenu" class="pl-12 pr-4 space-y-1 mt-1 {{ $isWithdrawalActive ? '' : 'hidden' }}">
                <a href="{{ route('admin.withdrawals.bank') }}" class="{{ request()->routeIs('admin.withdrawals.bank') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Transfer Bank">
                    <span class="text-xs font-medium hidden-on-collapse">Transfer Bank</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">account_balance_wallet</span>
                </a>
                <a href="{{ route('admin.withdrawals.ewallet') }}" class="{{ request()->routeIs('admin.withdrawals.ewallet') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="E-Wallet Reload">
                    <span class="text-xs font-medium hidden-on-collapse">E-Wallet Reload</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">send_to_mobile</span>
                </a>
            </div>
            @endcan
            @can('view-rating')
            <a href="{{ route('admin.ratings') }}" class="{{ request()->routeIs('admin.ratings') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Rating Customer">
                <span class="material-symbols-outlined shrink-0">star</span>
                <span class="text-sm font-medium hidden-on-collapse">Rating Customer</span>
            </a>
            @endcan
            @can('kelola-provider')
            <a href="{{ route('admin.providers') }}" class="{{ request()->routeIs('admin.providers') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Provider">
                <span class="material-symbols-outlined shrink-0">database</span>
                <span class="text-sm font-medium hidden-on-collapse">Provider</span>
            </a>
            @endcan
            @can('kelola-setting')
            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Pengaturan Website">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="text-sm font-medium hidden-on-collapse">Pengaturan Website</span>
            </a>
            @endcan

            @can('manajemen-user-akses')
            <div class="mt-4 mb-1 px-4 text-[10px] uppercase tracking-widest text-slate-500 font-bold hidden-on-collapse">Administrator</div>
            @php
                $isAksesActive = request()->routeIs('admin.management.*');
            @endphp
            <div class="{{ $isAksesActive ? 'sidebar-active text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" onclick="document.getElementById('akses-submenu').classList.toggle('hidden')" title="Manajemen Akses">
                <span class="material-symbols-outlined shrink-0">admin_panel_settings</span>
                <span class="text-sm font-medium hidden-on-collapse">Manajemen Akses</span>
                <span class="material-symbols-outlined text-sm ml-auto text-slate-500 transition-transform duration-200 hidden-on-collapse" id="akses-chevron">expand_more</span>
            </div>
            <div id="akses-submenu" class="pl-12 pr-4 space-y-1 mt-1 {{ $isAksesActive ? '' : 'hidden' }}">
                <a href="{{ route('admin.management.user.index') }}" class="{{ request()->routeIs('admin.management.user.index') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Manajemen User">
                    <span class="text-xs font-medium hidden-on-collapse">Manajemen User</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">person</span>
                </a>
                <a href="{{ route('admin.management.role.index') }}" class="{{ request()->routeIs('admin.management.role.*') ? 'text-primary' : 'text-slate-600 dark:text-slate-400' }} flex items-center gap-3 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all cursor-pointer centered-on-collapse" title="Role & Permission">
                    <span class="text-xs font-medium hidden-on-collapse">Role & Permission</span>
                    <span class="material-symbols-outlined text-lg lg:hidden">lock</span>
                </a>
            </div>
            @endcan
        </nav>
        <div class="p-4 mt-auto">
            <div class="glass-panel rounded-xl p-4 flex items-center gap-3 bg-white/5 border border-white/10 centered-on-collapse">
                <div class="size-10 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden border border-primary/50 shrink-0">
                    <img alt="Admin Profile" class="size-full object-cover" data-alt="Male designer avatar for admin profile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpx3F_kRlYUGhrdhuYndHtzTw0Tx9vdMk6GTEGZpB4YFmO2KIP_DisrQFbeHfXKzijvnoVx3X9RYbPYoDLE6E2mAhf1X-Tzn9dOY2BwRH6ydTG2Wyeae7QFibuyI3xo9MvbXwFgtq_Zd_hQMgcBYTJtxmKAM6cX4zCME1ly-36kQGX4ftooyHUzO4tPjVDVBfc9Me95HDNLWvBxH318XImlzRCzpy3CvXa8SjEyRxx7yhksiVSYAUmVxRSRTgR_yBv2Y2DEEYZJJA"/>
                </div>
                <div class="flex-1 overflow-hidden hidden-on-collapse">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-400">{{ Auth::user()->role ? Auth::user()->role->name : 'Admin' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                    @csrf
                </form>
                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-slate-400 hover:text-accent-magenta transition-colors hidden-on-collapse">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </div>
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div id="nf-sidebar-overlay" class="sidebar-overlay"></div>
    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto relative bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-primary/10 via-background-dark to-background-dark">
        <!-- Header -->
        <header class="sticky top-0 z-10 glass-panel px-8 py-4 flex items-center justify-between border-b border-white/5">
            <div class="flex items-center gap-4">
                <button id="nf-sidebar-toggle" class="size-10 glass-panel rounded-full flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div>
                    <h2 class="text-2xl font-bold">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-sm text-slate-400">@yield('page_description', 'Selamat datang kembali di pusat kendali Neon Flux.')</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xl">search</span>
                    <input class="bg-white/5 border border-white/10 rounded-full pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-64 outline-none" placeholder="Cari pesanan atau member..." type="text"/>
                </div>
                <button class="size-10 glass-panel rounded-full flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
            </div>
        </header>
        <div class="p-8 space-y-8">
            <!-- Session Alerts -->
            @if(session('success'))
            <div class="glass-panel p-4 rounded-2xl border-green-500/20 bg-green-500/10 flex gap-3 text-green-400 animate-in fade-in slide-in-from-top-4 duration-300">
                <span class="material-symbols-outlined">check_circle</span>
                <div class="text-xs font-medium leading-relaxed">
                    <p class="font-bold mb-1">Berhasil!</p>
                    {!! session('success') !!}
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="glass-panel p-4 rounded-2xl border-red-500/20 bg-red-500/10 flex gap-3 text-red-400 animate-in fade-in slide-in-from-top-4 duration-300">
                <span class="material-symbols-outlined">error</span>
                <div class="text-xs font-medium leading-relaxed">
                    <p class="font-bold mb-1">Terjadi Kesalahan</p>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            @if(session('info'))
            <div class="glass-panel p-4 rounded-2xl border-primary/20 bg-primary/10 flex gap-3 text-primary animate-in fade-in slide-in-from-top-4 duration-300">
                <span class="material-symbols-outlined">info</span>
                <div class="text-xs font-medium leading-relaxed">
                    <p class="font-bold mb-1">Informasi</p>
                    {{ session('info') }}
                </div>
            </div>
            @endif

            @yield('content')
        </div>
        <!-- Floating Decoration Background -->
        <div class="fixed bottom-0 right-0 w-[500px] h-[500px] bg-primary/5 blur-[120px] -z-10 rounded-full"></div>
        <div class="fixed top-0 left-0 w-[300px] h-[300px] bg-accent-red/5 blur-[100px] -z-10 rounded-full"></div>
    </main>
</div>
@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle Logic
        const body = document.body;
        const toggleBtn = document.getElementById('nf-sidebar-toggle');
        const overlay = document.getElementById('nf-sidebar-overlay');
        const sidebarNav = document.querySelector('aside');
        
        // Initial state based on screen size
        const checkIsMobile = () => window.innerWidth < 1024;
        
        // Load state
        const savedState = localStorage.getItem('admin-sidebar-collapsed');
        
        if (checkIsMobile()) {
            body.classList.add('sidebar-collapsed');
        } else {
            if (savedState === 'true') {
                body.classList.add('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            body.classList.toggle('sidebar-collapsed');
            if (!checkIsMobile()) {
                localStorage.setItem('admin-sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }

        // Close on overlay click (mobile only)
        if (overlay) {
            overlay.addEventListener('click', () => {
                if (checkIsMobile()) {
                    body.classList.add('sidebar-collapsed');
                }
            });
        }

        // Auto-hide on sidebar link click (mobile only)
        if (sidebarNav) {
            const links = sidebarNav.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', () => {
                    if (checkIsMobile()) {
                        setTimeout(() => {
                            body.classList.add('sidebar-collapsed');
                        }, 100);
                    }
                });
            });
        }
    });
</script>
</body>
</html>
