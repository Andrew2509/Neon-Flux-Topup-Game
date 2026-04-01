@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-6 pt-28 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute -top-24 -left-24 size-[500px] bg-primary/10 blur-[120px] rounded-full"></div>
    <div class="absolute -bottom-24 -right-24 size-[500px] bg-cyan-500/10 blur-[120px] rounded-full"></div>

    <!-- Unified Single Card (max-w-4xl) -->
    <div class="w-full max-w-4xl bg-[#0f172a]/80 backdrop-blur-xl rounded-[2.5rem] border border-white/5 shadow-2xl overflow-hidden z-10 flex flex-col md:flex-row">
        
        <!-- Left Section (65%) -->
        <div class="flex-1 p-8 lg:p-10 border-r border-white/5 relative">
            <!-- Header -->
            <div class="flex items-center gap-5 mb-8">
                <div class="size-14 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary">
                    @php $via = strtolower($ipaymuData['Via'] ?? ''); @endphp
                    @if($via == 'qris')
                        <span class="material-icons-round text-3xl">qr_code_2</span>
                    @else
                        <span class="material-icons-round text-3xl">account_balance</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight leading-none mb-1">
                        {{ $ipaymuData['PaymentName'] ?? 'Metode Pembayaran' }}
                    </h1>
                    <p class="text-xs text-slate-400 font-medium tracking-wide">Konfirmasi otomatis setelah pembayaran berhasil.</p>
                </div>
            </div>

            <!-- Payment Content -->
            <div class="bg-white/5 rounded-[2rem] border border-white/10 p-8 mb-8">
                @if($via == 'qris')
                    <div class="flex flex-col lg:flex-row items-center gap-10">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-primary/20 blur-md rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="bg-white p-4 rounded-2xl shadow-2xl relative">
                                <div class="size-44 overflow-hidden rounded-lg">
                                    <img src="{{ $qrUrl ?? '' }}" alt="QRIS" class="w-full h-full object-contain">
                                </div>
                            </div>
                            @if($qrUrl)
                                <a href="{{ $qrUrl }}" target="_blank" class="mt-3 block text-center text-[10px] text-slate-500 hover:text-primary transition-colors font-bold uppercase tracking-widest">
                                    <span class="material-icons-round text-xs align-middle mr-1">open_in_new</span>
                                    Buka Barcode
                                </a>
                            @endif
                        </div>
                        <div class="flex-1 text-center lg:text-left">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Total Pembayaran</p>
                            <h2 class="text-4xl font-black text-white tracking-tighter mb-4">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </h2>
                            <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 py-2 px-4 rounded-full">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-4">
                                <div class="h-4 w-px bg-white/10"></div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Instant Settlement</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Virtual Account</p>
                        <h2 class="text-5xl font-black text-white tracking-tighter mb-6 selection:bg-primary selection:text-slate-900">
                            {{ $ipaymuData['PaymentNo'] ?? '' }}
                        </h2>
                        <button onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')" class="mx-auto h-12 px-8 rounded-2xl bg-primary text-slate-950 font-black text-sm flex items-center justify-center gap-2 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-primary/20">
                            <span class="material-icons-round">content_copy</span>
                            <span>SALIN NOMOR VA</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Horizontal Instructions (Footer Left) -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Instruksi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $steps = $via == 'qris' 
                            ? ['Buka aplikasi e-Wallet', 'Scan/Upload QR di atas', 'Konfirmasi & Bayar']
                            : ['Masukkan nomor VA', 'Periksa nominal bayar', 'Transaksi selesai'];
                    @endphp
                    @foreach($steps as $idx => $step)
                        <div class="flex items-center gap-3 bg-white/5 border border-white/5 p-3 rounded-2xl">
                            <div class="size-6 rounded-lg bg-primary/10 border border-primary/20 text-primary text-[10px] font-black flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </div>
                            <span class="text-[11px] text-slate-400 font-bold leading-tight line-clamp-1">
                                {{ $step }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Section (35%) - Contrast Area -->
        <div class="w-full md:w-[350px] bg-[#0c1221]/80 p-8 flex flex-col justify-between">
            <div class="space-y-8">
                <!-- Status & Timer -->
                <div class="text-center p-6 rounded-3xl bg-white/5 border border-white/5">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Waktu Tersisa</p>
                    <div id="countdown" class="text-4xl font-black text-white font-mono tracking-widest mb-4">15:00</div>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-yellow-400/10 border border-yellow-400/20 rounded-full">
                        <div class="size-2 rounded-full bg-yellow-400 animate-pulse"></div>
                        <span class="text-[10px] font-black text-yellow-400 uppercase tracking-widest">Pending</span>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="space-y-5">
                    <div class="flex justify-between items-center border-b border-white/5 pb-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Detail Pesanan</span>
                        <span class="text-primary material-icons-round text-sm">receipt</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Order ID</span>
                            <span class="text-[10px] font-black text-white font-mono text-right truncate">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Item</span>
                            <span class="text-[10px] font-black text-white text-right leading-relaxed line-clamp-2 max-w-[150px]">{{ $order->product_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-10 space-y-3">
                <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full h-12 rounded-2xl bg-white text-slate-950 font-black text-xs flex items-center justify-center gap-2 hover:bg-slate-200 transition-colors uppercase tracking-widest shadow-xl">
                    <span class="material-icons-round text-sm">history</span>
                    <span>Cek Status</span>
                </a>
                <button onclick="window.print()" class="w-full h-11 rounded-2xl bg-white/5 text-slate-400 font-bold text-[10px] flex items-center justify-center gap-2 hover:bg-white/10 hover:text-white transition-all uppercase tracking-widest">
                    <span class="material-icons-round text-sm">print</span>
                    <span>Cetak Bukti</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersalin!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                background: '#0f172a',
                color: '#fff'
            });
        });
    }

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = minutes + ":" + seconds;
            if (--timer < 0) { display.textContent = "00:00"; }
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
