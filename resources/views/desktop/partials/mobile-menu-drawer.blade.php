{{-- Drawer harus di akhir body (setelah konten/banner) agar tidak ketutup lapisan halaman --}}
<div id="nf-drawer" class="fixed inset-0 z-[600] hidden lg:hidden" aria-hidden="true">
    <div class="absolute inset-0 z-0 bg-black/60 backdrop-blur-sm" data-nf-drawer-backdrop></div>
    <div class="absolute top-0 right-0 z-10 h-full w-[min(100%,20rem)] bg-[#0b0e14] border-l border-white/10 shadow-2xl flex flex-col nf-drawer-panel">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <span class="font-display font-bold text-white text-sm tracking-wide">Menu</span>
            <button type="button" id="nf-drawer-close" class="size-10 rounded-xl bg-white/5 flex items-center justify-center text-white touch-manipulation" aria-label="Tutup">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 flex flex-col gap-1" role="navigation">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 font-medium">Top-Up</a>
            <a href="{{ route('catalog') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 font-medium">Games</a>
            <a href="{{ route('track.order') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 font-medium">Cek Transaksi</a>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest px-4 pt-4 pb-1">Kalkulator</p>
            <a href="{{ route('kalkulator.winrate') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-sm">Win Rate</a>
            <a href="{{ route('kalkulator.magicwheel') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-sm">Magic Wheel</a>
            <a href="{{ route('kalkulator.zodiac') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-sm">Zodiac</a>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest px-4 pt-4 pb-1">Lainnya</p>
            <a href="{{ route('faq') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-sm">FAQ</a>
            <a href="{{ route('cara-order') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 text-sm">Cara order</a>
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
document.addEventListener('DOMContentLoaded', function () {
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
    if (openBtn) openBtn.addEventListener('click', openD);
    if (closeBtn) closeBtn.addEventListener('click', closeD);
    if (backdrop) backdrop.addEventListener('click', closeD);
});
</script>
