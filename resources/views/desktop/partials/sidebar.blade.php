{{-- ============================================================
    SIDEBAR — Left icon sidebar (desktop only)
    ============================================================ --}}
<aside class="hidden lg:flex lg:col-span-1 flex-col items-center space-y-6 pt-10">
    <div class="glass-panel p-2 rounded-2xl flex flex-col space-y-2">
        {{-- Home (Active) --}}
        <button class="w-12 h-12 rounded-xl bg-white/10 text-primary flex items-center justify-center transition-all shadow-neon-cyan border border-primary/30">
            <span class="material-icons-round">home</span>
        </button>

        {{-- Favorites --}}
        <button class="w-12 h-12 rounded-xl hover:bg-white/5 text-gray-400 hover:text-white flex items-center justify-center transition-all">
            <span class="material-icons-round">favorite_border</span>
        </button>

        {{-- History --}}
        <button class="w-12 h-12 rounded-xl hover:bg-white/5 text-gray-400 hover:text-white flex items-center justify-center transition-all">
            <span class="material-icons-round">history</span>
        </button>

        {{-- Settings --}}
        <button class="w-12 h-12 rounded-xl hover:bg-white/5 text-gray-400 hover:text-white flex items-center justify-center transition-all">
            <span class="material-icons-round">settings</span>
        </button>
    </div>
</aside>
