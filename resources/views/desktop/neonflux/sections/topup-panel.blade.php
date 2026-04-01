{{-- ============================================================
    TOP-UP PANEL — Player ID, amount selection, payment, buy button
    ============================================================ --}}
<div class="glass-panel p-6 rounded-3xl h-full border border-black/5 dark:border-white/10 relative overflow-hidden shadow-sm dark:shadow-none bg-white dark:bg-[#050510]/60">
    {{-- Ambient Glow --}}
    <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl pointer-events-none opacity-50 dark:opacity-100"></div>
    <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-secondary/20 rounded-full blur-3xl pointer-events-none opacity-50 dark:opacity-100"></div>

    <div class="relative z-10">
        {{-- Header --}}
        <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white mb-6 flex items-center transition-colors">
            <span class="material-icons-round mr-2 text-primary">shopping_cart</span>
            Top-Up Instan
        </h2>

        {{-- ========== STEP 1: Player ID ========== --}}
        <div class="mb-6">
            <label class="text-slate-500 dark:text-gray-400 text-sm font-semibold mb-2 block uppercase tracking-wider transition-colors">1. MASUKKAN ID PEMAIN</label>
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-xl opacity-10 dark:opacity-20 group-hover:opacity-30 dark:group-hover:opacity-40 transition-opacity blur-sm"></div>
                <div class="relative bg-white dark:bg-[#0a0a15] border border-black/10 dark:border-white/10 rounded-xl flex items-center p-1 group-focus-within:border-primary/50 transition-all shadow-sm">
                    <span class="material-icons-round text-slate-400 dark:text-gray-500 ml-3 group-focus-within:text-primary transition-colors">person</span>
                    <input id="player-id"
                           class="bg-transparent border-none text-slate-900 dark:text-white focus:ring-0 w-full px-3 py-2 font-mono tracking-wide placeholder-slate-400 dark:placeholder-gray-600 transition-colors"
                           placeholder="UID12345678"
                           type="text" />
                    <span class="material-icons-round text-primary mr-3 cursor-pointer hover:text-secondary transition-colors">help_outline</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 dark:text-gray-500 mt-2 pl-1">Server: Global (America)</p>
        </div>

        {{-- ========== STEP 2: Select Amount ========== --}}
        <div class="mb-6">
            <label class="text-slate-500 dark:text-gray-400 text-sm font-semibold mb-3 block uppercase tracking-wider transition-colors">2. PILIH JUMLAH</label>
            <div id="amount-grid" class="grid grid-cols-2 gap-3">
                {{-- 60 Crystals --}}
                <button class="amount-btn relative bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 border border-black/10 dark:border-white/10 hover:border-primary/50 dark:hover:border-primary/50 rounded-xl p-3 text-left transition-all group overflow-hidden shadow-sm dark:shadow-none">
                    <span class="amount-label block text-xs text-slate-500 dark:text-gray-400 font-medium transition-colors">60 Crystals</span>
                    <span class="amount-price block text-lg font-bold text-slate-900 dark:text-white mt-1 transition-colors">Rp 15.000</span>
                    <div class="amount-check absolute bottom-0 right-0 p-1 opacity-0 transition-opacity">
                        <span class="material-icons-round text-primary text-sm">check_circle</span>
                    </div>
                </button>

                {{-- 300 + 30 Bonus (HOT) --}}
                <button class="amount-btn active relative bg-gradient-to-br from-primary/10 to-transparent border border-primary/50 rounded-xl p-3 text-left transition-all shadow-neon-cyan overflow-hidden">
                    <span class="amount-label block text-xs text-primary font-semibold">300 + 30 Bonus</span>
                    <span class="amount-price block text-lg font-bold text-white mt-1 text-glow">Rp 75.000</span>
                    <div class="absolute top-0 right-0 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-bl-lg">HOT</div>
                </button>

                {{-- 980 + 110 Bonus --}}
                <button class="amount-btn relative bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 border border-black/10 dark:border-white/10 hover:border-primary/50 dark:hover:border-primary/50 rounded-xl p-3 text-left transition-all group overflow-hidden shadow-sm dark:shadow-none">
                    <span class="amount-label block text-xs text-slate-500 dark:text-gray-400 font-medium transition-colors">980 + 110 Bonus</span>
                    <span class="amount-price block text-lg font-bold text-slate-900 dark:text-white mt-1 transition-colors">Rp 225.000</span>
                </button>

                {{-- 1980 + 260 Bonus --}}
                <button class="amount-btn relative bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 border border-black/10 dark:border-white/10 hover:border-primary/50 dark:hover:border-primary/50 rounded-xl p-3 text-left transition-all group overflow-hidden shadow-sm dark:shadow-none">
                    <span class="amount-label block text-xs text-slate-500 dark:text-gray-400 font-medium transition-colors">1980 + 260 Bonus</span>
                    <span class="amount-price block text-lg font-bold text-slate-900 dark:text-white mt-1 transition-colors">Rp 450.000</span>
                </button>
            </div>
        </div>

        {{-- ========== STEP 3: Payment Method ========== --}}
        <div id="payment-section" class="mb-8">
            <label class="text-slate-500 dark:text-gray-400 text-sm font-semibold mb-3 block uppercase tracking-wider transition-colors">3. METODE PEMBAYARAN</label>
            <div class="space-y-3">
                {{-- GoPay --}}
                <label class="payment-option flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 hover:border-[#00AED6] dark:hover:border-[#00AED6] cursor-pointer transition-all shadow-sm dark:shadow-none">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-7 bg-white rounded flex items-center justify-center">
                            <span class="text-xs font-bold text-[#00AED6] font-display">GOPAY</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">GoPay</span>
                    </div>
                    <input class="form-radio text-[#00AED6] bg-transparent border-slate-300 dark:border-gray-500 focus:ring-offset-0 focus:ring-0" name="payment" type="radio" />
                </label>

                {{-- OVO --}}
                <label class="payment-option flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 hover:border-[#4C3494] dark:hover:border-[#4C3494] cursor-pointer transition-all shadow-sm dark:shadow-none">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-7 bg-white rounded border border-black/5 flex items-center justify-center">
                            <span class="text-xs font-bold text-[#4C3494] font-display">OVO</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">OVO</span>
                    </div>
                    <input class="form-radio text-[#4C3494] bg-transparent border-slate-300 dark:border-gray-500 focus:ring-offset-0 focus:ring-0" name="payment" type="radio" />
                </label>

                {{-- DANA --}}
                <label class="payment-option flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 hover:border-[#118EEA] dark:hover:border-[#118EEA] cursor-pointer transition-all shadow-sm dark:shadow-none">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-7 bg-white rounded border border-black/5 flex items-center justify-center">
                            <span class="text-xs font-bold text-[#118EEA] font-display">DANA</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">DANA</span>
                    </div>
                    <input class="form-radio text-[#118EEA] bg-transparent border-slate-300 dark:border-gray-500 focus:ring-offset-0 focus:ring-0" name="payment" type="radio" />
                </label>

                {{-- ShopeePay --}}
                <label class="payment-option flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 hover:border-[#EE4D2D] dark:hover:border-[#EE4D2D] cursor-pointer transition-all shadow-sm dark:shadow-none">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-7 bg-white rounded border border-black/5 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-[#EE4D2D] font-display">Shopee</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">ShopeePay</span>
                    </div>
                    <input class="form-radio text-[#EE4D2D] bg-transparent border-slate-300 dark:border-gray-500 focus:ring-offset-0 focus:ring-0" name="payment" type="radio" />
                </label>

                {{-- Razer Gold --}}
                <label class="payment-option active flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-primary/10 to-transparent border border-primary/30 hover:border-primary cursor-pointer transition-all shadow-sm dark:shadow-none">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-7 bg-slate-900 rounded border border-primary/30 flex items-center justify-center">
                            <span class="text-[10px] text-primary font-display font-bold">RAZER</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">Razer Gold</span>
                    </div>
                    <input checked class="form-radio text-primary bg-transparent border-primary focus:ring-offset-0 focus:ring-0" name="payment" type="radio" />
                </label>
            </div>
        </div>

        {{-- ========== BUY BUTTON ========== --}}
        <button id="btn-buy" class="w-full py-4 rounded-xl bg-gradient-to-r from-primary via-blue-500 to-secondary relative group overflow-hidden">
            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            <span class="relative z-10 font-display font-bold text-lg tracking-wider flex items-center justify-center">
                BELI SEKARANG
                <span class="material-icons-round ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </span>
            <div class="absolute inset-0 rounded-xl shadow-[inset_0_0_20px_rgba(255,255,255,0.3)]"></div>
        </button>

        {{-- Security Notice --}}
        <p class="text-center text-xs text-gray-500 mt-4 flex items-center justify-center">
            <span class="material-icons-round text-sm mr-1">lock</span>
            Pembayaran aman terenkripsi SSL 256-bit
        </p>
    </div>
</div>
