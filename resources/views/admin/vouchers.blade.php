@extends('admin.layouts.app')

@section('title', 'Voucher')
@section('page_title', 'Manajemen Voucher')
@section('page_description', 'Kelola kode diskon dan promo untuk pelanggan.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" placeholder="Cari Kode Voucher..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <button class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Filter
            </button>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
             <a href="{{ route('admin.vouchers.create') }}" class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Voucher
            </a>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Kode Voucher</th>
                        <th class="px-6 py-4">Potongan</th>
                        <th class="px-6 py-4 text-center">Kuota</th>
                        <th class="px-6 py-4">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">confirmation_number</span>
                                <span class="text-sm font-bold text-slate-100 font-mono tracking-tight">{{ $voucher->code }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-xs text-slate-300">{{ $voucher->quota }}</td>
                        <td class="px-6 py-4 text-xs {{ $voucher->expiry_date && \Carbon\Carbon::parse($voucher->expiry_date)->isPast() ? 'text-red-400' : 'text-slate-400' }}">
                            {{ $voucher->expiry_date ? \Carbon\Carbon::parse($voucher->expiry_date)->format('d M Y, H:i') : 'Tanpa Batas Waktu' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-500/10 text-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-400 text-[10px] font-bold rounded-full border border-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-500/20 uppercase tracking-wider">
                                {{ $voucher->status == 'Aktif' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.vouchers.show', $voucher->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Detail Voucher">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-secondary hover:border-secondary transition-all" title="Edit Voucher">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all" title="Hapus Voucher">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">confirmation_number</span>
                             <p class="text-xs">Belum ada voucher aktif.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t border-white/5">
            {{ $vouchers->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>
@endsection
