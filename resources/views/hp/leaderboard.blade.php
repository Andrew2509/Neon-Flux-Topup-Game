@extends('hp.layouts.neonflux')

@section('title', 'Leaderboard Player - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4 relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center relative z-10">
            <span class="material-icons-round text-primary text-2xl">emoji_events</span>
        </div>
        <div class="relative z-10">
            <h1 class="text-lg font-bold text-slate-900">Leaderboard</h1>
            <p class="text-[11px] text-slate-500">Top 10 Player Teraktif Bulan Ini</p>
        </div>
    </div>

    {{-- Top 3 Podium --}}
    @if($topSpenders->count() >= 3)
    <div class="flex items-end justify-center gap-2 px-2 pt-8 pb-4">
        {{-- Rank 2 --}}
        <div class="flex flex-col items-center gap-2 flex-1">
            <div class="relative">
                <div class="h-16 w-16 rounded-2xl bg-slate-100 p-1">
                    <img src="{{ $topSpenders[1]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[1]->user->name).'&background=64748b&color=fff' }}" 
                         class="h-full w-full rounded-xl object-cover" alt="">
                </div>
                <div class="absolute -top-3 -left-3 h-8 w-8 rounded-full bg-slate-400 border-4 border-white flex items-center justify-center shadow-lg">
                    <span class="text-[10px] font-bold text-white">2</span>
                </div>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-700 truncate w-16">{{ explode(' ', $topSpenders[1]->user->name)[0] }}</p>
                <p class="text-[8px] font-medium text-primary">Rp{{ number_format($topSpenders[1]->total_spent, 0, ',', '.') }}</p>
            </div>
            <div class="w-full h-16 bg-slate-50 rounded-t-xl border-x border-t border-slate-100 flex items-end justify-center pb-2">
                <span class="text-slate-300 font-black text-2xl">2nd</span>
            </div>
        </div>

        {{-- Rank 1 --}}
        <div class="flex flex-col items-center gap-2 flex-1 -mt-4">
            <div class="relative">
                <div class="h-20 w-20 rounded-2xl bg-primary/10 p-1 border-2 border-primary">
                    <img src="{{ $topSpenders[0]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[0]->user->name).'&background=6366f1&color=fff' }}" 
                         class="h-full w-full rounded-xl object-cover" alt="">
                </div>
                <div class="absolute -top-4 -left-4 h-10 w-10 rounded-full bg-primary border-4 border-white flex items-center justify-center shadow-lg animate-bounce">
                    <span class="material-icons-round text-white text-xs">emoji_events</span>
                </div>
            </div>
            <div class="text-center">
                <p class="text-xs font-bold text-slate-900 truncate w-20">{{ explode(' ', $topSpenders[0]->user->name)[0] }}</p>
                <p class="text-[10px] font-bold text-primary">Rp{{ number_format($topSpenders[0]->total_spent, 0, ',', '.') }}</p>
            </div>
            <div class="w-full h-24 bg-primary/5 rounded-t-2xl border-x border-t border-primary/10 flex items-end justify-center pb-3">
                <span class="text-primary/20 font-black text-4xl">1st</span>
            </div>
        </div>

        {{-- Rank 3 --}}
        <div class="flex flex-col items-center gap-2 flex-1">
            <div class="relative">
                <div class="h-16 w-16 rounded-2xl bg-orange-50 p-1 border border-orange-100">
                    <img src="{{ $topSpenders[2]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[2]->user->name).'&background=f97316&color=fff' }}" 
                         class="h-full w-full rounded-xl object-cover" alt="">
                </div>
                <div class="absolute -top-3 -left-3 h-8 w-8 rounded-full bg-orange-400 border-4 border-white flex items-center justify-center shadow-lg">
                    <span class="text-[10px] font-bold text-white">3</span>
                </div>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-700 truncate w-16">{{ explode(' ', $topSpenders[2]->user->name)[0] }}</p>
                <p class="text-[8px] font-medium text-primary">Rp{{ number_format($topSpenders[2]->total_spent, 0, ',', '.') }}</p>
            </div>
            <div class="w-full h-12 bg-orange-50/50 rounded-t-xl border-x border-t border-orange-100 flex items-end justify-center pb-2">
                <span class="text-orange-200 font-black text-2xl">3rd</span>
            </div>
        </div>
    </div>
    @endif

    {{-- List Section --}}
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100">
        <div class="p-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Peringkat Lainnya</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($topSpenders as $index => $spender)
                @if($index >= 3 || $topSpenders->count() < 3)
                <div class="p-4 flex items-center gap-4">
                    <div class="w-6 text-center text-xs font-bold text-slate-400">
                        {{ $index + 1 }}
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                        <img src="{{ $spender->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($spender->user->name).'&background=f1f5f9&color=64748b' }}" 
                             class="h-full w-full object-cover" alt="">
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900">{{ $spender->user->name }}</h4>
                        <p class="text-[10px] text-slate-500">{{ $spender->total_orders }} Pesanan Sukses</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-primary">Rp{{ number_format($spender->total_spent, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif
            @empty
                <div class="p-10 text-center space-y-2">
                    <span class="material-icons-round text-slate-200 text-4xl">analytics</span>
                    <p class="text-xs text-slate-500 font-medium">Belum ada data transaksi yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
