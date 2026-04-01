@extends('admin.layouts.app')

@section('title', 'E-Wallet Reload')
@section('page_title', 'E-Wallet Reload (TokoVoucher)')
@section('page_description', 'Isi ulang saldo e-wallet tujuan melalui saldo deposit TokoVoucher.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-white/5 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 size-48 bg-primary/10 blur-3xl rounded-full"></div>

        <form action="{{ route('admin.withdrawals.ewallet.process') }}" method="POST" class="space-y-6 relative">
            @csrf

            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-300 ml-1">Pilih E-Wallet</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach($wallets as $wallet)
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="ewallet" value="{{ $wallet['code'] }}" class="peer sr-only" required>
                        <div class="p-3 text-center rounded-xl bg-white/5 border border-white/10 text-[10px] font-bold text-slate-400 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary hover:bg-white/10 transition-all uppercase tracking-wider">
                            {{ $wallet['name'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('ewallet')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="tujuan" class="block text-sm font-bold text-slate-300 ml-1">Nomor HP / ID E-Wallet</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">smartphone</span>
                    <input type="text" name="tujuan" id="tujuan" placeholder="Contoh: 08123456789" value="{{ old('tujuan') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-slate-100 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all tracking-widest" required>
                </div>
                @error('tujuan')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="nominal" class="block text-sm font-bold text-slate-300 ml-1">Nominal Reload (Minimal Rp 1.000)</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">account_balance_wallet</span>
                    <input type="number" name="nominal" id="nominal" placeholder="Contoh: 50000" value="{{ old('nominal') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-slate-100 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" required min="1000">
                </div>
                @error('nominal')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 rounded-2xl bg-primary/10 border border-primary/20 flex gap-3 text-primary">
                <span class="material-symbols-outlined shrink-0 text-xl">info</span>
                <p class="text-[11px] font-medium leading-relaxed italic text-slate-400">
                    Pastikan nomor tujuan dan pilihan e-wallet benar. Reload akan diproses secara instan melalui sistem TokoVoucher.
                </p>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 transition-all">
                <span class="material-symbols-outlined">bolt</span>
                Proses Reload
            </button>
        </form>
    </div>
</div>
@endsection
