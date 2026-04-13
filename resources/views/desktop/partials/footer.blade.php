{{-- ============================================================
    FOOTER — Modern multi-column footer
    ============================================================ --}}
<footer class="bg-surface-alt dark:bg-[#0a0a15] border-t border-black/5 dark:border-white/5 pt-16 pb-8 px-4 sm:px-6 lg:px-8 mt-20 relative overflow-hidden">
    {{-- Decorative Background Grid/Blur --}}
    <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none" style="background-image: radial-gradient(#22d3ee 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8">
            {{-- Column 1: Brand --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="flex items-center space-x-3">
                    @if($logo = get_image_url('site_logo'))
                        <img src="{{ $logo }}" alt="{{ get_setting('site_name') }}" class="h-32 w-auto">
                    @endif
                </div>
                <p class="text-slate-600 dark:text-gray-400 text-sm leading-relaxed max-w-xs">
                    {{ get_setting('meta_description', 'Platform top-up game tercepat dan terpercaya di Indonesia. Transaksi otomatis 24 jam non-stop dengan berbagai metode pembayaran aman.') }}
                </p>
                <div class="flex items-center">
                    {{-- Instagram --}}
                    <a href="https://instagram.com/{{ get_setting('instagram_username', 'neonflux') }}" target="_blank" class="uiverse-btn ig" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" fill-rule="evenodd" fill="currentColor"></path>
                        </svg>
                    </a>
                    {{-- WhatsApp --}}
                    <a href="{{ get_setting('whatsapp_link', '#') }}" target="_blank" class="uiverse-btn wa" aria-label="WhatsApp">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z" fill-rule="evenodd" fill="currentColor"></path>
                            <path d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z" fill="currentColor"></path>
                        </svg>
                    </a>
                    {{-- Facebook --}}
                    <a href="{{ get_setting('facebook_link', '#') }}" target="_blank" class="uiverse-btn fb" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path>
                        </svg>
                    </a>
                    {{-- TikTok --}}
                    <a href="{{ get_setting('tiktok_link', '#') }}" target="_blank" class="uiverse-btn tt" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <style>
            .uiverse-btn {
              cursor: pointer;
              width: 50px;
              height: 50px;
              border-radius: 50px;
              border: none;
              background: linear-gradient(120deg, #833ab4, #fd1d1d, #fcb045);
              position: relative;
              z-index: 0;
              display: flex;
              align-items: center;
              justify-content: center;
              margin-right: 12px;
              transition: transform 0.1s ease;
            }
            .uiverse-btn svg {
              color: white;
              width: 25px;
              height: 25px;
              z-index: 9;
            }
            .uiverse-btn.wa {
              background: linear-gradient(120deg, #02ff2c, #008a12);
            }
            .uiverse-btn.fb {
              background: rgb(69, 187, 255);
            }
            .uiverse-btn.tt {
              background: #000000;
            }
            .uiverse-btn:active {
              transform: scale(0.85);
            }
            .uiverse-btn::before {
              content: "";
              position: absolute;
              width: 56px;
              height: 56px;
              background-color: #0d0d1a; /* Blend with footer bg */
              border-radius: 50px;
              z-index: -1;
              transition: 0.4s;
            }
            .uiverse-btn:hover::before {
              width: 0px;
              height: 0px;
            }
            </style>

            {{-- Column 2: Peta Situs --}}
            <div class="lg:col-span-2">
                <h3 class="text-primary font-display font-bold text-sm tracking-[0.2em] uppercase mb-6">Peta Situs</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('catalog') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Daftar Game</a></li>
                    <li><a href="{{ route('cara-order') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Cara Order</a></li>
                    <li><a href="{{ route('leaderboard') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Leaderboard Player</a></li>
                </ul>
            </div>

            {{-- Column 3: Bantuan --}}
            <div class="lg:col-span-3">
                <h3 class="text-primary font-display font-bold text-sm tracking-[0.2em] uppercase mb-6">Bantuan & Kontak</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('syarat-ketentuan') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('kebijakan-privasi') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('kebijakan-refund') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">Kebijakan Refund</a></li>
                    <li><a href="{{ route('faq') }}" class="text-slate-600 dark:text-gray-400 hover:text-primary dark:hover:text-white transition-colors text-sm">FAQ</a></li>
                    <li class="pt-4 border-t border-black/5 dark:border-white/5">
                        <div class="flex items-center gap-3 text-slate-600 dark:text-gray-400 text-sm group">
                            <span class="material-icons-round text-primary text-lg group-hover:scale-110 transition-transform">mail</span>
                            <span class="group-hover:text-primary transition-colors">{{ get_setting('email_support', 'masbrightly@gmail.com') }}</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center gap-3 text-slate-600 dark:text-gray-400 text-sm group">
                            <span class="material-icons-round text-primary text-lg group-hover:scale-110 transition-transform">phone_in_talk</span>
                            <span class="group-hover:text-primary transition-colors">{{ get_setting('site_phone', '+62 81343323155') }}</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-start gap-3 text-slate-600 dark:text-gray-400 text-sm group">
                            <span class="material-icons-round text-primary text-lg mt-0.5 group-hover:scale-110 transition-transform">location_on</span>
                            <span class="leading-relaxed group-hover:text-primary transition-colors">{{ get_setting('site_address', 'Jl. Garuda Xi no 52, Waru, Sidoarjo, Jawa Timur') }}</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-primary font-display font-bold text-sm tracking-[0.2em] uppercase mb-6">Pembayaran Aman</h3>
                <div class="grid grid-cols-3 gap-2">
                    @if(isset($footerPayments))
                        @foreach ($footerPayments as $pm)
                        <div class="h-10 rounded-lg bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-center overflow-hidden grayscale hover:grayscale-0 transition-all p-1.5" title="{{ $pm->name }}">
                            <img src="{{ asset($pm->image) }}" alt="{{ $pm->name }}" class="max-h-full max-w-full object-contain">
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-16 pt-8 border-t border-black/5 dark:border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 dark:text-gray-500 text-xs text-center md:text-left">
                &copy; {{ date('Y') }} {{ get_setting('site_name', 'PrincePay Gaming') }}. All Rights Reserved.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                <a href="{{ route('syarat-ketentuan') }}" class="text-slate-500 dark:text-gray-500 hover:text-primary dark:hover:text-white transition-colors text-xs">Syarat & Ketentuan</a>
                <a href="{{ route('kebijakan-privasi') }}" class="text-slate-500 dark:text-gray-500 hover:text-primary dark:hover:text-white transition-colors text-xs">Kebijakan Privasi</a>
                <a href="{{ route('kebijakan-refund') }}" class="text-slate-500 dark:text-gray-500 hover:text-primary dark:hover:text-white transition-colors text-xs">Kebijakan Refund</a>
                <a href="{{ route('faq') }}" class="text-slate-500 dark:text-gray-500 hover:text-primary dark:hover:text-white transition-colors text-xs">FAQ</a>
            </div>
        </div>
    </div>
</footer>

{{-- Floating Blur Decorations --}}
<div class="fixed top-1/4 left-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse"></div>
<div class="fixed bottom-1/4 right-10 w-64 h-64 bg-secondary/10 rounded-full blur-3xl -z-10 pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
