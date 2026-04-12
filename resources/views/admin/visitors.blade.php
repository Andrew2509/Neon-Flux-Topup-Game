@use('Illuminate\Support\Str')
@extends('admin.layouts.app')

@section('title', 'Visitor Tracking')
@section('page_title', 'Visitor Tracking')
@section('page_description', 'Pantau arus lalu lintas pengunjung website Neon Flux secara real-time.')

@section('content')
<!-- Visitor Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group border border-white/5 hover:border-primary/20 transition-all">
        <div class="absolute -right-4 -top-4 size-24 bg-primary/20 blur-3xl rounded-full group-hover:bg-primary/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary neon-border-blue">
                <span class="material-symbols-outlined text-3xl">sensors</span>
            </div>
            <div class="flex flex-col items-end">
                <span class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 text-[10px] font-bold animate-pulse">
                    <span class="size-1.5 rounded-full bg-green-500"></span> Live
                </span>
            </div>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Online Sekarang</p>
        <h3 class="text-3xl font-bold">{{ number_format($onlineCount, 0, ',', '.') }}</h3>
    </div>

    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group border border-white/5 hover:border-accent-blue/20 transition-all">
        <div class="absolute -right-4 -top-4 size-24 bg-accent-blue/20 blur-3xl rounded-full group-hover:bg-accent-blue/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-accent-blue/10 flex items-center justify-center text-accent-blue neon-border-blue">
                <span class="material-symbols-outlined text-3xl">today</span>
            </div>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Pengunjung Hari Ini</p>
        <h3 class="text-3xl font-bold">{{ number_format($todayUniqueCount, 0, ',', '.') }}</h3>
    </div>

    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group border border-white/5 hover:border-slate-500/20 transition-all">
        <div class="absolute -right-4 -top-4 size-24 bg-slate-500/20 blur-3xl rounded-full group-hover:bg-slate-500/30 transition-all"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="size-12 rounded-xl bg-slate-500/10 flex items-center justify-center text-slate-400">
                <span class="material-symbols-outlined text-3xl">history</span>
            </div>
        </div>
        <p class="text-slate-400 text-sm font-medium mb-1">Total Riwayat (30 Hari)</p>
        <h3 class="text-3xl font-bold">{{ number_format($totalRecordCount, 0, ',', '.') }}</h3>
    </div>
</div>

<!-- Detailed Logs Table -->
<div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
    <div class="p-6 border-b border-white/5 flex items-center justify-between">
        <h3 class="font-bold text-lg">Aktivitas Pengunjung Terbaru</h3>
        <button onclick="window.location.reload()" class="text-primary text-sm font-bold flex items-center gap-2 hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-all">
            <span class="material-symbols-outlined text-sm">refresh</span> Refresh
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">Informasi Pengunjung</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Halaman Terakhir</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($visitors as $visitor)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-slate-300">{{ $visitor->last_active_at->format('H:i:s') }}</p>
                        <p class="text-[9px] text-slate-500 uppercase">{{ $visitor->last_active_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-500 shrink-0">
                                <span class="material-symbols-outlined text-sm">public</span>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-accent-blue truncate">{{ $visitor->ip_address }}</p>
                                <p class="text-[10px] text-slate-500 truncate max-w-[200px]" title="{{ $visitor->user_agent }}">
                                    @if($visitor->user)
                                        <span class="text-primary">{{ $visitor->user->name }}</span> •
                                    @endif
                                    {{ Str::limit($visitor->user_agent, 50) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                             @if($visitor->country_code && $visitor->country_code !== '??')
                                <img src="https://flagcdn.com/24x18/{{ strtolower($visitor->country_code) }}.png" alt="{{ $visitor->country_code }}" class="rounded-sm opacity-80">
                             @else
                                <span class="material-symbols-outlined text-slate-600">location_off</span>
                             @endif
                            <div>
                                <p class="text-xs font-bold">{{ $visitor->city ?: 'Unknown' }}</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-tighter">{{ $visitor->country ?: 'Unknown' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ $visitor->url }}" target="_blank" class="text-xs text-slate-400 hover:text-primary underline decoration-slate-600 transition-all truncate block max-w-[150px]">
                            {{ Str::replace(config('app.url'), '', $visitor->url) ?: '/' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $isOnline = $visitor->last_active_at >= now()->subMinutes(5);
                        @endphp
                        @if($isOnline)
                            <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-[9px] font-bold rounded-full border border-green-500/20">ONLINE</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-500/10 text-slate-500 text-[9px] font-bold rounded-full">OFFLINE</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-600 text-sm">Belum ada data pengunjung yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-white/5 border-t border-white/5">
        {{ $visitors->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Refresh page every 30s to see live updates
    setInterval(function() {
        // window.location.reload(); // Might be annoying if user is reading table
    }, 30000);
</script>
@endpush
@endsection
