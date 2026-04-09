@extends('desktop.layouts.user')

@section('title', 'Isi Saldo — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Isi Saldo Akun')
@section('page_subtitle', 'Pilih metode pembayaran dan masukkan nominal deposit.')

@section('content')
<div class="max-w-5xl mx-auto w-full">
    <div class="content-card rounded-2xl p-8 md:p-12 space-y-12 relative overflow-hidden bg-white shadow-sm border border-corp-border">
        <div class="flex items-center justify-between mb-8 pb-8 border-b border-slate-100">
            <div>
                <h3 class="text-corp-navy font-bold text-lg uppercase tracking-wider">Saldo Saat Ini</h3>
                <p class="text-corp-muted text-xs font-semibold">Saldo Anda akan diperbarui otomatis setelah pembayaran terverifikasi.</p>
            </div>
            <div class="text-right">
                <p class="text-corp-accent font-black text-2xl">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
                <span class="text-[9px] font-bold text-green-500 uppercase tracking-widest">Akun Terverifikasi</span>
            </div>
        </div>

        <form action="{{ route('user.deposit.store') }}" method="POST" class="space-y-12">
            @csrf
            
            {{-- Nominal Input --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 ml-1">
                    <span class="size-6 rounded-lg bg-blue-500/10 text-corp-accent flex items-center justify-center text-[10px] font-bold italic border border-blue-500/20">01</span>
                    <label class="text-[11px] font-bold text-corp-muted uppercase tracking-[.3em]">NOMINAL DEPOSIT</label>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach([50000, 100000, 250000, 500000, 1000000, 2500000] as $amount)
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="amount" value="{{ $amount }}" class="peer sr-only">
                        <div class="p-5 text-center rounded-xl bg-slate-50 border border-corp-border peer-checked:border-corp-accent peer-checked:bg-blue-50 transition-all hover:bg-slate-100 group-hover:-translate-y-1">
                            <p class="text-corp-navy font-bold text-xs">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="absolute inset-0 rounded-xl border-2 border-corp-accent opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                    </label>
                    @endforeach
                </div>
                
                <div class="relative group">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-corp-accent font-bold text-sm group-focus-within:scale-110 transition-transform">RP</span>
                    <input type="number" name="custom_amount" id="custom_amount"
                           min="50000" max="2500000"
                           class="w-full bg-slate-50 border border-corp-border rounded-xl pl-16 pr-6 py-5 text-corp-navy font-bold text-lg focus:border-corp-accent focus:bg-white outline-none transition-all placeholder:text-slate-400"
                           placeholder="MIN: Rp 50.000, MAX: Rp 2.500.000">
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="space-y-8">
                <div class="flex items-center gap-3 ml-1">
                    <span class="size-6 rounded-lg bg-blue-500/10 text-corp-accent flex items-center justify-center text-[10px] font-bold italic border border-blue-500/20">02</span>
                    <label class="text-[11px] font-bold text-corp-muted uppercase tracking-[.3em]">METODE PEMBAYARAN</label>
                </div>
                
                <div class="space-y-12">
                    @foreach($paymentMethods->groupBy('type') as $type => $methods)
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-bold text-corp-accent uppercase tracking-[.3em] flex items-center gap-3">
                            <span class="w-10 h-px bg-corp-accent/20"></span>
                            {{ $type }}
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($methods as $pm)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="payment_method" value="{{ $pm->code }}" class="peer sr-only">
                                <div class="flex items-center gap-5 p-5 rounded-2xl bg-slate-50 border border-corp-border peer-checked:border-corp-accent peer-checked:bg-blue-50 transition-all hover:bg-slate-100 group-hover:translate-x-1">
                                    <div class="size-14 rounded-xl bg-white p-2 flex items-center justify-center overflow-hidden border border-corp-border shadow-sm group-hover:scale-105 transition-transform">
                                        <img src="{{ str_starts_with($pm->image, 'http') ? $pm->image : asset($pm->image) }}" alt="{{ $pm->name }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-corp-navy font-bold text-[11px] truncate uppercase">{{ $pm->name }}</p>
                                        <p class="text-[9px] text-corp-muted font-semibold uppercase tracking-wider mt-1 flex items-center gap-1.5">
                                            <span class="size-1 rounded-full bg-green-500 animate-pulse"></span>
                                            PROSES INSTAN
                                        </p>
                                    </div>
                                    <div class="size-6 rounded-full border-2 border-corp-border peer-checked:border-corp-accent peer-checked:bg-corp-accent flex items-center justify-center transition-all">
                                        <svg class="h-3 w-3 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" class="w-full bg-corp-accent text-white font-bold py-5 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 uppercase text-xs tracking-widest flex items-center justify-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 15l-4-4m0 0l4-4m-4 4h12M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    KONFIRMASI PEMBAYARAN
                </button>
                <p class="text-center text-[10px] text-corp-muted font-bold uppercase tracking-widest mt-8 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-7.618 3.04c0 4.833 1.254 9.408 3.447 13.513l.4-.73a11.955 11.955 0 0115.542-5.542l.4.73z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    TRANSAKSI TERENKRIPSI & AMAN
                </p>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customInput = document.getElementById('custom_amount');
        const radios = document.querySelectorAll('input[name="amount"]');

        customInput.addEventListener('input', function() {
            if (this.value) {
                radios.forEach(r => r.checked = false);
            }
        });

        radios.forEach(r => {
            r.addEventListener('change', function() {
                if (this.checked) {
                    customInput.value = this.value;
                }
            });
        });
    });
</script>
@endpush
@endsection
