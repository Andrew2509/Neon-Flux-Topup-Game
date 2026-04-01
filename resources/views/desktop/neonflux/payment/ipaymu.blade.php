@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    /* Custom Design Tokens from User Profile */
    :root {
        --primary: #99f7ff;
        --secondary: #c47fff;
        --background: #0b0e14;
        --surface: #161a21;
    }

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
    .font-headline { font-family: 'Space Grotesk', sans-serif; }
    .font-body { font-family: 'Manrope', sans-serif; }
    .font-label { font-family: 'Inter', sans-serif; }

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>
@endpush

@section('content')
<div class="relative bg-background text-on-surface font-body min-h-screen pt-32 pb-20 px-4 md:px-8">
    <!-- Ambient Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/5 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-secondary/5 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <main class="relative z-10 max-w-7xl mx-auto w-full">
        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Payment QR / VA & Summary -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-2xl p-8 flex flex-col items-center text-center relative overflow-hidden shadow-2xl">
                    <!-- Ambient Glow inside card -->
                    <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/10 blur-[80px] rounded-full"></div>
                    
                    <div class="mb-6 flex items-center justify-between w-full">
                        <span class="bg-primary/10 text-primary text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-widest font-label border border-primary/20">
                            @if(($ipaymuData['Via'] ?? '') == 'QRIS') Scan to Pay @else Virtual Account @endif
                        </span>
                        <div class="flex items-center gap-2 text-error font-mono font-bold text-lg">
                            <span class="material-symbols-outlined text-sm">timer</span>
                            <span id="countdown">15:00</span>
                        </div>
                    </div>

                    @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                        <!-- QR Display -->
                        <div class="relative p-5 bg-white rounded-2xl shadow-[0_0_50px_rgba(153,247,255,0.15)] mb-8 group overflow-hidden">
                            <img alt="Payment QR Code" src="{{ $qrUrl ?? '' }}" class="w-48 h-48 md:w-56 md:h-56 object-contain relative z-10">
                            <div class="absolute inset-0 border-4 border-primary/10 rounded-2xl pointer-events-none"></div>
                            
                            <!-- QR Fallback Link -->
                            @if($qrUrl)
                            <div class="absolute inset-0 bg-white/90 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20 px-4">
                                <a href="{{ $qrUrl }}" target="_blank" class="text-[10px] text-slate-800 font-bold flex flex-col items-center gap-2 text-center text-decoration-none">
                                    <span class="material-symbols-outlined text-xl">open_in_new</span>
                                    Gagal scan? Klik untuk lihat gambar manual
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <!-- VA Display -->
                        <div class="w-full bg-white/5 border border-white/10 rounded-2xl p-8 mb-8 text-center group active:scale-[0.98] transition-transform cursor-pointer" onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Nomor Virtual Account</p>
                            <h2 class="text-3xl md:text-4xl font-headline font-bold text-white tracking-widest mb-4 group-hover:text-primary transition-colors">
                                {{ $ipaymuData['PaymentNo'] ?? '---' }}
                            </h2>
                            <div class="inline-flex items-center gap-2 text-[10px] text-primary font-bold uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm">content_copy</span>
                                Klik untuk Salin Nomor
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-[1px] w-12 bg-primary/20"></div>
                        <span class="text-on-surface-variant font-label text-[10px] uppercase tracking-[0.2em] font-medium opacity-60">
                            {{ $ipaymuData['PaymentName'] ?? 'iPaymu Gateway' }}
                        </span>
                        <div class="h-[1px] w-12 bg-primary/20"></div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-on-surface-variant text-[10px] font-bold uppercase tracking-widest opacity-60">Total Pembayaran</p>
                        <h2 class="text-4xl md:text-5xl font-headline font-bold text-primary tracking-tighter">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6 border-l-4 border-secondary shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="p-2.5 rounded-xl bg-secondary/10 text-secondary">
                            <span class="material-symbols-outlined text-xl">info</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm mb-1 font-headline">Informasi Penting</h4>
                            <p class="text-xs text-on-surface-variant leading-relaxed opacity-80">
                                Mohon selesaikan pembayaran sebelum timer berakhir. Pastikan nominal yang Anda transfer tepat hingga digit terakhir agar sistem dapat memproses secara otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Instructions -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Transaction Details Card -->
                <div class="glass-card rounded-2xl p-8 relative overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-headline font-bold flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">receipt_long</span>
                            Detail Transaksi
                        </h3>
                        <span class="bg-secondary/10 text-secondary text-[10px] px-3 py-1.5 rounded-full font-black tracking-widest border border-secondary/20">PENDING</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-10">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest opacity-50">ID Pesanan</span>
                            <span class="font-mono text-primary font-bold tracking-wide text-sm">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest opacity-50">Produk</span>
                            <span class="text-sm font-bold text-white">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest opacity-50">Status</span>
                            <span class="text-sm font-bold flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse shadow-[0_0_10px_rgba(196,127,255,0.5)]"></span>
                                Menunggu Pembayaran
                            </span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest opacity-50">Metode Pembayaran</span>
                            <span class="text-sm font-bold italic text-white/90">{{ $ipaymuData['PaymentName'] ?? 'QRIS' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps Card -->
                <div class="glass-card rounded-2xl p-8 shadow-xl">
                    <h3 class="text-lg font-headline font-bold mb-8 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                        Instruksi Pembayaran
                    </h3>
                    
                    <div class="space-y-6">
                        @php
                            $steps = ($ipaymuData['Via'] ?? '') == 'QRIS'
                                ? [
                                    ['label' => 'Buka Aplikasi', 'desc' => 'Buka aplikasi e-Wallet (GoPay, OVO, Dana) atau m-Banking pilihan Anda.'],
                                    ['label' => 'Scan QR Code', 'desc' => "Pilih menu 'Scan' atau 'Bayar' dan arahkan kamera ponsel Anda ke Kode QR di samping."],
                                    ['label' => 'Konfirmasi & Bayar', 'desc' => 'Periksa detail transaksi, masukkan PIN, dan bayar. Status akan terupdate otomatis.']
                                ]
                                : [
                                    ['label' => 'Salin Nomor VA', 'desc' => 'Salin nomor Virtual Account yang tertera di samping.'],
                                    ['label' => 'Pilih Menu Transfer', 'desc' => 'Buka m-Banking Anda, pilih menu Transfer Antar Bank / VA sesuai tujuan.'],
                                    ['label' => 'Input & Bayar', 'desc' => 'Input nomor VA, pastikan nominal sesuai, dan selesaikan transaksi.']
                                ];
                        @endphp

                        @foreach($steps as $idx => $step)
                        <div class="flex gap-5 group">
                            <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-headline font-bold text-sm group-hover:bg-primary group-hover:text-on-primary transition-all duration-300">
                                {{ $idx + 1 }}
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-on-surface group-hover:text-primary transition-colors">{{ $step['label'] }}</p>
                                <p class="text-xs text-on-surface-variant leading-relaxed opacity-70">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-8 border-t border-white/5 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="flex-1 flux-gradient text-on-primary font-black py-4 px-6 rounded-xl flex items-center justify-center gap-2 active:scale-[0.97] transition-all neon-glow-primary uppercase tracking-widest text-xs decoration-none">
                            <span class="material-symbols-outlined text-lg">sync</span>
                            Cek Status Transaksi
                        </a>
                        <button onclick="window.print()" class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 text-on-surface font-black py-4 px-6 rounded-xl flex items-center justify-center gap-2 active:scale-[0.97] transition-all uppercase tracking-widest text-xs">
                            <span class="material-symbols-outlined text-lg">print</span>
                            Cetak Detail
                        </button>
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
                title: 'Disalin!',
                text: 'Nomor pembayaran telah disalin.',
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

    window.onload = function () {
        var fifteenMinutes = 60 * 15,
            display = document.querySelector('#countdown');
        if(display) startTimer(fifteenMinutes, display);
    };
</script>
@endpush
@endsection
