@extends('hp.layouts.neonflux')

@section('title', 'Selesaikan Pembayaran — ' . get_setting('site_name'))

@section('content')
<div class="px-4 py-2 max-w-lg mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 active:scale-90 transition-all">
            <span class="material-icons-round text-white">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-display font-black text-white tracking-tight uppercase leading-tight">Pembayaran</h1>
            <p class="text-xs text-white/50 font-medium">Order #{{ $order->order_id }}</p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="glass-panel-mobile p-6 rounded-3xl relative overflow-hidden border border-white/10 shadow-2xl space-y-6">
        {{-- Total Amount --}}
        <div class="text-center space-y-1">
            <p class="text-xs font-bold text-white/40 uppercase tracking-widest leading-none">Total Pembayaran</p>
            <h2 class="text-3xl font-display font-black text-primary drop-shadow-[0_0_15px_rgba(0,242,255,0.4)]">
                Rp {{ number_format($ipaymu['total'] ?? $order->total_price, 0, ',', '.') }}
            </h2>
        </div>

        {{-- Payment Content --}}
        <div class="bg-black/40 rounded-2xl p-4 border border-white/5">
            @if(!empty($ipaymu['qr_image']))
                {{-- QRIS --}}
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative p-3 bg-white rounded-2xl shadow-xl">
                        <img src="{{ $ipaymu['qr_image'] }}" alt="QRIS Code" class="w-48 h-48 sm:w-56 sm:h-56">
                        @if(!empty($ipaymu['expired']))
                            <div class="absolute -top-3 -right-3 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-lg uppercase">
                                QRIS Dynamic
                            </div>
                        @endif
                    </div>
                    <div class="text-center">
                        <h3 class="text-white font-bold text-sm">Scan QRIS untuk membayar</h3>
                        <p class="text-[10px] text-white/40 mt-1 max-w-[200px] mx-auto italic">Berlaku untuk semua aplikasi e-wallet & Mobile Banking</p>
                    </div>
                </div>
            @elseif(!empty($ipaymu['payment_no']))
                {{-- Virtual Account --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-6 bg-primary rounded-full"></span>
                            <span class="text-sm font-black text-white uppercase tracking-wider">{{ $ipaymu['via'] ?? 'Bank Transfer' }}</span>
                        </div>
                        <img src="https://img.business/api/v1/bank-logo/{{ strtolower($ipaymu['via'] ?? 'bank') }}" 
                             alt="{{ $ipaymu['via'] }}" 
                             class="h-6 object-contain opacity-80"
                             onerror="this.style.display='none'">
                    </div>

                    <div class="relative group">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 flex items-center justify-between group-active:bg-white/10 transition-colors">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-white/30 uppercase leading-none">Nomor VA / Pembayaran</p>
                                <span id="payment-no" class="text-xl font-mono font-bold text-white tracking-widest">{{ $ipaymu['payment_no'] }}</span>
                            </div>
                            <button onclick="copyToClipboard('{{ $ipaymu['payment_no'] }}')" 
                                    class="p-2 bg-primary text-black rounded-lg shadow-neon-cyan active:scale-90 transition-all">
                                <span class="material-icons-round text-base">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Metadata / Instructions --}}
        <div class="space-y-3">
            <div class="flex justify-between items-center text-xs">
                <span class="text-white/40 font-bold uppercase tracking-wider">Status Pesanan</span>
                <span class="px-2 py-1 rounded-md bg-white/5 border border-white/10 text-white font-black uppercase text-[9px] tracking-wide animate-pulse">Menunggu Pembayaran</span>
            </div>
            @if(!empty($ipaymu['expired']))
            <div class="flex justify-between items-center text-xs">
                <span class="text-white/40 font-bold uppercase tracking-wider">Batas Waktu</span>
                <span class="text-secondary font-black" id="expiry-timer">
                    {{ \Carbon\Carbon::parse($ipaymu['expired'])->format('d M Y, H:i') }}
                </span>
            </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="pt-2 space-y-3">
            <button id="btn-check-status" 
                    class="w-full h-14 bg-primary text-black font-display font-black text-sm rounded-2xl shadow-neon-cyan flex items-center justify-center gap-3 active:scale-[0.98] transition-all">
                <span class="material-icons-round animate-spin-slow">sync</span>
                CEK STATUS PEMBAYARAN
            </button>
            <p class="text-center text-[10px] text-white/30 px-4">
                Pembayaran akan diverifikasi secara otomatis dalam 1-5 menit setelah dana diterima.
            </p>
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="mt-8 mb-12 flex flex-col items-center gap-4">
        <div class="flex items-center gap-2 opacity-30">
            <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Secured by</span>
            <img src="https://ipaymu.com/wp-content/themes/ipaymu-v3/assets/img/logo-ipaymu.png" alt="iPaymu" class="h-4 brightness-0 invert">
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: 'Berhasil disalin!',
            showConfirmButton: false,
            timer: 1500,
            background: '#1e293b',
            color: '#fff'
        });
    });
}

document.getElementById('btn-check-status').addEventListener('click', function() {
    const btn = this;
    const icon = btn.querySelector('.material-icons-round');
    
    btn.disabled = true;
    btn.classList.add('opacity-70');
    icon.classList.add('animate-spin');

    // Re-check order status via existing tracking logic
    fetch(`{{ route('order.poll', ['order_id' => $order->order_id]) }}`)
        .then(response => response.json())
        .then(res => {
            const data = res.data;
            if (data.status === 'success' || data.status === 'processing' || data.status === 'paid') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Diterima!',
                    text: 'Pesanan Anda sedang diproses.',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#00f2ff',
                }).then(() => {
                    window.location.href = `{{ route('track.order', ['order_id' => $order->order_id]) }}`;
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Terbayar',
                    text: 'Pastikan Anda sudah mentransfer sesuai nominal. Jika baru saja bayar, tunggu 1-2 menit.',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#00f2ff',
                });
            }
        })
        .catch(err => {
            console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            icon.classList.remove('animate-spin');
        });
});

// Auto-polling every 15 seconds
const pollingInterval = setInterval(() => {
    fetch(`{{ route('order.poll', ['order_id' => $order->order_id]) }}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data;
            if (data.status === 'success' || data.status === 'processing' || data.status === 'paid') {
                clearInterval(pollingInterval);
                window.location.href = `{{ route('track.order', ['order_id' => $order->order_id]) }}`;
            }
        });
}, 15000);
</script>
@endpush

<style>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.shadow-neon-cyan {
    box-shadow: 0 0 20px rgba(0, 242, 255, 0.3), 0 0 40px rgba(0, 242, 255, 0.1);
}
</style>
@endsection
