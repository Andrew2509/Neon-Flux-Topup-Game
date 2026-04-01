@extends('desktop.layouts.user')

@section('title', 'Dashboard — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Ringkasan Akun')
@section('page_subtitle', 'Kelola akun dan pantau transaksi Anda di sini.')

@section('content')
<div class="space-y-8">
    {{-- Welcome Hero Section --}}
    <section class="relative overflow-hidden rounded-xl bg-white border border-corp-border p-8 shadow-sm" data-purpose="hero-section">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-2xl">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="w-1.5 h-1.5 bg-corp-accent rounded-full"></span>
                    <span class="text-[10px] font-bold text-corp-accent tracking-widest uppercase">Portal Aktif — {{ date('d F Y') }}</span>
                </div>
                <h2 class="text-3xl font-bold mb-3 text-corp-navy">
                    Selamat Datang, <span class="text-corp-accent">{{ explode(' ', $user->name)[0] }}</span>
                </h2>
                <p class="text-corp-muted text-sm leading-relaxed">
                    Ini adalah ringkasan akun Anda. Anda dapat mengelola transaksi, memantau riwayat saldo, dan memperbarui profil melalui dashboard profesional ini.
                </p>
            </div>
            <a href="{{ route('user.deposit') }}" class="px-6 py-3 bg-corp-accent text-white rounded-lg flex items-center justify-center transition-all hover:bg-blue-700 shadow-md">
                <svg class="h-5 w-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span class="text-sm font-semibold">Isi Saldo</span>
            </a>
        </div>
    </section>

    {{-- Stats Cards Grid --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6" data-purpose="stats-overview">
        <!-- Dompet Digital -->
        <div class="content-card rounded-xl p-6 flex flex-col justify-between h-48">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-corp-muted tracking-widest uppercase mb-1">Dompet Digital</p>
                    <h3 class="text-corp-navy font-semibold text-sm">Saldo Tersedia</h3>
                </div>
                <div class="p-2 bg-slate-100 rounded-lg text-corp-accent">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold text-corp-navy mb-3">Rp {{ number_format($user->balance, 0, ',', '.') }}</div>
                <div class="inline-flex items-center px-2 py-1 bg-blue-50 border border-blue-100 rounded">
                    <svg class="h-3 w-3 text-corp-accent mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd"></path>
                    </svg>
                    <span class="text-[9px] font-bold text-corp-accent uppercase tracking-wider">Status Member {{ $user->role }}</span>
                </div>
            </div>
        </div>

        <!-- Aktivitas Pasar -->
        <div class="content-card rounded-xl p-6 flex flex-col justify-between h-48">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-corp-muted tracking-widest uppercase mb-1">Aktivitas</p>
                    <h3 class="text-corp-navy font-semibold text-sm">Total Transaksi</h3>
                </div>
                <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold text-corp-navy mb-3">{{ number_format($user->orders()->count(), 0, ',', '.') }}</div>
                <div class="inline-flex items-center px-2 py-1 bg-slate-100 border border-slate-200 rounded">
                    <span class="text-[9px] font-bold text-corp-muted uppercase tracking-wider">Terupdate Real-time</span>
                </div>
            </div>
        </div>

        <!-- Tingkat Akses -->
        <div class="content-card rounded-xl p-6 flex flex-col justify-between h-48">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-corp-muted tracking-widest uppercase mb-1">Klasifikasi</p>
                    <h3 class="text-corp-navy font-semibold text-sm">Level Akun</h3>
                </div>
                <div class="p-2 bg-slate-100 rounded-lg text-amber-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold text-corp-navy mb-3 uppercase tracking-tight">{{ $user->role }}</div>
                <div class="inline-flex items-center px-2 py-1 bg-amber-50 border border-amber-100 rounded">
                    <span class="text-[9px] font-bold text-amber-700 uppercase tracking-wider">Terdaftar {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Recent Orders Section --}}
    <section class="space-y-4" data-purpose="recent-orders">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-corp-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-corp-navy uppercase tracking-widest">Pesanan Terbaru</h2>
            </div>
            <a href="{{ route('user.riwayat') }}" class="px-3 py-1.5 border border-corp-border rounded text-[10px] font-bold uppercase tracking-widest text-corp-muted hover:text-corp-accent hover:border-corp-accent transition-all flex items-center bg-white shadow-sm">
                Semua Pesanan
                <svg class="h-3 w-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </a>
        </div>

        <div class="content-card rounded-xl overflow-hidden min-h-[250px] flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-corp-border">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Rincian Transaksi</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-corp-border">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-corp-navy">{{ $order->product_name }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-medium text-corp-accent">#{{ $order->order_id }}</span>
                                        <span class="text-[10px] text-corp-muted">• {{ $order->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $statusClass = match($order->status) {
                                        'success' => 'bg-green-50 text-green-600 border-green-100',
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        default => 'bg-red-50 text-red-600 border-red-100',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full border {{ $statusClass }} text-[9px] font-bold uppercase tracking-wider">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-corp-muted uppercase tracking-widest">Tidak Ada Pesanan Aktif</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Aktivitas terbaru Anda akan ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Top-up Registry Section --}}
    <section class="space-y-4 pb-12" data-purpose="top-up-registry">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-slate-200 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-corp-navy uppercase tracking-widest">Riwayat Top-Up</h2>
            </div>
            <a href="{{ route('user.deposit.history') }}" class="px-3 py-1.5 border border-corp-border rounded text-[10px] font-bold uppercase tracking-widest text-corp-muted hover:text-corp-navy transition-all flex items-center bg-white shadow-sm">
                Lihat Log
                <svg class="h-3 w-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </a>
        </div>

        <div class="content-card rounded-xl overflow-hidden min-h-[250px] flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-corp-border">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-center">Jumlah</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-corp-muted uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-corp-border">
                        @forelse($recentDeposits as $deposit)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-corp-navy uppercase">{{ $deposit->method }}</span>
                                    <span class="text-[10px] text-corp-muted uppercase mt-0.5">REF: {{ strtoupper(substr($deposit->deposit_id, -8)) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-corp-navy">Rp{{ number_format($deposit->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $statusClass = match(strtolower($deposit->status)) {
                                        'success' => 'bg-green-50 text-green-600 border-green-100',
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        default => 'bg-red-50 text-red-600 border-red-100',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full border {{ $statusClass }} text-[9px] font-bold uppercase tracking-wider">
                                    {{ $deposit->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-corp-muted uppercase tracking-widest">Tidak Ada Log Deposit</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Riwayat pendanaan Anda akan ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
