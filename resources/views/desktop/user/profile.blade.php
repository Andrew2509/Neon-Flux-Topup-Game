@extends('desktop.layouts.user')

@section('title', 'Profil Saya — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun dan pengaturan keamanan Anda.')

@section('content')
<div class="space-y-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row items-center gap-10 bg-white p-10 rounded-xl border border-corp-border shadow-sm relative overflow-hidden">
        <div class="relative group">
            <div class="relative size-32 rounded-full border-4 border-slate-100 p-1 bg-white shadow-md overflow-hidden">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff' }}" 
                     class="size-full rounded-full object-cover group-hover:scale-110 transition-transform duration-700" 
                     alt="{{ $user->name }}">
            </div>
            <div class="absolute -bottom-1 -right-1 bg-corp-accent text-white size-8 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        <div class="relative text-center md:text-left flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-corp-navy truncate">{{ $user->name }}</h1>
            <p class="text-corp-muted text-sm mt-1 uppercase tracking-wider font-semibold">{{ $user->email }}</p>
            <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-3">
                <div class="px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100 text-[10px] font-bold text-corp-accent uppercase tracking-widest leading-none">
                    Status: {{ $user->role ? $user->role->name : 'Member' }}
                </div>
                <div class="px-3 py-1.5 rounded-lg bg-slate-50 border border-corp-border text-[10px] font-bold text-corp-muted uppercase tracking-widest leading-none">
                    Bergabung {{ $user->created_at->format('M Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Edit Profile Form --}}
        <div class="content-card p-8 md:p-10 rounded-xl border border-corp-border space-y-8 bg-white shadow-sm">
            <div class="flex items-center gap-4 border-b border-corp-border pb-6">
                <div class="size-10 rounded-lg bg-slate-100 text-corp-navy flex items-center justify-center">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-corp-navy uppercase tracking-widest">Informasi Identitas</h2>
            </div>
            
            <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-corp-muted uppercase tracking-widest ml-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full bg-slate-50 border border-corp-border rounded-xl px-5 py-3.5 text-sm font-bold text-corp-navy focus:border-corp-accent focus:bg-white outline-none transition-all placeholder:text-slate-400"
                           placeholder="Nama Lengkap">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-corp-muted uppercase tracking-widest ml-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                           class="w-full bg-slate-50 border border-corp-border rounded-xl px-5 py-3.5 text-sm font-bold text-corp-navy focus:border-corp-accent focus:bg-white outline-none transition-all placeholder:text-slate-400"
                           placeholder="628xxxxxxxx">
                </div>
                <button type="submit" class="w-full bg-corp-accent text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-md text-[10px] uppercase tracking-widest">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Change Password Form --}}
        <div class="content-card p-8 md:p-10 rounded-xl border border-corp-border space-y-8 bg-white shadow-sm">
            <div class="flex items-center gap-4 border-b border-corp-border pb-6">
                <div class="size-10 rounded-lg bg-slate-100 text-corp-navy flex items-center justify-center">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-corp-navy uppercase tracking-widest">Keamanan Akun</h2>
            </div>
            
            <form action="{{ route('user.password.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-corp-muted uppercase tracking-widest ml-1">Password Saat Ini</label>
                    <input type="password" name="current_password" 
                           class="w-full bg-slate-50 border border-corp-border rounded-xl px-5 py-3.5 text-sm font-bold text-corp-navy focus:border-corp-accent focus:bg-white outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-corp-muted uppercase tracking-widest ml-1">Password Baru</label>
                    <input type="password" name="password" 
                           class="w-full bg-slate-50 border border-corp-border rounded-xl px-5 py-3.5 text-sm font-bold text-corp-navy focus:border-corp-accent focus:bg-white outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-corp-muted uppercase tracking-widest ml-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full bg-slate-50 border border-corp-border rounded-xl px-5 py-3.5 text-sm font-bold text-corp-navy focus:border-corp-accent focus:bg-white outline-none transition-all">
                </div>
                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-4 rounded-xl hover:bg-corp-navy transition-all shadow-md text-[10px] uppercase tracking-widest">
                    Ganti Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

