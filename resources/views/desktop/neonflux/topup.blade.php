{{-- ============================================================
    TOP-UP PAGE — Halaman utama top-up game
    ============================================================ --}}
@extends('desktop.layouts.neonflux')

@section('title', get_setting('site_name') . ' — ' . get_setting('site_tagline'))

@section('content')
<main class="pt-32 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">

    {{-- ===== MAIN COLUMN: Hero + Game Grid ===== --}}
    <div class="lg:col-span-12 flex flex-col space-y-6">

        {{-- Hero Banner --}}
        @include('desktop.neonflux.sections.hero')

        @include('partials.neonflux.flash-sale')

        {{-- Popular Games --}}
        @include('desktop.neonflux.sections.popular-games')

        {{-- Category Tabs --}}
        @include('desktop.neonflux.sections.category-tabs')

        {{-- Game Grid --}}
        @include('desktop.neonflux.sections.game-grid')

        @include('desktop.neonflux.sections.testimonials-marquee')

    </div>

</main>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.category-tab');
    const cards = document.querySelectorAll('.game-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const group = tab.dataset.group;

            // 1. Update active tab UI
            tabs.forEach(t => {
                t.classList.remove('bg-primary', 'text-black', 'shadow-neon-cyan');
                t.classList.add('bg-black/5', 'dark:bg-white/5', 'border', 'border-black/10', 'dark:border-white/10', 'text-slate-600', 'dark:text-gray-400');
            });
            tab.classList.add('bg-primary', 'text-black', 'shadow-neon-cyan');
            tab.classList.remove('bg-black/5', 'dark:bg-white/5', 'border', 'border-black/10', 'dark:border-white/10', 'text-slate-600', 'dark:text-gray-400');

            // 2. Filter Grid
            cards.forEach(card => {
                if (group === 'all' || card.dataset.group === group) {
                    card.style.display = 'flex';
                    // Animation trigger if needed
                    // card.classList.add('animate-in', 'fade-in', 'zoom-in-95');
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Trigger first category on load
    const firstTab = document.querySelector('.category-tab');
    if (firstTab) firstTab.click();

    // Game grid: expand "Lihat semua"
    const gridViewport = document.getElementById('game-grid-viewport');
    const gridExpandBtn = document.getElementById('game-grid-expand-btn');
    const gridFade = document.getElementById('game-grid-fade');
    const gridExpandWrap = document.getElementById('game-grid-expand-wrap');
    if (gridExpandBtn && gridViewport) {
        gridExpandBtn.addEventListener('click', () => {
            const fullH = gridViewport.scrollHeight;
            gridViewport.style.transition = 'max-height 0.45s ease-out';
            gridViewport.style.maxHeight = fullH + 'px';
            const finish = () => {
                gridViewport.style.maxHeight = 'none';
                gridViewport.style.overflow = 'visible';
            };
            gridViewport.addEventListener('transitionend', finish, { once: true });
            gridFade?.classList.add('hidden');
            gridExpandWrap?.classList.add('hidden');
            window.setTimeout(() => {
                if (gridViewport.style.maxHeight !== 'none') {
                    finish();
                }
            }, 480);
        });
    }
});
</script>
@endpush
@endsection
