@extends('hp.layouts.neonflux')

@section('title', 'Selesaikan Pembayaran — ' . get_setting('site_name'))

@section('content')
{{-- Background Decor --}}
<div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-cyan-500/10 blur-[100px] rounded-full"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/10 blur-[100px] rounded-full"></div>
</div>

<div class="relative z-10 px-4 py-2 max-w-lg mx-auto space-y-3">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 active:scale-90 transition-all">
            <span class="material-icons-round text-white">arrow_back</span>
        </a>
        <div>
            <h1 class="text-lg font-display font-black text-white tracking-tight uppercase leading-tight">Pembayaran</h1>
            <p class="text-[10px] text-slate-500 font-mono">#{{ $order->order_id }}</p>
        </div>
    </div>

    {{-- Countdown Timer --}}
    @if(!empty($ipaymu['expired']))
    <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-xl p-3 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-cyan-400 animate-pulse text-base">schedule</span>
        </div>
        <div class="flex-1">
            <p class="text-[9px] font-bold text-cyan-400 uppercase tracking-tighter">Batas Pembayaran</p>
            <p class="text-sm font-black text-white" id="countdown-timer" data-expired="{{ $ipaymu['expired'] }}">
                {{ \Carbon\Carbon::parse($ipaymu['expired'])->format('d M Y, H:i') }}
            </p>
        </div>
    </div>
    @endif

    {{-- Main Payment Card --}}
    <div class="bg-gradient-to-b from-slate-900/80 to-black/80 border border-white/10 rounded-2xl p-4 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
        {{-- Top Glow Line --}}
        <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent opacity-50"></div>

        {{-- Amount Header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[9px] uppercase tracking-[0.15em] text-cyan-400 font-bold mb-0.5">Total Pembayaran</p>
                <h2 class="text-2xl font-black text-white flex items-baseline gap-1">
                    <span class="text-xs font-medium text-slate-500 italic">Rp</span>
                    {{ number_format($ipaymu['total'] ?? $order->total_price, 0, ',', '.') }}
                </h2>
            </div>
            <div class="bg-white/5 px-2.5 py-1 rounded-lg border border-white/10 text-right">
                <p class="text-[8px] uppercase text-slate-500 font-bold">Metode</p>
                <p class="text-[10px] font-bold text-white uppercase tracking-wider">{{ $ipaymu['via'] ?? 'iPaymu' }}</p>
            </div>
        </div>

        {{-- Payment Content --}}
        <div class="py-2">
            @if(($ipaymu['status'] ?? 'pending') === 'error' || (empty($ipaymu['qr_string']) && empty($ipaymu['qr_image']) && empty($ipaymu['payment_no']) && empty($ipaymu['payment_url'])))
                {{-- ERROR --}}
                <div class="text-center py-6 space-y-3">
                    <span class="material-icons-round text-red-500 text-4xl">report_problem</span>
                    <div class="space-y-1">
                        <h3 class="text-sm font-black text-white uppercase italic tracking-tighter">Gagal Inisiasi</h3>
                        <p class="text-[10px] text-slate-400 px-2 leading-relaxed">Sistem gagal menghubungi gateway iPaymu.</p>
                    </div>
                    <button onclick="window.location.reload()" class="px-4 py-1.5 bg-white/10 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest">Coba Lagi</button>
                </div>

            @elseif(!empty($ipaymu['qr_string']) || !empty($ipaymu['qr_image']))
                {{-- QRIS --}}
                @php
                    $qrSrc = !empty($ipaymu['qr_string']) 
                        ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ipaymu['qr_string']) 
                        : $ipaymu['qr_image'];
                @endphp
                <div class="flex flex-col items-center space-y-2.5">
                    <div class="relative p-3 bg-white rounded-xl shadow-[0_0_30px_rgba(255,255,255,0.06)]">
                        <img src="{{ $qrSrc }}" alt="QRIS Code" class="w-40 h-40">
                        {{-- Corner Decorations --}}
                        <div class="absolute -top-1 -left-1 w-5 h-5 border-t-2 border-l-2 border-cyan-500 rounded-tl-sm"></div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-2 border-r-2 border-cyan-500 rounded-br-sm"></div>
                        {{-- Center Logo --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="bg-white p-1 rounded shadow">
                                @if($logo = get_image_url('site_logo'))
                                    <img src="{{ $logo }}" alt="Logo" class="w-5 h-5 object-contain">
                                @else
                                    <div class="w-5 h-5 bg-black rounded flex items-center justify-center">
                                        <span class="text-[6px] font-black text-cyan-400 leading-none">NF</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 text-center">Scan menggunakan M-Banking atau E-Wallet</p>
                </div>

            @elseif(!empty($ipaymu['payment_no']))
                {{-- Virtual Account --}}
                <div class="space-y-3">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase mb-2">Nomor VA / Pembayaran</p>
                        <div class="flex items-center justify-between gap-3">
                            <span id="payment-no" class="text-xl font-mono font-bold text-white tracking-widest truncate">{{ $ipaymu['payment_no'] }}</span>
                            <button onclick="copyToClipboard('{{ $ipaymu['payment_no'] }}')" 
                                    class="p-2.5 bg-cyan-500 text-black rounded-lg shadow-[0_0_15px_rgba(6,182,212,0.3)] active:scale-90 transition-all">
                                <span class="material-icons-round text-base">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif(!empty($ipaymu['payment_url']))
                {{-- REDIRECT --}}
                <div class="text-center py-4 space-y-4">
                    <div class="w-14 h-14 bg-cyan-500/10 rounded-full flex items-center justify-center mx-auto">
                        <span class="material-icons-round text-cyan-400 text-3xl">rocket_launch</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-white font-black uppercase italic tracking-tighter text-sm">Langkah Terakhir</h3>
                        <p class="text-[10px] text-slate-400 px-4">Klik tombol di bawah untuk diarahkan ke halaman pembayaran aman <b class="text-white">iPaymu</b>.</p>
                    </div>
                    <a href="{{ $ipaymu['payment_url'] }}" target="_blank" class="block w-full py-4 bg-cyan-500 text-black font-black text-sm rounded-xl shadow-[0_10px_30px_rgba(6,182,212,0.3)] active:scale-95 transition-all uppercase tracking-wider">
                        LANJUTKAN KE PEMBAYARAN
                    </a>
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="pt-3 border-t border-white/5 mt-1">
            <button id="btn-check-status" 
                    class="w-full py-3 bg-cyan-500 text-black font-black text-xs uppercase tracking-widest rounded-xl shadow-[0_8px_25px_rgba(6,182,212,0.3)] flex items-center justify-center gap-2 active:scale-[0.98] transition-all">
                <span class="material-icons-round animate-spin-slow text-base">sync</span>
                Konfirmasi Pembayaran
            </button>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-md">
        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
            <div class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></div>
            Ringkasan Pesanan
        </h3>

        <div class="space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-bold text-white mb-0.5">{{ $order->product_name }}</p>
                    <p class="text-[10px] text-slate-500">{{ $order->game_name ?? $order->category_name ?? 'Top-Up Game' }}</p>
                </div>
                @php
                    $gameIcon = null;
                    $productCode = $order->payload['product_code'] ?? null;
                    if ($productCode) {
                        $svc = \App\Models\Service::where('product_code', $productCode)->first();
                        if ($svc && $svc->category) {
                            $gameIcon = $svc->category->icon;
                        }
                    }
                @endphp
                <div class="bg-white/5 p-1.5 rounded-lg overflow-hidden">
                    @if($gameIcon)
                        <img src="{{ $gameIcon }}" alt="Game" class="w-8 h-8 rounded-md object-cover">
                    @else
                        <span class="material-icons-round text-cyan-400 text-lg">videogame_asset</span>
                    @endif
                </div>
            </div>

            @if($order->data)
            <div class="bg-white/5 rounded-xl p-3 border border-white/5 space-y-1.5">
                @if($order->nickname)
                <div class="flex justify-between text-[10px]">
                    <span class="text-slate-500 uppercase font-bold tracking-wider">Nickname</span>
                    <span class="text-cyan-400 font-black uppercase italic">{{ $order->nickname }}</span>
                </div>
                @endif
                <div class="flex justify-between text-[10px]">
                    <span class="text-slate-500 uppercase font-bold tracking-wider">User ID</span>
                    <span class="text-white font-bold">{{ $order->data }}</span>
                </div>
            </div>
            @endif

            <div class="h-px bg-white/5"></div>

            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Harga Produk</span>
                    <span class="text-slate-300 font-medium">Rp {{ number_format($order->price ?? $order->total_price, 0, ',', '.') }}</span>
                </div>
                @if(($order->total_price - ($order->price ?? $order->total_price)) > 0)
                <div class="flex justify-between">
                    <span class="text-slate-500">Biaya Layanan</span>
                    <span class="text-slate-300 font-medium">Rp {{ number_format($order->total_price - ($order->price ?? $order->total_price), 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between pt-1.5">
                    <span class="text-white font-bold">Total Tagihan</span>
                    <span class="font-black text-cyan-400 text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Instruksi Pembayaran</h3>
        <div class="space-y-4">
            <div class="flex gap-3">
                <span class="text-[10px] font-black text-cyan-500/50">01</span>
                <p class="text-[11px] leading-relaxed text-slate-400">Salin/Scan data pembayaran yang tertera di atas.</p>
            </div>
            <div class="flex gap-3">
                <span class="text-[10px] font-black text-cyan-500/50">02</span>
                <p class="text-[11px] leading-relaxed text-slate-400">Pastikan nominal transfer <b class="text-white">tepat sama</b> untuk verifikasi otomatis.</p>
            </div>
            <div class="flex gap-3">
                <span class="text-[10px] font-black text-cyan-500/50">03</span>
                <p class="text-[11px] leading-relaxed text-slate-400">Klik <b class="text-white">"Konfirmasi Pembayaran"</b> setelah transfer berhasil.</p>
            </div>
        </div>
    </div>

    {{-- Help Widget --}}
    <a href="{{ get_setting('whatsapp_link', '#') }}" target="_blank" class="bg-gradient-to-r from-blue-600/20 to-cyan-500/20 border border-cyan-500/30 rounded-2xl p-4 flex items-center justify-between active:border-cyan-400 transition-all block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-400 flex items-center justify-center">
                <span class="material-icons-round text-black text-xl">chat</span>
            </div>
            <div>
                <p class="text-xs font-bold text-white">Butuh Bantuan?</p>
                <p class="text-[10px] text-slate-400">Hubungi Admin di WhatsApp</p>
            </div>
        </div>
        <span class="material-icons-round text-slate-500 text-lg">open_in_new</span>
    </a>

    {{-- Cancel Order --}}
    <div class="text-center pb-8">
        <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 text-xs text-slate-500 active:text-red-400 transition-colors uppercase font-bold tracking-widest py-3">
                <span class="material-icons-round text-sm">arrow_back</span>
                Batalkan Pesanan
            </button>
        </form>
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
            background: '#0f172a',
            color: '#fff'
        });
    });
}

// Countdown Timer
(function() {
    const el = document.getElementById('countdown-timer');
    if (!el) return;
    const expired = new Date(el.dataset.expired).getTime();
    
    function updateTimer() {
        const now = Date.now();
        const diff = Math.max(0, Math.floor((expired - now) / 1000));
        const h = Math.floor(diff / 3600);
        const m = Math.floor((diff % 3600) / 60);
        const s = diff % 60;
        el.textContent = (h > 0 ? h + ':' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        if (diff <= 0) { el.textContent = 'Expired'; el.classList.add('text-red-500'); }
    }
    updateTimer();
    setInterval(updateTimer, 1000);
})();

// Check Status Button
document.getElementById('btn-check-status').addEventListener('click', function() {
    const btn = this;
    const icon = btn.querySelector('.material-icons-round');
    
    btn.disabled = true;
    btn.classList.add('opacity-70');
    icon.classList.add('animate-spin');

    fetch(`{{ route('order.poll', ['order_id' => $order->order_id]) }}`)
        .then(response => response.json())
        .then(res => {
            const data = res.data;
            if (data.status === 'success' || data.status === 'processing' || data.status === 'paid') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Diterima!',
                    text: 'Pesanan Anda sedang diproses.',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#06b6d4',
                }).then(() => {
                    window.location.href = `{{ route('track.order', ['order_id' => $order->order_id]) }}`;
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Terdeteksi',
                    text: 'Pastikan pembayaran sudah berhasil, lalu tunggu 1-2 menit.',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#06b6d4',
                });
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            icon.classList.remove('animate-spin');
        });
});

// Auto-polling every 15 seconds
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
</style>
@endsection
