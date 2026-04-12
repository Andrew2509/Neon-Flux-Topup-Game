@extends('hp.layouts.neonflux')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Header Card -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm overflow-hidden relative">
        <div class="absolute -top-10 -right-10 size-32 bg-primary/10 blur-3xl rounded-full"></div>
        
        <div class="flex flex-col items-center text-center space-y-3 relative z-10">
            <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20">
                <span class="material-icons-round text-3xl">pageview</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lacak Pesanan</h1>
            <p class="text-slate-500 text-xs font-semibold leading-relaxed">Cek status pengiriman pesanan Anda secara real-time.</p>
        </div>

        <form action="{{ route('track.order') }}" method="GET" class="mt-6 space-y-3">
            <div class="relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">receipt_long</span>
                <input type="text" name="order_id" required value="{{ request('order_id') }}"
                       placeholder="Masukkan Nomor Transaksi..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all shadow-inner">
            </div>
            <button type="submit" class="w-full bg-primary text-slate-900 font-black text-sm py-4 rounded-2xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span>Lacak Sekarang</span>
                <span class="material-icons-round text-lg">search</span>
            </button>
        </form>
    </div>

    @if(isset($order))
        <!-- Order Result Card -->
        <div class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="glass-panel-mobile p-5 rounded-3xl border-slate-200 shadow-sm space-y-5">
                <!-- Status & ID -->
                <div class="flex justify-between items-start gap-3">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status Pesanan</p>
                        @php
                            $status_color = match($order->status) {
                                'success' => 'emerald',
                                'paid', 'processing' => 'cyan',
                                'pending_payment', 'pending' => 'amber',
                                'failed', 'failed_provider', 'failed_permanent' => 'red',
                                default => 'slate'
                            };
                            $status_label = match($order->status) {
                                'success' => 'Produk terkirim',
                                'paid', 'processing' => 'Bayar OK — proses game',
                                'pending_payment' => 'Menunggu Bayar',
                                'pending' => 'Tertunda',
                                'failed' => 'Gagal',
                                'failed_provider' => 'Gagal (Provider)',
                                'failed_permanent' => 'Gagal (hubungi admin)',
                                default => ucfirst($order->status)
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 text-{{ $status_color }}-600 font-black text-sm uppercase">
                            <span class="size-1.5 rounded-full bg-{{ $status_color }}-500 animate-pulse"></span>
                            {{ $status_label }}
                        </span>
                        @if(in_array($order->status, ['paid', 'processing'], true))
                            <p class="text-[10px] text-slate-500 font-medium leading-snug mt-1">Ref TokoVoucher = {{ $order->order_id }}. Cek list transaksi setelah beberapa menit.</p>
                        @endif
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ID Pesanan</p>
                        <p class="text-sm font-black text-slate-900 font-mono">{{ $order->order_id }}</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="bg-slate-50 rounded-2xl p-4 space-y-3.5 border border-slate-100">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Layanan</span>
                        <span class="text-xs font-black text-slate-800 text-right">{{ $order->product_name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran</span>
                        <span class="text-xs font-black text-slate-800">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
                        <span class="text-xs font-black text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waktu</span>
                        <span class="text-xs font-black text-slate-800">{{ $order->created_at->format('d/m/y H:i') }}</span>
                    </div>
                    @php $op = $order->payload ?? []; @endphp
                    @if(!empty($op['player_nickname'] ?? ''))
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Nama pemain</span>
                        <span class="text-xs font-bold text-slate-800 text-right max-w-[65%] break-words">{{ $op['player_nickname'] }}</span>
                    </div>
                    @endif
                </div>

                @if($order->status === 'success' && !empty($order->payload['tokovoucher']['sn'] ?? ''))
                    <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 space-y-2">
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Serial Number / Pesan</p>
                        <div class="flex items-center justify-between gap-2">
                            <code class="text-emerald-700 font-mono text-sm font-bold break-all">{{ $order->payload['tokovoucher']['sn'] }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ $order->payload['tokovoucher']['sn'] }}')" class="size-8 shrink-0 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                <span class="material-icons-round text-sm">content_copy</span>
                            </button>
                        </div>
                    </div>
                @endif

                @if($order->status === 'pending_payment')
                    @if(!empty($ipaymu))
                    <div class="pt-2">
                        <a href="{{ route('order.payment', $order->order_id) }}" class="w-full bg-primary text-slate-900 py-4 rounded-2xl font-black text-sm flex items-center justify-center gap-2 shadow-lg shadow-primary/20 active:scale-95 transition-all">
                            <span class="material-icons-round text-lg">payment</span>
                            Lanjutkan Pembayaran
                            <span class="material-icons-round text-lg">arrow_forward</span>
                        </a>
                    </div>
                    @endif
                    <div class="pt-2">
                        <form id="cancelOrderForm" action="{{ route('order.cancel', $order->order_id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmCancelOrder()" class="w-full bg-red-50 text-red-600 border border-red-100 py-4 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                                <span class="material-icons-round text-lg">cancel</span>
                                <span>Batalkan Pembayaran</span>
                            </button>
                        </form>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        function confirmCancelOrder() {
                            Swal.fire({
                                title: 'Batal Bayar?',
                                text: "Pesanan akan dibatalkan permanen.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Ya, Batal',
                                cancelButtonText: 'Tunggu',
                                background: '#ffffff',
                                color: '#0f172a'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('cancelOrderForm').submit();
                                }
                            })
                        }
                    </script>
                @endif
            </div>

            <!-- Help Message -->
            <div class="text-center px-4">
                <p class="text-[10px] font-bold text-slate-400">Punya kendala? <a href="#" class="text-primary hover:underline">Hubungi Layanan Konsumen</a></p>
            </div>
        </div>
    @endif

    <!-- Latest Transactions List (Mobile) -->
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <span class="material-icons-round text-primary text-lg">history</span>
                10 Transaksi Terakhir
            </h2>
            <span class="text-[9px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">LIVE</span>
        </div>

        <div class="glass-panel-mobile rounded-3xl border-slate-200 shadow-sm overflow-hidden">
            <div class="divide-y divide-slate-100">
                @foreach($latestOrders as $lOrder)
                <div class="p-4 flex items-center justify-between gap-3 active:bg-slate-50 transition-colors">
                    <div class="flex flex-col gap-0.5 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-900 font-mono tracking-tighter">{{ $lOrder->order_id }}</span>
                            <span class="text-[8px] font-bold text-slate-400">{{ $lOrder->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 truncate max-w-[150px]">{{ $lOrder->product_name }}</p>
                    </div>
                    
                    <div class="text-right shrink-0">
                        <p class="text-[11px] font-black text-primary">Rp {{ number_format($lOrder->total_price, 0, ',', '.') }}</p>
                        @php
                            $l_status_color = match($lOrder->status) {
                                'success' => 'emerald',
                                'paid', 'processing' => 'cyan',
                                'pending_payment', 'pending' => 'amber',
                                'failed', 'failed_provider', 'failed_permanent' => 'red',
                                default => 'slate'
                            };
                            $l_status_label = match($lOrder->status) {
                                'success' => 'Terkirim',
                                'paid', 'processing' => 'Proses',
                                'pending_payment' => 'Menunggu',
                                'pending' => 'Pending',
                                'failed' => 'Gagal',
                                'failed_provider' => 'Gagal',
                                'failed_permanent' => 'Gagal',
                                default => ucfirst($lOrder->status)
                            };
                        @endphp
                        <span class="text-[8px] font-black text-{{ $l_status_color }}-600 uppercase tracking-tighter">
                            {{ $l_status_label }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
