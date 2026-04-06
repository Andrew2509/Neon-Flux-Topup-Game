{{-- ============================================================
    GAME GRID — Large grid of available games
    ============================================================ --}}
<div id="game-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-4 gap-2 sm:gap-2.5 md:gap-3">
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
       class="game-card flex flex-col items-center glass-panel p-1.5 sm:p-2 rounded-xl sm:rounded-2xl card-hover transition-all cursor-pointer group relative overflow-hidden shadow-sm hover:shadow-xl dark:shadow-none">
        <div class="relative w-full aspect-square rounded-lg sm:rounded-xl overflow-hidden mb-1.5 sm:mb-2 max-h-[5.25rem] sm:max-h-none">
            <img src="{{ $game->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" alt="{{ $game->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($game->name) }}&background=random&color=fff'">
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-black/80 to-transparent opacity-60 dark:opacity-100"></div>
        </div>
        <h3 class="text-[10px] sm:text-[11px] md:text-xs font-bold text-slate-900 dark:text-white truncate w-full text-center group-hover:text-primary transition-colors leading-tight">{{ $game->name }}</h3>
        <p class="text-[8px] sm:text-[9px] text-slate-500 dark:text-gray-500 mt-0.5 text-center line-clamp-2">{{ $game->type }}</p>
    </a>
@endforeach
</div>
