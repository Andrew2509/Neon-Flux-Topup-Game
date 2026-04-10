<div class="w-full mb-8 overflow-x-auto no-scrollbar">
    <div id="category-tabs-container" class="flex items-center space-x-1.5 md:space-x-3 min-w-max pb-2">
        @foreach($activeGroups as $key => $group)
        <button data-group="{{ $key }}" class="category-tab shrink-0 whitespace-nowrap px-3 py-1.5 md:px-6 md:py-3 rounded-xl md:rounded-2xl {{ $loop->first ? 'bg-primary text-slate-900 shadow-neon-cyan' : 'bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-600 dark:text-gray-400' }} hover:text-primary dark:hover:text-white hover:bg-black/10 dark:hover:bg-white/10 text-[10px] md:text-base font-bold flex items-center space-x-1.5 md:space-x-2 transition-all transform active:scale-95 group shadow-sm hover:shadow-md dark:shadow-none">
            <span class="material-icons-round text-[14px] md:text-xl group-hover:rotate-12 transition-transform">{{ $group['icon'] }}</span>
            <span>{{ $group['name'] }}</span>
        </button>
        @endforeach
    </div>
</div>
