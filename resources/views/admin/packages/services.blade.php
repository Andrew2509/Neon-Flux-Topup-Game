@extends('admin.layouts.app')

@section('title', 'Layanan - ' . $jenis->name)
@section('page_title', $jenis->name)
@section('page_description', 'Berikut adalah daftar produk layanan aktif untuk jenis ini.')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-widest text-slate-500">
        <a href="{{ route('admin.packages.index') }}" class="hover:text-primary transition-colors">Paket Layanan</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="{{ route('admin.packages.operator', $jenis->category_id) }}" class="hover:text-primary transition-colors">{{ $jenis->category->name }}</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-slate-300">{{ $jenis->name }}</span>
    </div>

    <!-- Header Info -->
    <div class="glass-panel p-6 rounded-2xl border border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="size-16 rounded-xl bg-accent-blue/20 flex items-center justify-center border border-accent-blue/30">
                <span class="material-symbols-outlined text-3xl text-accent-blue">inventory</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-100">{{ $jenis->name }}</h2>
                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1">{{ $services->count() }} Layanan Terdaftar</div>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Nama Layanan</th>
                        <th class="px-6 py-4">Kode Produk</th>
                        <th class="px-6 py-4">Modal (Sync)</th>
                        <th class="px-6 py-4">Harga Jual</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($services as $s)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 text-sm text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-200">{{ $s->name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $s->provider }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-[10px] bg-white/5 px-2 py-1 rounded text-primary font-bold border border-white/5">{{ $s->product_code }}</code>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-400 font-mono">
                            Rp {{ number_format($s->cost, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-accent-blue">Rp {{ number_format($s->price, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-green-400 font-bold">Profit: Rp {{ number_format($s->price - $s->cost, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.packages.services.toggle', $s->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center group focus:outline-none">
                                    <div class="w-10 h-5 transition-colors rounded-full shadow-inner {{ $s->status == 'Aktif' ? 'bg-primary' : 'bg-slate-700' }}"></div>
                                    <div class="absolute left-0 w-5 h-5 transition-transform bg-white rounded-full shadow-md {{ $s->status == 'Aktif' ? 'translate-x-full' : 'translate-x-0' }}"></div>
                                    <span class="ml-3 text-[10px] font-bold uppercase tracking-wider {{ $s->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }}">
                                        {{ $s->status }}
                                    </span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">inventory_2</span>
                            <p class="text-xs">Belum ada layanan untuk jenis ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
