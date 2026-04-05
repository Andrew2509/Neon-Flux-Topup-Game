@extends('tablet.layouts.neonflux')

@section('title', 'Cek Transaksi')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-6 py-32 space-y-12">
    <div class="w-full max-w-xl">
        <div class="glass-panel p-10 rounded-4xl border-white/5 shadow-2xl space-y-8">
            <!-- Header -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-primary/10 text-primary mb-4 border border-primary/20">
                    <span class="material-icons-round text-3xl">pageview</span>
                </div>
                <h1 class="text-3xl font-black text-white tracking-tight">Cek Transaksi</h1>
                <p class="text-slate-400 text-sm font-medium">Validasi status pesanan Anda melalui ID Transaksi.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('track.order') }}" method="GET" class="space-y-4">
                <div class="relative">
                    <span class="material-icons-round absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-xl">receipt_long</span>
                    <input type="text" name="order_id" required value="{{ request('order_id') }}"
                           placeholder="Masukkan ID Pesanan (ORD-XXXX)"
                           class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-base font-bold text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary outline-none transition-all">
                </div>
                <button type="submit" class="w-full bg-primary text-slate-950 font-black text-base py-4 rounded-2xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 active:scale-95 transition-all">
                    <span>Lacak Status Pesanan</span>
                    <span class="material-icons-round text-lg">search</span>
                </button>
            </form>

            @if(isset($order))
                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6 pt-6 border-t border-white/5">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Status Sekarang</p>
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
                                    'pending' => 'Pending',
                                    'failed' => 'Gagal',
                                    'failed_provider' => 'Gagal (Provider)',
                                    'failed_permanent' => 'Gagal (hubungi admin)',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <span class="text-xl font-black text-{{ $status_color }}-400 uppercase italic">{{ $status_label }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Invoice ID</p>
                            <p class="text-lg font-black text-white font-mono leading-none">{{ $order->order_id }}</p>
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-2xl p-6 space-y-4 border border-white/5">
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase">Item Pesanan</span>
                            <span class="text-xs font-black text-white">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase">Gateway</span>
                            <span class="text-xs font-black text-white">{{ $order->payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase">Total Bayar</span>
                            <span class="text-xs font-black text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        @php $op = $order->payload ?? []; @endphp
                        @if(!empty($op['player_nickname'] ?? ''))
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-xs font-bold text-slate-400 uppercase shrink-0">Nama pemain</span>
                            <span class="text-xs font-black text-white text-right max-w-[60%] break-words">{{ $op['player_nickname'] }}</span>
                        </div>
                        @endif
                    </div>

                    @if($order->status === 'success' && isset($order->payload['tokovoucher']['sn']))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 space-y-2">
                            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Serial Number</p>
                            <div class="flex items-center justify-between">
                                <code class="text-emerald-400 font-mono text-base font-bold">{{ $order->payload['tokovoucher']['sn'] }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $order->payload['tokovoucher']['sn'] }}')" class="text-emerald-400 hover:text-white transition-colors">
                                    <span class="material-icons-round text-sm">content_copy</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Latest Transactions (Tablet) -->
    <div class="w-full max-w-4xl animate-in fade-in slide-in-from-bottom-6 duration-700">
        <div class="glass-panel rounded-4xl border border-white/5 overflow-hidden shadow-xl">
            <div class="px-8 py-5 border-b border-white/5 bg-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-icons-round text-primary">history</span>
                    <h2 class="text-lg font-black text-white tracking-tight">10 Transaksi Terakhir</h2>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                            <th class="px-8 py-4">Waktu</th>
                            <th class="px-8 py-4">Invoice</th>
                            <th class="px-8 py-4">Produk</th>
                            <th class="px-8 py-4">Total</th>
                            <th class="px-8 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($latestOrders as $lOrder)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-8 py-4">
                                <span class="text-[11px] font-bold text-slate-400">{{ $lOrder->created_at->format('d/m H:i') }}</span>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-xs font-black text-white font-mono">{{ $lOrder->order_id }}</span>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-xs font-bold text-slate-300 truncate max-w-[150px] inline-block">{{ $lOrder->product_name }}</span>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-xs font-black text-primary italic">Rp {{ number_format($lOrder->total_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-4 text-center">
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
                                <span class="px-2 py-0.5 bg-{{ $l_status_color }}-500/10 text-{{ $l_status_color }}-400 text-[9px] font-black rounded border border-{{ $l_status_color }}-500/20 uppercase">
                                    {{ $l_status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
