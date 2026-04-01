@extends('tablet.layouts.neonflux')

@section('title', 'Katalog Game — Neon Flux')

@section('content')
<div class="space-y-6">
    {{-- Search Bar Sticky-like container --}}
    <form action="{{ route('catalog') }}" method="GET" class="sticky top-[52px] z-30 pt-3 pb-1.5 bg-background-dark/90 backdrop-blur-md -mx-3 px-3 border-b border-white/5 shadow-sm">
        <div class="relative group">
            <div class="absolute inset-0 bg-linear-to-r from-primary to-secondary rounded-xl opacity-10 blur-sm"></div>
            <div class="relative bg-white/5 border border-white/10 rounded-xl flex items-center p-0.5 shadow-sm">
                <span class="material-icons-round text-white/60 ml-2.5 text-lg">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari game..." class="bg-transparent border-none text-white focus:ring-0 w-full px-2 py-2 text-xs placeholder-white/60 font-body tracking-wide">
            </div>
        </div>
    </form>

    {{-- Filter Row 1: Main Categories --}}
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 -ms-3 px-3">
        <a href="{{ route('catalog') }}" class="px-3.5 py-1.5 rounded-lg {{ !request('popular') && !request('platform') ? 'bg-primary text-black shadow-neon-cyan' : 'bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-600 dark:text-white/80' }} font-bold text-[11px] shadow-sm whitespace-nowrap transition-all">Semua ({{ $counts['all'] }})</a>
        <a href="{{ route('catalog', array_merge(request()->query(), ['popular' => 1, 'platform' => null])) }}" class="px-3.5 py-1.5 rounded-lg {{ request('popular') ? 'bg-primary text-black shadow-neon-cyan' : 'bg-white/5 border border-white/10 text-white/80' }} font-bold text-[11px] shadow-sm whitespace-nowrap transition-all">Populer ({{ $counts['popular'] }})</a>
        <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'mobile', 'popular' => null])) }}" class="px-3.5 py-1.5 rounded-lg {{ request('platform') == 'mobile' ? 'bg-primary text-black shadow-neon-cyan' : 'bg-white/5 border border-white/10 text-white/80' }} font-bold text-[11px] shadow-sm whitespace-nowrap transition-all">Mobile ({{ $counts['mobile'] }})</a>
        <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'pc', 'popular' => null])) }}" class="px-3.5 py-1.5 rounded-lg {{ request('platform') == 'pc' ? 'bg-primary text-black shadow-neon-cyan' : 'bg-white/5 border border-white/10 text-white/80' }} font-bold text-[11px] shadow-sm whitespace-nowrap transition-all">PC ({{ $counts['pc'] }})</a>
        <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => 'console', 'popular' => null])) }}" class="px-3.5 py-1.5 rounded-lg {{ request('platform') == 'console' ? 'bg-primary text-black shadow-neon-cyan' : 'bg-white/5 border border-white/10 text-white/80' }} font-bold text-[11px] shadow-sm whitespace-nowrap transition-all">Console ({{ $counts['console'] }})</a>
    </div>

    {{-- Filter Row 2: Platforms
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 -ms-3 px-3">
        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider shrink-0 me-1">Platform:</span>
        @foreach(['Android', 'iOS', 'Steam', 'PlayStation'] as $plat)
            <a href="{{ route('catalog', array_merge(request()->query(), ['platform' => strtolower($plat), 'popular' => null])) }}"
               class="px-2.5 py-1 rounded-md {{ request('platform') == strtolower($plat) ? 'bg-primary/20 text-primary border border-primary/30' : 'bg-black/5 dark:bg-white/5 border border-black/10 text-slate-500' }} font-bold text-[10px] whitespace-nowrap">
               {{ $plat }}
            </a>
        @endforeach
    </div> --}}

    {{-- 5-Column Responsive Grid --}}
    <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-1.5">
        @forelse($categories as $item)
        <a href="{{ route('topup.game', $item->slug) }}" class="glass-panel-mobile p-1 rounded-lg flex flex-col gap-1 group border border-black/5 dark:border-white/10 shadow-sm active:scale-95 transition-all">
            <div class="relative w-full aspect-square rounded-md overflow-hidden bg-black/5 dark:bg-white/5">
                <img src="{{ $item->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=random&color=fff'">
                <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent opacity-60 dark:opacity-100"></div>
                <div class="absolute bottom-1 right-1">
                     <span class="material-icons-round text-primary text-[8px]">flash_on</span>
                </div>
            </div>
            <div class="space-y-0.5">
                <h3 class="text-[8px] font-bold text-white truncate leading-tight">{{ $item->name }}</h3>
                <p class="text-[7px] text-white/70 font-medium">Instan</p>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-10">
            <p class="text-xs text-slate-500 dark:text-white/50">Belum ada game tersedia.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination removed for Tablet to show all operators at once --}}
</div>
@endsection
