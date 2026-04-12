@extends('hp.layouts.neonflux')

@section('title', 'Topup ' . $category->name . ' — Neon Flux')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/neonflux/topupgame.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <style>
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        .glass-panel-mobile { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/neonflux/topupgame.js') }}"></script>
@endpush

@section('content')
<div class="space-y-5 pb-20">
    {{-- Game Banner & Info --}}
    <div class="relative w-full aspect-[16/7] rounded-3xl overflow-hidden glass-panel-mobile dark:bg-white/5 shadow-xl border border-white/20">
        <img src="{{ $category->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" class="w-full h-full object-cover opacity-70 dark:opacity-90 transition-transform duration-700 hover:scale-110" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=random&color=fff'">
        <div class="absolute inset-0 bg-linear-to-t from-[#0f172a] via-[#0f172a]/40 to-transparent flex flex-col justify-end p-5">
            <h1 class="text-xl font-display font-black text-white tracking-tight leading-none mb-1">{{ $category->name }}</h1>
            <div class="flex items-center gap-2">
                <span class="flex h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                <p class="text-[10px] font-bold text-gray-200 uppercase tracking-widest opacity-80">Online 24 Jam • Proses Instan</p>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout') }}" method="POST" id="topup-form" class="space-y-6">
        @csrf
        @include('partials.neonflux.topup-customer-whatsapp', ['compact' => true])

        {{-- Step 1: Account Info --}}
        <div class="step-item fade-in-section visible">
            <div class="step-number">1</div>
            <div class="glass-panel-mobile dark:bg-white/5 p-5 rounded-3xl space-y-4 shadow-sm border border-black/[0.03] dark:border-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-tight">Data Akun</h3>
                    <span class="material-symbols-outlined text-primary text-lg">account_circle</span>
                </div>

                <div class="grid @if($category->has_zone) grid-cols-5 @else @endif gap-3">
                    <div class="@if($category->has_zone) col-span-3 @endif space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-white/50 ml-1 tracking-wider">{{ $category->input_label ?? 'User ID' }}</label>
                        <input type="text" name="user_id" id="user_id_input" data-operator-id="{{ $category->ext_id }}" data-game-slug="{{ $category->slug }}" data-requires-zone="{{ $category->has_zone ? '1' : '0' }}" required placeholder="{{ $category->input_placeholder ?? 'Masukkan ID' }}"
                            class="w-full rounded-2xl bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-hidden text-slate-900 dark:text-white font-mono transition-all">
                    </div>

                    @if($category->has_zone)
                    <div class="col-span-2 space-y-1.5">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-white/50 ml-1 tracking-wider">{{ $category->zone_label ?? 'Zone ID' }}</label>
                        <input type="text" name="zone_id" id="zone_id_input" required placeholder="{{ $category->zone_placeholder ?? 'Zone ID' }}"
                            class="w-full rounded-2xl bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-hidden text-slate-900 dark:text-white font-mono transition-all">
                    </div>
                    @endif
                </div>

                <div id="nickname-area" class="hidden items-center gap-3 px-4 py-3 bg-primary/10 rounded-2xl border border-primary/20 animate-in fade-in zoom-in duration-300">
                    <span class="material-symbols-outlined text-primary text-xl">verified_user</span>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-500 dark:text-white/60 font-bold uppercase tracking-widest">Player Terdeteksi</span>
                        <span id="player-nickname" class="js-player-nick font-black text-sm text-slate-900 dark:text-white"></span>
                    </div>
                </div>
                <input type="hidden" name="player_nickname" id="player_nickname_input" value="{{ old('player_nickname') }}">
            </div>
        </div>

    {{-- Step 2: Nominals --}}
    <div class="step-item fade-in-section visible">
        <div class="step-number">2</div>
        <div id="nominal-section" class="glass-panel-mobile dark:bg-white/5 p-5 rounded-3xl space-y-4 shadow-sm border border-black/[0.03] dark:border-white/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-tight">Pilih Nominal</h3>
                <span class="material-symbols-outlined text-primary text-lg">shopping_bag</span>
            </div>

            @if($activeJenis->count() > 1)
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none -mx-1 px-1">
                @foreach($activeJenis as $aj)
                <button type="button" onclick="filterServices({{ $aj->id }})" class="jenis-btn whitespace-nowrap px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all {{ $loop->first ? 'active-jenis bg-primary text-white shadow-lg shadow-primary/30' : 'bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-white/60' }}" data-id="{{ $aj->id }}">
                    {{ $aj->name }}
                </button>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-3 gap-3" id="services-grid">
                @forelse($services as $s)
                <div class="relative group active:scale-[0.97] transition-all service-item" data-jenis="{{ $s->product_jenis_id }}">
                    <input type="radio" name="product_code" id="n-{{ $loop->index }}" value="{{ $s->product_code }}" data-name="{{ $s->name }}" data-price="{{ number_format($s->price, 0, ',', '.') }}" required class="peer hidden radio-card">
                    <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white z-20 border-2 border-white dark:border-slate-900 shadow-xl scale-110">
                        <span class="material-symbols-outlined text-[12px] font-black">check</span>
                    </div>
                    <label for="n-{{ $loop->index }}" class="block p-3.5 rounded-2xl bg-slate-50/50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/[0.03] peer-checked:shadow-inner transition-all cursor-pointer h-full">
                        <div class="text-[11px] font-black text-slate-800 dark:text-white mb-2 line-clamp-2 leading-tight min-h-[2rem]">{{ $s->name }}</div>
                        <div class="text-[10px] text-primary font-black">Rp {{ number_format($s->price, 0, ',', '.') }}</div>
                    </label>
                </div>
                @empty
                <div class="col-span-2 text-center py-8 bg-slate-50 dark:bg-white/5 rounded-2xl">
                    <span class="material-symbols-outlined text-slate-300 dark:text-white/20 text-4xl mb-2">inventory_2</span>
                    <p class="text-xs text-slate-400 dark:text-white/40 font-bold uppercase tracking-widest">Produk Kosong</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Step 3: Payment --}}
    {{-- Step 3: Payment --}}
    <div class="step-item fade-in-section visible">
        <div class="step-number">3</div>
        <div id="payment-section" class="glass-panel-mobile dark:bg-white/5 p-5 rounded-3xl space-y-4 shadow-sm border border-black/[0.03] dark:border-white/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-tight">Metode Pembayaran</h3>
                <span class="material-symbols-outlined text-primary text-lg">payments</span>
            </div>

            <div class="space-y-5">
                 @auth
                 <div class="space-y-3">
                    <h4 class="text-[10px] font-black text-slate-400 dark:text-white/50 uppercase tracking-[0.15em] ml-1">Pembayaran Internal</h4>
                    <div class="relative group active:scale-[0.98] transition-all p-1">
                        <input type="radio" name="payment" id="p-saldo" value="SALDO" data-name="Saldo Akun" data-fee="0" required class="peer hidden method-card">
                        <div class="absolute top-0 right-0 w-6 h-6 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white z-20 border-2 border-white dark:border-slate-900 shadow-xl">
                            <span class="material-symbols-outlined text-[14px] font-black">check</span>
                        </div>
                        <label for="p-saldo" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/[0.03] transition-all cursor-pointer shadow-xs">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary/15 flex items-center justify-center text-primary shrink-0 shadow-inner">
                                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-900 dark:text-white leading-tight">Saldo Akun</span>
                                    <span class="text-[11px] text-primary font-black mt-0.5">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                 </div>
                 @endauth

                 @forelse($groupedPayments as $type => $payments)
                 <div class="space-y-3">
                    <h4 class="text-[10px] font-black text-slate-400 dark:text-white/50 uppercase tracking-[0.15em] ml-1">{{ $type }}</h4>
                    <div class="grid grid-cols-1 gap-3">
                    @foreach($payments as $p)
                        @if($p->code === 'SALDO') @continue @endif
                    <div class="relative group active:scale-[0.98] transition-all p-1">
                        <input type="radio" name="payment" id="p-{{ $p->id }}" value="{{ $p->code }}" data-name="{{ $p->name }}" data-image="{{ asset($p->image) }}" data-fee="{{ $p->fee }}" required class="peer hidden method-card">
                        <div class="absolute top-0 right-0 w-6 h-6 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white z-20 border-2 border-white dark:border-slate-900 shadow-xl">
                            <span class="material-symbols-outlined text-[14px] font-black">check</span>
                        </div>
                        <label for="p-{{ $p->id }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/[0.03] transition-all cursor-pointer shadow-xs">
                                <div class="flex items-center gap-4 w-full">
                                    <div class="w-12 h-8 rounded-lg bg-white overflow-hidden flex items-center justify-center shrink-0 border border-slate-100 p-1.5 shadow-xs">
                                        @if($p->image)
                                            <img src="{{ asset($p->image) }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="text-[9px] font-black text-slate-400">{{ strtoupper(substr($p->name, 0, 3)) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col flex-1">
                                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ $p->name }}</span>
                                        <span class="text-[9px] text-slate-400 dark:text-white/40 font-bold uppercase tracking-widest mt-0.5">Instan • Aman</span>
                                    </div>
                                </div>
                        </label>
                    </div>
                    @endforeach
                    </div>
                 </div>
                 @empty
                 <div class="text-center py-6 bg-slate-50 dark:bg-white/5 rounded-2xl">
                     <p class="text-[10px] text-slate-400 dark:text-white/40 font-bold uppercase tracking-widest">Metode Pembayaran Tidak Tersedia</p>
                 </div>
                 @endforelse
            </div>
        </div>
    </div>


</div>

{{-- Ringkasan Pesanan --}}
<div class="step-item fade-in-section visible">
    <div class="step-number">4</div>
    <div class="receipt-card p-6 shadow-xl shadow-slate-200/50 dark:shadow-none bg-white dark:bg-white/5">
        <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 uppercase tracking-[0.2em] flex items-center gap-3 border-b border-slate-100 dark:border-white/10 pb-4">
            <span class="material-symbols-outlined text-primary">receipt_long</span>
            Tinjau Pesanan
        </h3>
        
        <div class="space-y-4">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">Harga Produk</span>
                    <span class="text-xs font-black text-slate-900 dark:text-white" id="display-base-price">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">Biaya Admin</span>
                    <span class="text-xs font-black text-slate-900 dark:text-white" id="display-fee">Rp 0</span>
                </div>
                <div class="flex justify-between items-center hidden" id="row-discount">
                    <span class="text-[11px] font-bold text-green-500 uppercase tracking-widest">Diskon Voucher</span>
                    <span class="text-xs font-black text-green-500" id="display-discount">-Rp 0</span>
                </div>
            </div>

            <div class="pt-5 border-t border-dashed border-slate-200 dark:border-white/10">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Total Bayar</span>
                    <span class="text-xl font-display font-black text-primary" id="receipt-total">Rp 0</span>
                </div>

                <!-- Voucher Input in Summary -->
                <div class="relative group">
                    <input type="text" id="voucher_code" placeholder="Punya Kode Promo?" 
                        class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-4 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-hidden text-slate-900 dark:text-white font-black uppercase tracking-widest transition-all">
                    <button type="button" id="apply-voucher" class="absolute right-2 top-2 bottom-2 px-5 bg-slate-900 text-white dark:bg-primary dark:text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-md active:scale-95 transition-all">
                        Pakai
                    </button>
                </div>
                <div id="voucher-msg" class="mt-2 text-[9px] font-bold ml-2 hidden"></div>
            </div>
        </div>
    </div>
</div>

{{-- Sticky Bottom Action Bar --}}
<div class="fixed bottom-[56px] inset-x-0 z-40 sticky-action-bar p-4 pb-safe flex items-center justify-center shadow-[0_-12px_40px_rgba(0,0,0,0.1)]">
    <div class="w-full max-w-5xl flex items-center justify-between gap-4">
        <div class="flex flex-col min-w-0">
            <span class="text-[10px] text-slate-400 dark:text-white/50 font-black uppercase tracking-widest">Total Pembayaran</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xl font-display font-black text-slate-900 dark:text-white leading-tight" id="summary-total">Rp 0</span>
            </div>
            <span id="summary-player-name" data-sticky-summary="1" class="js-summary-player-name hidden text-[9px] text-primary font-bold truncate max-w-[40vw] mt-0.5"></span>
            
            {{-- New Detailed Selection Row --}}
            <div id="footer-selection-details" class="flex items-center gap-1.5 mt-1 hidden animate-in fade-in slide-in-from-left-2 duration-300">
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-md border border-slate-200 dark:border-white/10">
                    <img id="footer-nominal-img" src="{{ $category->icon }}" class="w-2.5 h-2.5 rounded-full object-cover">
                    <span id="footer-nominal-txt" class="text-[8px] font-black text-slate-600 dark:text-white/70 truncate max-w-[50px]"></span>
                </div>
                <span class="text-slate-300 dark:text-white/20 text-[8px]">•</span>
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded-md border border-slate-200 dark:border-white/10">
                    <img id="footer-payment-img" src="" class="w-2.5 h-2.5 object-contain hidden">
                    <span id="footer-payment-txt" class="text-[8px] font-black text-slate-600 dark:text-white/70 truncate max-w-[50px]"></span>
                </div>
            </div>
        </div>
        <button type="submit" class="flex-1 max-w-[200px] py-4 rounded-2xl bg-linear-to-r from-primary to-blue-600 text-white font-display font-black tracking-tight text-sm shadow-xl shadow-primary/30 active:scale-95 transition-all uppercase">
            Top Up Sekarang
        </button>
    </div>
</div>
    <div id="summary-nominal" class="hidden"></div>
    <div id="summary-payment" class="hidden"></div>
    <div id="summary-base-price" class="hidden"></div>
    <div id="summary-fee" class="hidden"></div>
    <div id="summary-whatsapp" class="hidden"></div>
    <div id="summary-userid" class="hidden"></div>
    <input type="hidden" name="voucher_code" id="applied_voucher_code">
    <input type="hidden" name="voucher_discount" id="applied_voucher_discount" value="0">
    </form>
</div>
@push('scripts')
<script>
    function filterServices(jenisId) {
        // Update Buttons
        document.querySelectorAll('.jenis-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'active-jenis');
            btn.classList.add('bg-black/5', 'dark:bg-white/5', 'text-slate-500', 'dark:text-white/70');

            if (jenisId === null && btn.dataset.id === 'all') {
                setActive(btn);
            } else if (btn.dataset.id == jenisId) {
                setActive(btn);
            }
        });

        // Update Items
        document.querySelectorAll('.service-item').forEach(item => {
            if (jenisId === null) {
                item.classList.remove('hidden');
            } else {
                if (item.dataset.jenis == jenisId) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            }
        });
    }

    function setActive(btn) {
        btn.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'active-jenis');
        btn.classList.remove('bg-black/5', 'dark:bg-white/5', 'text-slate-500', 'dark:text-white/70');
    }
</script>
@endpush

@push('styles')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    .active-jenis {
        background-color: var(--color-primary) !important;
        color: white !important;
    }
</style>
@endpush
@endsection
