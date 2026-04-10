@extends('admin.layouts.app')

@section('title', 'Tambah Member')
@section('page_title', 'Tambah Member Baru')
@section('page_description', 'Pendaftaran member baru secara manual ke sistem Neon Flux.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.members') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Daftar Member</span>
    </a>

    <form action="{{ route('admin.members.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-8 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 blur-[100px] rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Personal -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">person</span>
                        Informasi Personal
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NAMA LENGKAP</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">badge</span>
                                <input type="text" name="name" required placeholder="Contoh: Andi Wijaya" value="{{ old('name') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('name') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">ALAMAT EMAIL</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">mail</span>
                                <input type="email" name="email" required placeholder="andi@example.com" value="{{ old('email') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('email') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NOMOR WHATSAPP</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">call</span>
                                <input type="text" name="phone" placeholder="0812xxxxxxxx" value="{{ old('phone') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('phone') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Akun & Finansial -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-accent-blue uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                        Akun & Saldo
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 ml-1">ROLE</label>
                                <select name="role_id" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 ml-1">STATUS</label>
                                <select name="status" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Diblokir</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">SALDO AWAL (RP)</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold group-focus-within:text-accent-blue transition-colors">Rp</span>
                                <input type="number" name="balance" required placeholder="0" value="{{ old('balance', 0) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                            </div>
                            @error('balance') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">PASSWORD</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">lock</span>
                                <input type="password" name="password" required placeholder="Min. 8 Karakter"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            <p class="text-[9px] text-slate-500 mt-1 ml-1">Member dapat mengubah password ini nanti di halaman profil.</p>
                            @error('password') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.members') }}" class="px-6 py-3 rounded-2xl text-sm font-bold text-slate-400 hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="bg-primary text-black px-8 py-3 rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                    Simpan Member Baru
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
