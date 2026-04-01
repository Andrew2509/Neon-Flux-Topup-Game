@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description', 'Selamat datang kembali di pusat kendali Neon Flux.')

@section('content')
<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 size-24 bg-primary/20 blur-3xl rounded-full group-hover:bg-primary/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary neon-border-blue">
                <span class="material-symbols-outlined text-3xl">trending_up</span>
            </div>
            <span class="text-blue-400 text-xs font-bold bg-blue-400/10 px-2 py-1 rounded-full">+{{ rand(1, 15) }}%</span>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Total Penjualan</p>
        <h3 class="text-2xl font-bold">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</h3>
    </div>
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 size-24 bg-accent-blue/20 blur-3xl rounded-full group-hover:bg-accent-blue/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-accent-blue/10 flex items-center justify-center text-accent-blue neon-border-blue">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <span class="text-blue-400 text-xs font-bold bg-blue-400/10 px-2 py-1 rounded-full">+{{ rand(1, 10) }}%</span>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">User Aktif</p>
        <h3 class="text-2xl font-bold">{{ number_format($stats['active_users'], 0, ',', '.') }}</h3>
    </div>
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 size-24 bg-accent-red/20 blur-3xl rounded-full group-hover:bg-accent-red/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-accent-red/10 flex items-center justify-center text-accent-red neon-border-red">
                <span class="material-symbols-outlined text-3xl">pending_actions</span>
            </div>
            <span class="text-{{ $stats['pending_orders'] > 50 ? 'red' : 'amber' }}-400 text-xs font-bold bg-{{ $stats['pending_orders'] > 50 ? 'red' : 'amber' }}-400/10 px-2 py-1 rounded-full">{{ $stats['pending_orders'] > 50 ? 'High' : 'Normal' }}</span>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Pesanan Pending</p>
        <h3 class="text-2xl font-bold">{{ $stats['pending_orders'] }}</h3>
    </div>
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 size-24 bg-primary/20 blur-3xl rounded-full group-hover:bg-primary/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary neon-border-blue">
                <span class="material-symbols-outlined text-3xl">account_balance</span>
            </div>
            <span class="text-blue-400 text-xs font-bold bg-blue-400/10 px-2 py-1 rounded-full">+{{ rand(1, 12) }}%</span>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Total Deposit</p>
        <h3 class="text-2xl font-bold">Rp {{ number_format($stats['total_deposits'], 0, ',', '.') }}</h3>
    </div>
</div>
<!-- Main Content Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Transactions Table -->
    <div class="xl:col-span-2 glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-bold text-lg">Transaksi Terbaru</h3>
            <a href="{{ route('admin.orders') }}" class="text-primary text-sm font-bold hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">ID Pesanan</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recent_orders as $order)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">{{ $order->order_id }}</td>
                        <td class="px-6 py-4 text-sm">{{ $order->user->name ?? 'Guest' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-400 truncate max-w-[150px]">{{ $order->product_name }}</td>
                        <td class="px-6 py-4 text-sm font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $status_color = match($order->status) {
                                    'success' => 'blue',
                                    'pending' => 'amber',
                                    'failed' => 'red',
                                    default => 'slate'
                                };
                            @endphp
                            <span class="px-3 py-1 bg-{{ $status_color }}-500/10 text-{{ $status_color }}-400 text-[10px] font-bold rounded-full border border-{{ $status_color }}-500/20">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Side Stats / Activity -->
    <div class="space-y-8">
        <!-- Top Provider Chart Placeholder -->
        <div class="glass-panel p-6 rounded-2xl border border-white/5">
            <h3 class="font-bold text-lg mb-6">Provider Terlaris</h3>
            <div class="space-y-4">
                @php
                    $colors = ['primary', 'accent-red', 'accent-blue', 'slate-600'];
                    $total_orders = $top_providers->sum('count') ?: 1;
                @endphp
                @forelse($top_providers as $index => $provider)
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-wider">
                        <span>{{ $provider->product_name }}</span>
                        <span class="text-{{ $colors[$index] ?? 'primary' }}">{{ round(($provider->count / $total_orders) * 100) }}%</span>
                    </div>
                    <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $colors[$index] ?? 'primary' }} rounded-full" style="width: {{ ($provider->count / $total_orders) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>
        <!-- Recent Activity -->
        <div class="glass-panel p-6 rounded-2xl border border-white/5">
            <h3 class="font-bold text-lg mb-6">Aktivitas Terakhir</h3>
            <div class="space-y-6">
                @forelse($recent_orders as $order)
                <div class="flex gap-4">
                    <div class="size-2 mt-2 rounded-full bg-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber' : 'red') }}"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">Order {{ $order->product_name }} - {{ ucfirst($order->status) }}</p>
                        <p class="text-[10px] text-slate-500 uppercase font-bold mt-1">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500 italic">Tidak ada aktivitas baru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
