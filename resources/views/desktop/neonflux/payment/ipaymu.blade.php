@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@push('styles')
<!-- Primary Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

<!-- Tailwind Play CDN for Runtime Integration -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#99f7ff",
                    "secondary": "#c47fff",
                    "background": "#0b0e14",
                    "surface": "#161a21",
                    "on-surface": "#ecedf6",
                    "on-surface-variant": "#a9abb3",
                    "surface-container-high": "#1c2028",
                    "outline-variant": "#45484f",
                    "error": "#ff716c",
                    "primary-container": "#00f1fe",
                    "on-primary": "#005f64",
                },
                fontFamily: {
                    "headline": ["Space Grotesk"],
                    "body": ["Manrope"],
                    "label": ["Inter"]
                },
            },
        },
    }
</script>

<style>
    .glass-card {
        background: rgba(22, 26, 33, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 247, 255, 0.08);
    }
    .neon-glow-primary {
        box-shadow: 0 0 20px rgba(0, 241, 254, 0.15);
    }
    .flux-gradient {
        background: linear-gradient(135deg, #99f7ff 0%, #00f1fe 100%);
    }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    /* Override layout defaults */
    #app, .main-layout { background: transparent !important; }
</style>
@endpush

@section('content')
<div class="relative bg-[#0b0e14] text-[#ecedf6] font-body min-h-screen pt-32 pb-16 px-4 md:px-8 overflow-hidden">
    <!-- Ambient Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-cyan-500/5 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-purple-500/5 blur-[120px] rounded-full"></div>
    </div>

    <main class="relative z-10 max-w-4xl mx-auto w-full">
        <!-- Header / Status -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4 px-2">
            <div>
                <h1 class="text-2xl font-headline font-black tracking-tight text-white flex items-center gap-3">
                    <span class="material-symbols-outlined text-cyan-400 text-3xl">offline_bolt</span>
                    SECURE CHECKOUT
                </h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em] mt-1">Order Transaction Detail</p>
            </div>
            <div class="flex items-center gap-3 bg-rose-500/10 border border-rose-500/20 px-5 py-2.5 rounded-2xl">
                <span class="material-symbols-outlined text-rose-500 text-xl animate-pulse">timer</span>
                <span id="countdown" class="font-mono font-bold text-rose-500 text-lg">15:00</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Column: Summary & Payment -->
            <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Summary -->
                <div class="glass-card rounded-[1.5rem] p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
                        <span class="material-symbols-outlined text-9xl">receipt_long</span>
                    </div>
                    <h3 class="text-xs font-headline font-black mb-6 uppercase tracking-widest text-cyan-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">list_alt</span>
                        Order Details
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-white/5 pb-3">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-wider">ID Pesanan</span>
                            <span class="font-mono text-white font-bold text-xs tracking-wider">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-3">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-wider">Layanan</span>
                            <span class="text-white font-black text-xs uppercase">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-3">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-wider">Metode</span>
                            <span class="text-cyan-400 font-black text-xs uppercase">{{ $ipaymuData['PaymentName'] ?? 'QRIS' }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-wider">Total Pembayaran</span>
                            <div class="text-right">
                                <span class="text-[11px] font-bold text-cyan-400/60 mr-1">Rp</span>
                                <span class="text-2xl font-headline font-black text-cyan-400">
                                    {{ number_format($order->total_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Area -->
                <div class="glass-card rounded-[1.5rem] p-6 shadow-xl flex flex-col items-center justify-center min-h-[300px]">
                    @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                        <div class="text-center w-full">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-6">Scan QR Code Berikut</p>

                            <div class="relative inline-block p-4 bg-white rounded-2xl shadow-2xl mb-6 group">
                                @if(!empty($qrUrl))
                                    <img alt="Payment QR Code" src="{{ $qrUrl }}" class="w-48 h-48 object-contain">
                                    <div class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 z-10 p-4">
                                        <a href="{{ $qrUrl }}" target="_blank" class="text-[9px] text-slate-800 font-black flex flex-col items-center gap-2 no-underline">
                                            <span class="material-symbols-outlined text-2xl text-cyan-600">open_in_new</span>
                                            <span>LIHAT GAMBAR PENUH</span>
                                        </a>
                                    </div>
                                @else
                                    <div class="w-48 h-48 flex flex-col items-center justify-center text-slate-300 gap-3 border-2 border-dashed border-slate-200 rounded-xl">
                                        <span class="material-symbols-outlined text-4xl animate-pulse">qr_code_2</span>
                                        <p class="text-[8px] font-bold uppercase tracking-widest px-4">Processing...</p>
                                    </div>
                                @endif
                            </div>

                            <p class="text-[10px] font-medium text-slate-400 leading-relaxed px-4">
                                Scan menggunakan aplikasi <span class="text-white font-bold">GoPay, OVO, DANA, LinkAja</span> atau Banking App Anda.
                            </p>
                        </div>
                    @else
                        <!-- VA Number Area -->
                        <div class="text-center w-full">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-6">Nomor Virtual Account</p>

                            @if(!empty($ipaymuData['PaymentNo']))
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6 group hover:border-cyan-400/30 transition-all cursor-pointer relative" onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] }}')">
                                    <h2 class="text-3xl font-headline font-black text-white tracking-widest mb-4 group-hover:text-cyan-400 transition-colors">
                                        {{ $ipaymuData['PaymentNo'] }}
                                    </h2>
                                    <div class="inline-flex items-center gap-2 text-[8px] text-cyan-400 font-black uppercase tracking-widest bg-cyan-400/10 px-4 py-1.5 rounded-full border border-cyan-400/20">
                                        <span class="material-symbols-outlined text-[12px]">content_copy</span>
                                        CLICK TO COPY NUMBER
                                    </div>
                                </div>
                            @else
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-8 mb-6">
                                    <div class="animate-pulse flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-3xl text-slate-600">hourglass_empty</span>
                                        <p class="text-[9px] font-bold text-slate-500 uppercase">Menunggu nomor VA...</p>
                                    </div>
                                </div>
                            @endif

                            <p class="text-[10px] font-medium text-slate-400 leading-relaxed px-8">
                                Masukkan nomor VA di atas pada menu <span class="text-white font-bold">Transfer / Virtual Account</span> m-Banking Anda.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Guidance & Instructions -->
                <div class="glass-card rounded-[1.5rem] p-6 shadow-xl relative overflow-hidden">
                    <h3 class="text-xs font-headline font-black mb-6 uppercase tracking-widest text-cyan-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">help_outline</span>
                        How to Pay
                    </h3>
                    @php
                        $steps = ($ipaymuData['Via'] ?? '') == 'QRIS'
                            ? [
                                'Buka aplikasi e-wallet (GoPay, DANA, OVO, dll).',
                                'Scan barcode yang tampil di layar.',
                                'Pastikan nominal sesuai, lalu masukkan PIN.',
                                'Simpan atau screenshot bukti pembayaran.'
                            ]
                            : [
                                'Login ke Mobile Banking / ATM Anda.',
                                'Pilih menu Transfer Virtual Account.',
                                'Masukkan nomor VA yang tertera.',
                                'Selesaikan pembayaran sebelum batas waktu.'
                            ];
                    @endphp
                    <div class="space-y-4">
                        @foreach($steps as $idx => $step)
                        <div class="flex gap-4 items-start">
                            <span class="flex-shrink-0 w-5 h-5 rounded-md bg-cyan-400/10 text-cyan-400 border border-cyan-400/20 flex items-center justify-center font-black text-[10px] mt-0.5">
                                {{ $idx + 1 }}
                            </span>
                            <p class="text-[11px] text-slate-400 font-medium leading-relaxed">{{ $step }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions Column -->
                <div class="space-y-4">
                    <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full flux-gradient text-slate-900 font-black py-4 rounded-xl flex items-center justify-center gap-3 active:scale-[0.98] transition-all neon-glow-primary uppercase tracking-widest text-[10px] no-underline">
                        <span class="material-symbols-outlined text-lg">sync_alt</span>
                        Update Payment Status
                    </a>

                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="window.print()" class="bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 active:scale-[0.98] transition-all uppercase tracking-widest text-[9px]">
                            <span class="material-symbols-outlined text-lg opacity-60">print</span>
                            Print
                        </button>
                        <form id="cancelOrderForm" action="{{ route('order.cancel', $order->order_id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmCancelOrder()" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 font-bold py-4 rounded-xl flex items-center justify-center gap-2 active:scale-[0.98] transition-all uppercase tracking-widest text-[9px]">
                                <span class="material-symbols-outlined text-lg opacity-60">cancel</span>
                                Cancel
                            </button>
                        </form>
                    </div>

                    @if(!empty($hostedUrl))
                        <a href="{{ $hostedUrl }}" target="_blank" class="w-full bg-cyan-400/10 hover:bg-cyan-400/20 border border-cyan-400/30 text-cyan-400 font-black py-3 rounded-xl flex items-center justify-center gap-2 transition-all uppercase tracking-[0.2em] text-[8px] no-underline">
                            <span class="material-symbols-outlined text-base">portal</span>
                            Gunakan Portal Cadangan iPaymu
                        </a>
                    @endif

                    <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/10">
                        <p class="text-[9px] text-amber-500/80 leading-relaxed font-medium italic text-center">
                            Jika pembayaran tidak diverifikasi otomatis dalam 5 menit, silakan hubungi Customer Service kami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'COPIED!',
                text: 'Payment number successfully copied.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#161a21',
                color: '#fff'
            });
        });
    }

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        var interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(interval);
                display.textContent = "00:00";
            }
        }, 1000);
    }

    function confirmCancelOrder() {
        Swal.fire({
            title: 'Batalkan Pembayaran?',
            text: "Pesanan yang dibatalkan tidak dapat diproses kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff716c',
            cancelButtonColor: '#1c2028',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tunggu',
            background: '#161a21',
            color: '#ecedf6'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelOrderForm').submit();
            }
        })
    }

    window.onload = function () {
        var fifteenMinutes = 60 * 15,
            display = document.querySelector('#countdown');
        if(display) startTimer(fifteenMinutes, display);
    };

    (function () {
        var pollUrl = @json(route('order.poll', ['order_id' => $order->order_id]));
        var successUrl = @json(route('topup.success', ['order_id' => $order->order_id]));
        var trackUrl = @json(route('track.order', ['order_id' => $order->order_id]));
        var intervalMs = 4000;
        var timer = null;
        function poll() {
            fetch(pollUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (j) {
                    if (!j || !j.success || !j.data) return;
                    var st = j.data.status;
                    if (st === 'success') {
                        if (timer) clearInterval(timer);
                        window.location.href = successUrl;
                        return;
                    }
                    if (st === 'failed' || st === 'failed_permanent') {
                        if (timer) clearInterval(timer);
                        window.location.href = trackUrl;
                    }
                })
                .catch(function () {});
        }
        setTimeout(poll, 1200);
        timer = setInterval(poll, intervalMs);
    })();
</script>
@endpush
@endsection
