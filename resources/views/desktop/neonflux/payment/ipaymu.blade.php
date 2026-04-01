@extends('desktop.layouts.user')

@section('title', 'Pembayaran #' . $pay->order_id)

@section('content')
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
</style>

<div class="relative min-h-screen py-12 px-4 md:px-8 flex flex-col items-center overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-4xl z-10">
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Payment QR & Summary -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-xl p-8 flex flex-col items-center text-center relative overflow-hidden">
                    <!-- Ambient Glow -->
                    <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/10 blur-[80px] rounded-full"></div>
                    
                    <div class="mb-4 flex items-center justify-between w-full">
                        <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                            @if($pay->payment_method == 'QRIS') Scan to Pay @else Virtual Account @endif
                        </span>
                        <div class="flex items-center gap-2 text-red-500 font-mono font-bold text-lg">
                            <span class="material-symbols-outlined text-sm">timer</span>
                            <span id="countdown">15:00</span>
                        </div>
                    </div>

                    @if($pay->payment_method == 'QRIS')
                        <div class="relative p-4 bg-white rounded-lg shadow-[0_0_40px_rgba(153,247,255,0.15)] mb-6">
                            <img src="{{ $qrUrl ?? '#' }}" alt="QRIS" class="w-48 h-48 md:w-56 md:h-56">
                            <div class="absolute inset-0 border-2 border-primary/20 rounded-lg pointer-events-none"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mb-6">
                             Klik <a href="{{ $qrUrl }}" target="_blank" class="text-primary underline">di sini</a> jika barcode tidak muncul.
                        </p>
                    @else
                        <!-- VA Display -->
                        <div class="glass-card w-full p-6 rounded-lg mb-6 border border-primary/20 bg-white/5">
                            <p class="text-xs uppercase text-slate-400 mb-2">Nomor Virtual Account</p>
                            <h3 class="text-3xl font-bold text-primary tracking-wider">{{ $pay->va_number ?? '-' }}</h3>
                            <button onclick="copyToClipboard('{{ $pay->va_number }}')" class="mt-4 px-4 py-2 bg-primary/10 rounded-lg text-xs text-primary hover:bg-primary/20 flex items-center gap-2 mx-auto transition-all">
                                <span class="material-symbols-outlined text-sm">content_copy</span> Copy VA Number
                            </button>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-[2px] w-8 bg-primary/30"></div>
                        <span class="text-slate-400 text-sm uppercase tracking-widest">iPaymu Gateway</span>
                        <div class="h-[2px] w-8 bg-primary/30"></div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-slate-400 text-sm uppercase tracking-tighter">Total Pembayaran</p>
                        <h2 class="text-4xl md:text-5xl font-bold text-primary tracking-tighter">Rp {{ number_format($pay->price, 0, ',', '.') }}</h2>
                    </div>
                </div>

                <div class="glass-card rounded-xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-lg bg-purple-500/10 text-purple-500">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white mb-1">Informasi Penting</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Mohon lakukan pembayaran sebelum timer berakhir. Pastikan nominal yang Anda transfer tepat hingga digit terakhir agar otomatis terproses oleh sistem.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Instructions -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Transaction Details Card -->
                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">receipt_long</span>
                            Detail Transaksi
                        </h3>
                        <span class="bg-primary/20 text-primary text-[10px] px-3 py-1 rounded-full font-bold">
                            {{ strtoupper($pay->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-left">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-slate-400 uppercase">Order ID</span>
                            <span class="font-mono text-primary font-medium tracking-wide">{{ $pay->order_id }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-slate-400 uppercase">Produk</span>
                            <span class="font-medium text-white">{{ $pay->item_name ?? 'Top Up Games' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-slate-400 uppercase">Status</span>
                            <span class="font-medium flex items-center gap-1 text-white">
                                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                Menunggu Pembayaran
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-slate-400 uppercase">Metode</span>
                            <span class="font-medium text-white italic">{{ $pay->payment_method }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps Card -->
                <div class="glass-card rounded-xl p-6 overflow-hidden">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                        Instruksi Pembayaran
                    </h3>
                    <div class="space-y-6 text-left">
                        @if($pay->payment_method == 'QRIS')
                        <div class="flex gap-4 group">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/30 flex items-center justify-center font-bold">1</div>
                            <div class="space-y-1">
                                <p class="font-medium text-white">Buka Aplikasi Pembayaran</p>
                                <p class="text-xs text-slate-400">Buka aplikasi E-Wallet (Dana, OVO, GoPay) atau m-banking Anda.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 group">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/30 flex items-center justify-center font-bold">2</div>
                            <div class="space-y-1">
                                <p class="font-medium text-white">Scan Kode QR</p>
                                <p class="text-xs text-slate-400">Pilih menu 'Scan' atau 'Bayar' dan arahkan kamera ke Kode QR di samping.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 group">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/30 flex items-center justify-center font-bold">3</div>
                            <div class="space-y-1">
                                <p class="font-medium text-white">Konfirmasi & Bayar</p>
                                <p class="text-xs text-slate-400">Pastikan nominal sesuai, masukkan PIN, dan konfirmasi pembayaran.</p>
                            </div>
                        </div>
                        @else
                        <div class="flex gap-4 group">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/30 flex items-center justify-center font-bold">1</div>
                            <div class="space-y-1">
                                <p class="font-medium text-white">Salin Nomor VA</p>
                                <p class="text-xs text-slate-400">Salin nomor Virtual Account yang tertera di samping.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 group">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/30 flex items-center justify-center font-bold">2</div>
                            <div class="space-y-1">
                                <p class="font-medium text-white">Lakukan Transfer</p>
                                <p class="text-xs text-slate-400">Gunakan ATM, m-Banking, atau i-Banking untuk transfer ke nomor tersebut.</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row gap-4">
                        <button onclick="location.reload()" class="flex-1 flux-gradient text-black font-bold py-3 px-6 rounded-lg flex items-center justify-center gap-2 active:scale-95 transition-transform neon-glow-primary">
                            <span class="material-symbols-outlined">sync</span>
                            Cek Status Pembayaran
                        </button>
                        <button onclick="window.print()" class="flex-1 bg-white/5 border border-white/10 text-white font-bold py-3 px-6 rounded-lg flex items-center justify-center gap-2 active:scale-95 transition-transform hover:bg-white/10">
                            <span class="material-symbols-outlined">print</span>
                            Cetak Bukti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Nomor VA berhasil disalin!');
        });
    }

    let time = 15 * 60; 
    const display = document.getElementById('countdown');
    if(display) {
        setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            display.innerHTML = `${minutes}:${seconds}`;
            if (time > 0) time--;
        }, 1000);
    }
</script>
@endsection
