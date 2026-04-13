@extends('hp.layouts.neonflux')

@section('title', 'Neon Flux Indonesia — Game Top-up')

@section('content')
<div class="space-y-6">
    {{-- Hero Mobile --}}
    {{-- Hero Mobile Dynamic Banner --}}
    @if(isset($sliders) && $sliders->count() > 0)
        <!-- Swiper CSS for Mobile -->
        @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <link rel="stylesheet" href="{{ asset('neonflux/css/hero-social.css') }}?v={{ time() }}" />
        <style>
            .mobile-hero-swiper {
                width: 100%;
                height: 100%;
            }
            .swiper-pagination-bullet {
                background: rgba(255,255,255,0.3);
                opacity: 1;
            }
            .swiper-pagination-bullet-active {
                background: var(--primary, #00f2ff) !important;
                box-shadow: 0 0 10px var(--primary);
            }
        </style>
        @endpush

        <div class="relative w-full aspect-21/9 rounded-2xl overflow-hidden glass-panel-mobile shadow-md mb-6 group">
            <div class="swiper mobile-hero-swiper h-full">
                <div class="swiper-wrapper">
                    @foreach($sliders as $slider)
                    <div class="swiper-slide relative">
                        {{-- Background Image --}}
                        <img src="{{ $slider->image_path }}" 
                             alt="{{ $slider->title }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-active:scale-105">
                        
                        {{-- Main Link Overlay --}}
                        <a href="{{ $slider->link ?? '#' }}" class="absolute inset-0 z-10" aria-label="{{ $slider->title }}"></a>

                        <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/20 to-transparent pointer-events-none"></div>
                        <div class="absolute inset-0 bg-linear-to-r from-black/80 via-transparent to-transparent pointer-events-none"></div>

                        <div class="absolute inset-y-0 left-0 p-4 z-20 flex flex-col justify-center items-start transition-transform duration-500 group-active:translate-x-1 pointer-events-none">
                            <div class="max-w-[85%] space-y-1 pointer-events-auto">
                                {{-- Mobile Tags --}}
                                @if($slider->tags)
                                <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                    @foreach(explode(',', $slider->tags) as $index => $tag)
                                    <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-tighter border backdrop-blur-md shadow-lg
                                        {{ $index % 2 == 0 ? 'bg-secondary/90 border-secondary/30 text-white' : 'bg-blue-600/90 border-blue-400/30 text-white' }}">
                                        {{ trim($tag) }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <div>
                                    <h2 class="text-base font-display font-black tracking-tight text-white line-clamp-1 drop-shadow-xl uppercase leading-none">
                                        {{ $slider->title }}
                                    </h2>
                                    @if($slider->subtitle)
                                    <p class="text-[10px] font-black text-primary tracking-wide line-clamp-1 uppercase opacity-90 mt-0.5">
                                        {{ $slider->subtitle }}
                                    </p>
                                    @endif
                                </div>

                                @if($slider->description)
                                <p class="text-[9px] text-slate-300 line-clamp-2 max-w-[90%] leading-tight drop-shadow-md">
                                    {{ $slider->description }}
                                </p>
                                @endif

                                {{-- Mobile Social Icons --}}
                                <div class="hero-social-container pt-1.5">
                                    {{-- Instagram --}}
                                    @php $igUser = $slider->ig_link ?: get_setting('instagram_username', 'princepay.gaming'); @endphp
                                    <a href="https://instagram.com/{{ ltrim($igUser, '@') }}" target="_blank"
                                        class="hero-social-Btn instagram">
                                        <svg class="hero-social-svgIcon" viewBox="0 0 448 512">
                                            <path
                                                d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z">
                                            </path>
                                        </svg>
                                        <span class="hero-social-text">Instagram</span>
                                    </a>

                                    {{-- WhatsApp --}}
                                    @php $waLink = $slider->wa_link ?: get_setting('whatsapp_link', '#'); @endphp
                                    <a href="{{ $waLink }}" target="_blank"
                                        class="hero-social-Btn whatsapp">
                                        <svg class="hero-social-svgIcon" viewBox="0 0 448 512">
                                            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.4-8.6-44.6-27.6-16.5-14.7-27.6-32.8-30.8-38.4-3.2-5.6-.3-8.6 2.5-11.4 2.5-2.5 5.5-6.5 8.3-9.8 2.8-3.3 3.7-5.6 5.5-9.3 1.9-3.7.9-6.9-.5-9.8-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.3 5.7 23.7 9.1 31.7 11.7 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                                        </svg>
                                        <span class="hero-social-text">WhatsApp</span>
                                    </a>

                                    {{-- Facebook --}}
                                    @php $fbLink = $slider->fb_link ?: get_setting('facebook_link', '#'); @endphp
                                    <a href="{{ $fbLink }}" target="_blank"
                                        class="hero-social-Btn facebook">
                                        <svg class="hero-social-svgIcon" viewBox="0 0 512 512">
                                            <path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.75l-11 71.69h-57.75V501C413.31 482.38 504 379.78 504 256z"/>
                                        </svg>
                                        <span class="hero-social-text">Facebook</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination bottom-2!"></div>
            </div>
        </div>

        @push('scripts')
        <script src="{{ asset('neonflux/js/hero-social.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var slideCount = document.querySelectorAll('.mobile-hero-swiper .swiper-slide').length;
                new Swiper('.mobile-hero-swiper', {
                    loop: slideCount > 1,
                    autoplay: { delay: 4000 },
                    pagination: { el: '.swiper-pagination', clickable: true },
                    effect: 'fade',
                });
            });
        </script>
        @endpush
    @else
        <div class="relative w-full aspect-21/9 rounded-2xl overflow-hidden glass-panel-mobile shadow-md">
            <div class="absolute inset-0 bg-linear-to-r from-cyan-500 to-blue-600 opacity-15 z-0 animate-pulse"></div>
            <div class="relative z-10 h-full p-4 flex flex-col justify-center">
                <h2 class="text-xl font-display font-black tracking-tighter text-slate-950 dark:text-white leading-tight">
                    {{ get_setting('site_name', 'PrincePay Gaming') }}
                </h2>
                <p class="text-slate-600 dark:text-white/80 text-[10px] mt-1 line-clamp-1 italic">{{ get_setting('site_tagline', 'Otomatis 24 Jam.') }}</p>
                <div class="mt-2.5 flex gap-1.5">
                    <div class="px-2 py-1 rounded-md bg-primary text-black font-bold text-[8px] uppercase tracking-wider shadow-sm">Promo</div>
                    <div class="px-2 py-1 rounded-md bg-white/10 border border-white/20 text-slate-600 dark:text-white font-medium text-[8px] uppercase">Cashback 5%</div>
                </div>
            </div>
            <img src="https://placehold.co/600x250/1e293b/ccc?text={{ urlencode(get_setting('site_name', 'PrincePay')) }}" class="absolute top-0 right-0 w-1/2 h-full object-cover opacity-20 dark:opacity-40" style="mask-image: linear-gradient(to left, black, transparent);">
        </div>
    @endif

    @include('partials.neonflux.flash-sale')

    <div id="category-tabs-container-mobile" class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 -ms-3 px-3">
        @foreach($activeGroups as $key => $group)
        <button data-group="{{ $key }}" class="category-tab-mobile px-4 py-2.5 rounded-xl {{ $loop->first ? 'bg-primary text-black font-extrabold shadow-neon-cyan' : 'bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-600 dark:text-white/70 font-bold' }} whitespace-nowrap text-[11px] flex items-center gap-1.5 transition-all">
            <span class="material-icons-round text-base">{{ $group['icon'] }}</span>
            {{ $group['name'] }}
        </button>
        @endforeach
    </div>

    {{-- Popular Games (Horizontal Scroll) --}}
    {{-- ... (保持原樣, usually popular shouldn't be filtered by these tabs unless asked) --}}

    {{-- Main Game Grid (3 Columns) --}}
    <section>
        <div class="flex items-center gap-2 mb-3">
            <h3 class="text-base font-display font-bold text-slate-900 dark:text-white">Semua Game</h3>
        </div>
        @php $mobileGridCollapsible = isset($categories) && $categories->count() > 15; @endphp
        <div id="game-grid-mobile-viewport"
             class="@if($mobileGridCollapsible) relative overflow-hidden transition-[max-height] duration-500 ease-out @endif"
             @if($mobileGridCollapsible) style="max-height: min(82vh, 42rem);" @endif>
        <div id="game-grid-mobile" class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-6 gap-2">
            @forelse ($categories as $item)
            @php
                $gameGroup = 'topup'; // Default to topup if unknown
                if (in_array($item->type, ['Topup Game', 'Topup Game (Global)'])) $gameGroup = 'topup';
                elseif (in_array($item->type, ['Voucher Game', 'Voucher Data'])) $gameGroup = 'voucher';
                elseif (in_array($item->type, ['Pulsa', 'Paket Data', 'Telpon & SMS', 'Pulsa Transfer'])) $gameGroup = 'pulsa';
                elseif (in_array($item->type, ['Hiburan', 'TV', 'Lainnya'])) $gameGroup = 'streaming';
                
                if (stripos($item->name, 'Joki') !== false) $gameGroup = 'joki';
            @endphp
            <a href="{{ route('topup.game', $item->slug) }}" 
               data-group="{{ $gameGroup }}"
               class="game-card-mobile glass-panel-mobile p-1.5 rounded-xl flex flex-col items-center gap-1 shadow-sm active:scale-95 transition-all">
                <div class="w-full aspect-square rounded-lg bg-black/5 dark:bg-white/5 overflow-hidden">
                    <img src="{{ $item->icon ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop' }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=random&color=fff'">
                </div>
                <h4 class="text-[9px] font-bold text-slate-800 dark:text-white/90 text-center line-clamp-1 transition-colors">{{ $item->name }}</h4>
            </a>
            @empty
            <div class="col-span-3 text-center py-4 text-[10px] text-slate-500 dark:text-white/50">Belum ada game tersedia.</div>
            @endforelse
        </div>
        @if($mobileGridCollapsible)
        <div id="game-grid-mobile-fade" class="pointer-events-none absolute inset-x-0 bottom-0 z-[1] h-24 bg-linear-to-t from-[var(--bg-color)] via-[var(--bg-color)]/85 to-transparent" aria-hidden="true"></div>
        @endif
        </div>
        @if($mobileGridCollapsible)
        <div class="flex justify-center mt-4 mb-1" id="game-grid-mobile-expand-wrap">
            <button type="button" id="game-grid-mobile-expand-btn"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs bg-primary text-slate-900 shadow-neon-cyan active:scale-[0.98] transition-all">
                Lihat semua
                <span class="material-icons-round text-sm" aria-hidden="true">expand_more</span>
            </button>
        </div>
        @endif
    </section>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.category-tab-mobile');
    const cards = document.querySelectorAll('.game-card-mobile');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const group = tab.dataset.group;

            // 1. Update active tab UI
            tabs.forEach(t => {
                t.classList.remove('bg-primary', 'text-black', 'shadow-neon-cyan', 'font-extrabold');
                t.classList.add('bg-black/5', 'dark:bg-white/5', 'border', 'border-black/10', 'dark:border-white/10', 'text-slate-600', 'dark:text-gray-400', 'font-bold');
            });
            tab.classList.add('bg-primary', 'text-black', 'shadow-neon-cyan', 'font-extrabold');
            tab.classList.remove('bg-black/5', 'dark:bg-white/5', 'border', 'border-black/10', 'dark:border-white/10', 'text-slate-600', 'dark:text-gray-400', 'font-bold');

            // 2. Filter Grid
            cards.forEach(card => {
                if (group === 'all' || card.dataset.group === group) {
                    card.style.display = 'flex';
                    // card.classList.add('animate-in', 'fade-in', 'zoom-in-95');
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Trigger first category on load
    const firstTab = document.querySelector('.category-tab-mobile');
    if (firstTab) firstTab.click();

    const mViewport = document.getElementById('game-grid-mobile-viewport');
    const mBtn = document.getElementById('game-grid-mobile-expand-btn');
    const mFade = document.getElementById('game-grid-mobile-fade');
    const mWrap = document.getElementById('game-grid-mobile-expand-wrap');
    if (mBtn && mViewport) {
        mBtn.addEventListener('click', () => {
            const fullH = mViewport.scrollHeight;
            mViewport.style.transition = 'max-height 0.45s ease-out';
            mViewport.style.maxHeight = fullH + 'px';
            const finish = () => {
                mViewport.style.maxHeight = 'none';
                mViewport.style.overflow = 'visible';
            };
            mViewport.addEventListener('transitionend', finish, { once: true });
            mFade?.classList.add('hidden');
            mWrap?.classList.add('hidden');
            window.setTimeout(() => {
                if (mViewport.style.maxHeight !== 'none') {
                    finish();
                }
            }, 480);
        });
    }
});
</script>
@endpush
@endsection
