@extends('admin.layouts.app')

@section('title', 'Edit Kategori — '.$category->name)
@section('page_title', 'Edit Kategori')
@section('page_description', 'Nomor kontak dan label formulir untuk halaman top-up game ini.')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.categories', array_filter(['type' => $category->type])) }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke daftar kategori</span>
    </a>

    <div class="glass-panel p-6 rounded-2xl border border-white/5 mb-6 flex items-center gap-4">
        <div class="size-14 rounded-xl overflow-hidden bg-slate-800 border border-white/10 flex items-center justify-center p-2 shrink-0">
            <img src="{{ $category->icon }}" alt="" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=1e293b&color=fff';">
        </div>
        <div>
            <h2 class="font-bold text-lg text-slate-100">{{ $category->name }}</h2>
            <p class="text-xs text-slate-500 font-mono">/{{ $category->slug }}</p>
            @if($category->type)
                <p class="text-[10px] text-slate-400 mt-1">{{ $category->type }}</p>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-6 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 blur-[100px] rounded-full pointer-events-none"></div>

            <h3 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-3 relative z-10">
                <span class="size-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">call</span>
                </span>
                Kontak &amp; formulir
            </h3>

            <div class="space-y-1 relative z-10">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nomor dukungan (opsional)</label>
                <p class="text-[10px] text-slate-500 mb-2 ml-1">Ditampilkan di halaman top-up agar pemain bisa menghubungi tim untuk bantu verifikasi atau kendala.</p>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">phone_in_talk</span>
                    <input type="text" name="support_phone" value="{{ old('support_phone', $category->support_phone) }}" placeholder="Contoh: 0812-3456-7890 atau +62..."
                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                </div>
                @error('support_phone') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Label kolom ID</label>
                    <input type="text" name="input_label" value="{{ old('input_label', $category->input_label) }}" placeholder="User ID"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('input_label') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Placeholder ID</label>
                    <input type="text" name="input_placeholder" value="{{ old('input_placeholder', $category->input_placeholder) }}" placeholder="Contoh: 12345678"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('input_placeholder') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Label zona/server</label>
                    <input type="text" name="zone_label" value="{{ old('zone_label', $category->zone_label) }}" placeholder="Server"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('zone_label') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Placeholder zona</label>
                    <input type="text" name="zone_placeholder" value="{{ old('zone_placeholder', $category->zone_placeholder) }}" placeholder="Contoh: 1234"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                    @error('zone_placeholder') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2 relative z-10">
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(37,99,235,0.25)]">
                    Simpan perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
