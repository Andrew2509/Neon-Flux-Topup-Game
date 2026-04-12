@extends('desktop.layouts.neonflux')

@section('title', 'Selesaikan Pembayaran — ' . get_setting('site_name'))

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Breadcrumb / Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="group flex items-center gap-2 text-white/50 hover:text-primary transition-colors">
                <span class="material-icons-round text-xl group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="font-bold text-sm uppercase tracking-wider">Kembali</span>
            </a>
        </div>
        <div class="text-right">
            <h1 class="text-3xl font-display font-black text-white uppercase tracking-tighter">Pembayaran</h1>
            <p class="text-white/40 font-mono text-sm">Order ID: #{{ $order->order_id }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        {{-- Left: Payment Details Card --}}
        <div class="md:col-span-7">
            <div class="glass-panel p-8 rounded-[2.5rem] border border-white/10 shadow-2xl space-y-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <span class="material-icons-round text-8xl">contactless</span>
                </div>

                {{-- Amount and Channel --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">Total Tagihan</span>
                        <div class="text-4xl font-display font-black text-primary drop-shadow-[0_0_20px_rgba(0,242,255,0.3)]">
                            Rp {{ number_format($ipaymu['total'] ?? $order->total_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                        <span class="material-icons-round text-white/50 text-xl">account_balance</span>
                        <span class="font-black text-white uppercase tracking-widest text-sm">{{ $ipaymu['via'] ?? 'iPaymu' }}</span>
                    </div>
                </div>

                {{-- QRIS / VA / REDIRECT / ERROR Display --}}
                <div class="py-4">
                    @if(($ipaymu['status'] ?? 'pending') === 'error' || (empty($ipaymu['qr_image']) && empty($ipaymu['payment_no']) && empty($ipaymu['payment_url'])))
                        {{-- ERROR / FALLBACK --}}
                        <div class="bg-red-500/10 border border-red-500/20 rounded-3xl p-8 text-center space-y-4">
                            <span class="material-icons-round text-red-500 text-5xl">warning</span>
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold text-white uppercase italic">Gagal Menginisiasi Pembayaran</h3>
                                <p class="text-sm text-white/40">Sistem gagal menghubungi iPaymu. Hal ini biasanya dikarenakan IP Server belum terdaftar di whitelist iPaymu atau kendala API.</p>
                            </div>
                            <button onclick="window.location.reload()" class="px-6 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all uppercase tracking-widest">
                                Coba Lagi
                            </button>
                        </div>
                    @elseif(!empty($ipaymu['qr_string']) || !empty($ipaymu['qr_image']))
                        {{-- QRIS --}}
                        <div class="flex flex-col items-center gap-6">
                            <div class="p-4 bg-white rounded-3xl shadow-neon-white ring-8 ring-white/5">
                                @php
                                    $qrSrc = !empty($ipaymu['qr_string']) ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ipaymu['qr_string']) : $ipaymu['qr_image'];
                                @endphp
                                <img src="{{ $qrSrc }}" alt="QRIS" class="w-64 h-64">
                            </div>
                            <div class="text-center space-y-2">
                                <h3 class="text-xl font-bold text-white tracking-tight">Scan Kode QR di Atas</h3>
                                <p class="text-sm text-white/40 max-w-sm">Dapat dipindah menggunakan aplikasi M-Banking (BCA, Mandiri, BRI, dll) atau E-Wallet (OVO, Dana, GoPay, ShopeePay).</p>
                            </div>
                        </div>
                    @elseif(!empty($ipaymu['payment_url']))
                        {{-- REDIRECT (Session based) --}}
                        <div class="bg-primary/5 border border-primary/20 rounded-3xl p-10 text-center space-y-6">
                            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="material-icons-round text-primary text-5xl">rocket_launch</span>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-display font-black text-white uppercase tracking-tighter">Langkah Terakhir!</h3>
                                <p class="text-sm text-white/50 max-w-sm mx-auto">Klik tombol di bawah untuk menyelesaikan pembayaran via portal aman <b>iPaymu</b>.</p>
                            </div>
                            <a href="{{ $ipaymu['payment_url'] }}" target="_blank" class="inline-flex items-center gap-3 px-10 py-5 bg-primary text-black font-display font-black text-lg rounded-2xl shadow-neon-cyan hover:scale-[1.02] active:scale-[0.98] transition-all">
                                LANJUTKAN KE PEMBAYARAN
                                <span class="material-icons-round">open_in_new</span>
                            </a>
                            <p class="text-[10px] text-white/20 uppercase tracking-widest font-bold font-mono">ID Sesi: {{ $ipaymu['transaction_id'] ?? '-' }}</p>
                        </div>
                    @else
                        {{-- VA --}}
                        <div class="space-y-6">
                            <div class="bg-black/40 rounded-3xl p-6 border border-white/5 relative group">
                                <div class="flex flex-col gap-2">
                                    <span class="text-[10px] font-black text-white/30 uppercase tracking-widest">Nomor Virtual Account</span>
                                    <div class="flex items-center justify-between gap-4">
                                        <div id="payment-no" class="text-4xl font-mono font-bold text-white tracking-[0.3em] overflow-hidden truncate">
                                            {{ $ipaymu['payment_no'] }}
                                        </div>
                                        <button onclick="copyToClipboard('{{ $ipaymu['payment_no'] }}')" 
                                                class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-primary text-black rounded-xl shadow-neon-cyan hover:scale-105 active:scale-95 transition-all">
                                            <span class="material-icons-round text-xl">content_copy</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                                    <span class="block text-[10px] font-black text-white/30 uppercase mb-1">Status</span>
                                    <span class="text-xs text-secondary font-bold flex items-center gap-1.5 capitalize animate-pulse">
                                        <span class="w-2 h-2 bg-secondary rounded-full"></span>
                                        Menunggu Pembayaran
                                    </span>
                                </div>
                                @if(!empty($ipaymu['expired']))
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-right">
                                    <span class="block text-[10px] font-black text-white/30 uppercase mb-1">Batas Waktu</span>
                                    <span class="text-xs text-white font-mono font-bold uppercase">
                                        {{ \Carbon\Carbon::parse($ipaymu['expired'])->format('d M / H:i') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Status Check Button --}}
                <div class="pt-6 border-t border-white/5 space-y-4">
                    <button id="btn-check-status" 
                            class="w-full h-16 bg-primary text-black font-display font-black text-lg rounded-2xl shadow-neon-cyan hover:shadow-[0_0_30px_rgba(0,242,255,0.4)] flex items-center justify-center gap-4 transition-all active:scale-[0.98]">
                        <span class="material-icons-round animate-spin-slow">sync</span>
                        KONFIRMASI PEMBAYARAN
                    </button>
                    <p class="text-center text-xs text-white/30 italic">
                        Jangan tutup halaman ini sebelum transaksi Anda terverifikasi oleh sistem.
                    </p>
                </div>
            </div>
        </div>

        {{-- Right: Sidebar Info --}}
        <div class="md:col-span-5 space-y-6">
            {{-- Instructions --}}
            <div class="glass-panel p-6 rounded-[2rem] border border-white/10">
                <h3 class="text-lg font-display font-black text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-primary">info</span>
                    Instruksi Pembayaran
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">1</div>
                        <p class="text-sm text-white/60 leading-relaxed pt-1">Salin/Scan data pembayaran yang tertera di samping.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">2</div>
                        <p class="text-sm text-white/60 leading-relaxed pt-1">Pastikan nominal transfer **Tepat Sama** dengan yang diminta sistem agar verifikasi otomatis lancar.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">3</div>
                        <p class="text-sm text-white/60 leading-relaxed pt-1">Setelah melakukan pembayaran, klik tombol **"Konfirmasi Pembayaran"** atau tunggu sistem otomatis memperbarui status Anda.</p>
                    </div>
                </div>
            </div>

            {{-- Support Info --}}
            <div class="p-6 rounded-[2rem] bg-linear-to-br from-blue-600/20 to-cyan-500/20 border border-blue-500/20">
                <div class="flex items-start gap-4">
                    <span class="material-icons-round text-blue-400 text-3xl">headset_mic</span>
                    <div class="space-y-1">
                        <h4 class="text-white font-bold">Butuh Bantuan?</h4>
                        <p class="text-xs text-white/60 leading-normal">Jika pembayaran Anda belum terverifikasi setelah 10 menit, silakan hubungi Customer Service kami melalui WhatsApp dengan melampirkan bukti transfer.</p>
                        <a href="{{ get_setting('whatsapp_link', '#') }}" class="inline-flex items-center gap-1 text-primary text-xs font-bold mt-2 hover:underline">
                            Hubungi Admin
                            <span class="material-icons-round text-sm">open_in_new</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Berhasil disalin!',
            showConfirmButton: false,
            timer: 2000,
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

    // Polling order status
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
                    title: 'Belum Terdeteksi',
                    text: 'Silakan tunggu sebentar atau pastikan pembayaran Anda sudah berhasil.',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#00f2ff',
                });
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            icon.classList.remove('animate-spin');
        });
});

// Auto-check logic
const pollId = setInterval(() => {
    fetch(`{{ route('order.poll', ['order_id' => $order->order_id]) }}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data;
            if (data.status === 'success' || data.status === 'processing' || data.status === 'paid') {
                clearInterval(pollId);
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
.shadow-neon-white {
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
}
</style>
@endsection
