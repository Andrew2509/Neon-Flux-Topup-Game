@extends('desktop.layouts.neonflux')

@section('title', 'Top-up ' . $category->name . ' - ' . get_setting('site_name', 'Neon Flux'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/neonflux/topupgame.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('js/neonflux/topupgame.js') }}"></script>
@endpush

@section('content')
<div class="hero-bg"></div>

<main class="pt-32 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen relative">
    <form action="{{ route('checkout') }}" method="POST" id="topup-form">
        @csrf
    <!-- Header: MLBB Logo & Title -->
    <div class="flex flex-col md:flex-row items-center md:items-end gap-6 mb-10 pl-2">
        <div class="relative w-32 h-32 md:w-40 md:h-40 rounded-2xl overflow-hidden shadow-neon-cyan border-2 border-primary/50 group">
            <img alt="{{ $category->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" src="{{ $category->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=random&color=fff'" />
        </div>
        <div class="flex-1 text-center md:text-left transition-colors">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-950 dark:text-white mb-2 dark:text-glow">{{ $category->name }}</h1>
            <p class="text-slate-600 dark:text-gray-300 text-lg max-w-2xl">Top up {{ $category->name }} cepat, murah, dan aman. Cukup masukkan User ID, pilih nominal, dan selesaikan pembayaran.</p>
            <div class="flex items-center justify-center md:justify-start gap-4 mt-4">
                <span class="px-3 py-1 rounded-full bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/10 text-xs font-mono flex items-center gap-1 text-green-600 dark:text-green-400">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Layanan Aktif
                </span>
                <span class="px-3 py-1 rounded-full bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/10 text-xs font-mono text-primary">
                    Instant Delivery
                </span>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Area -->
        <div class="lg:col-span-2 space-y-8">
            @include('partials.neonflux.topup-customer-whatsapp')
            <!-- Step 1: ID -->
            <section class="glass-panel p-6 sm:p-8 rounded-3xl relative overflow-hidden group section-1">
                <div class="absolute top-0 left-0 w-1 h-full bg-primary shadow-neon-cyan"></div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl border border-primary/40 shadow-neon-cyan">1</div>
                    <h2 class="text-xl font-display font-bold text-slate-950 dark:text-white">Masukkan Data Akun</h2>
                </div>
                <div class="grid @if($category->has_zone) grid-cols-1 md:grid-cols-2 @else @endif gap-6 items-end">
                    <div>
                        <label class="block text-sm font-medium text-slate-500 dark:text-gray-400 mb-2">{{ $category->input_label ?? 'User ID' }}</label>
                        <input type="text" name="user_id" id="user_id_input" data-operator-id="{{ $category->ext_id }}" data-game-slug="{{ $category->slug }}" data-requires-zone="{{ $category->has_zone ? '1' : '0' }}" placeholder="{{ $category->input_placeholder ?? 'Contoh: 12345678' }}" class="w-full rounded-xl glass-input px-4 py-3 focus:ring-0 transition-all font-mono" required>
                    </div>

                    @if($category->has_zone)
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-500 dark:text-gray-400 mb-2">{{ $category->zone_label ?? 'Zone ID' }}</label>
                            <input type="text" name="zone_id" id="zone_id_input" placeholder="{{ $category->zone_placeholder ?? '1234' }}" class="w-full rounded-xl glass-input px-4 py-3 focus:ring-0 transition-all font-mono" required>
                        </div>
                    </div>
                    @endif
                </div>
                <div id="nickname-area" class="mt-4 hidden items-center gap-2 px-4 py-2 bg-primary/5 rounded-lg border border-primary/10 max-w-fit animate-pulse">
                    <span class="material-icons-round text-primary text-sm">verified_user</span>
                    <span class="text-sm text-slate-600 dark:text-gray-300">Nama Pemain: <span id="player-nickname" class="js-player-nick font-bold text-slate-950 dark:text-white tracking-wide"></span></span>
                </div>
                <input type="hidden" name="player_nickname" id="player_nickname_input" value="{{ old('player_nickname') }}">
                <p class="text-xs text-gray-500 mt-3 flex items-center">
                    <span class="material-icons-round text-sm mr-1">info</span>
                    Untuk menemukan User ID, ketuk avatar Anda di sudut kiri atas layar utama permainan. ID User akan terlihat dibawah nama karakter.
                </p>
            </section>

            <!-- Step 2: Nominal -->
            <section id="nominal-section" class="glass-panel p-6 sm:p-8 rounded-3xl relative overflow-hidden transition-all duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl border border-primary/40 shadow-neon-cyan">2</div>
                    <h2 class="text-xl font-display font-bold text-slate-950 dark:text-white">Pilih Nominal Top-Up</h2>
                </div>

                @if($activeJenis->count() > 0)
                <div class="relative flex items-center mb-6 group/nav">
                    <!-- Left Arrow -->
                    <button type="button" onclick="scrollJenis('left')" class="absolute -left-4 z-10 w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white opacity-0 group-hover/nav:opacity-100 transition-all hover:bg-primary/20 hover:border-primary/50 shadow-lg">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>

                    <!-- Scrollable Container -->
                    <div id="jenis-scroll-container" class="flex flex-nowrap gap-2 p-1 bg-slate-100 dark:bg-white/5 rounded-2xl overflow-x-auto scrollbar-none scroll-smooth w-full">
                        @foreach($activeJenis as $aj)
                        <button type="button" onclick="filterServices({{ $aj->id }})" class="jenis-btn whitespace-nowrap px-6 py-2.5 rounded-xl text-xs font-bold transition-all {{ $loop->first ? 'active-jenis bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-500 hover:text-primary dark:text-gray-400 dark:hover:text-white' }}" data-id="{{ $aj->id }}">
                            {{ $aj->name }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Right Arrow -->
                    <button type="button" onclick="scrollJenis('right')" class="absolute -right-4 z-10 w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white opacity-0 group-hover/nav:opacity-100 transition-all hover:bg-primary/20 hover:border-primary/50 shadow-lg">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="services-grid">
                    @forelse($services as $s)
                    <label class="cursor-pointer group relative service-item" data-jenis="{{ $s->product_jenis_id }}">
                        <input type="radio" name="product_code" value="{{ $s->product_code }}" class="peer hidden radio-card" data-name="{{ $s->name }}" data-price="{{ number_format($s->price, 0, ',', '.') }}" required>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white transition-all duration-300 z-20 border-2 border-white dark:border-slate-900 shadow-lg shadow-primary/20 overflow-hidden">
                            <span class="material-symbols-outlined text-[14px] font-bold">check</span>
                        </div>
                        <div class="glass-panel-light p-4 rounded-xl flex flex-col items-center justify-center h-full transition-all duration-300 peer-checked:bg-primary/10 peer-checked:border-primary border border-white/5 hover:border-white/20">
                            @if(Str::contains($s->name, 'HOT'))
                            <div class="absolute top-2 right-2">
                                <span class="bg-secondary text-[10px] font-bold px-1.5 py-0.5 rounded text-white shadow-none dark:shadow-neon-magenta">HOT</span>
                            </div>
                            @endif
                            <img alt="{{ $category->name }}" class="w-8 h-8 mb-2 rounded-lg object-cover" src="{{ $category->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=random&color=fff'" />
                            <span class="font-bold text-center text-sm md:text-base text-slate-950 dark:text-white">{{ $s->name }}</span>
                            <div class="mt-3 bg-slate-100 dark:bg-black/30 rounded-lg px-3 py-1 w-full text-center border border-slate-200 dark:border-white/5 group-hover:border-primary/30 transition-colors">
                                <span class="text-sm font-semibold text-primary">Rp {{ number_format($s->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </label>
                    @empty
                    <div class="col-span-full py-10 text-center opacity-50">Produk tidak tersedia.</div>
                    @endforelse
                </div>
            </section>

            <!-- Step 3: Payment -->
            <section id="payment-section" class="glass-panel p-6 sm:p-8 rounded-3xl relative overflow-hidden transition-all duration-300">
                <div class="absolute top-0 left-0 w-1 h-full bg-primary shadow-neon-cyan"></div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl border border-primary/40 shadow-neon-cyan">3</div>
                    <h2 class="text-xl font-display font-bold text-slate-950 dark:text-white">Pilih Metode Pembayaran</h2>
                </div>

                <div class="flex flex-col gap-6">
                    @auth
                    <div>
                        <h3 class="text-sm font-bold text-slate-500 dark:text-gray-400 mb-3 uppercase tracking-wide ml-1">Internal</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                            <label class="cursor-pointer w-full group relative">
                                <input type="radio" name="payment" value="SALDO" class="peer hidden method-card" data-name="Saldo Akun" data-fee="0" required>
                                <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white transition-all duration-300 z-20 border-2 border-white dark:border-slate-900 shadow-lg shadow-primary/20 overflow-hidden">
                                    <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                </div>
                                <div class="glass-panel-light p-3 rounded-xl flex items-center justify-between transition-all hover:bg-white/5 border border-white/10 h-auto min-h-16 peer-checked:bg-primary/10 peer-checked:border-primary">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center text-primary shrink-0">
                                            <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-slate-950 dark:text-white">Saldo Akun</div>
                                            <div class="text-[10px] text-slate-500 dark:text-gray-400">Saldo: Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endauth

                    @forelse($groupedPayments as $type => $payments)
                    <div>
                        <h3 class="text-sm font-bold text-slate-500 dark:text-gray-400 mb-3 uppercase tracking-wide ml-1">{{ $type }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($payments as $p)
                                @if($p->code === 'SALDO') @continue @endif
                            <label class="cursor-pointer w-full group relative">
                                <input type="radio" name="payment" value="{{ $p->code }}" class="peer hidden method-card" data-name="{{ $p->name }}" data-fee="{{ $p->fee }}" required>
                                <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-primary rounded-full hidden peer-checked:flex items-center justify-center text-white transition-all duration-300 z-20 border-2 border-white dark:border-slate-900 shadow-lg shadow-primary/20 overflow-hidden">
                                    <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                </div>
                                <div class="glass-panel-light p-3 rounded-xl flex items-center justify-between transition-all hover:bg-white/5 border border-white/10 h-auto min-h-16 peer-checked:bg-primary/10 peer-checked:border-primary">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center overflow-hidden p-1 shadow-sm shrink-0">
                                            @if($p->image)
                                                <img src="{{ asset($p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-contain">
                                            @else
                                                <span class="font-bold text-[10px] text-center text-slate-800">{{ strtoupper(substr($p->name, 0, 3)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-slate-950 dark:text-white">{{ $p->name }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-gray-400">Proses Otomatis</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 opacity-50">Metode pembayaran tidak tersedia.</div>
                    @endforelse
            </section>


        </div>

        <!-- Sidebar Summary -->
        <div class="lg:col-span-1 relative">
            <div class="sticky top-28 space-y-4">
                <div class="glass-panel p-6 rounded-3xl border border-secondary/30 shadow-none dark:shadow-neon-magenta">
                    <h3 class="text-xl font-display font-bold text-slate-950 dark:text-white mb-6 flex items-center border-b border-black/5 dark:border-white/10 pb-4">
                        <span class="material-icons-round mr-2 text-secondary">shopping_cart</span>
                        Ringkasan Pesanan
                    </h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">Item:</span>
                            <span class="text-slate-950 dark:text-white font-medium text-right">{{ $category->name }}</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">Nominal:</span>
                            <span class="font-medium text-right font-mono text-lg text-primary" id="summary-nominal">Pilih Produk</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">Metode Bayar:</span>
                            <span class="text-slate-950 dark:text-white font-medium text-right" id="summary-payment">Pilih Metode</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">Biaya Produk:</span>
                            <span class="text-slate-950 dark:text-white font-medium text-right font-mono" id="summary-base-price">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">Biaya Layanan:</span>
                            <span class="text-slate-950 dark:text-white font-medium text-right font-mono" id="summary-fee">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-start text-sm hidden" id="row-discount">
                            <span class="text-green-500 font-bold">Potongan Voucher:</span>
                            <span class="text-green-500 font-bold text-right font-mono" id="display-discount">-Rp 0</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">WhatsApp:</span>
                            <span class="text-slate-950 dark:text-white font-medium text-right font-mono text-xs" id="summary-whatsapp">Belum Diisi</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-slate-500 dark:text-gray-400">User ID:</span>
                            <span class="text-slate-400 dark:text-gray-500 font-mono text-xs text-right" id="summary-userid">Belum Diisi</span>
                        </div>
                    </div>

                    <!-- Voucher Input in Sidebar -->
                    <div class="mb-6 pb-6 border-b border-dashed border-black/10 dark:border-white/20">
                        <div class="flex gap-2">
                            <input type="text" id="voucher_code" placeholder="Kode Voucher" 
                                class="flex-1 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2 text-xs focus:ring-primary focus:border-primary outline-hidden text-slate-950 dark:text-white font-mono uppercase">
                            <button type="button" id="apply-voucher" class="px-4 py-2 bg-primary text-white rounded-lg text-xs font-bold shadow-md shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                                Pakai
                            </button>
                        </div>
                        <div id="voucher-msg" class="mt-2 text-[10px] hidden"></div>
                    </div>

                    <input type="hidden" name="voucher_code" id="applied_voucher_code">
                    <input type="hidden" name="voucher_discount" id="applied_voucher_discount" value="0">
                    <div class="border-t border-dashed border-black/10 dark:border-white/20 pt-4 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-slate-600 dark:text-gray-300 font-bold">Total Bayar:</span>
                            <span class="text-2xl font-bold font-mono text-secondary dark:text-glow" id="summary-total">Rp 0</span>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-gray-500 text-right mt-1">*Sudah termasuk pajak & biaya layanan</p>
                    </div>
                    <button type="submit" class="w-full py-4 rounded-xl bg-secondary dark:bg-linear-to-r dark:from-secondary dark:to-pink-600 text-white font-bold font-display tracking-wider text-lg shadow-lg dark:shadow-neon-magenta hover:shadow-xl dark:hover:shadow-neon-magenta/50 hover:scale-[1.02] transition-all flex items-center justify-center gap-2 group">
                        <span class="material-icons-round group-hover:animate-bounce">bolt</span>
                        BELI SEKARANG
                    </button>
                    <div class="mt-6 flex items-center justify-center gap-2 opacity-60">
                        <span class="material-icons-round text-gray-400 text-sm">security</span>
                        <span class="text-xs text-gray-400">Pembayaran 100% Aman & Terpercaya</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</main>

<!-- Decorative Blur Elements -->
<div class="fixed top-1/4 left-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse"></div>
<div class="fixed bottom-1/4 right-10 w-64 h-64 bg-secondary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
@push('scripts')
<script>
    function filterServices(jenisId) {
        // Update Buttons
        document.querySelectorAll('.jenis-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'active-jenis');
            btn.classList.add('text-slate-500', 'hover:text-primary', 'dark:text-gray-400', 'dark:hover:text-white');

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
        btn.classList.remove('text-slate-500', 'hover:text-primary', 'dark:text-gray-400', 'dark:hover:text-white');
    }

    function scrollJenis(direction) {
        const container = document.getElementById('jenis-scroll-container');
        const scrollAmount = 200;
        if (direction === 'left') {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
</script>
@endpush

@push('styles')
<style>
    .active-jenis {
        background-color: var(--color-primary) !important;
        color: white !important;
    }
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush
@endsection
