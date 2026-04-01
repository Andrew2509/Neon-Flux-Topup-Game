@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran QRIS - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pt-32 pb-20 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-2xl z-10">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-primary/10 text-primary mb-4 border border-primary/20 shadow-lg shadow-primary/5">
                    <span class="material-icons-round text-3xl">qr_code_2</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Pembayaran QRIS</h1>
                <p class="text-slate-400 font-medium">Scan kode QR di bawah ini menggunakan aplikasi pembayaran Anda.</p>
            </div>

            <!-- QR Code Display -->
            <div class="flex flex-col items-center justify-center bg-white rounded-4xl p-8 shadow-2xl mb-8 relative group">
                <div class="absolute -top-4 bg-primary text-slate-900 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-primary/20 z-20">
                    Auto Scan QRIS
                </div>
                
                <div class="relative w-full max-w-[280px] aspect-square overflow-hidden rounded-xl border-4 border-slate-50">
                    <img src="{{ $qrImage }}" alt="QRIS Payment" class="w-full h-full object-contain">
                    
                    <!-- Overlay Decor -->
                    <div class="absolute inset-0 border-16 border-white/0 group-hover:border-white/5 transition-all duration-700 pointer-events-none"></div>
                </div>

                <div class="mt-6 flex items-center gap-3 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS Logo" class="h-6">
                    <div class="w-px h-4 bg-slate-300"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">GPN & Bersama</span>
                </div>
            </div>

            <!-- Payment Info Grid -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white/5 border border-white/5 rounded-3xl p-5 flex flex-col items-center justify-center text-center">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Bayar</p>
                    <p class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-3xl p-5 flex flex-col items-center justify-center text-center">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Batas Waktu</p>
                    <div id="countdown" class="text-2xl font-black text-white tracking-tight">--:--</div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white/5 border border-white/5 rounded-3xl p-6 space-y-4 mb-8">
                <div class="flex justify-between items-center text-sm font-medium">
                    <span class="text-slate-400">Order ID</span>
                    <span class="text-white font-mono uppercase tracking-tight">{{ $order->order_id }}</span>
                </div>
                <div class="flex justify-between items-center text-sm font-medium">
                    <span class="text-slate-400">Produk</span>
                    <span class="text-white text-right leading-tight max-w-[180px]">{{ $order->product_name }}</span>
                </div>
                <div class="flex justify-between items-center text-sm font-medium">
                    <span class="text-slate-400">Metode</span>
                    <span class="text-white">QRIS (iPaymu)</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full bg-white/5 text-white font-black text-sm py-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                    <span class="material-icons-round text-sm">history</span>
                    <span>Cek Status</span>
                </a>
                <button onclick="window.print()" class="w-full bg-primary text-slate-950 font-black text-sm py-4 rounded-2xl shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all flex items-center justify-center gap-2">
                    <span class="material-icons-round text-sm">download</span>
                    <span>Simpan QR</span>
                </button>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 space-y-4">
            <h3 class="text-white font-black text-center text-lg uppercase tracking-widest">Cara Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center">
                    <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto mb-3 text-xs font-black">1</span>
                    <p class="text-xs text-slate-400 leading-relaxed">Simpan atau screenshot kode QR di atas</p>
                </div>
                <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center">
                    <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto mb-3 text-xs font-black">2</span>
                    <p class="text-xs text-slate-400 leading-relaxed">Buka aplikasi E-Wallet (OVO, DANA) atau Mobile Banking</p>
                </div>
                <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center">
                    <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto mb-3 text-xs font-black">3</span>
                    <p class="text-xs text-slate-400 leading-relaxed">Pilih Scan/QRIS lalu unggah foto Kode QR</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple countdown timer
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
