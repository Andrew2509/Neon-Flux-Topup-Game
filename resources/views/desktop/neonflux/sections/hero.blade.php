@if (isset($sliders) && $sliders->count() > 0)
    <!-- Swiper CSS -->
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <link rel="stylesheet" href="{{ asset('neonflux/css/hero-social.css') }}?v={{ time() }}" />
        <style>
            .hero-swiper {
                width: 100%;
                height: 100%;
            }

            .swiper-pagination-bullet {
                background: rgba(255, 255, 255, 0.3);
                opacity: 1;
            }

            .swiper-pagination-bullet-active {
                background: var(--primary, #00f2ff) !important;
                box-shadow: 0 0 10px var(--primary);
            }

            .hero-overlay-bottom {
                background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 40%, transparent 100%);
            }

            .hero-overlay-left {
                background: linear-gradient(to right, rgba(0, 0, 0, 0.8) 0%, transparent 100%);
            }
        </style>
    @endpush

    <div
        class="relative w-full h-80 rounded-3xl overflow-hidden group glass-panel border border-black/5 dark:border-white/10 shadow-sm dark:shadow-none bg-white dark:bg-transparent mb-6">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                @foreach ($sliders as $slider)
                    <div class="swiper-slide relative">
                        {{-- Background Image --}}
                        <img src="{{ $slider->image_path }}" alt="{{ $slider->title }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            onerror="this.onerror=null; this.src='https://placehold.co/1200x400/1e293b/ccc?text={{ urlencode($slider->title) }}';">

                        {{-- Main Link Overlay (Underneath interactive content) --}}
                        <a href="{{ $slider->link ?? '#' }}" class="absolute inset-0 z-10" aria-label="{{ $slider->title }}"></a>

                        <div class="absolute inset-0 hero-overlay-bottom opacity-90 pointer-events-none"></div>
                        <div class="absolute inset-0 hero-overlay-left opacity-70 pointer-events-none"></div>

                        <div
                            class="absolute inset-y-0 left-0 p-6 md:p-10 z-20 flex flex-col justify-center items-start transition-transform duration-500 group-hover:translate-x-1 pointer-events-none">
                            <div class="max-w-2xl space-y-1.5 pointer-events-auto">
                                {{-- Tags --}}
                                @if ($slider->tags)
                                    <div class="flex flex-wrap items-center gap-2">
                                        @foreach (explode(',', $slider->tags) as $index => $tag)
                                            <span
                                                class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border backdrop-blur-md shadow-lg
                                    {{ $index % 2 == 0 ? 'bg-secondary/90 border-secondary/30 text-white' : 'bg-blue-600/90 border-blue-400/30 text-white' }}">
                                                {{ trim($tag) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div>
                                    {{-- Title --}}
                                    <h2
                                        class="text-3xl md:text-5xl font-display font-black text-white mb-1 leading-none drop-shadow-2xl uppercase tracking-tighter">
                                        {{ $slider->title }}
                                    </h2>

                                    {{-- Subtitle --}}
                                    @if ($slider->subtitle)
                                        <h3
                                            class="text-xl md:text-3xl font-display font-black text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary drop-shadow-lg uppercase">
                                            {{ $slider->subtitle }}
                                        </h3>
                                    @endif
                                </div>

                                {{-- Description --}}
                                @if ($slider->description)
                                    <p
                                        class="text-slate-300 dark:text-gray-300 max-w-lg text-xs md:text-sm line-clamp-2 drop-shadow-md font-medium leading-relaxed opacity-90">
                                        {{ $slider->description }}
                                    </p>
                                @endif

                                {{-- Interactive Social Media Buttons (Aligned with Content) --}}
                                <div class="hero-social-container pt-4">
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

            <!-- Add Pagination -->
            <div class="swiper-pagination bottom-4!"></div>

            <!-- Add Navigation -->
            <div
                class="swiper-button-next text-white/50! hover:text-primary! size-8! after:text-lg! opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <div
                class="swiper-button-prev text-white/50! hover:text-primary! size-8! after:text-lg! opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('neonflux/js/hero-social.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var slideCount = document.querySelectorAll('.hero-swiper .swiper-slide').length;
                new Swiper('.hero-swiper', {
                    loop: slideCount > 1,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
            });
        </script>
    @endpush
@else
    {{-- Static Fallback if no sliders --}}
    <div
        class="relative w-full h-80 rounded-3xl overflow-hidden group glass-panel border border-black/5 dark:border-white/10 shadow-sm dark:shadow-none bg-white dark:bg-transparent mb-6">
        <img alt="Default Banner" class="absolute inset-0 w-full h-full object-cover opacity-40 dark:opacity-60"
            src="https://placehold.co/1200x400/1e293b/ccc?text={{ urlencode(get_setting('site_name', 'PrincePay')) }}" />

        <div class="absolute inset-0 hero-overlay-bottom"></div>

        <div class="absolute bottom-0 left-0 p-8 w-full">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-2 leading-tight">
                {{ get_setting('site_name', 'PrincePay Gaming') }}
            </h1>
            <p class="text-slate-600 dark:text-gray-300 max-w-lg text-sm md:text-base line-clamp-2">
                {{ get_setting('site_tagline', 'Premium Gaming Experience') }}
            </p>
        </div>
    </div>
@endif
