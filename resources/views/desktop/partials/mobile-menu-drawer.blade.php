{{-- Drawer harus di akhir body (setelah konten/banner) agar tidak ketutup lapisan halaman --}}
<div id="nf-drawer" class="fixed inset-0 z-[10000] hidden md:hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" data-nf-drawer-backdrop></div>
    <div class="absolute top-0 right-0 z-[1] h-full w-[min(100%,20rem)] flex flex-col nf-drawer-panel border-l border-white/15 bg-[rgba(12,14,24,0.72)] backdrop-blur-2xl shadow-2xl supports-[backdrop-filter]:bg-[rgba(12,14,24,0.55)]">
        <div class="flex items-center justify-between p-4 border-b border-white/10 bg-white/[0.04]">
            <span class="font-display font-bold text-white text-sm tracking-wide">Menu</span>
            <button type="button" id="nf-drawer-close" class="size-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white touch-manipulation" aria-label="Tutup">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 flex flex-col gap-1.5" role="navigation">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-lg font-bold">Top-Up</a>
            <a href="{{ route('catalog') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-lg font-bold">Games</a>
            <a href="{{ route('track.order') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-lg font-bold">Cek Transaksi</a>
            
            <p class="text-xs font-black text-white/50 uppercase tracking-widest px-4 pt-5 pb-2">Kalkulator</p>
            <a href="{{ route('kalkulator.winrate') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-base font-medium">Win Rate</a>
            <a href="{{ route('kalkulator.magicwheel') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-base font-medium">Magic Wheel</a>
            <a href="{{ route('kalkulator.zodiac') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-base font-medium">Zodiac</a>
            
            <p class="text-xs font-black text-white/50 uppercase tracking-widest px-4 pt-5 pb-2">Lainnya</p>
            <a href="{{ route('faq') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-base font-medium">FAQ</a>
            <a href="{{ route('cara-order') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-base font-medium">Cara order</a>
        </nav>
    </div>
</div>

<style>
    .nf-drawer-panel {
        padding-top: max(1rem, env(safe-area-inset-top));
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
<script>
(function () {
    var drawer = document.getElementById('nf-drawer');
    if (!drawer) return;
    var openBtn = document.getElementById('nf-drawer-open');
    var closeBtn = document.getElementById('nf-drawer-close');
    var backdrop = drawer.querySelector('[data-nf-drawer-backdrop]');
    function openD() {
        drawer.classList.remove('hidden');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeD() {
        drawer.classList.add('hidden');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    openBtn && openBtn.addEventListener('click', openD);
    closeBtn && closeBtn.addEventListener('click', closeD);
    backdrop && backdrop.addEventListener('click', closeD);
})();
</script>
