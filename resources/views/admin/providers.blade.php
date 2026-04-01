@extends('admin.layouts.app')

@section('title', 'Provider')
@section('page_title', 'Manajemen Provider')
@section('page_description', 'Kelola integrasi API dan saldo layanan dari pihak ketiga.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" placeholder="Cari Provider..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
             <a href="{{ route('admin.providers.create') }}" class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">add_link</span>
                Tambah Provider
            </a>
        </div>
    </div>

    <!-- Providers Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">API Key / Id</th>
                        <th class="px-6 py-4">Saldo (Balance)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($providers as $prov)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 group-hover:text-primary group-hover:border-primary transition-all">
                                    <span class="material-symbols-outlined text-xl">{{ $prov->icon }}</span>
                                </div>
                                <div class="text-sm font-bold text-slate-100">{{ $prov->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-400 font-mono tracking-tighter">ID: {{ $prov->provider_id ?: '-' }}</div>
                            <div class="text-[10px] text-slate-500 font-mono">Key: {{ substr($prov->api_key, 0, 4) . '****' . substr($prov->api_key, -4) }}</div>
                            @if($prov->mode)
                            <div class="mt-1">
                                <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase {{ $prov->mode === 'sandbox' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">
                                    {{ $prov->mode }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">
                            @if(str_contains(strtolower($prov->name), 'whatsapp') || str_contains(strtolower($prov->name), 'orbit'))
                                <div class="flex items-center gap-1.5 {{ $prov->status === 'Aktif' ? 'text-green-400' : 'text-red-400' }}">
                                    <span class="size-2 rounded-full {{ $prov->status === 'Aktif' ? 'bg-green-400' : 'bg-red-400' }} animate-pulse"></span>
                                    {{ $prov->status === 'Aktif' ? 'Device Connected' : 'Disconnected' }}
                                </div>
                            @else
                                Rp {{ number_format($prov->balance, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColor = 'blue';
                                if ($prov->status === 'Nonaktif') $statusColor = 'slate';
                                if ($prov->status === 'Error') $statusColor = 'red';
                            @endphp
                            <span class="px-3 py-1 bg-{{ $statusColor }}-500/10 text-{{ $statusColor }}-400 text-[10px] font-bold rounded-full border border-{{ $statusColor }}-500/20">
                                {{ $prov->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.providers.edit', $prov->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Edit Kredensial">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                @if(str_contains(strtolower($prov->name), 'toko') || str_contains(strtolower($prov->name), 'digiflazz'))
                                    <a href="{{ route('admin.providers.deposit.form', $prov->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-green-400 hover:border-green-400 transition-all" title="Topup Deposit">
                                        <span class="material-symbols-outlined text-lg">add_card</span>
                                    </a>
                                @endif
                                <form action="{{ route('admin.providers.balance', $prov->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-accent-blue hover:border-accent-blue transition-all" title="Refresh Saldo">
                                        <span class="material-symbols-outlined text-lg">sync</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.providers.destroy', $prov->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus provider ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all" title="Hapus Provider">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">hub</span>
                             <p class="text-xs">Belum ada provider API yang dikonfigurasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($providers->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $providers->links('vendor.pagination.tailwind-admin') }}
        </div>
        @endif
        <!-- Alert Note -->
        <div class="p-4 bg-primary/5 border-t border-white/5">
            <div class="flex gap-3">
                <span class="material-symbols-outlined text-primary text-sm mt-0.5">info</span>
                <p class="text-[10px] text-slate-400 font-medium">Pastikan API Key dan Secret yang dimasukkan sudah benar untuk menghindari kegagalan transaksi otomatis. Gunakan tombol sync untuk memperbarui saldo secara manual.</p>
            </div>
        </div>
    </div>
</div>
@endsection
