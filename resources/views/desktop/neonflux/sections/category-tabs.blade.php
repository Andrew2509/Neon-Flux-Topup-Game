<div class="w-full mb-8 overflow-x-auto no-scrollbar">
    <div id="category-tabs-container" class="flex items-center space-x-3 min-w-max pb-2">
        @foreach($activeGroups as $key => $group)
        <button data-group="{{ $key }}" class="category-tab px-6 py-3 rounded-2xl {{ $loop->first ? 'bg-primary text-slate-900 shadow-neon-cyan' : 'bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-600 dark:text-gray-400' }} hover:text-primary dark:hover:text-white hover:bg-black/10 dark:hover:bg-white/10 font-bold flex items-center space-x-2 transition-all transform active:scale-95 group shadow-sm hover:shadow-md dark:shadow-none">
            <span class="material-icons-round text-xl group-hover:rotate-12 transition-transform">{{ $group['icon'] }}</span>
            <span>{{ $group['name'] }}</span>
        </button>
        @endforeach
    </div>
</div>
