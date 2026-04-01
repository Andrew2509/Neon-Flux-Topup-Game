<nav
    class="fixed top-0 inset-x-0 z-50 px-3 py-2.5 glass-panel-mobile border-b shadow-sm dark:shadow-none flex justify-between items-center bg-white/80 dark:bg-background-dark/80">
    <div class="flex items-center gap-2">
        @if($logo = get_image_url('site_logo'))
            <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'Neon Flux') }}" class="h-10 w-auto">
        @endif
        <span class="font-display font-bold text-lg tracking-tight">
            <span class="text-cyan-600 dark:text-primary">{{ explode(' ', get_setting('site_name', 'NEON'))[0] }}</span><span
                class="text-slate-950 dark:text-white transition-colors">{{ substr(get_setting('site_name', 'NEONFLUX'), strlen(explode(' ', get_setting('site_name', 'NEON'))[0])) }}</span>
        </span>
    </div>
    <div class="flex items-center gap-3">


        <a href="{{ route('kalkulator.index') }}" class="text-slate-600 dark:text-gray-400">
            <span class="material-icons-round text-2xl">calculate</span>
        </a>
        
        @auth
            <div class="w-8 h-8 rounded-full border border-primary overflow-hidden">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" alt="User" class="w-full h-full object-cover">
            </div>
        @else
            <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary">
                <span class="material-icons-round text-lg">login</span>
                <span class="text-xs font-bold uppercase tracking-wider">Login</span>
            </a>
        @endauth
    </div>
</nav>
