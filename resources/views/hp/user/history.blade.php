@extends('hp.layouts.neonflux')

@section('title', 'Riwayat - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-6 pb-20">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-primary">history</span>
            Riwayat Pesanan
        </h1>
        <div class="px-3 py-1 bg-white/5 border border-white/10 rounded-full">
            <span class="text-[10px] font-bold dark:text-white uppercase tracking-widest">{{ $orders->total() }} Transaksi</span>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
        <div class="glass-panel-mobile p-4 rounded-3xl border border-black/5 dark:border-white/10 flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-primary font-orbitron uppercase tracking-tighter">{{ $order->order_id }}</p>
                    <h3 class="text-sm font-bold dark:text-white mt-0.5">{{ $order->product_name }}</h3>
                </div>
                @php
                    $statusColor = match($order->status) {
                        'success' => 'primary',
                        'pending' => 'amber',
                        'failed', 'cancel' => 'red',
                        default => 'slate'
                    };
                    $statusShadow = match($order->status) {
                        'success' => 'neon-cyan',
                        'pending' => 'none',
                        'failed', 'cancel' => 'neon-magenta',
                        default => 'none'
                    };
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-widest bg-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-500' : 'red-500') }}/10 text-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-400' : 'red-400') }} border border-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-500' : 'red-500') }}/20">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            
            <div class="h-px bg-black/5 dark:bg-white/5"></div>
            
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <p class="text-[9px] text-slate-500 font-medium tracking-tight">
                        {{ $order->created_at->format('d M Y, H:i') }} • {{ $order->payment_method }}
                    </p>
                    <p class="text-base font-bold dark:text-white tracking-tighter">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" class="bg-primary/10 text-primary border border-primary/20 size-8 rounded-xl flex items-center justify-center active:scale-90 transition-all">
                    <span class="material-icons-round text-lg">receipt</span>
                </a>
            </div>
        </div>
        @empty
        <div class="py-20 text-center space-y-4">
            <span class="material-icons-round text-6xl text-slate-200 dark:text-white/5">history</span>
            <p class="text-xs text-slate-500 dark:text-white/40 font-bold uppercase tracking-widest">Belum ada riwayat pesanan</p>
            <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-primary text-slate-950 rounded-2xl text-xs font-bold uppercase tracking-widest transition-all active:scale-95">Mulai Belanja</a>
        </div>
        @endforelse
    </div>

    @if($orders->hasPages())
    <div class="mt-4">
        {{ $orders->links('vendor.pagination.neonflux-mobile') }}
    </div>
    @endif
</div>
@endsection
