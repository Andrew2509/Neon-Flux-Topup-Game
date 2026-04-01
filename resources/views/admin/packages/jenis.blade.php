@extends('admin.layouts.app')

@section('title', 'Jenis Produk - ' . $operator->name)
@section('page_title', $operator->name)
@section('page_description', 'Pilih jenis produk untuk melihat daftar layanan.')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-widest text-slate-500">
        <a href="{{ route('admin.packages.index') }}" class="hover:text-primary transition-colors">Paket Layanan</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-slate-300">{{ $operator->name }}</span>
    </div>

    <!-- Header Info -->
    <div class="glass-panel p-6 rounded-2xl border border-white/5 flex flex-col md:flex-row items-center gap-6">
        <div class="size-24 rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <img src="{{ $operator->icon }}" class="w-full h-full object-cover">
        </div>
        <div class="text-center md:text-left">
            <h2 class="text-2xl font-bold text-slate-100">{{ $operator->name }}</h2>
            <div class="flex items-center justify-center md:justify-start gap-4 mt-2">
                <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold rounded-full border border-primary/20">
                    {{ $operator->type }}
                </span>
                <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $jenis->count() }} Jenis Produk Tersedia</span>
            </div>
        </div>
    </div>

    <!-- Jenis Products Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Jenis Produk</th>
                        <th class="px-6 py-4 text-center">Jumlah Layanan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($jenis as $item)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 text-sm text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-200">{{ $item->name }}</div>
                            <div class="text-[10px] text-slate-500">ID Tokovoucher: {{ $item->id }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-accent-blue/10 text-accent-blue text-[10px] font-bold rounded-full border border-accent-blue/20">
                                {{ $item->services_count }} Layanan
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.packages.jenis.toggle', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center group focus:outline-none">
                                    <div class="w-10 h-5 transition-colors rounded-full shadow-inner {{ $item->status == 'Aktif' ? 'bg-primary' : 'bg-slate-700' }}"></div>
                                    <div class="absolute left-0 w-5 h-5 transition-transform bg-white rounded-full shadow-md {{ $item->status == 'Aktif' ? 'translate-x-full' : 'translate-x-0' }}"></div>
                                    <span class="ml-3 text-[10px] font-bold uppercase tracking-wider {{ $item->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }}">
                                        {{ $item->status }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.packages.jenis', $item->id) }}" class="inline-flex items-center gap-2 bg-white/5 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-300 border border-white/10 hover:border-primary/50 hover:bg-primary/10 hover:text-primary transition-all">
                                Lihat Layanan
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">inventory_2</span>
                            <p class="text-xs">Belum ada jenis produk untuk operator ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
