{{-- ============================================================
    NAVBAR — Fixed top; menu penuh di md+; drawer + bottom bar di HP
    ============================================================ --}}
<nav class="fixed top-0 w-full z-50 px-3 sm:px-6 py-3 md:py-4 glass-panel border-b border-black/5 dark:border-white/10 flex justify-between items-center gap-2 rounded-b-2xl max-w-7xl mx-auto left-0 right-0 mt-1 md:mt-2 shadow-sm dark:shadow-none bg-white/80 dark:bg-transparent">
    {{-- Logo --}}
    <div class="flex items-center gap-2 min-w-0 flex-shrink">
        @if($logo = get_image_url('site_logo'))
            <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'Neon Flux') }}" class="h-9 w-auto sm:h-11 md:h-14 flex-shrink-0">
        @endif
        <span class="font-display font-bold text-lg sm:text-xl md:text-2xl tracking-wider text-slate-900 dark:text-white transition-colors truncate min-w-0 hidden sm:inline">
            <span class="text-cyan-600 dark:text-primary">{{ explode(' ', get_setting('site_name', 'NEON FLUX'))[0] }}</span>{{ substr(get_setting('site_name', 'NEON FLUX'), strlen(explode(' ', get_setting('site_name', 'NEON FLUX'))[0])) }}
        </span>
    </div>

    {{-- Nav Links (tablet & desktop) --}}
    <div class="hidden md:flex flex-1 justify-center max-w-3xl px-4 space-x-6 lg:space-x-8 text-base lg:text-lg font-medium text-slate-500 dark:text-gray-400">
        <a class="hover:text-primary transition-colors whitespace-nowrap {{ request()->is('/') ? 'text-slate-900 dark:text-white text-glow' : '' }}" href="{{ url('/') }}">Top-Up</a>
        <a class="hover:text-primary transition-colors whitespace-nowrap {{ request()->is('catalog') ? 'text-slate-900 dark:text-white text-glow' : '' }}" href="{{ route('catalog') }}">Games</a>
        <a class="hover:text-primary transition-colors whitespace-nowrap {{ request()->routeIs('track.order') ? 'text-slate-900 dark:text-white text-glow' : '' }}" href="{{ route('track.order') }}">Cek Transaksi</a>
        <div class="relative group">
            <button type="button" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer whitespace-nowrap {{ request()->is('kalkulator*') ? 'text-slate-900 dark:text-white text-glow' : '' }}">
                <span>Kalkulator</span>
                <span class="material-icons-round text-sm transition-transform group-hover:rotate-180">expand_more</span>
            </button>
            <div class="absolute left-1/2 -translate-x-1/2 mt-4 w-72 glass-panel border border-black/5 dark:border-white/10 rounded-4xl py-4 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 bg-white/95 dark:bg-[#0a0a15]/95 backdrop-blur-xl">
                <a href="{{ route('kalkulator.winrate') }}" class="flex items-start gap-4 px-4 py-3 hover:bg-primary/10 transition-colors group/item">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover/item:bg-primary group-hover/item:text-slate-950 transition-all">
                        <span class="material-icons-round font-bold">calculate</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Win Rate</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight mt-1">Hitung match untuk target win rate.</p>
                    </div>
                </a>
                <a href="{{ route('kalkulator.magicwheel') }}" class="flex items-start gap-4 px-4 py-3 hover:bg-primary/10 transition-colors group/item">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover/item:bg-primary group-hover/item:text-slate-950 transition-all">
                        <span class="material-icons-round font-bold">auto_fix_high</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Magic Wheel</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight mt-1">Estimasi diamond skin Legends.</p>
                    </div>
                </a>
                <a href="{{ route('kalkulator.zodiac') }}" class="flex items-start gap-4 px-4 py-3 hover:bg-primary/10 transition-colors group/item">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover/item:bg-primary group-hover/item:text-slate-950 transition-all">
                        <span class="material-icons-round font-bold">stars</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Zodiac</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight mt-1">Hitung diamond skin Zodiac.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- User + menu HP --}}
    <div class="flex items-center gap-1 sm:gap-3 flex-shrink-0">
        @auth
            <div class="relative group/user hidden sm:block">
                <div class="flex items-center gap-2 glass-panel px-2 sm:px-3 py-1.5 rounded-full cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-all">
                    <img alt="" class="w-8 h-8 rounded-full border border-primary"
                         src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" />
                    <span class="font-bold text-sm text-slate-950 dark:text-white transition-colors max-w-[100px] truncate lg:max-w-none">{{ auth()->user()->name }}</span>
                    <span class="material-icons-round text-sm text-slate-600 dark:text-gray-400">expand_more</span>
                </div>
                <div class="absolute right-0 mt-2 w-48 glass-panel border border-black/5 dark:border-white/10 rounded-2xl py-2 shadow-xl opacity-0 invisible group-hover/user:opacity-100 group-hover/user:visible transition-all duration-300 z-50 bg-white/95 dark:bg-[#0a0a15]/95 backdrop-blur-xl">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors">
                            <span class="material-icons-round text-lg">dashboard</span>
                            <span>Admin Panel</span>
                        </a>
                        <div class="border-t border-black/5 dark:border-white/5 my-1"></div>
                    @endif
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors">
                        <span class="material-icons-round text-lg">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors">
                        <span class="material-icons-round text-lg">person</span>
                        <span>Profil</span>
                    </a>
                    <a href="{{ route('user.riwayat') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors">
                        <span class="material-icons-round text-lg">history</span>
                        <span>Riwayat</span>
                    </a>
                    <div class="border-t border-black/5 dark:border-white/5 my-1"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-500/10 transition-colors">
                            <span class="material-icons-round text-lg">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <a href="{{ route('user.dashboard') }}" class="sm:hidden flex items-center justify-center size-10 rounded-full glass-panel border border-white/10" title="Akun">
                <img alt="" class="w-8 h-8 rounded-full border border-primary" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" />
            </a>
        @else
            <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 glass-panel px-4 sm:px-6 py-2 rounded-full cursor-pointer hover:bg-primary group transition-all duration-300">
                <span class="material-icons-round text-sm text-primary group-hover:text-white transition-colors">login</span>
                <span class="font-bold text-sm text-slate-950 dark:text-white group-hover:text-white transition-colors">Masuk</span>
            </a>
            <a href="{{ route('login') }}" class="sm:hidden flex items-center justify-center size-10 rounded-full glass-panel border border-white/10 text-primary" title="Masuk">
                <span class="material-icons-round text-xl">login</span>
            </a>
        @endauth

        <button type="button" id="nf-drawer-open" class="md:hidden flex items-center justify-center size-10 rounded-xl border border-white/15 bg-white/10 dark:bg-white/5 text-slate-900 dark:text-white touch-manipulation" aria-label="Buka menu">
            <span class="material-icons-round text-2xl">menu</span>
        </button>
    </div>
</nav>
