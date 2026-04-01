@extends('admin.layouts.app')

@section('title', 'Tambah Metode Pembayaran')
@section('page_title', 'Tambah Metode')
@section('page_description', 'Tambahkan opsi pembayaran baru untuk pengguna.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.payments') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Kelola Pembayaran</span>
    </a>

    <form action="{{ route('admin.payments.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-8 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 blur-[100px] rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                <!-- Detail Dasar -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">info</span>
                        Informasi Dasar
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NAMA METODE</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">payments</span>
                                <input type="text" name="name" required placeholder="Contoh: BCA Transfer" value="{{ old('name') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('name') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">KODE (UNIQUE)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">code</span>
                                <input type="text" name="code" required placeholder="Contoh: BCA" value="{{ old('code') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all uppercase">
                            </div>
                            @error('code') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">TIPE PEMBAYARAN</label>
                            <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet / VA</option>
                                <option value="qris" {{ old('type') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="retail" {{ old('type') == 'retail' ? 'selected' : '' }}>Retail Outlet (Alfamart/Indomaret)</option>
                            </select>
                            @error('type') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NAMA MITRA (PROVIDER)</label>
                            <select name="provider" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                <option value="Duitku" {{ old('provider') == 'Duitku' ? 'selected' : '' }}>Duitku</option>
                                <option value="iPaymu" {{ old('provider') == 'iPaymu' ? 'selected' : '' }}>iPaymu</option>
                            </select>
                            @error('provider') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Keuangan & Status -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-accent-blue uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                        Keuangan & Status
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">BIAYA ADMIN (FEE)</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold group-focus-within:text-accent-blue transition-colors">Rp</span>
                                <input type="number" name="fee" required placeholder="0" value="{{ old('fee', 0) }}" min="0" step="0.01"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-accent-blue focus:border-accent-blue outline-none transition-all font-mono">
                            </div>
                            @error('fee') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NOMOR REKENING / INTRUKSI (OPSIONAL)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-accent-blue transition-colors">pin</span>
                                <input type="text" name="account_number" placeholder="Contoh: 1234567890 a/n Prince" value="{{ old('account_number') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-accent-blue focus:border-accent-blue outline-none transition-all font-mono">
                            </div>
                            @error('account_number') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">STATUS</label>
                            <select name="status" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 relative z-10">
                <a href="{{ route('admin.payments') }}" class="px-6 py-3 rounded-2xl text-sm font-bold text-slate-400 hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="bg-primary text-black px-8 py-3 rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                    Simpan Pembayaran
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
