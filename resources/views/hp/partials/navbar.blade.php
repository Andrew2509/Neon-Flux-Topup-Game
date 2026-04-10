<nav
    class="fixed top-0 inset-x-0 z-[200] px-3 py-2.5 glass-panel-mobile border-b shadow-sm dark:shadow-none flex justify-between items-center bg-white/80 dark:bg-background-dark/80">
    <div class="flex items-center gap-2">
        @if($logo = get_image_url('site_logo'))
            <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'Neon Flux') }}" class="h-10 w-auto">
        @endif
        <span class="font-display font-bold text-lg tracking-tight">
            <span class="text-cyan-600 dark:text-primary">{{ explode(' ', get_setting('site_name', 'NEON'))[0] }}</span><span
                class="text-slate-950 dark:text-white transition-colors">{{ substr(get_setting('site_name', 'NEONFLUX'), strlen(explode(' ', get_setting('site_name', 'NEON'))[0])) }}</span>
        </span>
    </div>
    <div class="flex items-center gap-1 flex-shrink-0">
        <button type="button" id="nf-drawer-open" class="relative z-[2100] flex items-center justify-center size-10 rounded-xl glass-panel-mobile border border-black/10 dark:border-white/15 bg-white/60 dark:bg-[rgba(20,20,35,0.5)] backdrop-blur-md text-slate-900 dark:text-white touch-manipulation shadow-sm" aria-label="Buka menu">
            <span class="material-icons-round text-2xl">menu</span>
        </button>
    </div>
</nav>

{{-- Drawer Menu (Sama seperti home) --}}
@include('desktop.partials.mobile-menu-drawer')
