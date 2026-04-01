@extends('hp.layouts.neonflux')

@section('title', 'Profil - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-6 pb-20">
    {{-- Header Card --}}
    <div class="glass-panel-mobile p-6 rounded-3xl border border-black/5 dark:border-white/10 flex flex-col items-center text-center gap-3">
        <div class="relative">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff' }}" 
                 class="size-20 rounded-full border-2 border-primary object-cover shadow-lg" 
                 alt="{{ $user->name }}">
            <div class="absolute bottom-0 right-0 size-6 bg-primary rounded-full border-2 border-white dark:border-background-dark flex items-center justify-center">
                <span class="material-icons-round text-white text-xs">verified</span>
            </div>
        </div>
        <div>
            <h1 class="text-xl font-bold dark:text-white">{{ $user->name }}</h1>
            <p class="text-xs text-slate-500 dark:text-white/60 mt-0.5">{{ $user->email }}</p>
        </div>
        <div class="w-full grid grid-cols-2 gap-3 mt-2">
            <div class="bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-black/5 dark:border-white/5">
                <p class="text-[8px] uppercase tracking-widest text-slate-500 font-bold mb-1">Saldo</p>
                <p class="text-xs font-bold dark:text-white uppercase tracking-tighter">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-black/5 dark:border-white/5">
                <p class="text-[8px] uppercase tracking-widest text-slate-500 font-bold mb-1">Role</p>
                <p class="text-xs font-bold dark:text-white uppercase tracking-widest">{{ $user->role }}</p>
            </div>
        </div>
    </div>

    {{-- Forms Section --}}
    <div class="space-y-4">
        {{-- Profile Form --}}
        <div class="glass-panel-mobile p-6 rounded-3xl border border-black/5 dark:border-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-icons-round text-primary text-xl">person</span>
                <h2 class="text-sm font-bold dark:text-white uppercase tracking-wider">Info Personal</h2>
            </div>
            
            <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" 
                           class="w-full bg-slate-50 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs dark:text-white outline-none focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 ml-1">No. WhatsApp</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" 
                           class="w-full bg-slate-50 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs dark:text-white outline-none focus:border-primary transition-all">
                </div>
                <button type="submit" class="w-full bg-primary text-slate-950 font-bold py-3 rounded-2xl text-xs uppercase tracking-widest shadow-lg shadow-primary/20 active:scale-95 transition-all">
                    Update Profil
                </button>
            </form>
        </div>

        {{-- Password Form --}}
        <div class="glass-panel-mobile p-6 rounded-3xl border border-black/5 dark:border-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-icons-round text-red-500 text-xl">lock</span>
                <h2 class="text-sm font-bold dark:text-white uppercase tracking-wider">Ubah Sandi</h2>
            </div>
            
            <form action="{{ route('user.password.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 ml-1">Sandi Lama</label>
                    <input type="password" name="current_password" 
                           class="w-full bg-slate-50 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs dark:text-white outline-none focus:border-red-500 transition-all">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 ml-1">Sandi Baru</label>
                    <input type="password" name="password" 
                           class="w-full bg-slate-50 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs dark:text-white outline-none focus:border-red-500 transition-all">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 ml-1">Konfirmasi Sandi Baru</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full bg-slate-50 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs dark:text-white outline-none focus:border-red-500 transition-all">
                </div>
                <button type="submit" class="w-full bg-red-500 text-white font-bold py-3 rounded-2xl text-xs uppercase tracking-widest shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                    Ganti Password
                </button>
            </form>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-4 text-xs font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest hover:text-red-500 transition-colors">
                Keluar Dari Akun
            </button>
        </form>
    </div>
</div>
@endsection
