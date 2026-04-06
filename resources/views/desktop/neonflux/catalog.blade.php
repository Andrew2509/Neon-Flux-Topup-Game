@extends('desktop.layouts.neonflux')

@section('title', 'Katalog Game ' . get_setting('site_name', 'Neon Flux'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/neonflux/catalog.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('js/neonflux/catalog.js') }}"></script>
@endpush

@section('content')
<main class="pt-32 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 h-full min-h-screen">
    <!-- Sidebar: Kategori -->
    <aside class="w-full lg:w-64 shrink-0">
        <div class="glass-panel p-6 rounded-3xl sticky top-32 bg-white dark:bg-background-dark/60 shadow-sm dark:shadow-none border border-black/5 dark:border-white/10">
            <h3 class="text-xl font-display font-bold text-slate-900 dark:text-white mb-6 flex items-center transition-colors">
                <span class="material-icons-round mr-2 text-primary">filter_list</span>
                Kategori
            </h3>
            <div class="flex flex-col space-y-2">
                <a href="{{ route('catalog') }}" class="flex items-center justify-between w-full px-4 py-3 rounded-xl {{ !request('popular') && !request('platform') ? 'bg-primary/10 dark:bg-primary/20 border border-primary/30 dark:border-primary/40 text-slate-900 dark:text-white shadow-sm dark:shadow-neon-cyan' : 'text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5' }} font-bold transition-all group">
                    <div class="flex items-center">
                        <span class="material-icons-round mr-3 {{ !request('popular') && !request('platform') ? 'text-primary' : '' }} group-hover:text-primary transition-colors">apps</span>
                        Semua Game
                    </div>
                    <span class="text-xs {{ !request('popular') && !request('platform') ? 'bg-primary/20 text-primary' : 'bg-black/5 dark:bg-white/10 text-slate-500' }} px-2 py-0.5 rounded-md font-mono uppercase">{{ $counts['all'] }} items</span>
                </a>
                <a href="{{ route('catalog', array_merge(request()->query(), ['popular' => 1, 'platform' => null])) }}" class="flex items-center justify-between w-full px-4 py-3 rounded-xl {{ request('popular') ? 'bg-primary/10 dark:bg-primary/20 border border-primary/30 dark:border-primary/40 text-slate-950 dark:text-white shadow-sm dark:shadow-neon-cyan' : 'text-slate-700 dark:text-gray-400 hover:text-primary dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5' }} font-bold transition-all group">
                    <div class="flex items-center">
                        <span class="material-icons-round mr-3 text-secondary group-hover:text-secondary/80 transition-colors">local_fire_department</span>
                        Populer
                    </div>
                    <span class="text-xs {{ request('popular') ? 'bg-primary/20 text-primary' : 'bg-black/5 dark:bg-white/10 text-slate-500' }} px-2 py-0.5 rounded-md font-mono uppercase text-[9px]">{{ $counts['popular'] }}</span>
                </a>
                <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'mobile', 'popular' => null])) }}" class="flex items-center justify-between w-full px-4 py-3 rounded-xl {{ request('platform') == 'mobile' ? 'bg-primary/10 dark:bg-primary/20 border border-primary/30 dark:border-primary/40 text-slate-950 dark:text-white shadow-sm dark:shadow-neon-cyan' : 'text-slate-700 dark:text-gray-400 hover:text-primary dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5' }} font-bold transition-all group">
                    <div class="flex items-center">
                        <span class="material-icons-round mr-3 {{ request('platform') == 'mobile' ? 'text-primary' : '' }} group-hover:text-primary transition-colors">phone_iphone</span>
                        Mobile
                    </div>
                    <span class="text-xs {{ request('platform') == 'mobile' ? 'bg-primary/20 text-primary' : 'bg-black/5 dark:bg-white/10 text-slate-500' }} px-2 py-0.5 rounded-md font-mono uppercase text-[9px]">{{ $counts['mobile'] }}</span>
                </a>
                <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'pc', 'popular' => null])) }}" class="flex items-center justify-between w-full px-4 py-3 rounded-xl {{ request('platform') == 'pc' ? 'bg-primary/10 dark:bg-primary/20 border border-primary/30 dark:border-primary/40 text-slate-900 dark:text-white shadow-sm dark:shadow-neon-cyan' : 'text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5' }} font-bold transition-all group">
                    <div class="flex items-center">
                        <span class="material-icons-round mr-3 {{ request('platform') == 'pc' ? 'text-primary' : '' }} group-hover:text-primary transition-colors">computer</span>
                        PC
                    </div>
                    <span class="text-xs {{ request('platform') == 'pc' ? 'bg-primary/20 text-primary' : 'bg-black/5 dark:bg-white/10 text-slate-500' }} px-2 py-0.5 rounded-md font-mono uppercase text-[9px]">{{ $counts['pc'] }}</span>
                </a>
                <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'console', 'popular' => null])) }}" class="flex items-center justify-between w-full px-4 py-3 rounded-xl {{ request('platform') == 'console' ? 'bg-primary/10 dark:bg-primary/20 border border-primary/30 dark:border-primary/40 text-slate-900 dark:text-white shadow-sm dark:shadow-neon-cyan' : 'text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5' }} font-bold transition-all group">
                    <div class="flex items-center">
                        <span class="material-icons-round mr-3 {{ request('platform') == 'console' ? 'text-primary' : '' }} group-hover:text-primary transition-colors">gamepad</span>
                        Console
                    </div>
                    <span class="text-xs {{ request('platform') == 'console' ? 'bg-primary/20 text-primary' : 'bg-black/5 dark:bg-white/10 text-slate-500' }} px-2 py-0.5 rounded-md font-mono uppercase text-[9px]">{{ $counts['console'] }}</span>
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-black/5 dark:border-white/10">
                <h3 class="text-sm font-bold text-slate-500 dark:text-gray-500 mb-4 uppercase tracking-wider">Platform</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Android', 'iOS', 'Steam', 'PlayStation'] as $plat)
                        <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => strtolower($plat), 'popular' => null])) }}"
                           class="px-3 py-1 rounded-lg {{ request('platform') == strtolower($plat) ? 'bg-primary text-slate-900 font-bold' : 'bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-500 dark:text-gray-400 hover:bg-black/10 dark:hover:bg-white/10' }} shadow-sm dark:shadow-none transition-all cursor-pointer platform-badge hover:border-primary/50">
                           {{ $plat }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>

    <div class="flex-1">
        <form action="{{ route('catalog') }}" method="GET" class="glass-panel p-4 rounded-2xl mb-8 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-background-dark/60 border border-black/5 dark:border-white/10 shadow-sm dark:shadow-none">
            <div class="relative w-full sm:max-w-md group">
                <div class="relative bg-white dark:bg-[#0a0a15] border border-black/10 dark:border-white/10 rounded-xl flex items-center p-1 group-focus-within:border-primary/50 transition-all shadow-sm">
                    <span class="material-icons-round text-slate-400 dark:text-gray-500 ml-3 group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari game favoritmu..." class="bg-transparent border-none text-slate-900 dark:text-white focus:ring-0 w-full px-3 py-2.5 font-body tracking-wide placeholder-slate-400 dark:placeholder-gray-600 transition-colors">
                </div>
            </div>
            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <span class="text-slate-600 dark:text-gray-400 text-sm hidden sm:block">Filter:</span>
                <select name="type" onchange="this.form.submit()" class="bg-white dark:bg-[#0a0a15] border border-black/10 dark:border-white/10 text-slate-950 dark:text-white text-sm rounded-xl focus:ring-primary focus:border-primary block p-2.5 w-full sm:w-auto cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors shadow-sm">
                    <option value="">Semua Tipe</option>
                    <option value="Topup Game" {{ request('type') == 'Topup Game' ? 'selected' : '' }}>Topup Game</option>
                    <option value="Voucher Game" {{ request('type') == 'Voucher Game' ? 'selected' : '' }}>Voucher Game</option>
                </select>
            </div>
        </form>

        <!-- Game Grid -->
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-2.5 md:gap-2.5 lg:gap-2">
            @forelse($categories as $game)
            <a href="{{ route('topup.game', $game->slug) }}" class="glass-panel p-1.5 sm:p-2 md:p-2.5 lg:p-2 rounded-xl sm:rounded-2xl lg:rounded-xl transition-all cursor-pointer group border border-black/5 dark:border-white/5 relative overflow-hidden flex flex-col items-center card-hover bg-white dark:bg-transparent shadow-sm dark:shadow-none hover:shadow-xl dark:hover:shadow-none">
                <div class="relative w-full aspect-[4/5] sm:aspect-square shrink-0 rounded-lg sm:rounded-xl lg:rounded-xl overflow-hidden mb-1.5 sm:mb-2">
                    <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent z-10 opacity-60 dark:opacity-100 transition-opacity"></div>
                    <img src="{{ $game->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" alt="{{ $game->name }}" class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($game->name) }}&background=random&color=fff'">

                    <div class="absolute top-0.5 right-0.5 lg:top-0.5 lg:right-0.5 z-20 bg-black/40 dark:bg-black/60 backdrop-blur-md p-0.5 lg:p-0.5 rounded-md border border-white/10">
                        <span class="material-icons-round text-primary text-[9px] sm:text-[10px] lg:text-[8px]">flash_on</span>
                    </div>
                </div>
                <h3 class="text-[10px] sm:text-xs md:text-sm lg:text-[10px] font-bold text-slate-900 dark:text-white truncate w-full text-center group-hover:text-primary transition-colors leading-tight">{{ $game->name }}</h3>
                <p class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[8px] text-slate-500 dark:text-gray-500 mt-0.5 transition-colors text-center line-clamp-2 leading-snug">{{ $game->type }}</p>
            </a>
            @empty
            <div class="col-span-full py-20 text-center">
                <span class="material-icons-round text-6xl text-slate-300 dark:text-white/10 mb-4">search_off</span>
                <p class="text-slate-500 dark:text-gray-400">Game tidak ditemukan.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            {{ $categories->links('vendor.pagination.neonflux') }}
        </div>
    </div>
</main>

<!-- Decorative Blur Elements -->
<div class="fixed top-1/4 left-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse"></div>
<div class="fixed bottom-1/4 right-10 w-64 h-64 bg-secondary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
@endsection
