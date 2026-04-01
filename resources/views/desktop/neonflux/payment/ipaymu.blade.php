@extends('desktop.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[90vh] flex flex-col items-center justify-center px-4 pt-32 pb-20 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-5xl z-10">
        <!-- Dashboard Wrapper -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Content (Left: 8 Cols) -->
            <div class="lg:col-span-12 xl:col-span-8 space-y-6">
                <div class="glass-panel p-8 md:p-10 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
                    <!-- Header Mobile-Style but wider -->
                    <div class="flex flex-col md:flex-row items-center gap-6 mb-10 border-b border-white/5 pb-8">
                        <div class="size-20 rounded-2xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 shadow-lg shadow-primary/5 shrink-0">
                            @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                                <span class="material-icons-round text-4xl">qr_code_2</span>
                            @elseif(($ipaymuData['Channel'] ?? '') == 'VA')
                                <span class="material-icons-round text-4xl">account_balance</span>
                            @else
                                <span class="material-icons-round text-4xl">payments</span>
                            @endif
                        </div>
                        <div class="text-center md:text-left space-y-1">
                            <h1 class="text-3xl font-black text-white tracking-tight">
                                {{ $ipaymuData['PaymentName'] ?? 'Selesaikan Pembayaran' }}
                            </h1>
                            <p class="text-slate-400 font-medium">Ikuti langkah di bawah untuk memproses pesanan Anda secara otomatis.</p>
                        </div>
                    </div>

                    @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                        <!-- QR Code Display -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                            <div class="flex flex-col items-center justify-center bg-white rounded-4xl p-8 shadow-2xl relative group">
                                <div class="absolute -top-4 bg-primary text-slate-900 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-primary/20 z-20">
                                    Auto Scan QRIS
                                </div>
                                
                                <div class="relative w-full max-w-[240px] aspect-square overflow-hidden rounded-xl border-4 border-slate-50">
                                    <img src="{{ $qrUrl ?? '' }}" alt="QRIS Payment" class="w-full h-full object-contain">
                                </div>
                                
                                @if($qrUrl)
                                <div class="mt-4 text-center">
                                    <a href="{{ $qrUrl }}" target="_blank" class="text-[10px] text-slate-400 hover:text-primary transition-colors flex items-center justify-center gap-1 font-bold">
                                        <span class="material-icons-round text-xs">open_in_new</span>
                                        Barcode tidak muncul? Klik di sini
                                    </a>
                                </div>
                                @endif
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center gap-4 bg-white/5 border border-white/10 p-5 rounded-3xl">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-8">
                                    <div class="h-8 w-px bg-white/10"></div>
                                    <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                        Scan QR di samping menggunakan aplikasi m-Banking atau E-Wallet pilihan Anda.
                                    </p>
                                </div>
                                <div class="space-y-4">
                                    <h3 class="text-white font-black text-xs uppercase tracking-widest flex items-center gap-2">
                                        <span class="size-5 rounded-full bg-primary text-slate-950 flex items-center justify-center text-[10px]">!</span>
                                        Penting
                                    </h3>
                                    <ul class="text-[11px] text-slate-400 space-y-2 list-disc list-inside">
                                        <li>Pastikan nominal yang muncul di aplikasi sesuai</li>
                                        <li>Jangan tutup halaman ini sebelum status berubah</li>
                                        <li>Pesanan diproses instan setelah scan berhasil</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- VA / Payment Number Display -->
                        <div class="bg-white/5 border border-white/5 rounded-4xl p-10 text-center relative group">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Nomor Pembayaran / VA</p>
                            <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                                <h2 id="paymentCode" class="text-5xl md:text-6xl font-black text-white tracking-tighter">{{ $ipaymuData['PaymentNo'] ?? '' }}</h2>
                                <button onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')" class="px-8 h-14 rounded-2xl bg-primary text-slate-950 flex items-center justify-center gap-3 font-black shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                                    <span class="material-icons-round">content_copy</span>
                                    <span>Salin VA</span>
                                </button>
                            </div>
                            <p class="mt-6 text-sm font-bold text-primary uppercase tracking-widest border-t border-white/5 pt-6 inline-block">
                                {{ $ipaymuData['PaymentName'] ?? 'Virtual Account' }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Instructions Dashboard -->
                <div class="glass-panel p-8 rounded-[2.5rem] border border-white/5 shadow-2xl relative">
                    <h3 class="text-white font-black text-lg uppercase tracking-widest mb-6">Instruksi Pembayaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $steps = ($ipaymuData['Via'] ?? '') == 'QRIS' 
                                ? ['Simpan atau Screenshot kode QR di atas', 'Buka aplikasi e-Wallet atau m-Banking pilihan Anda', 'Pilih menu SCAN/QRIS dan unggah gambar kode QR']
                                : ['Salin nomor Virtual Account yang tertera', 'Lakukan transfer via ATM atau m-Banking sesuai bank tujuan', 'Simpan struk jika perlu, status akan terupdate otomatis'];
                        @endphp
                        @foreach($steps as $idx => $step)
                        <div class="relative group">
                            <div class="size-10 rounded-xl bg-white/5 text-white flex items-center justify-center mb-4 font-black border border-white/10 group-hover:bg-primary group-hover:text-slate-950 transition-all duration-500">
                                {{ $idx + 1 }}
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">
                                {{ $step }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right: 4 Cols) -->
            <div class="lg:col-span-12 xl:col-span-4 space-y-6">
                <!-- Status & Timer Card -->
                <div class="glass-panel p-8 rounded-[2.5rem] border border-white/5 shadow-2xl text-center space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Batas Waktu</p>
                        <div class="flex items-center justify-center gap-3">
                            <span class="material-icons-round text-primary animate-pulse">timer</span>
                            <div id="countdown" class="text-4xl font-black text-white tracking-widest font-mono">15:00</div>
                        </div>
                    </div>
                    <div class="bg-primary/5 border border-primary/10 rounded-2xl p-4">
                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Total yang harus dibayar</p>
                        <p class="text-3xl font-black text-white tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Mini Order Summary -->
                <div class="glass-panel p-8 rounded-[2.5rem] border border-white/5 shadow-2xl space-y-6">
                    <h4 class="text-white font-black text-sm uppercase tracking-widest mb-4 border-b border-white/5 pb-4">Detil Transaksi</h4>
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-500 font-bold shrink-0">ORDER ID</span>
                            <span class="text-white font-black text-right break-all font-mono">{{ $order->order_id }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-500 font-bold shrink-0">PRODUK</span>
                            <span class="text-white font-black text-right leading-tight">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-500 font-bold shrink-0">STATUS</span>
                            <div class="flex items-center gap-2 text-yellow-400 font-black">
                                <div class="size-2 rounded-full bg-yellow-400 animate-ping"></div>
                                <span>Menunggu Bayar</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 grid grid-cols-1 gap-3">
                        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-full bg-white/5 text-white font-black text-[11px] py-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                            <span class="material-icons-round text-sm">history</span>
                            <span>Cek Status Pesanan</span>
                        </a>
                        <button onclick="window.print()" class="w-full bg-white/5 text-slate-400 font-black text-[11px] py-3 rounded-2xl hover:text-white transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                            <span class="material-icons-round text-sm">print</span>
                            <span>Cetak Detail</span>
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
