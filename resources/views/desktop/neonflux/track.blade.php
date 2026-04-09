@extends('desktop.layouts.neonflux')

@section('title', 'Cek Transaksi - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pt-32 pb-20 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-2xl z-10">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-primary/10 text-primary mb-4 border border-primary/20 shadow-lg shadow-primary/5">
                    <span class="material-icons-round text-3xl">pageview</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Lacak Pesanan</h1>
                <p class="text-slate-400 font-medium">Masukkan nomor transaksi Anda untuk melihat status pesanan.</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('track.order') }}" method="GET" class="space-y-6">
                <div class="relative group">
                    <span class="material-icons-round absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-2xl group-focus-within:text-primary transition-colors">receipt_long</span>
                    <input type="text" name="order_id" required value="{{ request('order_id') }}" 
                           placeholder="Contoh: ORD-XXXXXXXX"
                           class="w-full bg-white/5 border border-white/10 rounded-2xl pl-14 pr-6 py-5 text-lg font-bold text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                </div>
                
                <button type="submit" class="w-full bg-primary text-slate-950 font-black text-lg py-5 rounded-2xl shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3 group">
                    <span>Periksa Sekarang</span>
                    <span class="material-icons-round group-hover:translate-x-1 transition-transform">search</span>
                </button>
            </form>

            @if(isset($order))
                <!-- Result Section -->
                <div class="mt-12 pt-10 border-t border-white/5 space-y-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Status Pesanan</p>
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
                                    'paid', 'processing' => 'Pembayaran OK — proses ke game',
                                    'pending_payment' => 'Menunggu Pembayaran',
                                    'pending' => 'Pending',
                                    'failed' => 'Gagal',
                                    'failed_provider' => 'Gagal (Provider)',
                                    'failed_permanent' => 'Gagal (hubungi admin)',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 rounded-full bg-{{ $status_color }}-500 animate-ping"></span>
                                    <span class="text-xl font-black text-{{ $status_color }}-400 uppercase tracking-tight">{{ $status_label }}</span>
                                </div>
                                @if(in_array($order->status, ['paid', 'processing'], true))
                                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed max-w-md">Uang sudah masuk; top-up ke akun game dijalankan oleh supplier. Biasanya selesai dalam beberapa menit. <strong class="text-slate-300">Ref ID TokoVoucher</strong> sama dengan nomor transaksi di atas — cek di member TokoVoucher dengan filter Ref ID itu.</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">ID Transaksi</p>
                            <p class="text-xl font-black text-white tracking-tight">{{ $order->order_id }}</p>
                        </div>
                    </div>

                    <div class="bg-white/5 border border-white/5 rounded-3xl p-6 space-y-4">
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Produk</span>
                            <span class="text-white text-right">{{ $order->product_name }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Metode Pembayaran</span>
                            <span class="text-white">{{ $order->payment_method }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Harga Produk</span>
                            <span class="text-white">Rp {{ number_format($order->original_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Diskon (Pengguna Baru 10%)</span>
                            <span class="text-emerald-400">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Total Harga</span>
                            <span class="text-primary font-black">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-400">Waktu Transaksi</span>
                            <span class="text-white">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @php $op = $order->payload ?? []; @endphp
                        @if(!empty($op['player_nickname'] ?? ''))
                        <div class="flex justify-between items-start gap-3 text-sm font-medium">
                            <span class="text-slate-400 shrink-0">Nama pemain (cek ID)</span>
                            <span class="text-white max-w-[65%] break-words text-right">{{ $op['player_nickname'] }}</span>
                        </div>
                        @endif
                    </div>

                    @if($order->status === 'success' && !empty($order->payload['tokovoucher']['sn'] ?? ''))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 flex flex-col gap-2">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Serial Number / Pesan</p>
                            <div class="flex items-center justify-between gap-3">
                                <code class="text-emerald-400 font-mono text-lg font-bold overflow-hidden text-ellipsis">{{ $order->payload['tokovoucher']['sn'] }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $order->payload['tokovoucher']['sn'] }}')" class="size-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-colors">
                                    <span class="material-icons-round text-sm">content_copy</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Help Link -->
        <div class="mt-8 text-center">
            <p class="text-slate-500 font-medium">Pesanan belum masuk? <a href="#" class="text-primary hover:underline font-bold transition-all ml-1">Hubungi CS WhatsApp</a></p>
        </div>
    </div>

    <!-- Latest Transactions Table -->
    <div class="w-full max-w-5xl mt-16 z-10 animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <div class="glass-panel rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-white/5 bg-white/2 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-icons-round text-primary">history</span>
                    <h2 class="text-xl font-black text-white tracking-tight">10 Transaksi Terakhir</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Real-time Update</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/1 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-white/5">
                            <th class="px-8 py-5">Tanggal</th>
                            <th class="px-8 py-5">No. Invoice</th>
                            <th class="px-8 py-5">Item</th>
                            <th class="px-8 py-5">Harga</th>
                            <th class="px-8 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($latestOrders as $lOrder)
                        <tr class="hover:bg-white/2 transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold text-slate-400 leading-tight">
                                    {{ $lOrder->created_at->format('d/m/Y') }}<br>
                                    <span class="text-[10px] font-medium opacity-50">{{ $lOrder->created_at->format('H:i') }} WIB</span>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-black text-white font-mono tracking-tight group-hover:text-primary transition-colors">{{ $lOrder->order_id }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="max-w-[200px] truncate">
                                    <span class="text-sm font-bold text-slate-300">{{ $lOrder->product_name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-black text-primary italic">Rp {{ number_format($lOrder->total_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-5 text-center">
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
                                        'paid', 'processing' => 'Proses game',
                                        'pending_payment' => 'Menunggu',
                                        'pending' => 'Pending',
                                        'failed' => 'Gagal',
                                        'failed_provider' => 'Gagal',
                                        'failed_permanent' => 'Gagal',
                                        default => ucfirst($lOrder->status)
                                    };
                                @endphp
                                <span class="px-3 py-1 bg-{{ $l_status_color }}-500/10 text-{{ $l_status_color }}-400 text-[10px] font-black rounded-lg border border-{{ $l_status_color }}-500/20 uppercase tracking-tighter">
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
