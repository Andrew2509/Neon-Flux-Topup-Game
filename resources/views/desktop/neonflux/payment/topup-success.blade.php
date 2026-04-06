@extends('desktop.layouts.neonflux')

@section('title', 'Top-up berhasil — ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
@php
    $sn = trim((string) data_get($order->payload, 'tokovoucher.sn', ''));
    $testimonialAlreadySent = $testimonialAlreadySent ?? null;
    $showTestimonialForm = empty($testimonialAlreadySent);
@endphp
<div class="min-h-[70vh] flex items-center justify-center px-4 py-24">
    <div class="max-w-lg w-full rounded-3xl border border-white/10 bg-white/5 dark:bg-white/[0.03] backdrop-blur-xl p-8 sm:p-10 text-center shadow-2xl">
        <div class="inline-flex size-20 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 mb-6 border border-emerald-500/30">
            <span class="material-icons-round text-5xl">verified</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white font-orbitron tracking-tight mb-2">Top-up berhasil</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Pembayaran terverifikasi dan item telah dikirim ke akun game Anda.</p>

        <div class="text-left space-y-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 p-5 mb-6 text-sm">
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

        @if($showTestimonialForm)
        <div id="bagian-ulasan" class="text-left rounded-2xl border border-primary/30 bg-primary/5 dark:bg-primary/10 p-5 mb-6 scroll-mt-28">
            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide mb-1 flex items-center gap-2">
                <span class="material-icons-round text-primary text-lg">rate_review</span>
                Bagikan pengalamanmu
            </h2>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 mb-4">Bantu pemain lain dengan ulasan singkat (sekali per pesanan).</p>
            <form method="post" action="{{ route('testimoni.store') }}" class="space-y-4" id="form-testimoni-sukses">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <div>
                    <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 block mb-2">Rating</label>
                    <div class="flex justify-center gap-1 sm:gap-2" id="star-bar" role="group" aria-label="Bintang">
                        @for($s = 1; $s <= 5; $s++)
                            <button type="button" data-star="{{ $s }}" class="nf-star-btn text-amber-400 opacity-35 hover:opacity-100 transition-opacity p-1" aria-label="{{ $s }} bintang">
                                <span class="material-icons-round text-3xl sm:text-4xl">star</span>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="stars" id="stars-value" value="5" required>
                </div>
                <div>
                    <label for="author_nickname" class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 block mb-1">Nama tampilan (opsional)</label>
                    <input type="text" name="author_nickname" id="author_nickname" maxlength="40" placeholder="Contoh: Andi G."
                        class="w-full rounded-xl border border-black/10 dark:border-white/15 bg-white dark:bg-[#0a0a15] px-3 py-2 text-sm text-slate-900 dark:text-white placeholder:text-slate-400">
                </div>
                <div>
                    <label for="comment" class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 block mb-1">Ulasan</label>
                    <textarea name="comment" id="comment" rows="3" required minlength="8" maxlength="500" placeholder="Cepat, aman, dll."
                        class="w-full rounded-xl border border-black/10 dark:border-white/15 bg-white dark:bg-[#0a0a15] px-3 py-2 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 resize-none"></textarea>
                </div>
                @error('comment')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('stars')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <button type="submit" class="w-full rounded-xl bg-primary text-slate-950 font-black py-3 text-sm uppercase tracking-wide hover:opacity-90 transition-opacity">
                    Kirim testimoni
                </button>
            </form>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}#testimoni" class="inline-flex items-center justify-center rounded-xl bg-primary text-slate-950 font-black py-3 px-6 text-sm no-underline hover:opacity-90 transition-opacity">Kembali ke beranda</a>
            <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 text-slate-900 dark:text-white font-bold py-3 px-6 text-sm no-underline hover:bg-white/5">Lacak transaksi</a>
        </div>

        <p class="mt-6 text-[11px] text-slate-500">Kami juga mengirim ringkasan ke WhatsApp Anda (jika notifikasi aktif).</p>
    </div>
</div>
@endsection

@push('scripts')
@if($showTestimonialForm)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var section = document.getElementById('bagian-ulasan');
    if (section) {
        setTimeout(function () {
            section.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 400);
    }
    var hidden = document.getElementById('stars-value');
    var btns = document.querySelectorAll('.nf-star-btn');
    function paint(n) {
        btns.forEach(function (b) {
            var v = parseInt(b.getAttribute('data-star'), 10);
            b.style.opacity = v <= n ? '1' : '0.35';
        });
        if (hidden) hidden.value = String(n);
    }
    paint(5);
    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            paint(parseInt(b.getAttribute('data-star'), 10));
        });
    });
});
</script>
@endif
@endpush
