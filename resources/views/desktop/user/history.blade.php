@extends('desktop.layouts.user')

@section('title', 'Riwayat Transaksi — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Riwayat Transaksi')
@section('page_subtitle', 'Pantau semua pesanan dan status transaksi Anda.')

@section('content')
<div class="space-y-6">
    <div class="content-card rounded-xl overflow-hidden bg-white border border-corp-border shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-corp-border">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">ID Transaksi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Produk & Layanan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-corp-border">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-bold text-corp-accent uppercase">#{{ strtoupper($order->order_id) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-corp-navy">{{ $order->product_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] text-corp-muted font-semibold uppercase">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold text-corp-navy uppercase px-2 py-1 bg-slate-100 rounded-lg border border-corp-border">{{ $order->payment_method }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-corp-navy text-xs">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClasses = match($order->status) {
                                    'success' => 'bg-green-50 text-green-600 border-green-100',
                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'failed', 'cancel' => 'bg-red-50 text-red-600 border-red-100',
                                    default => 'bg-slate-50 text-corp-muted border-corp-border'
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 {{ $statusClasses }} text-[9px] font-bold uppercase tracking-wider rounded-full border">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-24">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-corp-navy uppercase tracking-widest">Belum Ada Transaksi</h3>
                                <p class="text-[10px] text-corp-muted mt-1">Riwayat pesanan Anda akan muncul setelah Anda melakukan pembelian.</p>
                                <a href="{{ url('/') }}" class="mt-6 px-6 py-2 bg-corp-accent text-white text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-blue-700 transition-all shadow-md">Beli Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-6 border-t border-corp-border bg-slate-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
