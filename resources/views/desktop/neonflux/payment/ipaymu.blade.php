@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@push('styles')
<!-- Primary Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

<!-- Tailwind Play CDN for Runtime Integration (to support custom colors provided by User) -->
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
        background: rgba(34, 38, 47, 0.4);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(153, 247, 255, 0.1);
    }
    .neon-glow-primary {
        box-shadow: 0 0 15px rgba(0, 241, 254, 0.3);
    }
    .flux-gradient {
        background: linear-gradient(135deg, #99f7ff 0%, #00f1fe 100%);
    }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    /* Override layout defaults to prevent overlap */
    #app, .main-layout { background: transparent !important; }
</style>
@endpush

@section('content')
<div class="relative bg-[#0b0e14] text-[#ecedf6] font-body min-h-screen pt-40 pb-20 px-4 md:px-8 overflow-hidden">
    <!-- Ambient Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-cyan-500/5 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-purple-500/5 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <main class="relative z-10 max-w-5xl mx-auto w-full">
        <!-- Dashboard Wrapper -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Payment QR / VA -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-[2rem] p-8 flex flex-col items-center text-center relative overflow-hidden shadow-2xl">
                    <!-- Glow effect -->
                    <div class="absolute -top-24 -left-24 w-48 h-48 bg-cyan-400/10 blur-[80px] rounded-full"></div>
                    
                    <div class="mb-8 flex items-center justify-between w-full">
                        <span class="bg-cyan-400/10 text-cyan-400 text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-[0.2em] font-label border border-cyan-400/20">
                            @if(($ipaymuData['Via'] ?? '') == 'QRIS') Scan to Pay @else Virtual Account @endif
                        </span>
                        <div class="flex items-center gap-2 text-rose-500 font-mono font-bold text-xl">
                            <span class="material-symbols-outlined text-base">timer</span>
                            <span id="countdown">15:00</span>
                        </div>
                    </div>

                    @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                        <!-- QR Display -->
                        <div class="relative p-6 bg-white rounded-3xl shadow-[0_0_60px_rgba(153,247,255,0.1)] mb-10 group overflow-hidden flex items-center justify-center">
                            @if(!empty($qrUrl))
                                <img alt="Payment QR Code" src="{{ $qrUrl }}" class="w-52 h-52 md:w-64 md:h-64 object-contain relative z-10">
                                <div class="absolute inset-0 border-4 border-cyan-400/5 rounded-3xl pointer-events-none"></div>
                                
                                <div class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 px-6">
                                    <a href="{{ $qrUrl }}" target="_blank" class="text-[11px] text-slate-800 font-bold flex flex-col items-center gap-3 text-center no-underline">
                                        <span class="material-symbols-outlined text-2xl text-cyan-600">open_in_new</span>
                                        <span>Gagal scan? Klik untuk lihat gambar manual</span>
                                    </a>
                                </div>
                            @else
                                <div class="w-52 h-52 md:w-64 md:h-64 flex flex-col items-center justify-center text-slate-400 gap-4 bg-slate-50 rounded-2xl">
                                    <span class="material-symbols-outlined text-5xl animate-pulse">qr_code_2</span>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-center px-4">
                                        QR Code Sedang Diproses...<br>
                                        <span class="text-[8px] opacity-60">Silakan Tunggu atau Hubungi CS</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- VA Display -->
                        <div class="w-full bg-white/5 border border-white/10 rounded-3xl p-10 mb-10 text-center group hover:border-cyan-400/30 transition-all cursor-pointer" onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Nomor Virtual Account</p>
                            <h2 class="text-3xl md:text-5xl font-headline font-black text-white tracking-[0.1em] mb-6 group-hover:text-cyan-400 transition-colors">
                                {{ $ipaymuData['PaymentNo'] ?? '---' }}
                            </h2>
                            <div class="inline-flex items-center gap-2 text-[10px] text-cyan-400 font-bold uppercase tracking-widest bg-cyan-400/10 px-4 py-2 rounded-full border border-cyan-400/20">
                                <span class="material-symbols-outlined text-sm">content_copy</span>
                                Click to Copy VA Number
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4 mb-10 w-full">
                        <div class="h-px flex-grow bg-white/10"></div>
                        <span class="text-slate-500 font-label text-[10px] uppercase tracking-[0.3em] font-black italic">
                            {{ $ipaymuData['PaymentName'] ?? 'iPaymu Gateway' }}
                        </span>
                        <div class="h-px flex-grow bg-white/10"></div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-widest">Total Bayar</p>
                        <h2 class="text-5xl md:text-6xl font-headline font-black text-cyan-400 tracking-tighter">
                            <span class="text-2xl font-medium mr-1 text-cyan-400/60">Rp</span>{{ number_format($order->total_price, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border-l-4 border-purple-500 shadow-2xl">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-2xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                            <span class="material-symbols-outlined text-2xl">info</span>
                        </div>
                        <div>
                            <h4 class="font-black text-white text-sm mb-1 font-headline uppercase tracking-wide">Informasi Penting</h4>
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">
                                Selesaikan pembayaran sebelum timer berakhir. Pastikan nominal transfer pas hingga digit terakhir agar sistem dapat memproses secara otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Instructions -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Transaction Details Card -->
                <div class="glass-card rounded-[2rem] p-10 relative overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xl font-headline font-black flex items-center gap-4 uppercase tracking-tight text-white">
                            <span class="material-symbols-outlined text-cyan-400 text-3xl">receipt_long</span>
                            Detail Pesanan
                        </h3>
                        <span class="bg-amber-500/10 text-amber-500 text-[10px] px-4 py-2 rounded-full font-black tracking-widest border border-amber-500/20 shadow-lg shadow-amber-500/5 animate-pulse">PENDING</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-12">
                        <div class="flex flex-col gap-2">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Order Identifier</span>
                            <span class="font-mono text-cyan-400 font-black tracking-widest text-base">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Package Item</span>
                            <span class="text-sm font-black text-white uppercase tracking-tight">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Process Status</span>
                            <span class="text-sm font-black flex items-center gap-3 text-white">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500"></span>
                                </span>
                                Menunggu Gateway
                            </span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Channel Payment</span>
                            <span class="text-sm font-black text-cyan-400 italic uppercase">{{ $ipaymuData['PaymentName'] ?? 'QRIS' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps Card -->
                <div class="glass-card rounded-[2rem] p-10 shadow-2xl">
                    <h3 class="text-xl font-headline font-black mb-10 flex items-center gap-4 uppercase tracking-tight text-white">
                        <span class="material-symbols-outlined text-cyan-400 text-3xl">account_balance_wallet</span>
                        Langkah Pembayaran
                    </h3>
                    
                    <div class="space-y-8">
                        @php
                            $steps = ($ipaymuData['Via'] ?? '') == 'QRIS'
                                ? [
                                    ['label' => 'Buka Aplikasi Pembayaran', 'desc' => 'Gunakan aplikasi GoPay, Dana, OVO, atau aplikasi m-Banking kesayangan Anda.'],
                                    ['label' => 'Scan & Verifikasi', 'desc' => "Arahkan scanner ke Kode QR di samping, pastikan nama 'PrincePay' atau 'iPaymu' muncul."],
                                    ['label' => 'Selesaikan Transaksi', 'desc' => 'Masukkan PIN Anda dan jangan tutup halaman ini sampai status transaksi berhasil.']
                                ]
                                : [
                                    ['label' => 'Salin VA Number', 'desc' => 'Klik pada nomor VA di samping untuk menyalin secara otomatis.'],
                                    ['label' => 'Transfer via Bank', 'desc' => 'Gunakan menu Transfer VA / Antar Bank pada ATM atau Aplikasi m-Banking Anda.'],
                                    ['label' => 'Konfirmasi Bayar', 'desc' => 'Lakukan pembayaran sesuai nominal tepat. Pesanan akan aktif secara otomatis.']
                                ];
                        @endphp

                        @foreach($steps as $idx => $step)
                        <div class="flex gap-6 group">
                            <div class="flex-shrink-0 w-10 h-10 rounded-2xl bg-cyan-400/10 text-cyan-400 border border-cyan-400/20 flex items-center justify-center font-headline font-black text-lg group-hover:bg-cyan-400 group-hover:text-slate-900 transition-all duration-500 shadow-xl shadow-cyan-400/5">
                                {{ $idx + 1 }}
                            </div>
                            <div class="space-y-2">
                                <p class="text-base font-black text-white group-hover:text-cyan-400 transition-colors uppercase tracking-tight">{{ $step['label'] }}</p>
                                <p class="text-xs text-slate-400 leading-relaxed font-medium opacity-80">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-12 pt-10 border-t border-white/5 flex flex-col sm:flex-row gap-6">
                        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="flex-1 flux-gradient text-slate-900 font-black py-5 px-8 rounded-2xl flex items-center justify-center gap-3 active:scale-[0.97] transition-all neon-glow-primary uppercase tracking-[0.2em] text-xs no-underline">
                            <span class="material-symbols-outlined text-xl">sync</span>
                            Check Order Update
                        </a>
                        <button onclick="window.print()" class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-black py-5 px-8 rounded-2xl flex items-center justify-center gap-3 active:scale-[0.97] transition-all uppercase tracking-[0.2em] text-xs">
                            <span class="material-symbols-outlined text-xl">print</span>
                            Print Summary
                        </button>
                        <form id="cancelOrderForm" action="{{ route('order.cancel', $order->order_id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="button" onclick="confirmCancelOrder()" class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-slate-950 border border-red-500/20 font-black py-5 px-8 rounded-2xl flex items-center justify-center gap-3 active:scale-[0.97] transition-all uppercase tracking-[0.2em] text-xs">
                                <span class="material-symbols-outlined text-xl">cancel</span>
                                Cancel Payment
                            </button>
                        </form>
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
