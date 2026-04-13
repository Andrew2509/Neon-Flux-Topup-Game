{{-- ============================================================
    GAME GRID — Large grid of available games
    (>15 item ≈ >5 baris di grid 3 kolom: pratinjau ~5 baris + "Lihat semua")
    ============================================================ --}}
@php
    $gameGridCollapsible = isset($categories) && $categories->count() > 15;
@endphp
<div id="game-grid-viewport"
     class="@if($gameGridCollapsible) relative overflow-hidden transition-[max-height] duration-500 ease-out @endif"
     @if($gameGridCollapsible) style="max-height: min(92vh, 76rem);" @endif>
    <div id="game-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-2.5 md:gap-2.5 lg:gap-2">
@foreach($categories as $game)
    @php
        $gameGroup = 'topup'; // Default to topup if unknown
        if (in_array($game->type, ['Topup Game', 'Topup Game (Global)'])) $gameGroup = 'topup';
        elseif (in_array($game->type, ['Voucher Game', 'Voucher Data'])) $gameGroup = 'voucher';
        elseif (in_array($game->type, ['Pulsa', 'Paket Data', 'Telpon & SMS', 'Pulsa Transfer'])) $gameGroup = 'pulsa';
        elseif (in_array($game->type, ['Hiburan', 'TV', 'Lainnya'])) $gameGroup = 'streaming';

        if (stripos($game->name, 'Joki') !== false) $gameGroup = 'joki';
    @endphp
    <a href="{{ route('topup.game', $game->slug) }}"
       data-group="{{ $gameGroup }}"
       class="game-card flex flex-col items-center glass-panel p-1.5 sm:p-2 lg:p-2 rounded-xl sm:rounded-2xl lg:rounded-xl card-hover transition-all cursor-pointer group relative overflow-hidden shadow-sm hover:shadow-xl dark:shadow-none">
        @if($game->has_active_flash_sale)
        <!-- Flash Sale Badge -->
        <div class="absolute top-2 left-2 z-10 animate-bounce">
            <span class="bg-linear-to-r from-secondary to-pink-500 text-white text-[8px] md:text-[10px] font-black px-2 py-0.5 rounded-lg shadow-lg flex items-center gap-1">
                <span class="material-icons-round text-[10px] md:text-sm">bolt</span>
                FLASH SALE
            </span>
        </div>
        @endif
        <div class="relative w-full aspect-square shrink-0 rounded-lg sm:rounded-xl lg:rounded-xl overflow-hidden mb-1.5 sm:mb-2">
            <img src="{{ $game->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" alt="{{ $game->name }}" class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($game->name) }}&background=random&color=fff'">
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-black/80 to-transparent opacity-60 dark:opacity-100"></div>
        </div>
        <h3 class="text-[10px] sm:text-[11px] md:text-xs lg:text-[10px] font-bold text-slate-900 dark:text-white truncate w-full text-center group-hover:text-primary transition-colors leading-tight">{{ $game->name }}</h3>
        <p class="text-[8px] sm:text-[9px] lg:text-[8px] text-slate-500 dark:text-gray-500 mt-0.5 text-center line-clamp-2 leading-snug">{{ $game->type }}</p>
    </a>
@endforeach
    </div>
    @if($gameGridCollapsible)
    <div id="game-grid-fade" class="pointer-events-none absolute inset-x-0 bottom-0 z-[1] h-28 bg-linear-to-t from-[var(--bg-color)] via-[var(--bg-color)]/85 to-transparent" aria-hidden="true"></div>
    @endif
</div>
@if($gameGridCollapsible)
<div class="flex justify-center mt-5 mb-1" id="game-grid-expand-wrap">
    <button type="button" id="game-grid-expand-btn"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl font-bold text-sm bg-primary text-slate-900 shadow-neon-cyan hover:brightness-110 active:scale-[0.98] transition-all border border-primary/30">
        Lihat semua
        <span class="material-icons-round text-base" aria-hidden="true">expand_more</span>
    </button>
</div>
@endif
