@extends('admin.layouts.app')

@section('title', 'Slider & Banner')
@section('page_title', 'Slider dan Banner')
@section('page_description', 'Kelola gambar promosi yang tampil di halaman depan aplikasi.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" placeholder="Cari Judul Promo..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <select class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none appearance-none text-slate-300">
                <option value="">Semua Tipe</option>
                <option value="slider">Slider Utama</option>
                <option value="banner">Banner Promo</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto md:justify-end">
             <a href="{{ route('admin.sliders.create') }}" class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">add_photo_alternate</span>
                Tambah Slider/Banner
            </a>
        </div>
    </div>

    <!-- Sliders Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($sliders as $slider)
        <div class="glass-panel rounded-2xl border border-white/5 overflow-hidden group hover:border-white/10 transition-all flex flex-col h-full bg-slate-900/40">
            <!-- Image Preview -->
            <div class="aspect-video relative overflow-hidden bg-slate-800">
                <img src="{{ $slider->image_path }}" alt="{{ $slider->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null; this.src='https://placehold.co/600x337/1e293b/ccc?text=Preview+Image';">
                <div class="absolute inset-0 bg-linear-to-t from-slate-950/80 to-transparent flex items-end p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white size-10 rounded-full flex items-center justify-center border border-white/20 transition-all">
                        <span class="material-symbols-outlined">zoom_in</span>
                    </button>
                </div>
                <div class="absolute top-3 left-3 flex gap-2">
                    <span class="px-2 py-1 bg-slate-950/60 backdrop-blur-md text-slate-200 text-[10px] font-bold rounded-lg border border-white/10">Slider</span>
                </div>
            </div>

            <!-- Details -->
            <div class="p-5 flex flex-col flex-1">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-slate-100 group-hover:text-primary transition-colors line-clamp-1">{{ $slider->title }}</h3>
                    @if($slider->status === 'Aktif')
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 text-[9px] font-bold rounded-full border border-green-500/20">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-500/10 text-red-400 text-[9px] font-bold rounded-full border border-red-500/20">Nonaktif</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-mono truncate mb-4">{{ $slider->link }}</p>

                <div class="mt-auto flex items-center justify-between pt-4 border-t border-white/5">
                    <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-sm text-accent-blue">ads_click</span>
                        {{ number_format($slider->clicks) }} Clicks
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="size-8 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </a>
                        <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slider ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="size-8 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-lg flex items-center justify-center text-red-400 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="lg:col-span-2 p-12 border-2 border-dashed border-white/5 rounded-2xl flex flex-col items-center justify-center text-slate-500">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-20">add_photo_alternate</span>
            <p class="text-sm font-medium">Belum ada slider atau banner yang ditambahkan.</p>
        </div>
        @endforelse
    </div>

    <!-- Empty State Hint -->
    <div class="p-8 border-2 border-dashed border-white/5 rounded-2xl flex flex-col items-center justify-center text-slate-500">
        <span class="material-symbols-outlined text-4xl mb-2">add_photo_alternate</span>
        <p class="text-sm font-medium">Seret dan lepas file di sini atau klik tombol Tambah untuk membuat slider baru</p>
    </div>
</div>
@endsection
