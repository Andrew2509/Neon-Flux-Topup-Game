@extends('desktop.layouts.user')

@section('title', 'Riwayat Deposit — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Riwayat Deposit')
@section('page_subtitle', 'Daftar semua pengisian saldo akun Anda.')

@section('content')
<div class="space-y-6">
    <div class="content-card rounded-xl overflow-hidden bg-white border border-corp-border shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-corp-border">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">ID Deposit</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Metode Pembayaran</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-center">Nominal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-corp-border">
                    @forelse($deposits as $deposit)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-bold text-corp-navy uppercase">#{{ strtoupper($deposit->deposit_id) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-corp-navy uppercase">{{ $deposit->method }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-corp-navy">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] text-corp-muted font-semibold uppercase">{{ $deposit->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @php
                                $status = strtolower($deposit->status);
                                $statusClasses = match($status) {
                                    'success' => 'bg-green-50 text-green-600 border-green-100',
                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'failed', 'cancel' => 'bg-red-50 text-red-600 border-red-100',
                                    default => 'bg-slate-50 text-corp-muted border-corp-border'
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 {{ $statusClasses }} text-[9px] font-bold uppercase tracking-wider rounded-full border">
                                {{ strtoupper($deposit->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-corp-navy uppercase tracking-widest">Tidak Ada Riwayat</h3>
                                <p class="text-[10px] text-corp-muted mt-1">Sistem belum mendeteksi adanya riwayat pengisian saldo.</p>
                                <a href="{{ route('user.deposit') }}" class="mt-6 px-6 py-2 bg-corp-accent text-white text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-blue-700 transition-all shadow-md">Isi Saldo Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($deposits->hasPages())
        <div class="px-6 py-6 border-t border-corp-border bg-slate-50">
            {{ $deposits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
