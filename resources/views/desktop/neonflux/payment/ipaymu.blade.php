@extends('desktop.layouts.neonflux')

@section('title', 'Selesaikan Pembayaran — ' . get_setting('site_name'))

@section('content')
{{-- Background Decor --}}
<div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-cyan-500/10 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/10 blur-[120px] rounded-full"></div>
    <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(0deg, rgba(13,18,28,0.8) 0px, rgba(13,18,28,0.8) 1px, transparent 1px, transparent 40px), repeating-linear-gradient(90deg, rgba(13,18,28,0.8) 0px, rgba(13,18,28,0.8) 1px, transparent 1px, transparent 40px);"></div>
</div>

<main class="relative z-10 max-w-6xl mx-auto px-6 pt-28 pb-12">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- LEFT COLUMN: Payment / QR Card --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="flex-1">
            <div class="bg-gradient-to-b from-slate-900/80 to-black/80 border border-white/10 rounded-3xl p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group">
                {{-- Top Glow Line --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent opacity-50"></div>

                {{-- Amount + Method Header --}}
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-cyan-400 font-bold mb-1">Total Pembayaran</p>
                        <h1 class="text-4xl font-black text-white flex items-baseline gap-2">
                            <span class="text-lg font-medium text-slate-500 italic">Rp</span>
                            {{ number_format($ipaymu['total'] ?? $order->total_price, 0, ',', '.') }}
                        </h1>
                    </div>
                    <div class="bg-white/5 px-4 py-2 rounded-xl border border-white/10 text-right">
                        <p class="text-[10px] uppercase text-slate-500 font-bold">Metode</p>
                        <p class="text-sm font-bold text-white uppercase tracking-wider">{{ $ipaymu['via'] ?? 'iPaymu' }}</p>
                    </div>
                </div>

                {{-- ──── Payment Content ──── --}}
                <div class="py-4">
                    @if(($ipaymu['status'] ?? 'pending') === 'error' || (empty($ipaymu['qr_string']) && empty($ipaymu['qr_image']) && empty($ipaymu['payment_no']) && empty($ipaymu['payment_url'])))
                        {{-- ERROR / FALLBACK --}}
                        <div class="bg-red-500/10 border border-red-500/20 rounded-3xl p-8 text-center space-y-4">
                            <span class="material-icons-round text-red-500 text-5xl">warning</span>
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold text-white uppercase italic">Gagal Menginisiasi Pembayaran</h3>
                                <p class="text-sm text-white/40">Sistem gagal menghubungi iPaymu. Hal ini biasanya dikarenakan IP Server belum terdaftar di whitelist iPaymu.</p>
                            </div>
                            <button onclick="window.location.reload()" class="px-6 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all uppercase tracking-widest">
                                Coba Lagi
                            </button>
                        </div>

                    @elseif(!empty($ipaymu['qr_string']) || !empty($ipaymu['qr_image']))
                        {{-- ═══ QRIS ═══ --}}
                        @php
                            $qrSrc = !empty($ipaymu['qr_string']) 
                                ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ipaymu['qr_string']) 
                                : $ipaymu['qr_image'];
                        @endphp
                        <div class="relative aspect-square max-w-[320px] mx-auto bg-white p-6 rounded-2xl shadow-[0_0_50px_rgba(255,255,255,0.1)] mb-8 transition-transform group-hover:scale-[1.02] duration-500">
                            <img src="{{ $qrSrc }}" alt="QRIS" class="w-full h-full">
                            {{-- Corner Decorations --}}
                            <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-cyan-500 rounded-tl-lg"></div>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-cyan-500 rounded-br-lg"></div>
                            {{-- Center Logo --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="bg-white p-1.5 rounded-lg shadow-lg">
                                    @if($logo = get_image_url('site_logo'))
                                        <img src="{{ $logo }}" alt="Logo" class="w-8 h-8 object-contain">
                                    @else
                                        <div class="w-8 h-8 bg-black rounded flex items-center justify-center">
                                            <span class="text-[8px] font-black text-cyan-400 leading-none">NF</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-slate-400 max-w-xs mx-auto mb-6">
                                Scan kode di atas menggunakan aplikasi M-Banking atau E-Wallet pilihanmu.
                            </p>
                        </div>

                    @elseif(!empty($ipaymu['payment_url']))
                        {{-- ═══ REDIRECT ═══ --}}
                        <div class="bg-cyan-500/5 border border-cyan-500/20 rounded-3xl p-10 text-center space-y-6">
                            <div class="w-20 h-20 bg-cyan-500/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="material-icons-round text-cyan-400 text-5xl">rocket_launch</span>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-display font-black text-white uppercase tracking-tighter">Langkah Terakhir!</h3>
                                <p class="text-sm text-white/50 max-w-sm mx-auto">Klik tombol di bawah untuk menyelesaikan pembayaran via portal aman <b>iPaymu</b>.</p>
                            </div>
                            <a href="{{ $ipaymu['payment_url'] }}" target="_blank" class="inline-flex items-center gap-3 px-10 py-5 bg-cyan-500 text-black font-black text-lg rounded-2xl shadow-[0_10px_30px_rgba(6,182,212,0.3)] hover:bg-cyan-400 hover:scale-[1.02] active:scale-[0.98] transition-all uppercase tracking-wider">
                                LANJUTKAN KE PEMBAYARAN
                                <span class="material-icons-round">open_in_new</span>
                            </a>
                            <p class="text-[10px] text-white/20 uppercase tracking-widest font-bold font-mono">ID Sesi: {{ $ipaymu['transaction_id'] ?? '-' }}</p>
                        </div>

                    @else
                        {{-- ═══ VIRTUAL ACCOUNT ═══ --}}
                        <div class="space-y-6">
                            <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Nomor Virtual Account</span>
                                <div class="flex items-center justify-between gap-4">
                                    <div id="payment-no" class="text-3xl font-mono font-bold text-white tracking-[0.2em] overflow-hidden truncate">
                                        {{ $ipaymu['payment_no'] }}
                                    </div>
                                    <button onclick="copyToClipboard('{{ $ipaymu['payment_no'] }}')" 
                                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-cyan-500 text-black rounded-xl shadow-[0_0_20px_rgba(6,182,212,0.3)] hover:scale-105 active:scale-95 transition-all">
                                        <span class="material-icons-round text-xl">content_copy</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-white/5 space-y-4">
                    <button id="btn-check-status" 
                            class="w-full py-4 bg-cyan-500 hover:bg-cyan-400 text-black font-black uppercase tracking-widest rounded-2xl shadow-[0_10px_30px_rgba(6,182,212,0.3)] transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                        <span class="material-icons-round animate-spin-slow">sync</span>
                        Konfirmasi Pembayaran
                    </button>
                    
                    <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" class="text-center">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 text-xs text-slate-500 hover:text-red-400 transition-colors uppercase font-bold tracking-widest py-2">
                            <span class="material-icons-round text-sm">arrow_back</span>
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- RIGHT COLUMN: Details & Instructions --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="w-full lg:w-[400px] flex flex-col gap-6">

            {{-- Countdown Box --}}
            @if(!empty($ipaymu['expired']))
            <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-2xl p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center">
                        <span class="material-icons-round text-cyan-400 animate-pulse text-xl">schedule</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-cyan-400 uppercase tracking-tighter">Batas Pembayaran</p>
                        <p class="text-xl font-black text-white" id="countdown-timer" data-expired="{{ $ipaymu['expired'] }}">
                            {{ \Carbon\Carbon::parse($ipaymu['expired'])->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
                <span class="material-icons-round text-slate-500 text-xl">help_outline</span>
            </div>
            @endif

            {{-- Order Summary Box --}}
            <div class="bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-md">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></div>
                    Ringkasan Pesanan
                </h3>

                <div class="space-y-4">
                    {{-- Product Info --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-white mb-1">{{ $order->product_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $order->game_name ?? $order->category_name ?? 'Top-Up Game' }}</p>
                        </div>
                        @php
                            $gameIcon = null;
                            $productCode = $order->payload['product_code'] ?? null;
                            if ($productCode) {
                                $svc = \App\Models\Service::where('code', $productCode)->first();
                                if ($svc && $svc->category) {
                                    $gameIcon = $svc->category->icon;
                                }
                            }
                        @endphp
                        <div class="bg-white/5 p-2 rounded-lg overflow-hidden">
                            @if($gameIcon)
                                <img src="{{ $gameIcon }}" alt="Game" class="w-10 h-10 rounded-md object-cover">
                            @else
                                <span class="material-icons-round text-cyan-400">videogame_asset</span>
                            @endif
                        </div>
                    </div>

                    {{-- User Detail --}}
                    @if($order->data)
                    <div class="bg-white/5 rounded-xl p-3 border border-white/5 space-y-2">
                        @if($order->nickname)
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-500 uppercase font-bold tracking-wider">Nickname</span>
                            <span class="text-cyan-400 font-black uppercase italic">{{ $order->nickname }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-500 uppercase font-bold tracking-wider">User ID</span>
                            <span class="text-white font-bold">{{ $order->data }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="h-px bg-white/5"></div>

                    {{-- Price Breakdown --}}
                    <div class="space-y-2 text-sm">
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
                        <div class="flex justify-between pt-2">
                            <span class="text-white font-bold">Total Tagihan</span>
                            <button onclick="copyToClipboard('{{ $order->total_price }}')" class="flex items-center gap-2 font-black text-cyan-400 text-lg transition-all hover:scale-105">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                <span class="material-icons-round text-base">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instructions Box --}}
            <div class="bg-white/5 border border-white/10 rounded-3xl p-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6">Instruksi Pembayaran</h3>
                <div class="space-y-6">
                    <div class="flex gap-4 group">
                        <span class="text-xs font-black text-cyan-500/50 group-hover:text-cyan-400 transition-colors">01</span>
                        <p class="text-xs leading-relaxed text-slate-400">Salin/Scan data pembayaran yang tertera di samping.</p>
                    </div>
                    <div class="flex gap-4 group">
                        <span class="text-xs font-black text-cyan-500/50 group-hover:text-cyan-400 transition-colors">02</span>
                        <p class="text-xs leading-relaxed text-slate-400">Pastikan nominal transfer <b class="text-white">tepat sama</b> untuk verifikasi otomatis.</p>
                    </div>
                    <div class="flex gap-4 group">
                        <span class="text-xs font-black text-cyan-500/50 group-hover:text-cyan-400 transition-colors">03</span>
                        <p class="text-xs leading-relaxed text-slate-400">Klik <b class="text-white">"Konfirmasi Pembayaran"</b> setelah transfer berhasil.</p>
                    </div>
                </div>
            </div>

            {{-- Help Widget --}}
            <a href="{{ get_setting('whatsapp_link', '#') }}" target="_blank" class="bg-gradient-to-r from-blue-600/20 to-cyan-500/20 border border-cyan-500/30 rounded-2xl p-4 flex items-center justify-between group cursor-pointer hover:border-cyan-400 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-400 flex items-center justify-center">
                        <span class="material-icons-round text-black text-xl">chat</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Butuh Bantuan?</p>
                        <p class="text-[10px] text-slate-400">Hubungi Admin di WhatsApp</p>
                    </div>
                </div>
                <span class="material-icons-round text-slate-500 group-hover:text-cyan-400 text-lg">open_in_new</span>
            </a>

        </div>
    </div>
</main>

{{-- Trust Badges --}}
<div class="relative z-10 max-w-6xl mx-auto px-6 py-8">
    <div class="flex flex-wrap justify-center gap-8 opacity-30 hover:opacity-100 transition-opacity duration-500">
        <div class="flex items-center gap-2 text-xs font-bold text-white uppercase tracking-tighter">
            <span class="material-icons-round text-cyan-400 text-base">qr_code_2</span>
            QRIS
        </div>
        <div class="h-6 w-px bg-white/10"></div>
        <span class="text-xs text-white/60 font-bold uppercase tracking-wider">BCA</span>
        <span class="text-xs text-white/60 font-bold uppercase tracking-wider">BNI</span>
        <span class="text-xs text-white/60 font-bold uppercase tracking-wider">Mandiri</span>
        <div class="h-6 w-px bg-white/10"></div>
        <div class="flex items-center gap-2 text-xs font-bold text-white uppercase tracking-tighter">
            <span class="material-icons-round text-green-500 text-base">verified_user</span>
            Secure 256-bit SSL
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
                    text: 'Silakan tunggu sebentar atau pastikan pembayaran Anda sudah berhasil.',
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
