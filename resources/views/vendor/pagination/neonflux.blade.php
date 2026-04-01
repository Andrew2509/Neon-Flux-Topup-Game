@if ($paginator->hasPages())
    <nav class="flex items-center space-x-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 text-slate-400 dark:text-gray-600 cursor-not-allowed">
                <span class="material-icons-round">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-10 h-10 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/5 text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors">
                <span class="material-icons-round">chevron_left</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="text-slate-400 dark:text-gray-600 px-2">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 rounded-lg flex items-center justify-center bg-primary/20 border border-primary text-primary font-bold shadow-neon-cyan">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/5 text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-10 h-10 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 hover:bg-black/5 dark:hover:bg-white/5 text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors">
                <span class="material-icons-round">chevron_right</span>
            </a>
        @else
            <span class="w-10 h-10 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 text-slate-400 dark:text-gray-600 cursor-not-allowed">
                <span class="material-icons-round">chevron_right</span>
            </span>
        @endif
    </nav>
@endif
