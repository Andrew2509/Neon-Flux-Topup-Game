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
    <div class="relative w-full aspect-21/9 rounded-2xl overflow-hidden glass-panel-mobile dark:bg-white/5 shadow-md">
        <img src="{{ $category->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" class="w-full h-full object-cover opacity-60 dark:opacity-80" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=random&color=fff'">
        <div class="absolute inset-0 bg-linear-to-t from-black/80 to-transparent flex flex-col justify-end p-3">
            <h1 class="text-lg font-display font-bold text-white tracking-tight">{{ $category->name }}</h1>
            <p class="text-[9px] text-gray-300 dark:text-white/90">Proses Instan • Terpercaya • 24 Jam</p>
        </div>
    </div>

    <form action="{{ route('checkout') }}" method="POST" id="topup-form">
        @csrf
        @include('partials.neonflux.topup-customer-whatsapp', ['compact' => true])
        {{-- Step 1: Account Info --}}
    <div class="glass-panel-mobile dark:bg-white/5 p-4 rounded-2xl space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-primary/20 flex items-center justify-center text-primary font-bold text-xs">1</div>
            <h3 class="font-bold text-sm text-slate-950 dark:text-white">Masukkan Data Akun</h3>
        </div>

        <div class="grid @if($category->has_zone) grid-cols-5 @else @endif gap-2">
            <div class="@if($category->has_zone) col-span-3 @endif space-y-1">
                <label class="text-[9px] font-bold uppercase text-slate-500 dark:text-white/70 ml-1">{{ $category->input_label ?? 'User ID' }}</label>
                <input type="text" name="user_id" id="user_id_input" data-operator-id="{{ $category->ext_id }}" required placeholder="{{ $category->input_placeholder ?? 'Masukkan ID' }}"
                    class="w-full rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 px-3 py-2.5 text-xs focus:ring-primary focus:border-primary text-slate-950 dark:text-white font-mono">
            </div>

            @if($category->has_zone)
            <div class="col-span-2 space-y-1">
                <label class="text-[9px] font-bold uppercase text-slate-500 dark:text-white/70 ml-1">{{ $category->zone_label ?? 'Zone ID' }}</label>
                <input type="text" name="zone_id" id="zone_id_input" required placeholder="{{ $category->zone_placeholder ?? 'Zone ID' }}"
                    class="w-full rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 px-3 py-2.5 text-xs focus:ring-primary focus:border-primary text-slate-950 dark:text-white font-mono">
            </div>
            @endif
        </div>

        <div id="nickname-area" class="hidden items-center gap-2 px-3 py-1.5 bg-primary/5 rounded-lg border border-primary/10">
            <span class="material-icons-round text-primary text-[10px]">verified_user</span>
            <span class="text-[10px] text-slate-600 dark:text-white font-medium">Player: <span id="player-nickname" class="font-bold text-slate-950 dark:text-white"></span></span>
        </div>
        <input type="hidden" name="player_nickname" id="player_nickname_input" value="{{ old('player_nickname') }}">
    </div>

    {{-- Step 2: Nominals --}}
    <div id="nominal-section" class="glass-panel-mobile dark:bg-white/5 p-4 rounded-2xl space-y-3 transition-all duration-300">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-primary/20 flex items-center justify-center text-primary font-bold text-xs">2</div>
            <h3 class="font-bold text-sm text-slate-950 dark:text-white">Pilih Nominal</h3>
        </div>

        @if($activeJenis->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            @foreach($activeJenis as $aj)
            <button type="button" onclick="filterServices({{ $aj->id }})" class="jenis-btn whitespace-nowrap px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all {{ $loop->first ? 'active-jenis bg-primary text-white shadow-lg shadow-primary/20' : 'bg-black/5 dark:bg-white/5 text-slate-500 dark:text-white/70' }}" data-id="{{ $aj->id }}">
                {{ $aj->name }}
            </button>
            @endforeach
        </div>
        @endif

        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-2" id="services-grid">
            @forelse($services as $s)
            <div class="relative group active:scale-95 transition-transform service-item" data-jenis="{{ $s->product_jenis_id }}">
                <input type="radio" name="product_code" id="n-{{ $loop->index }}" value="{{ $s->product_code }}" data-name="{{ $s->name }}" data-price="{{ number_format($s->price, 0, ',', '.') }}" required class="peer hidden radio-card">
                <label for="n-{{ $loop->index }}" class="block p-2 rounded-lg bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/5 transition-all cursor-pointer">
                    <div class="text-[10px] font-bold text-slate-950 dark:text-white mb-0.5 line-clamp-2 h-7 flex items-center">{{ $s->name }}</div>
                    <div class="text-[8.5px] text-slate-500 dark:text-white/80 font-medium">Rp {{ number_format($s->price, 0, ',', '.') }}</div>
                </label>
            </div>
            @empty
            <div class="col-span-2 text-center py-4 text-[10px] text-slate-500 dark:text-white/50">
                Produk sedang tidak tersedia.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Step 3: Payment --}}
    <div class="glass-panel-mobile dark:bg-white/5 p-4 rounded-2xl space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-primary/20 flex items-center justify-center text-primary font-bold text-xs">3</div>
            <h3 class="font-bold text-sm text-slate-950 dark:text-white">Metode Pembayaran</h3>
        </div>
        <div class="space-y-4">
             @auth
             <div class="space-y-2 mb-4">
                <h4 class="text-[10px] font-bold text-slate-500 dark:text-white/90 uppercase tracking-widest ml-1">Pembayaran Internal</h4>
                <div class="relative group active:scale-[0.98] transition-all">
                    <input type="radio" name="payment" id="p-saldo" value="SALDO" data-name="Saldo Akun" data-fee="0" required class="peer hidden method-card">
                    <label for="p-saldo" class="flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-5 bg-white rounded overflow-hidden flex items-center justify-center">
                                <img src="https://ui-avatars.com/api/?name=SA&background=00f0ff&color=fff" class="w-full h-full object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[12px] font-bold text-slate-950 dark:text-white">Saldo Akun</span>
                                <span class="text-[9px] text-primary font-bold">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-green-500 uppercase tracking-tighter">Bebas Biaya</div>
                        </div>
                    </label>
                </div>
             </div>
             @endauth
             @forelse($groupedPayments as $type => $payments)
             <div class="space-y-2">
                <h4 class="text-[10px] font-bold text-slate-500 dark:text-white/90 uppercase tracking-widest ml-1">{{ $type }}</h4>
                @foreach($payments as $p)
                <div class="relative group active:scale-[0.98] transition-all">
                    <input type="radio" name="payment" id="p-{{ $p->id }}" value="{{ $p->code }}" data-name="{{ $p->name }}" data-fee="{{ $p->fee }}" required class="peer hidden method-card">
                    <label for="p-{{ $p->id }}" class="flex items-center justify-between p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-5 bg-white rounded overflow-hidden flex items-center justify-center">
                                @if($p->image)
                                    <img src="{{ asset($p->image) }}" class="w-full h-full object-contain">
                                @else
                                    <span class="text-[7px] font-bold">{{ strtoupper(substr($p->name, 0, 3)) }}</span>
                                @endif
                            </div>
                            <span class="text-[12px] font-bold text-slate-950 dark:text-white">{{ $p->name }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-primary">{{ $p->fee }}</div>
                            <div class="text-[8px] text-slate-400 dark:text-white/60">Biaya Layanan</div>
                        </div>
                    </label>
                </div>
                @endforeach
             </div>
             @empty
             <div class="text-center py-4 text-[10px] text-slate-500 dark:text-white/50">
                 Metode pembayaran tidak tersedia.
             </div>
             @endforelse
        </div>
    </div>
</div>

{{-- Sticky Bottom Action Bar --}}
<div class="fixed bottom-[52px] inset-x-0 z-40 bg-white/80 backdrop-blur-xl border-t border-black/5 p-3 pb-safe flex items-center justify-center shadow-[0_-8px_30px_rgba(0,0,0,0.12)]">
    <div class="w-full max-w-5xl flex items-center justify-between">
        <div class="flex flex-col min-w-0">
            <span class="text-[9px] text-slate-500 dark:text-white/70 font-bold uppercase tracking-widest">Total Bayar</span>
            <span id="summary-player-name" data-sticky-summary="1" class="hidden text-[8px] text-slate-500 dark:text-white/60 font-medium max-w-[46vw] truncate leading-tight text-left"></span>
            <span class="text-base font-display font-black text-secondary leading-none" id="summary-total">Rp 0</span>
        </div>
        <button type="submit" class="px-6 py-3 rounded-xl bg-linear-to-r from-secondary to-pink-600 text-white font-bold font-display tracking-tighter text-[13px] shadow-lg shadow-secondary/30 active:scale-95 transition-all">
            BELI SEKARANG
        </button>
    </div>
</div>
    <div id="summary-nominal" class="hidden"></div>
    <div id="summary-payment" class="hidden"></div>
    <div id="summary-whatsapp" class="hidden"></div>
    <div id="summary-userid" class="hidden"></div>
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
