@extends('hp.layouts.neonflux')

@section('title', 'Riwayat Deposit - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-6 pb-20">
    <div class="flex items-center gap-2">
        <a href="{{ route('user.dashboard') }}" class="size-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white active:scale-90 transition-all">
            <span class="material-icons-round text-sm">arrow_back</span>
        </a>
        <h1 class="text-xl font-bold dark:text-white">Riwayat Deposit</h1>
    </div>

    <div class="space-y-4">
        @forelse($deposits as $deposit)
        <div class="glass-panel-mobile p-4 rounded-2xl border border-white/5 flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-secondary font-orbitron uppercase tracking-tighter">#{{ $deposit->deposit_id }}</p>
                    <h3 class="text-sm font-bold dark:text-white truncate mt-0.5">{{ $deposit->method }}</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-widest bg-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-500' : 'red-500') }}/10 text-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-400' : 'red-400') }} border border-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-500' : 'red-500') }}/20">
                    {{ $deposit->status }}
                </span>
            </div>
            
            <div class="h-px bg-white/5"></div>
            
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <p class="text-[9px] text-slate-500 font-medium tracking-tight">
                        {{ $deposit->created_at->format('d M Y, H:i') }}
                    </p>
                    <p class="text-base font-bold dark:text-white tracking-tighter">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</p>
                </div>
                <div class="size-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-secondary">
                    <span class="material-icons-round text-lg">account_balance_wallet</span>
                </div>
            </div>
        </div>
        @empty
        <div class="py-20 text-center space-y-4">
            <span class="material-icons-round text-6xl text-white/5">account_balance_wallet</span>
            <p class="text-xs text-slate-500 dark:text-white/40 font-bold uppercase tracking-widest">Belum ada riwayat deposit</p>
            <a href="{{ route('user.deposit') }}" class="inline-block px-8 py-3 bg-secondary text-white rounded-2xl text-xs font-bold uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-secondary/20">Isi Saldo</a>
        </div>
        @endforelse
    </div>

    @if($deposits->hasPages())
    <div class="mt-4">
        {{ $deposits->links('vendor.pagination.neonflux-mobile') }}
    </div>
    @endif
</div>
@endsection
