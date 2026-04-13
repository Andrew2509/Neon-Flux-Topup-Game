<nav
    class="fixed top-0 inset-x-0 z-[200] px-3 py-2.5 glass-panel-mobile border-b shadow-sm dark:shadow-none flex justify-between items-center bg-white/80 dark:bg-background-dark/80">
    <div class="flex items-center gap-2">
        @if($logo = get_image_url('site_logo'))
            <img src="{{ $logo }}" alt="{{ get_setting('site_name') }}" class="h-12 w-auto scale-[1.35] origin-left">
        @endif
    </div>
    <div class="flex items-center gap-1 flex-shrink-0">
        {{-- Mobile Search Trigger --}}
        <button type="button" class="nf-search-trigger relative z-[2100] flex items-center justify-center size-10 rounded-xl glass-panel-mobile border border-black/10 dark:border-white/15 bg-white/60 dark:bg-[rgba(20,20,35,0.5)] backdrop-blur-md text-slate-900 dark:text-white touch-manipulation shadow-sm" aria-label="Cari game">
            <span class="material-icons-round text-2xl">search</span>
        </button>

        <button type="button" id="nf-drawer-open" class="relative z-[2100] flex items-center justify-center size-10 rounded-xl glass-panel-mobile border border-black/10 dark:border-white/15 bg-white/60 dark:bg-[rgba(20,20,35,0.5)] backdrop-blur-md text-slate-900 dark:text-white touch-manipulation shadow-sm" aria-label="Buka menu">
            <span class="material-icons-round text-2xl">menu</span>
        </button>
    </div>
</nav>

{{-- Drawer Menu (Sama seperti home) --}}
@include('desktop.partials.mobile-menu-drawer')
