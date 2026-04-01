<nav class="fixed bottom-0 inset-x-0 z-50 glass-panel-mobile border-t pb-safe bg-white/90 dark:bg-background-dark/95 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
    <div class="flex justify-around items-center h-14">
        <a href="/" class="flex flex-col items-center gap-0.5 {{ request()->is('/') ? 'text-primary' : 'text-slate-400 dark:text-white/70' }}">
            <span class="material-icons-round text-xl">home</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
        </a>
        <a href="/catalog" class="flex flex-col items-center gap-0.5 {{ request()->is('catalog') ? 'text-primary' : 'text-slate-400 dark:text-white/70' }}">
            <span class="material-icons-round text-xl">grid_view</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Catalog</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 text-slate-400 dark:text-white/70">
            <div class="relative">
                <span class="material-icons-round text-xl">local_offer</span>
                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-secondary"></span>
                </span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider">Promo</span>
        </a>
        <a href="{{ route('user.profile') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('user.profile') ? 'text-primary' : 'text-slate-400 dark:text-white/70' }}">
            <span class="material-icons-round text-xl">person</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Profile</span>
        </a>
    </div>
</nav>
