@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-white/5 border border-white/10 cursor-default rounded-xl">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-500 bg-white/5 border border-white/10 cursor-default rounded-xl">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] text-slate-500 uppercase font-bold">
                    {!! __('Showing') !!}
                    <span class="font-bold text-slate-300">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-bold text-slate-300">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-bold text-slate-300">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-slate-500 bg-white/5 border border-white/10 cursor-default rounded-l-xl" aria-hidden="true">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-slate-300 bg-white/5 border border-white/10 rounded-l-xl hover:bg-white/10 transition-all" aria-label="{{ __('pagination.previous') }}">
                            <span class="material-symbols-outlined text-lg">chevron_left</span>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-500 bg-white/5 border border-white/10 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-black bg-primary border border-primary shadow-lg shadow-primary/20">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-400 bg-white/5 border border-white/10 hover:bg-white/10 transition-all" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-slate-300 bg-white/5 border border-white/10 rounded-r-xl hover:bg-white/10 transition-all" aria-label="{{ __('pagination.next') }}">
                            <span class="material-symbols-outlined text-lg">chevron_right</span>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-slate-500 bg-white/5 border border-white/10 cursor-default rounded-r-xl" aria-hidden="true">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
