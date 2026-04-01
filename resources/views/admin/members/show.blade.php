@extends('admin.layouts.app')

@section('title', 'Detail Member')
@section('page_title', 'Profil Member')
@section('page_description', 'Detail informasi dan histori aktivitas member di sistem Neon Flux.')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.members') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors group w-fit">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            <span class="text-sm font-medium">Kembali ke Daftar Member</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.members.edit', $user->id) }}" class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all text-secondary">
                <span class="material-symbols-outlined text-lg">edit</span>
                Edit Data
            </a>
            <form action="{{ route('admin.members.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-lg">delete</span>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-panel p-6 rounded-3xl border border-white/5 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4">
                     <span class="px-3 py-1 bg-{{ $user->status == 'active' ? 'blue' : 'red' }}-500/10 text-{{ $user->status == 'active' ? 'blue' : 'red' }}-400 text-[10px] font-bold rounded-full border border-{{ $user->status == 'active' ? 'blue' : 'red' }}-500/20 uppercase tracking-wider">
                        {{ $user->status }}
                    </span>
                </div>
                <div class="size-24 rounded-full border-4 border-primary/20 mx-auto mb-4 p-1">
                    <img src="{{ $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2563eb&color=fff' }}" alt="{{ $user->name }}" class="size-full rounded-full object-cover">
                </div>
                <h2 class="text-xl font-bold text-slate-100">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">{{ $user->role }}</p>

                <div class="mt-8 grid grid-cols-1 gap-3">
                    <div class="p-3 bg-white/5 rounded-2xl border border-white/5 text-center">
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Total Saldo</div>
                        <div class="text-lg font-bold text-accent-blue">Rp {{ number_format($user->balance, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-3xl border border-white/5 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Kontak</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <span class="material-symbols-outlined text-slate-500">mail</span>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <span class="material-symbols-outlined text-slate-500">call</span>
                        {{ $user->phone ?: 'Tidak ada nomor HP' }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <span class="material-symbols-outlined text-slate-500">calendar_month</span>
                        Bergabung: {{ $user->created_at?->format('d M Y') ?: '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity/History -->
        <div class="lg:col-span-2 space-y-6">
             <div class="glass-panel rounded-3xl border border-white/5 overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-100">Riwayat Pesanan Terakhir</h3>
                    <a href="#" class="text-xs text-primary font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 text-[9px] uppercase tracking-wider text-slate-500 font-bold">
                            <tr>
                                <th class="px-6 py-3">Order ID</th>
                                <th class="px-6 py-3">Layanan</th>
                                <th class="px-6 py-3">Harga</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            {{-- Placeholder for future real data --}}
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2 text-slate-600">
                                        <span class="material-symbols-outlined text-4xl opacity-20">shopping_bag</span>
                                        <p class="text-xs font-medium italic">Belum ada riwayat pesanan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel rounded-3xl border border-white/5 overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-100">Riwayat Deposit</h3>
                    <a href="#" class="text-xs text-primary font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 text-[9px] uppercase tracking-wider text-slate-500 font-bold">
                            <tr>
                                <th class="px-6 py-3">Deposit ID</th>
                                <th class="px-6 py-3">Metode</th>
                                <th class="px-6 py-3">Jumlah</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                             {{-- Placeholder for future real data --}}
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2 text-slate-600">
                                        <span class="material-symbols-outlined text-4xl opacity-20">account_balance_wallet</span>
                                        <p class="text-xs font-medium italic">Belum ada riwayat deposit.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
