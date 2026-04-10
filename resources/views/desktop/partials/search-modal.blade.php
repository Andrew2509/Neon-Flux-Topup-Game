{{-- ============================================================
    SMART SEARCH MODAL — Overlay pencarian cepat (Cmd+K)
    ============================================================ --}}
<div id="nf-search-modal" 
     class="fixed inset-0 z-[3000] invisible opacity-0 transition-all duration-300 flex items-start justify-center pt-20 px-4 sm:pt-32"
     role="dialog" aria-modal="true" aria-labelledby="nf-search-title">
    
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#050510]/80 backdrop-blur-md" id="nf-search-close-overlay"></div>

    {{-- Modal Content --}}
    <div class="relative w-full max-w-2xl transform scale-95 opacity-0 transition-all duration-300 glass-panel border border-white/10 dark:bg-[#0a0a15]/90 rounded-3xl shadow-2xl overflow-hidden" 
         id="nf-search-content">
        
        {{-- Search Header --}}
        <div class="px-6 py-4 border-b border-white/10 flex items-center gap-4">
            <span class="material-icons-round text-primary text-2xl">search</span>
            <input type="text" id="nf-search-input" 
                   class="flex-1 bg-transparent border-none text-white text-lg placeholder:text-gray-500 focus:ring-0 outline-none" 
                   placeholder="Cari game favoritmu (Mobile Legends, FF, dll)..." 
                   autocomplete="off" />
            <kbd class="hidden sm:flex items-center gap-1 border border-white/15 bg-white/5 px-2 py-0.5 rounded text-[10px] text-gray-400 font-mono">
                ESC
            </kbd>
        </div>

        {{-- Search Results --}}
        <div class="max-h-[50vh] overflow-y-auto custom-scrollbar" id="nf-search-results-wrap">
            <div id="nf-search-initial" class="p-10 text-center space-y-3">
                <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mx-auto border border-primary/20">
                    <span class="material-icons-round text-4xl">rocket_launch</span>
                </div>
                <p class="text-white font-bold">Cari Game Favoritmu</p>
                <p class="text-sm text-gray-400">Ketik minimal 2 karakter untuk mulai mencari.</p>
            </div>
            
            <div id="nf-search-loading" class="hidden p-10 text-center">
                <div class="animate-spin size-8 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-sm text-gray-400 font-medium">Sedang mencari...</p>
            </div>

            <div id="nf-search-empty" class="hidden p-10 text-center space-y-2">
                <span class="material-icons-round text-gray-600 text-5xl">sentiment_dissatisfied</span>
                <p class="text-white font-bold">Yah, tidak ditemukan...</p>
                <p class="text-sm text-gray-400">Coba kata kunci lain atau pastikan ejaan sudah benar.</p>
            </div>

            <div id="nf-search-results-list" class="hidden divide-y divide-white/5 py-2">
                {{-- Results will be injected here --}}
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-white/10 bg-white/5 flex items-center justify-between text-[10px] text-gray-500 uppercase tracking-widest font-bold">
            <div class="flex gap-4">
                <span class="flex items-center gap-1"><span class="border border-white/15 bg-white/5 px-1 rounded">↑↓</span> Navigasi</span>
                <span class="flex items-center gap-1"><span class="border border-white/15 bg-white/5 px-1 rounded">ENTER</span> Pilih</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-primary tracking-normal font-display">Neon Flux Search</span>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(var(--primary-rgb), 0.5);
}

#nf-search-modal.active {
    visibility: visible;
    opacity: 1;
}
#nf-search-modal.active #nf-search-content {
    opacity: 1;
    transform: scale(100%);
}
</style>
