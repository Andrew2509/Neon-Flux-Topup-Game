@extends('desktop.layouts.neonflux')

@section('title', 'Leaderboard Player - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="glass-panel rounded-3xl p-12 mb-10 relative overflow-hidden text-center">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-6">
                    <span class="material-icons-round text-primary text-4xl">emoji_events</span>
                </div>
                <h1 class="text-4xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                    Player <span class="text-primary">Leaderboard</span>
                </h1>
                <p class="text-slate-500 dark:text-gray-400 max-w-2xl mx-auto">
                    Penghargaan untuk player teraktif bulan ini. Jadilah bagian dari jajaran top player dan nikmati keuntungan eksklusif di {{ get_setting('site_name', 'Prince Pay') }}.
                </p>
            </div>
        </div>

        {{-- Podium Section --}}
        @if($topSpenders->count() >= 3)
        <div class="flex flex-col md:flex-row items-end justify-center gap-8 mb-16 pt-10">
            {{-- Rank 2 --}}
            <div class="flex-1 w-full md:w-auto order-2 md:order-1">
                <div class="glass-panel rounded-3xl p-8 pb-0 text-center group hover:border-slate-300 transition-all duration-500 relative">
                    <div class="absolute -top-12 left-1/2 -translate-x-1/2">
                        <div class="h-24 w-24 rounded-3xl bg-slate-100 dark:bg-slate-800 p-1 border-4 border-white dark:border-slate-900 shadow-xl group-hover:scale-110 transition-transform duration-500">
                             <img src="{{ $topSpenders[1]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[1]->user->name).'&background=64748b&color=fff' }}" 
                                 class="h-full w-full rounded-2xl object-cover" alt="">
                        </div>
                        <div class="absolute -top-4 -right-4 h-10 w-10 rounded-full bg-slate-400 border-4 border-white dark:border-slate-900 flex items-center justify-center text-white font-bold shadow-lg">
                            2
                        </div>
                    </div>
                    <div class="mt-12 mb-8 space-y-1">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $topSpenders[1]->user->name }}</h3>
                        <p class="text-primary font-bold">Rp{{ number_format($topSpenders[1]->total_spent, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400 font-medium">{{ $topSpenders[1]->total_orders }} Transaksi</p>
                    </div>
                    <div class="h-40 bg-slate-50/50 dark:bg-white/5 rounded-t-3xl border-x border-t border-slate-100 dark:border-white/10 flex items-center justify-center">
                        <span class="text-slate-200 dark:text-white/5 font-black text-6xl">2nd</span>
                    </div>
                </div>
            </div>

            {{-- Rank 1 --}}
            <div class="flex-1 w-full md:w-auto order-1 md:order-2 scale-110 relative z-10">
                <div class="glass-panel rounded-3xl p-8 pb-0 text-center border-primary/30 group hover:border-primary/50 transition-all duration-500 relative">
                    <div class="absolute -top-16 left-1/2 -translate-x-1/2">
                        <div class="h-32 w-32 rounded-3xl bg-primary/10 p-1 border-4 border-primary shadow-2xl group-hover:scale-110 transition-transform duration-500">
                             <img src="{{ $topSpenders[0]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[0]->user->name).'&background=6366f1&color=fff' }}" 
                                 class="h-full w-full rounded-2xl object-cover" alt="">
                        </div>
                        <div class="absolute -top-6 -right-6 h-14 w-14 rounded-full bg-primary border-4 border-white dark:border-slate-900 flex items-center justify-center text-white font-bold shadow-2xl animate-pulse">
                            <span class="material-icons-round text-2xl">emoji_events</span>
                        </div>
                    </div>
                    <div class="mt-16 mb-8 space-y-1">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $topSpenders[0]->user->name }}</h3>
                        <p class="text-primary font-black text-lg">Rp{{ number_format($topSpenders[0]->total_spent, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400 font-medium">{{ $topSpenders[0]->total_orders }} Transaksi</p>
                    </div>
                    <div class="h-56 bg-primary/5 rounded-t-3xl border-x border-t border-primary/10 flex items-center justify-center">
                        <span class="text-primary/10 font-black text-8xl">1st</span>
                    </div>
                </div>
            </div>

            {{-- Rank 3 --}}
            <div class="flex-1 w-full md:w-auto order-3">
                <div class="glass-panel rounded-3xl p-8 pb-0 text-center group hover:border-orange-300 transition-all duration-500 relative text-orange-600">
                    <div class="absolute -top-12 left-1/2 -translate-x-1/2">
                        <div class="h-24 w-24 rounded-3xl bg-orange-50 dark:bg-orange-950 p-1 border-4 border-white dark:border-slate-900 shadow-xl group-hover:scale-110 transition-transform duration-500">
                             <img src="{{ $topSpenders[2]->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($topSpenders[2]->user->name).'&background=f97316&color=fff' }}" 
                                 class="h-full w-full rounded-2xl object-cover" alt="">
                        </div>
                        <div class="absolute -top-4 -right-4 h-10 w-10 rounded-full bg-orange-400 border-4 border-white dark:border-slate-900 flex items-center justify-center text-white font-bold shadow-lg">
                            3
                        </div>
                    </div>
                    <div class="mt-12 mb-8 space-y-1">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $topSpenders[2]->user->name }}</h3>
                        <p class="text-primary font-bold">Rp{{ number_format($topSpenders[2]->total_spent, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400 font-medium">{{ $topSpenders[2]->total_orders }} Transaksi</p>
                    </div>
                    <div class="h-32 bg-orange-50/30 dark:bg-white/5 rounded-t-3xl border-x border-t border-orange-100 dark:border-white/10 flex items-center justify-center">
                        <span class="text-orange-100 dark:text-white/5 font-black text-6xl">3rd</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Table Section --}}
        <div class="glass-panel rounded-4xl overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <span class="material-icons-round text-primary">format_list_numbered</span>
                    Peringkat Player
                </h3>
                <span class="px-4 py-2 bg-slate-50 dark:bg-white/5 rounded-full text-xs font-bold text-slate-500 dark:text-gray-400">
                    Update Berkala
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5">
                            <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest w-20">Rank</th>
                            <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Player</th>
                            <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Pesanan</th>
                            <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Total Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($topSpenders as $index => $spender)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-6">
                                <span class="font-display font-black text-lg {{ $index < 3 ? 'text-primary' : 'text-slate-300 dark:text-white/20' }}">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 dark:bg-slate-800 p-0.5 shrink-0 group-hover:scale-110 transition-transform">
                                        <img src="{{ $spender->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($spender->user->name).'&background=f1f5f9&color=64748b' }}" 
                                             class="h-full w-full rounded-[14px] object-cover" alt="">
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">{{ $spender->user->name }}</h4>
                                        <p class="text-xs text-slate-400 dark:text-gray-500 line-clamp-1 italic">{{ $spender->user->email ?? 'Verified Player' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 rounded-lg text-xs font-bold text-slate-600 dark:text-gray-400">
                                    {{ $spender->total_orders }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-lg font-black text-primary">Rp{{ number_format($spender->total_spent, 0, ',', '.') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="space-y-4 max-w-xs mx-auto">
                                    <div class="w-16 h-16 rounded-3xl bg-slate-50 dark:bg-white/5 flex items-center justify-center mx-auto text-slate-300">
                                        <span class="material-icons-round text-4xl">folder_off</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500 dark:text-gray-400 italic">Belum ada data transaksi yang tersedia untuk leaderboard saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
@endsection
