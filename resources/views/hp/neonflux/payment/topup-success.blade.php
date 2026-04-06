@extends('hp.layouts.neonflux')

@section('title', 'Top-up berhasil')

@section('content')
@php
    $sn = trim((string) data_get($order->payload, 'tokovoucher.sn', ''));
@endphp
<div class="px-5 pt-28 pb-16 min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-md glass-panel rounded-[2rem] p-8 text-center border border-white/10">
        <div class="size-16 mx-auto rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-5 border border-emerald-500/30">
            <span class="material-icons-round text-4xl">verified</span>
        </div>
        <h1 class="text-xl font-black text-white mb-2">Top-up berhasil</h1>
        <p class="text-[10px] text-slate-400 mb-6 leading-relaxed">Pembayaran selesai dan top-up sudah diproses.</p>

        <div class="bg-white/5 rounded-2xl p-4 text-left text-[10px] space-y-2 mb-6">
            <div class="flex justify-between gap-2">
                <span class="text-slate-500">Order</span>
                <span class="font-mono text-white break-all text-right">{{ $order->order_id }}</span>
            </div>
            <div class="flex justify-between gap-2 items-start">
                <span class="text-slate-500 shrink-0">Produk</span>
                <span class="text-white text-right leading-tight">{{ $order->product_name }}</span>
            </div>
            @if($sn !== '')
            <div class="pt-2 border-t border-white/10">
                <p class="text-slate-500 mb-1">SN</p>
                <p class="font-mono text-primary text-[9px] break-all">{{ $sn }}</p>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-3">
            <a href="{{ route('home') }}" class="py-4 rounded-2xl bg-primary text-slate-950 font-black text-[10px] uppercase no-underline text-center">Beranda</a>
            <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="py-4 rounded-2xl bg-white/5 text-white font-black text-[10px] uppercase no-underline text-center border border-white/10">Lacak</a>
        </div>
        <p class="mt-6 text-[9px] text-slate-500">Cek WhatsApp untuk notifikasi ringkas.</p>
    </div>
</div>
@endsection
