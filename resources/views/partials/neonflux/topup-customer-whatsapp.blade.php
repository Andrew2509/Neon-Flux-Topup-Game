@php
    $compact = $compact ?? false;
    $waCs = get_setting('whatsapp_cs', '');
    $waPlaceholder = $waCs !== '' ? 'Contoh: '.$waCs : 'Contoh: 6281234567890';
    $defaultWa = old('customer_whatsapp', auth()->user()->phone ?? '');
@endphp

@if ($compact)
    <div class="glass-panel-mobile dark:bg-white/5 p-4 rounded-2xl space-y-3">
        <div class="flex items-center gap-2">
            <span class="material-icons-round text-primary text-base">chat</span>
            <h3 class="font-bold text-sm text-slate-950 dark:text-white">Nomor WhatsApp</h3>
        </div>
        <p class="text-[10px] text-slate-500 dark:text-white/70 leading-relaxed">Wajib diisi sebelum ID game. Dipakai untuk konfirmasi dan bantuan pesanan.</p>
        <div class="space-y-1">
            <label for="customer_whatsapp_input" class="text-[9px] font-bold uppercase text-slate-500 dark:text-white/70 ml-1">WhatsApp</label>
            <input type="tel" name="customer_whatsapp" id="customer_whatsapp_input" required
                value="{{ $defaultWa }}"
                placeholder="{{ $waPlaceholder }}"
                autocomplete="tel"
                inputmode="numeric"
                class="w-full rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 px-3 py-2.5 text-xs focus:ring-primary focus:border-primary text-slate-950 dark:text-white font-mono @error('customer_whatsapp') ring-2 ring-red-500 @enderror">
        </div>
        @error('customer_whatsapp')
            <p class="text-[10px] text-red-500">{{ $message }}</p>
        @enderror
    </div>
@else
    <section class="glass-panel p-6 sm:p-8 rounded-3xl relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-1 h-full bg-secondary/80 shadow-neon-magenta"></div>
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary border border-secondary/40">
                <span class="material-icons-round text-2xl">chat</span>
            </div>
            <div>
                <h2 class="text-xl font-display font-bold text-slate-950 dark:text-white">Nomor WhatsApp</h2>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-0.5">Isi nomor aktif terlebih dahulu — untuk konfirmasi pesanan sebelum melanjutkan ke ID pemain.</p>
            </div>
        </div>
        <div>
            <label for="customer_whatsapp_input" class="block text-sm font-medium text-slate-500 dark:text-gray-400 mb-2">WhatsApp</label>
            <input type="tel" name="customer_whatsapp" id="customer_whatsapp_input" required
                value="{{ $defaultWa }}"
                placeholder="{{ $waPlaceholder }}"
                autocomplete="tel"
                inputmode="numeric"
                class="w-full rounded-xl glass-input px-4 py-3 focus:ring-0 transition-all font-mono @error('customer_whatsapp') ring-2 ring-red-500 @enderror">
        </div>
        @error('customer_whatsapp')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </section>
@endif
