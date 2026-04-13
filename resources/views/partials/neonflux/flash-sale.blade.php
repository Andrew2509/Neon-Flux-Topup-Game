@if(count($flashSaleItems) > 0)
<!-- Flash Sale Section -->
<div class="mb-12 w-full glass-premium rounded-3xl overflow-hidden border border-white/20 shadow-2xl relative">
    <!-- Decorative Background Glows -->
    <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-secondary/20 blur-[100px] pointer-events-none"></div>

    <!-- Flash Sale Header Box -->
    <div class="relative overflow-hidden p-5 md:p-6 flex flex-col md:flex-row items-center justify-between border-b border-white/10 gap-4 bg-white/5">
        <!-- Shine Effect Background -->
        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent);"></div>
        
        <div class="flex items-center gap-4 relative z-10 w-full md:w-auto justify-center md:justify-start">
            <!-- Lightning Bolt Icon (Using SVG/Icon) -->
            <div class="bg-primary p-2 rounded-xl shadow-[0_0_20px_rgba(0,240,255,0.6)] animate-pulse flex-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="text-2xl md:text-3xl font-display font-black text-white italic tracking-tighter uppercase leading-none text-glow">Flash Sale</h2>
                <span class="text-[10px] md:text-xs text-primary font-bold tracking-widest uppercase mt-1 opacity-80">Limited Time Offers</span>
            </div>
            
            <!-- Countdown Timer -->
            <div id="flash-sale-timer" class="ml-2 bg-white/10 backdrop-blur-xl px-4 py-2 rounded-xl border border-white/20 font-mono text-base md:text-xl font-black text-white shadow-[inset_0_0_10px_rgba(255,255,255,0.1)] min-w-[120px] text-center">
                00:00:00
            </div>
        </div>
        
        <!-- Live Status Badge -->
        <div class="flex items-center gap-2 bg-green-500/20 text-green-400 px-3 py-1.5 rounded-full border border-green-500/30 text-xs font-black uppercase tracking-widest relative z-10">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Live Now
        </div>
    </div>

    <!-- Flash Sale Items Container (Horizontal Scroll) -->
    <div class="p-6 overflow-hidden bg-transparent">
        <div class="flex flex-nowrap gap-5 overflow-x-auto pb-6 -mb-6 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent scroll-smooth">
            @foreach($flashSaleItems as $sale)
            @php
                $item = $sale->service;
                $diff = $item->price - $sale->discount_price;
                $percent = $item->price > 0 ? floor(($diff / $item->price) * 100) : 0;
            @endphp
            <div class="flex-none w-[220px] md:w-[320px] group cursor-pointer" onclick="selectFromFlashSale('{{ $item->product_code }}', '{{ $item->category->slug }}')">
                <div class="glass-card-premium rounded-2xl p-4 h-full relative overflow-hidden animate-shine border-white/5">
                    
                    <div class="flex flex-col md:flex-row gap-4 items-center md:items-start text-center md:text-left">
                        <!-- Thumbnail Area -->
                        <div class="relative w-20 h-20 md:w-24 md:h-24 flex-none rounded-2xl overflow-hidden border border-white/10 shadow-lg group-hover:scale-110 transition-transform duration-500">
                            <img src="{{ $item->category->icon }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            <!-- Discount Badge -->
                            <div class="absolute top-0 right-0 bg-primary text-[10px] md:text-[12px] font-black text-slate-900 px-2 py-1 rounded-bl-xl shadow-lg">
                                -{{ $percent }}%
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0 w-full">
                            <h4 class="text-[10px] md:text-sm text-primary/80 font-bold truncate mb-1 uppercase tracking-wider">{{ $item->category->name }}</h4>
                            <h3 class="text-sm md:text-base font-black text-white leading-tight line-clamp-2 min-h-[2.5rem] mb-2 group-hover:text-primary transition-colors">{{ $item->name }}</h3>
                            
                            <div class="space-y-0.5">
                                <span class="text-[10px] md:text-xs text-white/40 line-through decoration-secondary/50">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <div class="text-lg md:text-xl font-black text-white flex items-center gap-2">
                                    <span class="text-white">Rp</span>
                                    <span class="text-glow">{{ number_format($sale->discount_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Stock Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-[10px] text-white/50 uppercase font-bold tracking-tight">Stock Left</span>
                                    <span class="text-[10px] {{ $sale->stock <= 10 ? 'text-secondary' : 'text-primary' }} font-black">
                                        {{ $sale->stock == -1 ? 'Unlimited' : $sale->stock }}
                                    </span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
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
