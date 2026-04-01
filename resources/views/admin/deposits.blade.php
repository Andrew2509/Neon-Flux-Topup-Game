@extends('admin.layouts.app')

@section('title', 'Deposit Member')
@section('page_title', 'Deposit Member')
@section('page_description', 'Kelola pengajuan dan riwayat deposit saldo pelanggan.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" placeholder="Cari ID Deposit atau Nama..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <button class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Filter Status
            </button>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Data
            </button>
        </div>
    </div>

    <!-- Deposits Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">ID Deposit</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Nominal Topup</th>
                        <th class="px-6 py-4">Metode Bayar</th>
                        <th class="px-6 py-4">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($deposits as $deposit)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 text-sm font-bold text-slate-300">{{ $deposit->deposit_id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">{{ $deposit->user->name ?? 'Deleted User' }}</div>
                            <div class="text-[10px] text-slate-500">ID: NF-USR-{{ $deposit->user_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-xs">{{ $deposit->method }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $deposit->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $status_color = match($deposit->status) {
                                    'success' => 'blue',
                                    'pending' => 'amber',
                                    'failed' => 'red',
                                    default => 'slate'
                                };
                            @endphp
                            <span class="px-3 py-1 bg-{{ $status_color }}-500/10 text-{{ $status_color }}-400 text-[10px] font-bold rounded-full border border-{{ $status_color }}-500/20">
                                {{ ucfirst($deposit->status === 'success' ? 'Selesai' : ($deposit->status === 'failed' ? 'Ditolak' : 'Pending')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($deposit->status === 'pending')
                                    <button class="size-8 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400 hover:bg-green-500 hover:text-white transition-all shadow-lg shadow-green-500/10" title="Terima Deposit">
                                        <span class="material-symbols-outlined text-lg">check</span>
                                    </button>
                                    <button class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/10" title="Tolak Deposit">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                @else
                                    <button class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Detail Riwayat">
                                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">Belum ada request deposit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t border-white/5">
            {{ $deposits->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>
    </div>
</div>
@endsection
