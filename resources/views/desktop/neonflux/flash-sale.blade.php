@extends('desktop.layouts.neonflux')

@section('title', 'Flash Sale — ' . get_setting('site_name'))

@section('content')
<main class="pt-32 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen">
    
    <!-- Hero Header -->
    <div class="mb-12 relative overflow-hidden glass-premium rounded-3xl p-8 md:p-12 border border-white/20 shadow-2xl">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/20 blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-secondary/20 blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="bg-primary p-3 rounded-2xl shadow-[0_0_30px_rgba(0,240,255,0.6)] animate-pulse mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <h1 class="text-4xl md:text-6xl font-display font-black text-white italic tracking-tighter uppercase mb-2 text-glow">Semua Promo Flash Sale</h1>
            <p class="text-primary font-bold tracking-[0.2em] uppercase text-sm md:text-base opacity-80 mb-8">Diskon Gila-Gilaan Setiap Hari</p>
            
            <div class="flex items-center gap-3 bg-white/5 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shadow-inner">
                <span class="text-white/60 font-medium uppercase tracking-widest text-xs">Total Promo Aktif:</span>
                <span class="text-white font-black text-xl">{{ $flashSaleItems->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Flash Sale Grid -->
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4 mb-12">
        @forelse($flashSaleItems as $sale)
            @php
                $item = $sale->service;
                $diff = $item->price - $sale->discount_price;
                $percent = $item->price > 0 ? floor(($diff / $item->price) * 100) : 0;
            @endphp
            <div class="group cursor-pointer" onclick="goToTopup('{{ $item->category->slug }}', '{{ $item->product_code }}')">
                <div class="glass-card-premium rounded-xl p-2.5 md:p-3 h-full relative overflow-hidden animate-shine border-white/10 hover:scale-[1.02] transition-all duration-300">
                    
                    <div class="flex flex-col gap-2.5">
                        <!-- Thumbnail Area -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden border border-white/10 shadow-lg mb-1">
                            <img src="{{ $item->category->icon }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <!-- Discount Badge -->
                            <div class="absolute top-0 right-0 bg-primary text-[10px] md:text-xs font-black text-slate-900 px-2 py-1 rounded-bl-xl shadow-xl z-20">
                                -{{ $percent }}%
                            </div>
                            
                            <!-- Category Badge Float -->
                            <div class="absolute bottom-1.5 left-1.5 bg-black/40 backdrop-blur-md text-[8px] font-bold text-white px-1.5 py-0.5 rounded-md border border-white/10 z-20 uppercase tracking-tighter">
                                {{ $item->category->name }}
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1">
                            <h3 class="text-xs md:text-sm font-black text-white leading-tight line-clamp-2 min-h-[2.5rem] mb-2 group-hover:text-primary transition-colors italic tracking-tight">{{ $item->name }}</h3>
                            
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[9px] md:text-[10px] text-white/40 line-through decoration-secondary/60">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <div class="text-base md:text-lg font-black text-white flex items-center gap-1.5">
                                    <span class="text-primary text-[10px] font-bold">Rp</span>
                                    <span class="text-glow">{{ number_format($sale->discount_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Stock Bar -->
                            <div class="mt-4 pt-4 border-t border-white/5">
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-[8px] md:text-[9px] text-white/50 uppercase font-black tracking-widest">Stock Level</span>
                                    <span class="text-[8px] md:text-[9px] {{ $sale->stock <= 10 ? 'text-secondary' : 'text-primary' }} font-bold">
                                        {{ $sale->stock == -1 ? '∞' : $sale->stock }}
                                    </span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full bg-gradient-to-r {{ $sale->stock <= 10 ? 'from-secondary to-pink-500' : 'from-primary to-cyan-400' }} rounded-full" 
                                         style="width: {{ $sale->stock == -1 ? '100' : min(100, max(5, ($sale->stock / 100) * 100)) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Buy Button -->
                        <div class="mt-3 pt-3">
                            <div class="w-full bg-white/5 border border-white/10 py-2 rounded-lg text-center text-[9px] md:text-[10px] font-black uppercase tracking-widest text-white group-hover:bg-primary group-hover:text-black transition-all shadow-[inset_0_0_10px_rgba(255,255,255,0.05)]">
                                Ambil Promo
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center glass-premium rounded-3xl border border-white/10">
                <div class="text-white/20 mb-4">
                    <span class="material-icons-round text-6xl">timer_off</span>
                </div>
                <h2 class="text-2xl font-bold text-white/60 uppercase tracking-widest uppercase">Belum ada promo aktif</h2>
                <p class="text-white/40 mt-2">Cek kembali nanti untuk diskon gila-gilaan lainnya!</p>
                <div class="mt-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary px-6 py-3 rounded-xl text-black font-black uppercase text-xs tracking-widest shadow-neon-cyan hover:scale-105 transition-transform">
                        Kembali Ke Beranda
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12 flex justify-center">
        {{ $flashSaleItems->links() }}
    </div>

</main>

@push('scripts')
<script>
    function goToTopup(slug, productCode) {
        location.href = `/topup/${slug}?select=${productCode}`;
    }
</script>
@endpush
@endsection
