@extends('admin.layouts.app')

@section('title', 'Transfer Bank')
@section('page_title', 'Transfer Bank (TokoVoucher)')
@section('page_description', 'Kirim saldo deposit TokoVoucher ke rekening bank tujuan.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-white/5 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 size-48 bg-primary/10 blur-3xl rounded-full"></div>

        <form action="{{ route('admin.withdrawals.bank.process') }}" method="POST" class="space-y-6 relative">
            @csrf

            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-300 ml-1">Pilih Bank</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($banks as $bank)
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="bank" value="{{ $bank['code'] }}" class="peer sr-only" required>
                        <div class="p-3 text-center rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-slate-400 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary hover:bg-white/10 transition-all uppercase tracking-wider">
                            {{ $bank['name'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('bank')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="tujuan" class="block text-sm font-bold text-slate-300 ml-1">Nomor Rekening</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">account_balance</span>
                    <input type="text" name="tujuan" id="tujuan" placeholder="Contoh: 1234567890" value="{{ old('tujuan') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-slate-100 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all uppercase tracking-wider" required>
                </div>
                @error('tujuan')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="nominal" class="block text-sm font-bold text-slate-300 ml-1">Nominal Transfer (Minimal Rp 10.000)</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">payments</span>
                    <input type="number" name="nominal" id="nominal" placeholder="Contoh: 100000" value="{{ old('nominal') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-slate-100 outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" required min="10000">
                </div>
                @error('nominal')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 rounded-2xl bg-accent-red/10 border border-accent-red/20 flex gap-3 text-accent-red">
                <span class="material-symbols-outlined shrink-0 text-xl">warning</span>
                <p class="text-[11px] font-medium leading-relaxed">
                    Pastikan nomor rekening dan bank tujuan sudah benar. Saldo TokoVoucher Anda akan langsung terpotong setelah pengajuan diproses. Progres transaksi bisa dicek langsung di dashboard member TokoVoucher.
                </p>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 transition-all">
                <span class="material-symbols-outlined">send</span>
                Kirim Transfer
            </button>
        </form>
    </div>
</div>
@endsection
