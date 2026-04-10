@extends('hp.layouts.neonflux')

@section('title', 'Dashboard - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-5 pb-20">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold dark:text-white tracking-tight leading-tight">
                Halo, <span class="text-primary">{{ explode(' ', $user->name)[0] }}</span>!
            </h1>
            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5 opacity-80">Ringkasan akun Anda hari ini</p>
        </div>
        <a href="{{ route('user.profile') }}" class="size-9 rounded-full border-2 border-primary/20 overflow-hidden ring-2 ring-white/10">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff' }}" class="size-full object-cover">
        </a>
    </div>

    {{-- Balance Card --}}
    <div class="glass-panel-mobile p-5 rounded-[1.75rem] border border-primary/10 bg-linear-to-br from-primary/15 via-transparent to-transparent relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-20 h-20 bg-primary/20 rounded-full blur-2xl"></div>
        <div class="relative z-10 space-y-4">
            <div>
                <p class="text-[8.5px] font-bold text-primary uppercase tracking-[0.2em] mb-1 opacity-90">Saldo Anda</p>
                <h2 class="text-2xl font-orbitron font-bold dark:text-white tracking-tighter">Rp {{ number_format($user->balance, 0, ',', '.') }}</h2>
            </div>
            <a href="{{ route('user.deposit') }}" class="flex items-center justify-center gap-2 bg-primary text-slate-950 px-4 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[9.5px] active:scale-95 transition-all w-full shadow-lg shadow-primary/20">
                <span class="material-icons-round text-sm">add_card</span>
                Isi Saldo
            </a>
        </div>
    </div>

    {{-- Quick Menu --}}
    <div class="grid grid-cols-4 gap-2">
        <a href="{{ route('user.profile') }}" class="flex flex-col items-center gap-1.5 py-1">
            <div class="size-10.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90 active:bg-primary/10">
                <span class="material-icons-round text-xl">person</span>
            </div>
            <span class="text-[8px] font-bold uppercase tracking-widest dark:text-slate-400">Profil</span>
        </a>
        <a href="{{ route('user.riwayat') }}" class="flex flex-col items-center gap-1.5 py-1">
            <div class="size-10.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90 active:bg-primary/10">
                <span class="material-icons-round text-xl">history</span>
            </div>
            <span class="text-[8px] font-bold uppercase tracking-widest dark:text-slate-400">Riwayat</span>
        </a>
        <a href="{{ route('user.deposit.history') }}" class="flex flex-col items-center gap-1.5 py-1">
            <div class="size-10.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center dark:text-white transition-all active:scale-90 active:bg-primary/10">
                <span class="material-icons-round text-xl">account_balance_wallet</span>
            </div>
            <span class="text-[8px] font-bold uppercase tracking-widest dark:text-slate-400">Deposit</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="contents">
            @csrf
            <button type="submit" class="flex flex-col items-center gap-1.5 py-1">
                <div class="size-10.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-red-400 opacity-80 transition-all active:scale-90">
                    <span class="material-icons-round text-xl">logout</span>
                </div>
                <span class="text-[8px] font-bold uppercase tracking-widest dark:text-slate-400">Keluar</span>
            </button>
        </form>
    </div>

    {{-- Recent Transactions --}}
    <div class="space-y-3.5">
        <div class="flex items-center justify-between">
            <h3 class="text-[10px] font-bold dark:text-white uppercase tracking-widest flex items-center gap-1.5 opacity-90">
                <span class="material-icons-round text-primary text-xs">receipt_long</span>
                Transaksi Terakhir
            </h3>
            <a href="{{ route('user.riwayat') }}" class="text-[8px] font-bold text-primary uppercase tracking-widest hover:underline">Semua</a>
        </div>
        <div class="space-y-2">
            @forelse($recentOrders as $order)
            <div class="glass-panel-mobile p-3 rounded-2xl border border-white/5 flex items-center gap-3 active:bg-white/5 transition-colors">
                <div class="size-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-primary shrink-0">
                    <span class="material-icons-round text-lg">shopping_cart</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9.5px] font-bold dark:text-white truncate">{{ $order->product_name }}</p>
                    <p class="text-[8.5px] text-slate-500 font-medium tracking-tight mt-0.5">{{ $order->created_at->format('d M, H:i') }} • #{{ substr($order->order_id, -8) }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10.5px] font-bold dark:text-white tracking-tighter">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                    <p class="text-[7.5px] font-bold uppercase tracking-widest text-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-400' : 'red-400') }} mt-0.5 px-1.5 py-0.5 rounded bg-{{ $order->status === 'success' ? 'primary' : ($order->status === 'pending' ? 'amber-400' : 'red-400') }}/10 inline-block">{{ $order->status }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center glass-panel-mobile rounded-2xl border border-white/5">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Deposits --}}
    <div class="space-y-3.5">
        <div class="flex items-center justify-between">
            <h3 class="text-[10px] font-bold dark:text-white uppercase tracking-widest flex items-center gap-1.5 opacity-90">
                <span class="material-icons-round text-secondary text-xs">account_balance</span>
                Riwayat Deposit
            </h3>
            <a href="{{ route('user.deposit.history') }}" class="text-[8px] font-bold text-secondary uppercase tracking-widest hover:underline">Semua</a>
        </div>
        <div class="space-y-2">
            @forelse($recentDeposits as $deposit)
            <div class="glass-panel-mobile p-3 rounded-2xl border border-white/5 flex items-center gap-3 active:bg-white/5 transition-colors">
                <div class="size-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-secondary shrink-0">
                    <span class="material-icons-round text-lg">payment</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9.5px] font-bold dark:text-white truncate">{{ $deposit->method }}</p>
                    <p class="text-[8.5px] text-slate-500 font-medium tracking-tight mt-0.5">{{ $deposit->created_at->format('d M, H:i') }} • #{{ substr($deposit->deposit_id, -8) }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10.5px] font-bold dark:text-white tracking-tighter">Rp{{ number_format($deposit->amount, 0, ',', '.') }}</p>
                    <p class="text-[7.5px] font-bold uppercase tracking-widest text-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-400' : 'red-400') }} mt-0.5 px-1.5 py-0.5 rounded bg-{{ strtolower($deposit->status) === 'success' ? 'primary' : (strtolower($deposit->status) === 'pending' ? 'amber-400' : 'red-400') }}/10 inline-block">{{ $deposit->status }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center glass-panel-mobile rounded-2xl border border-white/5">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Belum ada deposit</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
