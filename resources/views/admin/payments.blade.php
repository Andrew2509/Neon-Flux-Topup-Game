@extends('admin.layouts.app')

@section('title', 'Pembayaran')
@section('page_title', 'Metode Pembayaran')
@section('page_description', 'Kelola berbagai metode pembayaran yang tersedia untuk pelanggan.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <form action="{{ route('admin.payments') }}" method="GET" class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Metode..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <select name="type" onchange="this.form.submit()" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none appearance-none text-slate-300">
                <option value="">Semua Tipe</option>
                <option value="bank" {{ request('type') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="ewallet" {{ request('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                <option value="qris" {{ request('type') == 'qris' ? 'selected' : '' }}>QRIS</option>
                <option value="retail" {{ request('type') == 'retail' ? 'selected' : '' }}>Retail Outlet</option>
            </select>
            @if(request()->anyFilled(['search', 'type']))
                <a href="{{ route('admin.payments') }}" class="text-xs text-slate-500 hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">close</span>
                    Reset
                </a>
            @endif
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button type="submit" class="hidden"></button>
            <form action="{{ route('admin.payments.sync') }}" method="POST">
                @csrf
                <button type="submit" class="bg-accent-blue/20 hover:bg-accent-blue/30 text-accent-blue border border-accent-blue/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-accent-blue/5">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Sync Duitku
                </button>
            </form>
            <form action="{{ route('admin.payments.sync_ipaymu') }}" method="POST">
                @csrf
                <button type="submit" class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-500 border border-emerald-500/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-emerald-500/5">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Sync iPaymu
                </button>
            </form>
            <form action="{{ route('admin.payments.sync_midtrans') }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-400 border border-indigo-500/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-indigo-500/5">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Sync Midtrans
                </button>
            </form>
            <form action="{{ route('admin.payments.sync_doku') }}" method="POST">
                @csrf
                <button type="submit" class="bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-rose-500/5">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Sync DOKU
                </button>
            </form>
             <a href="{{ route('admin.payments.create') }}" class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">add_card</span>
                Tambah Metode
            </a>
        </div>
    </div>

    <!-- Payment Methods Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Mitra</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Biaya (Fee)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($payments as $pay)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden">
                                    @if($pay->image)
                                        <img src="{{ $pay->image }}" class="size-full object-contain p-1" alt="{{ $pay->name }}">
                                    @else
                                        <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-all">payments</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-100">{{ $pay->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono tracking-tighter">{{ $pay->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $pLower = strtolower($pay->provider);
                                $badgeClass = 'bg-accent-blue/10 text-accent-blue border border-accent-blue/20';
                                if (str_contains($pLower, 'ipaymu')) $badgeClass = 'bg-primary/10 text-primary border border-primary/20';
                                if (str_contains($pLower, 'midtrans')) $badgeClass = 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20';
                                if (str_contains($pLower, 'doku')) $badgeClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $badgeClass }}">
                                {{ $pay->provider ?: 'Duitku' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400 font-medium">{{ $pay->type }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">{{ $pay->fee }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.payments.toggle', $pay->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center group focus:outline-none">
                                    <div class="w-10 h-5 transition-colors rounded-full shadow-inner {{ $pay->status == 'Aktif' ? 'bg-primary' : 'bg-slate-700' }}"></div>
                                    <div class="absolute left-0 w-5 h-5 transition-transform bg-white rounded-full shadow-md {{ $pay->status == 'Aktif' ? 'translate-x-full' : 'translate-x-0' }}"></div>
                                    <span class="ml-3 text-[10px] font-bold uppercase tracking-wider {{ $pay->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }}">
                                        {{ $pay->status }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.payments.edit', $pay->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Edit Metode">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.payments.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all" title="Hapus Metode">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">payments</span>
                             <p class="text-xs">Belum ada metode pembayaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $payments->links('vendor.pagination.tailwind-admin') }}
        </div>
        @endif
        <!-- Note -->
        <div class="p-4 border-t border-white/5">
            <p class="text-[10px] text-slate-500 font-medium italic">* Biaya (Fee) dapat dikonfigurasi secara manual atau otomatis jika menggunakan Payment Gateway.</p>
        </div>
    </div>
</div>
@endsection
