@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4 pt-24 pb-12 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-80 bg-primary/10 blur-[100px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-64 bg-cyan-500/10 blur-[100px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-4xl z-10">
        <!-- Dashboard Compact Wrapper -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Main Content (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="glass-panel p-6 rounded-3xl border border-white/5 shadow-2xl relative overflow-hidden">
                    <!-- Compact Header -->
                    <div class="flex items-center gap-4 mb-6 border-b border-white/5 pb-5">
                        <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 shrink-0">
                            @php $via = strtolower($ipaymuData['Via'] ?? ''); @endphp
                            @if($via == 'qris')
                                <span class="material-icons-round text-2xl">qr_code_2</span>
                            @else
                                <span class="material-icons-round text-2xl">account_balance</span>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-white tracking-tight">
                                {{ $ipaymuData['PaymentName'] ?? 'Selesaikan Pembayaran' }}
                            </h1>
                            <p class="text-[11px] text-slate-400 font-medium">Ikuti langkah berikut untuk proses instan.</p>
                        </div>
                    </div>

                    @if($via == 'qris')
                        <!-- QR Code Compact -->
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex flex-col items-center justify-center bg-white rounded-3xl p-5 shadow-xl relative shrink-0">
                                <div class="absolute -top-3 bg-primary text-slate-900 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-tighter z-20">
                                    Scan QRIS
                                </div>
                                <div class="relative size-40 overflow-hidden rounded-lg">
                                    <img src="{{ $qrUrl ?? '' }}" alt="QRIS" class="w-full h-full object-contain">
                                </div>
                                @if($qrUrl)
                                    <a href="{{ $qrUrl }}" target="_blank" class="mt-3 text-[9px] text-slate-400 hover:text-primary transition-colors font-bold flex items-center gap-1">
                                        <span class="material-icons-round text-[10px]">open_in_new</span>
                                        Klik jika error
                                    </a>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-4 rounded-2xl">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-6">
                                    <div class="h-6 w-px bg-white/10"></div>
                                    <p class="text-[10px] text-slate-400 font-medium leading-normal">
                                        Buka aplikasi m-Banking atau E-Wallet pilihan Anda untuk mulai scan.
                                    </p>
                                </div>
                                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-4">
                                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Total Bayar</p>
                                    <p class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- VA Compact -->
                        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 text-center">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Pembayaran / VA</p>
                            <div class="flex flex-col items-center gap-4">
                                <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter">{{ $ipaymuData['PaymentNo'] ?? '' }}</h2>
                                <button onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')" class="px-6 h-11 rounded-1.5xl bg-primary text-slate-950 flex items-center justify-center gap-2 font-black text-xs shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                                    <span class="material-icons-round text-sm">content_copy</span>
                                    <span>Salin VA</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Instructions Compact -->
                <div class="glass-panel p-6 rounded-3xl border border-white/5 shadow-2xl relative">
                    <h3 class="text-white font-black text-xs uppercase tracking-widest mb-4">Cara Pembayaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $steps = $via == 'qris' 
                                ? ['Screenshot QR di atas', 'Buka aplikasi e-Wallet', 'Scan/Upload gambar QR']
                                : ['Salin nomor VA di atas', 'Transfer via ATM/m-Banking', 'Simpan bukti bayar'];
                        @endphp
                        @foreach($steps as $idx => $step)
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-lg bg-white/5 text-[10px] text-white flex items-center justify-center font-black border border-white/10 shrink-0">
                                {{ $idx + 1 }}
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium leading-tight">
                                {{ $step }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (4 Cols) -->
            <div class="lg:col-span-4 space-y-4 sticky top-24">
                <!-- Status/Timer Compact -->
                <div class="glass-panel p-6 rounded-3xl border border-white/5 shadow-2xl text-center">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Batas Waktu</p>
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <span class="material-icons-round text-primary text-sm animate-pulse">timer</span>
                        <div id="countdown" class="text-2xl font-black text-white tracking-widest font-mono">15:00</div>
                    </div>
                    <div class="flex items-center justify-center gap-2 py-2 px-4 bg-yellow-400/10 border border-yellow-400/20 rounded-full">
                        <div class="size-1.5 rounded-full bg-yellow-400 animate-ping"></div>
                        <span class="text-[9px] font-black text-yellow-400 uppercase tracking-widest">Menunggu Bayar</span>
                    </div>
                </div>

                <!-- Detil Dashboard -->
                <div class="glass-panel p-6 rounded-3xl border border-white/5 shadow-2xl space-y-4">
                    <h4 class="text-white font-black text-[10px] uppercase tracking-widest pb-3 border-b border-white/5">Order Detail</h4>
                    <div class="space-y-3 text-[10px]">
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">ORDER ID</span>
                            <span class="text-white font-black text-right truncate max-w-[120px] font-mono">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-slate-500 font-bold uppercase tracking-tighter">PRODUK</span>
                            <span class="text-white font-black text-right line-clamp-2 leading-tight">{{ $order->product_name }}</span>
                        </div>
                    </div>

                    <div class="pt-4 space-y-2">
                        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full bg-primary text-slate-950 font-black text-[10px] py-3 rounded-1.5xl shadow-lg shadow-primary/10 hover:shadow-primary/20 transition-all flex items-center justify-center gap-2 uppercase tracking-wide">
                            <span class="material-icons-round text-sm">history</span>
                            <span>Cek Status</span>
                        </a>
                        <button onclick="window.print()" class="w-full bg-white/5 text-slate-500 font-black text-[9px] py-2.5 rounded-1.5xl hover:text-white transition-all uppercase tracking-wide flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">print</span>
                            <span>Print</span>
                        </button>
                    </div>
                </div>
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
                title: 'VA Disalin!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
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
