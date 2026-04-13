<footer class="mt-20 pb-20 px-4 border-t border-black/5 bg-white">
    <div class="py-10 space-y-8">
        {{-- Brand Section --}}
        <div class="flex flex-col items-center text-center space-y-4">
            @if($logo = get_image_url('site_logo'))
                <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'Neon Flux') }}" class="h-24 w-auto">
            @endif
            <p class="text-[11px] text-slate-500 leading-relaxed px-6">
                {{ get_setting('meta_description', 'Platform top-up game tercepat dan terpercaya di Indonesia.') }}
            </p>
        </div>

        {{-- Contact Support Section --}}
        <div class="bg-slate-50 rounded-2xl p-6 space-y-5 border border-slate-100">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Kontak Support</h4>
            
            <div class="space-y-4">
                <a href="mailto:{{ get_setting('email_support', 'support@princepay.id') }}" class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200">
                    <span class="material-icons-round text-primary text-lg">mail</span>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Email</span>
                        <span class="text-xs font-bold text-slate-700">{{ get_setting('email_support', 'support@princepay.id') }}</span>
                    </div>
                </a>

                <a href="tel:{{ str_replace(' ', '', get_setting('site_phone', '+6281234567890')) }}" class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200">
                    <span class="material-icons-round text-primary text-lg">phone</span>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Telepon</span>
                        <span class="text-xs font-bold text-slate-700">{{ get_setting('site_phone', '+62 812 3456 7890') }}</span>
                    </div>
                </a>

                <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-slate-200">
                    <span class="material-icons-round text-primary text-lg mt-0.5">location_on</span>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Alamat</span>
                        <span class="text-xs font-bold text-slate-700 leading-relaxed">{{ get_setting('site_address', 'Jl. Jend. Sudirman No. 123, Jakarta') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Simple Links --}}
        <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
            <a href="{{ route('syarat-ketentuan') }}" class="text-[10px] font-bold text-slate-500">Syarat & Ketentuan</a>
            <a href="{{ route('kebijakan-privasi') }}" class="text-[10px] font-bold text-slate-500">Kebijakan Privasi</a>
            <a href="{{ route('kebijakan-refund') }}" class="text-[10px] font-bold text-slate-500">Kebijakan Refund</a>
            <a href="{{ route('faq') }}" class="text-[10px] font-bold text-slate-500">FAQ</a>
            <a href="{{ route('cara-order') }}" class="text-[10px] font-bold text-slate-500">Cara Order</a>
            <a href="{{ route('leaderboard') }}" class="text-[10px] font-bold text-slate-500">Leaderboard</a>
            <a href="{{ route('catalog') }}" class="text-[10px] font-bold text-slate-500">Daftar Game</a>
        </div>

        <div class="pt-6 border-t border-slate-100 text-center">
                &copy; {{ date('Y') }} {{ get_setting('site_name', 'PrincePay Gaming') }}
        </div>
    </div>
</footer>
