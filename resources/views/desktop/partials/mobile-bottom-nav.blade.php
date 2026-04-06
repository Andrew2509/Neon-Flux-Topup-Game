{{-- Navigasi bawah — hanya layar kecil; menyamai pola HP tetapi selaras tema gelap desktop --}}
<nav class="fixed bottom-0 inset-x-0 z-40 md:hidden border-t border-white/10 bg-[rgba(15,18,28,0.65)] backdrop-blur-2xl shadow-[0_-8px_32px_rgba(0,0,0,0.35)] supports-[backdrop-filter]:bg-[rgba(15,18,28,0.5)] pb-[max(0.35rem,env(safe-area-inset-bottom))]" aria-label="Menu utama">
    <div class="flex justify-around items-stretch h-[3.5rem] max-w-lg mx-auto">
        <a href="{{ url('/') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 min-w-0 touch-manipulation {{ request()->is('/') ? 'text-primary' : 'text-white/55' }}">
            <span class="material-icons-round text-[22px]">home</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
        </a>
        <a href="{{ route('catalog') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 min-w-0 touch-manipulation {{ request()->is('catalog') ? 'text-primary' : 'text-white/55' }}">
            <span class="material-icons-round text-[22px]">grid_view</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Games</span>
        </a>
        <a href="{{ route('track.order') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 min-w-0 touch-manipulation {{ request()->routeIs('track.order') ? 'text-primary' : 'text-white/55' }}">
            <span class="material-icons-round text-[22px]">receipt_long</span>
            <span class="text-[9px] font-bold uppercase tracking-wider">Cek</span>
        </a>
        @auth
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 min-w-0 touch-manipulation {{ request()->routeIs('user.*') ? 'text-primary' : 'text-white/55' }}">
                <span class="material-icons-round text-[22px]">person</span>
                <span class="text-[9px] font-bold uppercase tracking-wider">Akun</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 min-w-0 touch-manipulation {{ request()->routeIs('login') ? 'text-primary' : 'text-white/55' }}">
                <span class="material-icons-round text-[22px]">login</span>
                <span class="text-[9px] font-bold uppercase tracking-wider">Masuk</span>
            </a>
        @endauth
    </div>
</nav>
