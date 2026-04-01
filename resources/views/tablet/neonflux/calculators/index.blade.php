@extends('tablet.layouts.neonflux')

@section('title', 'Kalkulator Gaming')

@section('content')
<div class="min-h-[80vh] py-12 flex flex-col items-center justify-center">
    <div class="text-center space-y-3 mb-12">
        <div class="inline-flex size-20 rounded-3_5xl bg-primary/10 text-primary items-center justify-center border border-primary/20 shadow-xl shadow-primary/5">
            <span class="material-icons-round text-5xl">calculate</span>
        </div>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Kalkulator Gaming</h1>
        <p class="text-slate-400 font-medium">Lengkapi kebutuhan MLBB Anda dengan alat bantu terpercaya kami.</p>
    </div>

    <div class="grid grid-cols-3 gap-8 w-full max-w-4xl">
        <a href="{{ route('kalkulator.winrate') }}" class="glass-panel p-8 rounded-4xl border-white/5 flex flex-col items-center text-center space-y-4 hover:border-primary/50 transition-all group">
            <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-slate-950 transition-all duration-500">
                <span class="material-icons-round text-3xl">trending_up</span>
            </div>
            <div>
                <p class="text-lg font-black text-white">Win Rate</p>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Hitung jumlah match untuk mencapai target WR Anda.</p>
            </div>
        </a>

        <a href="{{ route('kalkulator.magicwheel') }}" class="glass-panel p-8 rounded-4xl border-white/5 flex flex-col items-center text-center space-y-4 hover:border-primary/50 transition-all group">
            <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-slate-950 transition-all duration-500">
                <span class="material-icons-round text-3xl">auto_fix_high</span>
            </div>
            <div>
                <p class="text-lg font-black text-white">Magic Wheel</p>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Estimasi diamond maksimal untuk Skin Legends.</p>
            </div>
        </a>

        <a href="{{ route('kalkulator.zodiac') }}" class="glass-panel p-8 rounded-4xl border-white/5 flex flex-col items-center text-center space-y-4 hover:border-primary/50 transition-all group">
            <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-slate-950 transition-all duration-500">
                <span class="material-icons-round text-3xl">stars</span>
            </div>
            <div>
                <p class="text-lg font-black text-white">Zodiac</p>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Kalkulasi star power untuk skin zodiak idaman.</p>
            </div>
        </a>
    </div>
</div>
@endsection
