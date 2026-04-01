@extends('hp.layouts.neonflux')

@section('title', 'Kalkulator Gaming')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Header -->
    <div class="flex flex-col items-center text-center space-y-2 py-4">
        <div class="size-16 rounded-3xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-lg shadow-primary/5">
            <span class="material-icons-round text-3xl">calculate</span>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kalkulator Gaming</h1>
        <p class="text-slate-500 text-xs font-semibold leading-relaxed px-6">Gunakan alat bantu kami untuk memaksimalkan pengalaman bermain Anda.</p>
    </div>

    <!-- Grid Items -->
    <div class="grid grid-cols-1 gap-4">
        <a href="{{ route('kalkulator.winrate') }}" class="glass-panel-mobile p-5 rounded-3xl border-slate-200 flex items-center gap-5 active:scale-[0.98] transition-all">
            <div class="size-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600 border border-blue-500/20">
                <span class="material-icons-round text-2xl">trending_up</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-slate-900">Win Rate</p>
                <p class="text-[10px] text-slate-500 font-medium">Hitung target kemenangan Anda.</p>
            </div>
            <span class="material-icons-round text-slate-300">chevron_right</span>
        </a>

        <a href="{{ route('kalkulator.magicwheel') }}" class="glass-panel-mobile p-5 rounded-3xl border-slate-200 flex items-center gap-5 active:scale-[0.98] transition-all">
            <div class="size-14 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-600 border border-purple-500/20">
                <span class="material-icons-round text-2xl">auto_fix_high</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-slate-900">Magic Wheel</p>
                <p class="text-[10px] text-slate-500 font-medium">Estimasi diamond Skin Legends.</p>
            </div>
            <span class="material-icons-round text-slate-300">chevron_right</span>
        </a>

        <a href="{{ route('kalkulator.zodiac') }}" class="glass-panel-mobile p-5 rounded-3xl border-slate-200 flex items-center gap-5 active:scale-[0.98] transition-all">
            <div class="size-14 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600 border border-amber-500/20">
                <span class="material-icons-round text-2xl">stars</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-slate-900">Zodiac</p>
                <p class="text-[10px] text-slate-500 font-medium">Hitung star power skin Zodiac.</p>
            </div>
            <span class="material-icons-round text-slate-300">chevron_right</span>
        </a>
    </div>
</div>
@endsection
