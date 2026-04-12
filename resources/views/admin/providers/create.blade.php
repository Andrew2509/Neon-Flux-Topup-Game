@extends('admin.layouts.app')

@section('title', 'Tambah Provider')
@section('page_title', 'Tambah Provider Baru')
@section('page_description', 'Integrasikan provider baru untuk layanan Anda.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.providers') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Manajemen Provider</span>
    </a>



    <form action="{{ route('admin.providers.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-8 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 blur-[100px] rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                <!-- Detail Dasar -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">integration_instructions</span>
                        API Kredensial
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">NAMA PROVIDER</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">dns</span>
                                <input type="text" name="name" required placeholder="Contoh: Digiflazz" value="{{ old('name') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('name') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">PROVIDER / MERCHANT ID (OPSIONAL)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">badge</span>
                                <input type="text" name="provider_id" placeholder="Contoh: dg-99281" value="{{ old('provider_id') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                            </div>
                            @error('provider_id') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">API KEY / SECRET</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">key</span>
                                <input type="text" name="api_key" required placeholder="••••••••••••••••" value="{{ old('api_key') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                            </div>
                            @error('api_key') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Keuangan & Status -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-accent-blue uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">settings</span>
                        Pengaturan
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">SALDO AWAL (OPTIONAL)</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold group-focus-within:text-accent-blue transition-colors">Rp</span>
                                <input type="number" name="balance" required placeholder="0" value="{{ old('balance', 0) }}" min="0" step="0.01"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-accent-blue focus:border-accent-blue outline-none transition-all font-mono">
                            </div>
                            @error('balance') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">IKON (MATERIAL SYMBOL)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-accent-blue transition-colors">style</span>
                                <input type="text" name="icon" placeholder="Contoh: api, hub, games" value="{{ old('icon', 'hub') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-accent-blue focus:border-accent-blue outline-none transition-all font-mono">
                            </div>
                            <p class="text-[9px] text-slate-500 mt-1 ml-1">Nama ID ikon dari Google Material Symbols.</p>
                            @error('icon') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">MODE ENVIRONMENT</label>
                            <select name="mode" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-accent-blue focus:border-accent-blue outline-none transition-all appearance-none cursor-pointer">
                                <option value="" {{ old('mode') == '' ? 'selected' : '' }}>Tidak Berlaku</option>
                                <option value="sandbox" {{ old('mode') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                <option value="production" {{ old('mode') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                            <p class="text-[9px] text-slate-500 mt-1 ml-1">Untuk Payment Gateway seperti iPaymu.</p>
                            @error('mode') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">STATUS KONEKSI</label>
                            <select name="status" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif / Terhubung</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="Error" {{ old('status') == 'Error' ? 'selected' : '' }}>Error Connection</option>
                            </select>
                            @error('status') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 relative z-10">
                <a href="{{ route('admin.providers') }}" class="px-6 py-3 rounded-2xl text-sm font-bold text-slate-400 hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="bg-primary text-black px-8 py-3 rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                    Simpan Provider
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
