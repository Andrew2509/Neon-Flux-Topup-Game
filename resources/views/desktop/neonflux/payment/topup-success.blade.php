@extends('desktop.layouts.neonflux')

@section('title', 'Top-up berhasil — ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
@php
    $sn = trim((string) data_get($order->payload, 'tokovoucher.sn', ''));
@endphp
<div class="min-h-[70vh] flex items-center justify-center px-4 py-24">
    <div class="max-w-lg w-full rounded-3xl border border-white/10 bg-white/5 dark:bg-white/[0.03] backdrop-blur-xl p-10 text-center shadow-2xl">
        <div class="inline-flex size-20 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 mb-6 border border-emerald-500/30">
            <span class="material-icons-round text-5xl">verified</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white font-orbitron tracking-tight mb-2">Top-up berhasil</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-8">Pembayaran terverifikasi dan item telah dikirim ke akun game Anda.</p>

        <div class="text-left space-y-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 p-5 mb-8 text-sm">
            <div class="flex justify-between gap-4">
                <span class="text-slate-500 dark:text-slate-400">Order</span>
                <span class="font-mono font-bold text-slate-900 dark:text-white text-right break-all">{{ $order->order_id }}</span>
            </div>
            <div class="flex justify-between gap-4 items-start">
                <span class="text-slate-500 dark:text-slate-400 shrink-0">Produk</span>
                <span class="font-semibold text-slate-900 dark:text-white text-right">{{ $order->product_name }}</span>
            </div>
            @if($sn !== '')
            <div class="pt-2 border-t border-black/5 dark:border-white/10">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">SN / Bukti</p>
                <p class="font-mono text-xs text-cyan-600 dark:text-cyan-300 break-all select-all">{{ $sn }}</p>
                <button type="button" onclick="navigator.clipboard.writeText(@json($sn)); this.textContent='Disalin!';" class="mt-2 text-xs font-bold text-primary hover:underline">Salin SN</button>
            </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-primary text-slate-950 font-black py-3 px-6 text-sm no-underline hover:opacity-90 transition-opacity">Kembali ke beranda</a>
            <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 text-slate-900 dark:text-white font-bold py-3 px-6 text-sm no-underline hover:bg-white/5">Lacak transaksi</a>
        </div>

        <p class="mt-8 text-[11px] text-slate-500">Kami juga mengirim ringkasan ke WhatsApp Anda (jika notifikasi aktif).</p>
    </div>
</div>
@endsection
