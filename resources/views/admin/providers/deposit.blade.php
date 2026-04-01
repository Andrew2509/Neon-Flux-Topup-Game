@extends('admin.layouts.app')

@section('title', 'Topup Deposit')
@section('page_title', 'Topup Saldo ' . $provider->name)
@section('page_description', 'Buat tiket deposit untuk mengisi ulang saldo API Anda secara otomatis.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-6 md:p-8 rounded-3xl relative border border-white/5 overflow-hidden">
        <!-- Glow Effect -->
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

        <form action="{{ route('admin.providers.deposit.process', $provider->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nominal -->
            <div class="space-y-2 group">
                <label for="nominal" class="text-sm font-bold text-slate-300 group-focus-within:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    Nominal Topup
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                    <input type="number" id="nominal" name="nominal" min="10000" step="1" required class="w-full bg-white/5 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none" placeholder="Minimal 10.000">
                </div>
                @error('nominal')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            <!-- Metode Pembayaran -->
            <div class="space-y-2 group">
                <label for="metode" class="text-sm font-bold text-slate-300 group-focus-within:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                    Metode Pembayaran
                </label>
                <div class="relative">
                    <select id="metode" name="metode" required class="w-full bg-[#111322] border border-white/10 rounded-xl px-4 py-3 text-slate-100 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none appearance-none">
                        <option value="" disabled selected>Pilih Metode Topup...</option>
                        <option value="QRIS">QRIS (Semua E-Wallet/M-Banking)</option>
                        <option value="BCAVA">BCA Virtual Account</option>
                        <option value="MANDIRIVA">Mandiri Virtual Account</option>
                        <option value="BNIVA">BNI Virtual Account</option>
                        <option value="BRIVA">BRI Virtual Account</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Metode bayar merujuk pada kode Channel TokoVoucher.</p>
                @error('metode')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            <!-- Warning Box -->
            <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl flex gap-3 mb-6">
                <span class="material-symbols-outlined text-orange-400">warning</span>
                <div class="text-sm">
                    <p class="text-orange-400 font-bold mb-1">Penting</p>
                    <p class="text-orange-400/80">Setelah menekan tombol 'Buat Tiket', Anda akan menerima petunjuk pembayaran. Harap transfer sesuai _Nominal_ dan _Kode Unik_ (jika ada) yang diberikan.</p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-white/5">
                <a href="{{ route('admin.providers') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-colors text-sm font-bold">
                    Batal
                </a>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-[#0f172a] px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 hover:shadow-primary/40 flex items-center gap-2">
                    <span class="material-symbols-outlined">add_card</span>
                    Buat Tiket Deposit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
