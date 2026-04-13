<nav class="fixed top-0 inset-x-0 z-[200] px-3 py-2.5 glass-panel-mobile border-b shadow-sm dark:shadow-none flex justify-between items-center bg-white/80 dark:bg-background-dark/80">
    <div class="flex items-center gap-2">
        @if($logo = get_image_url('site_logo'))
            <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'Neon Flux') }}" class="h-12 w-auto">
        @endif
    </div>
    <div class="flex items-center gap-3">


        <a href="{{ route('kalkulator.index') }}" class="text-slate-600 dark:text-gray-400 ml-2">
            <span class="material-icons-round text-2xl">calculate</span>
        </a>

        @auth
            <div class="w-8 h-8 rounded-full border border-primary overflow-hidden">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" alt="User" class="w-full h-full object-cover">
            </div>
        @else
            <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary">
                <span class="material-icons-round text-lg">login</span>
                <span class="text-sm font-bold uppercase tracking-wider">Login</span>
            </a>
        @endauth
    </div>
</nav>
