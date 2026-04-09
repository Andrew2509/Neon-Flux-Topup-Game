@extends('hp.layouts.neonflux')

@section('title', 'Isi Saldo - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="space-y-6 pb-20">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('user.dashboard') }}" class="size-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white active:scale-90 transition-all">
                <span class="material-icons-round text-sm">arrow_back</span>
            </a>
            <h1 class="text-xl font-bold dark:text-white">Isi Saldo</h1>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Saldo</p>
            <p class="text-primary font-black text-sm">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
        </div>
    </div>

    <form action="{{ route('user.deposit.store') }}" method="POST" class="space-y-8">
        @csrf
        
        {{-- Nominal Section --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold dark:text-white uppercase tracking-widest flex items-center gap-2">
                <span class="size-5 rounded-full bg-primary text-slate-950 flex items-center justify-center text-[10px] font-black">1</span>
                Pilih Nominal
            </h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach([50000, 100000, 250000, 500000, 1000000, 2500000] as $amount)
                <label class="relative cursor-pointer group">
                    <input type="radio" name="amount" value="{{ $amount }}" class="peer sr-only">
                    <div class="p-3 text-center rounded-2xl bg-white/5 border border-white/10 peer-checked:border-primary peer-checked:bg-primary/10 transition-all">
                        <p class="text-white font-bold text-xs">Rp{{ number_format($amount, 0, ',', '.') }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                <input type="number" name="custom_amount" id="custom_amount"
                       min="50000" max="2500000"
                       class="w-full bg-black/20 border border-white/10 rounded-2xl pl-10 pr-4 py-3.5 text-white font-bold text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                       placeholder="Min: 50.000, Max: 2.500.000">
            </div>
        </div>

        {{-- Payment Method Section --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold dark:text-white uppercase tracking-widest flex items-center gap-2">
                <span class="size-5 rounded-full bg-primary text-slate-950 flex items-center justify-center text-[10px] font-black">2</span>
                Metode Pembayaran
            </h3>
            <div class="space-y-6">
                @foreach($paymentMethods->groupBy('type') as $type => $methods)
                <div class="space-y-3">
                    <p class="text-[9px] font-bold text-primary uppercase tracking-widest">{{ $type }}</p>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($methods as $pm)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="{{ $pm->code }}" class="peer sr-only">
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10 peer-checked:border-primary peer-checked:bg-primary/10 transition-all">
                                <div class="size-10 rounded-xl bg-white p-1.5 flex items-center justify-center overflow-hidden">
                                    <img src="{{ str_starts_with($pm->image, 'http') ? $pm->image : asset($pm->image) }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold text-[10px] truncate">{{ $pm->name }}</p>
                                    <p class="text-[8px] text-slate-500 uppercase tracking-widest mt-0.5">Otomatis</p>
                                </div>
                                <div class="size-4 rounded-full border-2 border-white/10 peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center transition-all">
                                    <div class="size-1.5 rounded-full bg-slate-950 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full bg-primary text-slate-950 font-bold py-4 rounded-2xl active:scale-95 transition-all text-xs uppercase tracking-[0.2em] shadow-lg shadow-primary/20 sticky bottom-4 z-10">
            Bayar Sekarang
        </button>
    </form>
</div>

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
@endsection
