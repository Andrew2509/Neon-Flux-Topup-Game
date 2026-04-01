@extends('admin.layouts.app')

@section('title', 'Detail Voucher')
@section('page_title', 'Detail Voucher Promo')
@section('page_description', 'Rincian lengkap syarat dan performa kode voucher.')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.vouchers') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors group w-fit">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            <span class="text-sm font-medium">Kembali ke Kelola Voucher</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all text-secondary">
                <span class="material-symbols-outlined text-lg">edit</span>
                Edit Data
            </a>
            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-lg">delete</span>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Voucher Details Card -->
    <div class="glass-panel p-8 rounded-3xl border border-white/5 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6">
             <span class="px-4 py-1.5 bg-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-500/10 text-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-400 text-xs font-bold rounded-full border border-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-500/20 uppercase tracking-wider shadow-lg shadow-{{ $voucher->status == 'Aktif' ? 'blue' : 'red' }}-500/10">
                {{ $voucher->status == 'Aktif' ? 'Aktif' : 'Tidak Aktif' }}
            </span>
        </div>

        <div class="flex items-center gap-6 mb-8 mt-2">
            <div class="size-20 rounded-2xl bg-linear-to-br from-primary/20 to-accent-blue/20 flex items-center justify-center border border-white/10 shadow-xl shadow-primary/10">
                <span class="material-symbols-outlined text-4xl text-primary">local_activity</span>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Kode Voucher</div>
                <h2 class="text-3xl font-black text-slate-100 font-mono tracking-tight">{{ $voucher->code }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-white/5 p-6 rounded-2xl border border-white/5">
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Potongan Harga</p>
                <p class="text-xl font-bold text-accent-blue">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Minimal Pembelian</p>
                <p class="text-xl font-bold text-slate-100">Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
            <div class="bg-white/5 p-5 rounded-2xl border border-white/5 text-center">
                <span class="material-symbols-outlined text-slate-400 mb-2">supervisor_account</span>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Sisa Kuota</p>
                <p class="text-2xl font-black text-slate-100 mt-1">{{ $voucher->quota }} <span class="text-xs text-slate-500 font-normal">Trx</span></p>
            </div>
            <div class="bg-white/5 p-5 rounded-2xl border border-white/5 text-center sm:col-span-2 flex flex-col justify-center">
                 <span class="material-symbols-outlined text-slate-400 mb-2">event_available</span>
                 <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Masa Berlaku Hingga</p>
                 <p class="text-lg font-bold {{ $voucher->expiry_date && \Carbon\Carbon::parse($voucher->expiry_date)->isPast() ? 'text-red-400' : 'text-slate-100' }} mt-1">
                     {{ $voucher->expiry_date ? \Carbon\Carbon::parse($voucher->expiry_date)->format('d F Y H:i:s') : 'Tanpa Batas Waktu' }}
                 </p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-xs text-slate-500 italic">Voucher ini dibuat pada {{ $voucher->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>
@endsection
