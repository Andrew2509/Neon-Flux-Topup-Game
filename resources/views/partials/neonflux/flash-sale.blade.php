@if(count($flashSaleItems) > 0)
<!-- Flash Sale Section -->
<div class="mb-12 w-full glass-premium rounded-3xl overflow-hidden border border-white/20 shadow-2xl relative">
    <!-- Decorative Background Glows -->
    <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-secondary/20 blur-[100px] pointer-events-none"></div>

    <!-- Flash Sale Header Box -->
    <div class="relative overflow-hidden p-2 md:p-3 flex items-center justify-between border-b border-white/10 gap-3 bg-white/5">
        <!-- Shine Effect Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
        
        <div class="flex items-center gap-2.5 relative z-10 w-full md:w-auto justify-center md:justify-start">
            <!-- Lightning Bolt Icon (Using SVG/Icon) -->
            <div class="bg-primary p-2 rounded-xl shadow-[0_0_20px_rgba(0,240,255,0.6)] animate-pulse flex-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="text-lg md:text-xl font-display font-black text-white italic tracking-tighter uppercase leading-none text-glow">Flash Sale</h2>
                <span class="text-[8px] md:text-[9px] text-primary font-bold tracking-widest uppercase mt-0.5 opacity-80">Limited Edition</span>
            </div>
            
            <!-- Countdown Timer -->
            <div id="flash-sale-timer" class="ml-1 bg-white/10 backdrop-blur-xl px-2.5 py-1 rounded-lg border border-white/20 font-mono text-sm md:text-base font-black text-white shadow-[inset_0_0_10px_rgba(255,255,255,0.1)] min-w-[80px] text-center">
                00:00:00
            </div>
        </div>
        
        <!-- Status & View All Wrapper -->
        <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 relative z-10">
            <!-- Live Status Badge -->
            <div class="flex items-center gap-2 bg-green-500/20 text-green-400 px-3 py-1.5 rounded-full border border-green-500/30 text-xs font-black uppercase tracking-widest">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Live Now
            </div>

            <!-- View All Button -->
            <a href="{{ route('flash-sale') }}" class="group flex items-center gap-1.5 bg-white/5 hover:bg-primary transition-all duration-300 px-3 py-1 rounded-full border border-white/10 text-[9px] font-black uppercase tracking-widest text-white/80 hover:text-slate-900 shadow-lg">
                View All
                <span class="material-icons-round text-xs group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Flash Sale Items Container (Horizontal Scroll) -->
    <div class="p-3 overflow-hidden bg-transparent">
        <div class="flex flex-nowrap gap-3 overflow-x-auto pb-3 -mb-3 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent scroll-smooth">
            @foreach($flashSaleItems as $sale)
            @php
                $item = $sale->service;
                $diff = $item->price - $sale->discount_price;
                $percent = $item->price > 0 ? floor(($diff / $item->price) * 100) : 0;
            @endphp
            <div class="flex-none w-[180px] md:w-[260px] group cursor-pointer" onclick="selectFromFlashSale('{{ $item->product_code }}', '{{ $item->category->slug }}')">
                <div class="glass-card-premium rounded-lg p-2.5 h-full relative overflow-hidden animate-shine border-white/5">
                    
                    <div class="flex gap-2.5 items-start text-left">
                        <!-- Thumbnail Area -->
                        <div class="relative w-14 h-14 md:w-16 md:h-16 flex-none rounded-lg overflow-hidden border border-white/10 shadow-lg group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ $item->category->icon }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            <!-- Discount Badge -->
                            <div class="absolute top-0 right-0 bg-primary text-[10px] md:text-[12px] font-black text-slate-900 px-2 py-1 rounded-bl-xl shadow-lg">
                                -{{ $percent }}%
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[9px] text-primary/80 font-bold truncate uppercase tracking-wider">{{ $item->category->name }}</h4>
                            <h3 class="text-xs md:text-sm font-black text-white leading-tight line-clamp-1 group-hover:text-primary transition-colors">{{ $item->name }}</h3>
                            
                            <div class="mt-1">
                                <span class="text-[9px] text-white/40 line-through">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <div class="text-base md:text-lg font-black text-white flex items-center gap-1">
                                    <span class="text-white text-[10px]">Rp</span>
                                    <span class="text-glow">{{ number_format($sale->discount_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Stock Bar -->
                            <div class="mt-2 pt-2 border-t border-white/5">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[8px] text-white/30 uppercase font-bold tracking-tight">Stock</span>
                                    <span class="text-[8px] {{ $sale->stock <= 10 ? 'text-secondary' : 'text-primary' }} font-bold">
                                        {{ $sale->stock == -1 ? '∞' : $sale->stock }}
                                    </span>
                                </div>
                                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $sale->stock <= 10 ? 'from-secondary to-pink-500' : 'from-primary to-cyan-400' }} rounded-full" 
                                         style="width: {{ $sale->stock == -1 ? '100' : min(100, max(5, ($sale->stock / 100) * 100)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function selectFromFlashSale(productCode, categorySlug) {
        // Find input with this code in the main nominal section
        const input = document.querySelector(`input[name="product_code"][value="${productCode}"]`);
        
        if (input) {
            // Fill ID first check handled by the main Nominal listener
            // We just trigger click on the real input
            input.click();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            // Redirect to the game detail page with product code as auto-select hint
            location.href = `/topup/${categorySlug}?select=${productCode}`;
        }
    }

    // Countdown Timer Logic
    @if($flashSaleItems->count() > 0)
    (function() {
        const endTime = new Date("{{ $flashSaleItems->first()->end_time->toIso8601String() }}").getTime();
        const timerElement = document.getElementById('flash-sale-timer');

        const updateTimer = setInterval(function() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(updateTimer);
                timerElement.innerHTML = "PROMO BERAKHIR";
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerElement.innerHTML = 
                (hours < 10 ? "0" + hours : hours) + ":" + 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
        }, 1000);
    })();
    @endif
</script>
@endif
