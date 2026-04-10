{{-- ============================================================
    POPULER SEKARANG — Trending games section
    ============================================================ --}}
<div class="w-full mb-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-base md:text-2xl font-display font-bold text-slate-950 dark:text-white flex items-center">
            <span class="material-icons-round mr-2 text-primary animate-pulse text-lg md:text-2xl">trending_up</span>
            Populer Sekarang!
        </h2>
        <a href="{{ route('catalog') }}" class="text-primary hover:text-secondary transition-colors text-[10px] md:text-sm font-bold flex items-center group">
            Lihat Semua
            <span class="material-icons-round text-[10px] md:text-sm ml-1 group-hover:translate-x-1 transition-transform">chevron_right</span>
        </a>
    </div>

    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-2.5 md:gap-2.5 lg:gap-2">
        @forelse($popular as $game)
    <a href="{{ route('topup.game', $game->slug) }}" class="flex flex-col items-center glass-panel p-1.5 sm:p-2 lg:p-2 rounded-xl sm:rounded-2xl lg:rounded-xl card-hover transition-all cursor-pointer group relative overflow-hidden shadow-sm hover:shadow-xl dark:shadow-none">
        <div class="relative w-full aspect-square shrink-0 rounded-lg sm:rounded-xl lg:rounded-xl overflow-hidden mb-1.5 sm:mb-2">
            <img src="{{ $game->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" alt="{{ $game->name }}" class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($game->name) }}&background=random&color=fff'">
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-black/80 to-transparent opacity-60 dark:opacity-100"></div>
        </div>
        <h3 class="text-[10px] sm:text-[11px] md:text-xs lg:text-[10px] font-bold text-slate-900 dark:text-white truncate w-full text-center group-hover:text-primary transition-colors leading-tight">{{ $game->name }}</h3>
        <p class="text-[8px] sm:text-[9px] lg:text-[8px] text-slate-500 dark:text-gray-500 mt-0.5 text-center transition-colors line-clamp-2 leading-snug">{{ $game->type }}</p>
    </a>
    @empty
    <div class="col-span-full py-10 text-center opacity-50 text-sm">Tidak ada game populer tersedia.</div>
    @endforelse
    </div>
</div>
