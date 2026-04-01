@extends('hp.layouts.neonflux')

@section('title', 'Dashboard - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-6 pb-20">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold dark:text-white tracking-tight">
                Halo, <span class="text-primary">{{ explode(' ', $user->name)[0] }}</span>!
            </h1>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Ringkasan akun Anda hari ini</p>
        </div>
        <a href="{{ route('user.profile') }}" class="size-10 rounded-full border-2 border-primary/20 overflow-hidden">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff' }}" class="size-full object-cover">
        </a>
    </div>

    {{-- Balance Card --}}
    <div class="glass-panel-mobile p-6 rounded-[2rem] border border-primary/10 bg-linear-to-br from-primary/10 to-transparent relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/20 rounded-full blur-2xl transition-all"></div>
        <div class="relative z-10 space-y-4">
            <div>
                <p class="text-[9px] font-bold text-primary uppercase tracking-[0.2em] mb-1">Saldo Anda</p>
                <h2 class="text-3xl font-orbitron font-bold dark:text-white tracking-tighter">Rp {{ number_format($user->balance, 0, ',', '.') }}</h2>
            </div>
            <a href="{{ route('user.deposit') }}" class="flex items-center justify-center gap-2 bg-primary text-slate-950 px-4 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[10px] active:scale-95 transition-all w-full shadow-lg shadow-primary/20">
                <span class="material-icons-round text-sm">add_card</span>
                Isi Saldo
            </a>
        </div>
    </div>

    {{-- Quick Menu --}}
    <div class="grid grid-cols-4 gap-3">
        <a href="{{ route('user.profile') }}" class="flex flex-col items-center gap-2">
            <div class="size-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90">
                <span class="material-icons-round">person</span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-widest dark:text-slate-400">Profil</span>
        </a>
        <a href="{{ route('user.riwayat') }}" class="flex flex-col items-center gap-2">
            <div class="size-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90">
                <span class="material-icons-round">history</span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-widest dark:text-slate-400">Riwayat</span>
        </a>
        <a href="{{ route('user.deposit.history') }}" class="flex flex-col items-center gap-2">
            <div class="size-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90">
                <span class="material-icons-round">account_balance_wallet</span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-widest dark:text-slate-400">Deposit</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="contents">
            @csrf
            <button type="submit" class="flex flex-col items-center gap-2">
                <div class="size-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-red-500 transition-all active:scale-90">
                    <span class="material-icons-round">logout</span>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-widest dark:text-slate-400">Keluar</span>
            </button>
        </form>
    </div>

    {{-- Recent Transactions --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold dark:text-white uppercase tracking-widest flex items-center gap-2">
                <span class="material-icons-round text-primary text-sm">receipt_long</span>
                Transaksi Terakhir
            </h3>
            <a href="{{ route('user.riwayat') }}" class="text-[9px] font-bold text-primary uppercase tracking-widest">Semua</a>
        </div>
        <div class="space-y-3">
            @forelse($recentOrders as $order)
            <div class="glass-panel-mobile p-4 rounded-2xl border border-white/5 flex items-center gap-4">
                <div class="size-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-primary">
                    <span class="material-icons-round">shopping_cart</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold dark:text-white truncate">{{ $order->product_name }}</p>
                    <p class="text-[9px] text-slate-500 font-medium tracking-tight mt-0.5">{{ $order->created_at->format('d M, H:i') }} • #{{ substr($order->order_id, -8) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold dark:text-white tracking-tighter">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                    <p class="text-[8px] font-bold uppercase tracking-widest text-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-400' : 'red-400') }} mt-0.5">{{ $order->status }}</p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center glass-panel-mobile rounded-2xl border border-white/5">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Deposits --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold dark:text-white uppercase tracking-widest flex items-center gap-2">
                <span class="material-icons-round text-secondary text-sm">account_balance</span>
                Riwayat Deposit
            </h3>
            <a href="{{ route('user.deposit.history') }}" class="text-[9px] font-bold text-secondary uppercase tracking-widest">Semua</a>
        </div>
        <div class="space-y-3">
            @forelse($recentDeposits as $deposit)
            <div class="glass-panel-mobile p-4 rounded-2xl border border-white/5 flex items-center gap-4">
                <div class="size-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-secondary">
                    <span class="material-icons-round">payment</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold dark:text-white truncate">{{ $deposit->method }}</p>
                    <p class="text-[9px] text-slate-500 font-medium tracking-tight mt-0.5">{{ $deposit->created_at->format('d M, H:i') }} • #{{ substr($deposit->deposit_id, -8) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold dark:text-white tracking-tighter">Rp{{ number_format($deposit->amount, 0, ',', '.') }}</p>
                    <p class="text-[8px] font-bold uppercase tracking-widest text-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-400' : 'red-400') }} mt-0.5">{{ $deposit->status }}</p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center glass-panel-mobile rounded-2xl border border-white/5">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Belum ada deposit</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
