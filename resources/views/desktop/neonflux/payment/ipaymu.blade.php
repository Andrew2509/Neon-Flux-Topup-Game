@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FL>

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pt-32 pb-20 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse" style="an>

    <div class="w-full max-w-2xl z-10">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-primary/10 text-primary mb-4>
                    @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                        <span class="material-icons-round text-3xl">qr_code_2</span>
                    @elseif(($ipaymuData['Channel'] ?? '') == 'VA')
                        <span class="material-icons-round text-3xl">account_balance</span>
                    @else
                        <span class="material-icons-round text-3xl">payments</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    {{ $ipaymuData['PaymentName'] ?? 'Pembayaran' }}
                </h1>
                <p class="text-slate-400 font-medium">Selesaikan pembayaran untuk memproses pesanan Anda.</p>
            </div>

            @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                <!-- QR Code Display -->
                <div class="flex flex-col items-center justify-center bg-white rounded-4xl p-8 shadow-2xl mb-8 relative>
                    <div class="absolute -top-4 bg-primary text-slate-900 text-[10px] font-black px-4 py-1.5 rounded-fu>
                        Auto Scan QRIS
                    </div>

                    <div class="relative w-full max-w-[280px] aspect-square overflow-hidden rounded-xl border-4 border->
                        <img src="{{ $qrUrl ?? '' }}" alt="QRIS Payment" class="w-full h-full object-contain">
                        <div class="absolute inset-0 border-16 border-white/0 group-hover:border-white/5 transition-all>
                    </div>

                    @if($qrUrl)
                    <div class="mt-4 text-center">
                        <a href="{{ $qrUrl }}" target="_blank" class="text-[10px] text-slate-400 hover:text-primary tra>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentCo>
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.7>
                            </svg>
                            Klik di sini jika barcode tidak muncul
                        </a>
                    </div>
                    @endif
                </div>

                    <div class="mt-6 flex items-center gap-3 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS Logo" cl>
                        <div class="w-px h-4 bg-slate-300"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Accepted Nationwide</sp>
                    </div>
                </div>
            @else
                <!-- VA / Payment Number Display -->
                <div class="bg-white/5 border border-white/5 rounded-4xl p-8 mb-8 text-center relative group">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Nomor Pembayaran / >
                    <div class="flex items-center justify-center gap-4">
                        <h2 id="paymentCode" class="text-4xl md:text-5xl font-black text-white tracking-tighter">{{ $ip>
                        <button onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')" class="size-12 rounde>
                            <span class="material-icons-round">content_copy</span>
                        </button>
                    </div>
                    @if(($ipaymuData['Channel'] ?? '') == 'VA')
                        <p class="mt-4 text-sm font-bold text-primary uppercase tracking-widest">{{ $ipaymuData['Paymen>
                    @endif
                </div>
            @endif

            <!-- Payment Info Grid -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white/5 border border-white/5 rounded-3xl p-5 flex flex-col items-center justify-center >
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Bayar</p>
                    <p class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($order->total_price,>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-3xl p-5 flex flex-col items-center justify-center >
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Batas Waktu</p>
                    <div id="countdown" class="text-2xl font-black text-white tracking-tight">15:00</div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white/5 border border-white/5 rounded-3xl p-6 space-y-4 mb-8 text-sm font-medium">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Order ID</span>
                    <span class="text-white font-mono uppercase tracking-tight">{{ $order->order_id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Produk</span>
                    <span class="text-white text-right leading-tight max-w-[200px]">{{ $order->product_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Metode</span>
                    <span class="text-white">{{ $ipaymuData['PaymentName'] ?? 'iPaymu' }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full bg-white/5 text-wh>
                    <span class="material-icons-round text-sm">history</span>
                    <span>Cek Status</span>
                </a>
                <button onclick="window.print()" class="w-full bg-primary text-slate-950 font-black text-sm py-4 rounde>
                    <span class="material-icons-round text-sm">download</span>
                    <span>Simpan Detail</span>
                </button>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 space-y-4">
            <h3 class="text-white font-black text-center text-lg uppercase tracking-widest">Cara Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Simpan atau screenshot kode QR di atas</p>
                    </div>
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Buka aplikasi E-Wallet (OVO, DANA) atau Mobile Banking</p>
                    </div>
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Pilih Scan/QRIS lalu unggah foto Kode QR</p>
                    </div>
                @else
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Salin nomor Virtual Account yang muncul di atas</p>
                    </div>
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Lakukan transfer melalui ATM atau Mobile Banking</p>
                    </div>
                    <div class="glass-panel p-5 rounded-2xl border border-white/5 text-center text-xs text-slate-400 le>
                        <span class="size-8 rounded-full bg-white/5 text-white flex items-center justify-center mx-auto>
                        <p>Simpan bukti bayar. Pesanan akan diproses otomatis.</p>
                    </div>
                @endif
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
                title: 'Disalin!',
                text: 'Nomor pembayaran telah disalin ke clipboard.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1e293b',
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

            if (--timer < 0) {
                display.textContent = "EXT";
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
