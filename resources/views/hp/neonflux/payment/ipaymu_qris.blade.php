@extends('hp.layouts.neonflux')

@section('title', 'Pembayaran QRIS - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="px-5 pt-28 pb-10 min-h-screen relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-10 size-64 bg-primary/10 blur-[80px] rounded-full"></div>
    <div class="absolute bottom-1/4 -right-10 size-48 bg-cyan-500/10 blur-[80px] rounded-full"></div>

    <div class="w-full z-10 space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-primary/10 text-primary mb-2 border border-primary/20 shadow-lg">
                <span class="material-icons-round text-2xl">qr_code_scanner</span>
            </div>
            <h1 class="text-2xl font-black text-white px-4">Selesaikan Pembayaran</h1>
            <p class="text-[10px] text-slate-400 font-medium px-8 leading-relaxed uppercase tracking-widest">Pindai kode QRIS di bawah ini untuk menyelesaikan pesanan Anda.</p>
        </div>

        <!-- Main QR Card -->
        <div class="glass-panel p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden text-center">
            <div class="flex items-center justify-between mb-6 px-1">
                <div class="text-left">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Total Tagihan</p>
                    <p class="text-xl font-black text-primary tracking-tight leading-none">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Bayar Sebelum</p>
                    <div id="countdown" class="text-xl font-black text-white tracking-tight leading-none">15:00</div>
                </div>
            </div>

            <!-- QR Code Display -->
            <div class="bg-white rounded-3xl p-6 shadow-xl mb-6 relative mx-auto max-w-[240px]">
                <div class="relative w-full aspect-square overflow-hidden rounded-xl border border-slate-50">
                    <img src="{{ $qrImage }}" alt="QRIS Payment" class="w-full h-full object-contain">
                </div>
                
                <div class="mt-4 flex items-center justify-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS Logo" class="h-4">
                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Accepted Nationwide</span>
                </div>
            </div>

            <!-- Order Brief -->
            <div class="bg-white/5 border border-white/5 rounded-2xl p-4 space-y-2 text-left mb-6">
                <div class="flex justify-between items-center text-[10px] font-medium leading-none">
                    <span class="text-slate-500 uppercase tracking-wider">Invoice</span>
                    <span class="text-white font-mono uppercase">{{ $order->order_id }}</span>
                </div>
                <div class="flex justify-between items-start text-[10px] font-medium leading-none">
                    <span class="text-slate-500 uppercase tracking-wider">Product</span>
                    <span class="text-white text-right leading-tight max-w-[140px]">{{ $order->product_name }}</span>
                </div>
            </div>

            <!-- Download Button -->
            <button onclick="window.print()" class="w-full bg-primary text-slate-950 font-black text-xs py-4 rounded-2xl shadow-xl shadow-primary/20 flex items-center justify-center gap-2 active:scale-95 transition-transform">
                <span class="material-icons-round text-sm">file_download</span>
                <span>DOWNLOAD KODE QR</span>
            </button>
        </div>

        <!-- Help Info -->
        <div class="glass-panel p-5 rounded-2xl border border-white/5 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-white/5 flex items-center justify-center shrink-0">
                <span class="material-icons-round text-primary text-lg">support_agent</span>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold text-white mb-1">Butuh Bantuan?</p>
                <p class="text-[9px] text-slate-500 leading-tight">Hubungi Customer Service kami jika Anda mengalami kesulitan dalam pembayaran.</p>
            </div>
            <a href="#" class="size-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <span class="material-icons-round text-lg">chat</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                display.textContent = "00:00";
            }
        }, 1000);
    }

    window.onload = function () {
        var fifteenMinutes = 60 * 15,
            display = document.querySelector('#countdown');
        startTimer(fifteenMinutes, display);
    };
</script>
@endpush
@endsection
