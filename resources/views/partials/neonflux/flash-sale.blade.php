<!-- Flash Sale Section -->
<div class="mb-10 w-full">
    <!-- Flash Sale Header Box -->
    <div class="relative overflow-hidden rounded-t-2xl bg-primary/20 backdrop-blur-xl p-4 flex items-center justify-between border-x border-t border-white/10 shadow-[0_0_20px_rgba(0,240,255,0.15)]">
        <!-- Shine Effect Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);"></div>
        
        <div class="flex items-center gap-3 relative z-10">
            <!-- Lightning Bolt Icon (Using SVG/Icon) -->
            <div class="bg-primary p-1.5 rounded-lg shadow-[0_0_15px_rgba(0,240,255,0.5)] animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <h2 class="text-xl md:text-2xl font-display font-black text-white italic tracking-tighter uppercase">Flash Sale</h2>
            
            <!-- Countdown Timer -->
            <div id="flash-sale-timer" class="ml-2 bg-black/30 backdrop-blur-md px-3 py-1 rounded-lg border border-white/20 font-mono text-sm md:text-base font-bold text-white shadow-inner">
                00:00:00
            </div>
        </div>
        
        <!-- Decorative Ribbon Pattern -->
        <div class="hidden md:block opacity-30">
            <svg width="100" height="30" viewBox="0 0 100 30" class="fill-white">
                <rect x="0" y="5" width="40" height="20" rx="2" opacity="0.4"/>
                <rect x="45" y="0" width="30" height="30" rx="4" opacity="0.6"/>
                <rect x="80" y="5" width="20" height="20" rx="2" opacity="0.4"/>
            </svg>
        </div>
    </div>

    <!-- Flash Sale Items Container (Horizontal Scroll) -->
    <div class="bg-black/10 dark:bg-black/40 border-x border-b border-white/5 rounded-b-2xl p-4 overflow-hidden">
        <div class="flex flex-nowrap gap-4 overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-transparent scroll-smooth">
            @foreach($flashSaleItems as $sale)
            @php
                $item = $sale->service;
                $diff = $item->price - $sale->discount_price;
                $percent = $item->price > 0 ? floor(($diff / $item->price) * 100) : 0;
            @endphp
            <div class="flex-none w-[200px] md:w-[280px] group cursor-pointer" onclick="selectFromFlashSale('{{ $item->product_code }}', '{{ $item->category->slug }}')">
                <div class="glass-panel-light dark:bg-slate-900/80 rounded-2xl border border-white/10 p-3 h-full transition-all duration-300 hover:border-primary/50 hover:shadow-[0_0_20px_rgba(0,240,255,0.15)] relative overflow-hidden">
                    
                    <div class="flex gap-3">
                        <!-- Thumbnail Area -->
                        <div class="relative w-16 h-16 md:w-20 md:h-20 flex-none rounded-xl overflow-hidden border border-white/10">
                            <img src="{{ $item->category->icon }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            <!-- Discount Badge -->
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-primary to-cyan-500 text-[9px] md:text-[11px] font-black text-slate-900 text-center py-0.5 uppercase">
                                {{ $percent }}% OFF
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[10px] md:text-xs text-slate-500 dark:text-gray-400 font-medium truncate mb-0.5">{{ $item->category->name }}</h4>
                            <h3 class="text-xs md:text-sm font-bold text-slate-900 dark:text-white leading-tight line-clamp-2 h-8 md:h-10">{{ $item->name }}</h3>
                            
                            <div class="mt-2">
                                <span class="text-[10px] md:text-xs text-slate-400 dark:text-gray-500 line-through">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <div class="text-sm md:text-base font-black text-primary">Rp {{ number_format($sale->discount_price, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-[9px] md:text-[10px] text-slate-500 dark:text-gray-400">
                                    Stok: <span class="{{ $sale->stock <= 10 ? 'text-secondary' : 'text-orange-500' }} font-bold">
                                        {{ $sale->stock == -1 ? 'Tersedia' : $sale->stock }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Glow Effect on Hover -->
                    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
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
