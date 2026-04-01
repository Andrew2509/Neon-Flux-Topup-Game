@if ($paginator->hasPages())
    <div class="flex items-center justify-center gap-2 pt-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 rounded-lg bg-black/5 dark:bg-white/5 flex items-center justify-center text-slate-400 opacity-50 cursor-not-allowed">
                <span class="material-icons-round text-lg">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-black/5 dark:bg-white/5 flex items-center justify-center text-slate-600 dark:text-gray-300">
                <span class="material-icons-round text-lg">chevron_left</span>
            </a>
        @endif

        {{-- Current Page Status --}}
        <div class="px-3 py-1.5 rounded-lg bg-primary/20 border border-primary/50 text-[10px] font-bold text-primary">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-primary text-black flex items-center justify-center shadow-sm">
                <span class="material-icons-round text-lg">chevron_right</span>
            </a>
        @else
            <span class="w-8 h-8 rounded-lg bg-primary text-black flex items-center justify-center shadow-sm opacity-50 cursor-not-allowed">
                <span class="material-icons-round text-lg">chevron_right</span>
            </span>
        @endif
    </div>
@endif
