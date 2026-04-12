@extends('hp.layouts.neonflux')

@section('title', 'Pembayaran ' . ($ipaymuData['PaymentName'] ?? 'Pesanan') . ' - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="px-5 pt-28 pb-10 min-h-screen relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-10 size-64 bg-primary/10 blur-[80px] rounded-full"></div>
    <div class="absolute bottom-1/4 -right-10 size-48 bg-cyan-500/10 blur-[80px] rounded-full"></div>

    <div class="w-full z-10 space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-primary/10 text-primary mb-2 border border-primary/20 shadow-lg">
                @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                    <span class="material-icons-round text-2xl">qr_code_scanner</span>
                @elseif(($ipaymuData['Channel'] ?? '') == 'VA')
                    <span class="material-icons-round text-2xl">account_balance</span>
                @else
                    <span class="material-icons-round text-2xl">payments</span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-white px-2">Selesaikan Pembayaran</h1>
            <p class="text-[10px] text-slate-400 font-medium px-6 leading-relaxed uppercase tracking-widest">{{ $ipaymuData['PaymentName'] ?? 'Pembayaran' }}</p>
        </div>

        <!-- Main Card -->
        <div class="glass-panel p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden text-center">
            <div class="flex items-center justify-between mb-6 px-1">
                <div class="text-left font-black tracking-tight">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest leading-none mb-1">Tagihan</p>
                    <p class="text-xl text-primary leading-none">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right font-black tracking-tight">
                    <p class="text-[9px] text-slate-500 uppercase tracking-widest leading-none mb-1">Time Left</p>
                    <div id="countdown" class="text-xl text-white leading-none">15:00</div>
                </div>
            </div>

            @if(($ipaymuData['Via'] ?? '') == 'QRIS')
                <!-- QR Code Display -->
                <div class="bg-white rounded-3xl p-6 shadow-xl mb-6 relative mx-auto max-w-[240px] flex items-center justify-center">
                    @if(!empty($qrUrl))
                        <div class="relative w-full aspect-square overflow-hidden rounded-xl border border-slate-50">
                            <img src="{{ $qrUrl }}" alt="QRIS Payment" class="w-full h-full object-contain">
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ $qrUrl }}" target="_blank" class="text-[10px] text-slate-400 font-medium">
                                Klik di sini jika barcode tidak muncul
                            </a>
                        </div>
                    @else
                        <div class="w-full aspect-square flex flex-col items-center justify-center text-slate-400 gap-3 bg-slate-50 rounded-xl py-8">
                            <span class="material-icons-round text-4xl animate-pulse">qr_code_2</span>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-center px-4 leading-relaxed">
                                QRIS Sedang Diproses...<br>
                                <span class="text-[7px] opacity-60">Silakan Refresh atau Hubungi CS</span>
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <!-- VA Display -->
                <div class="bg-white/5 border border-white/5 rounded-3xl p-6 mb-6 text-center">
                    <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-2">Virtual Account / Pembayaran</p>
                    <div class="flex flex-col items-center gap-3">
                        <h2 class="text-3xl font-black text-white tracking-tighter">{{ $ipaymuData['PaymentNo'] ?? '' }}</h2>
                        <button onclick="copyToClipboard('{{ $ipaymuData['PaymentNo'] ?? '' }}')" class="w-full py-3 rounded-2xl bg-primary text-slate-950 font-black text-[10px] uppercase tracking-widest shadow-lg active:scale-95 transition-transform flex items-center justify-center gap-2">
                            <span class="material-icons-round text-sm">content_copy</span>
                            <span>Salin Kode</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Order Details -->
            <div class="bg-white/5 rounded-2xl p-4 space-y-2 text-left mb-6 text-[10px] font-bold">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 uppercase tracking-wider">Invoice</span>
                    <span class="text-white font-mono uppercase">{{ $order->order_id }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-slate-500 uppercase tracking-wider">Product</span>
                    <span class="text-white text-right leading-tight max-w-[140px]">{{ $order->product_name }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3 mb-3">
                <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="bg-white/5 text-white font-black text-[10px] py-4 rounded-2xl flex items-center justify-center gap-2">
                    <span class="material-icons-round text-sm">history</span>
                    <span>STATUS</span>
                </a>
                <button onclick="window.print()" class="bg-primary text-slate-950 font-black text-[10px] py-4 rounded-2xl flex items-center justify-center gap-2">
                    <span class="material-icons-round text-sm">download</span>
                    <span>SAVE</span>
                </button>
            </div>
            
            <form id="cancelOrderForm" action="{{ route('order.cancel', $order->order_id) }}" method="POST">
                @csrf
                <button type="button" onclick="confirmCancelOrder()" class="w-full bg-red-500/10 text-red-500 border border-red-500/20 font-black text-[10px] py-4 rounded-2xl flex items-center justify-center gap-2 uppercase tracking-widest active:scale-95 transition-transform">
                    <span class="material-icons-round text-sm">cancel</span>
                    <span>Batalkan Pesanan</span>
                </button>
            </form>
        </div>

        <!-- Help Info -->
        <div class="glass-panel p-5 rounded-2xl border border-white/5 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-white/5 flex items-center justify-center shrink-0">
                <span class="material-icons-round text-primary text-lg">help_outline</span>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold text-white mb-1">Panduan Pembayaran</p>
                <p class="text-[9px] text-slate-500 leading-tight">Pastikan nominal transfer sesuai. Pesanan diproses otomatis.</p>
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
                title: 'Berhasil!',
                text: 'Kode pembayaran disalin.',
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

            if (--timer < 0) {
                display.textContent = "00:00";
            }
        }, 1000);
    }

    function confirmCancelOrder() {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            text: "Pesanan yang dibatalkan tidak dapat diproses kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tunggu',
            background: '#1e293b',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelOrderForm').submit();
            }
        })
    }

    window.onload = function () {
        var fifteenMinutes = 60 * 15,
            display = document.querySelector('#countdown');
        startTimer(fifteenMinutes, display);
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
