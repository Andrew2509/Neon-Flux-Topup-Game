{{-- Testimoni — auto-scroll horizontal --}}
@php
    $list = isset($testimonials) ? $testimonials : collect();
@endphp

<section id="testimoni" class="w-full mb-10 scroll-mt-28">
    <div class="flex items-center justify-between mb-4 px-1">
        <h2 class="text-base md:text-2xl font-display font-bold text-slate-950 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-primary text-xl md:text-2xl">format_quote</span>
            Testimoni pelanggan
        </h2>
    </div>

    @if($list->isEmpty())
        <div class="glass-panel rounded-2xl border border-black/5 dark:border-white/10 p-8 text-center text-sm text-slate-500 dark:text-slate-400">
            Belum ada ulasan yang ditampilkan. Setelah top-up berhasil, Anda bisa meninggalkan testimoni dari halaman sukses.
        </div>
    @else
        <div class="relative overflow-hidden rounded-2xl border border-black/5 dark:border-white/10 bg-black/[0.02] dark:bg-white/[0.02] py-4 md:py-5 marquee-mask">
            <div class="nf-testimonial-marquee flex w-max gap-4 md:gap-5 pr-4 md:pr-5">
                @foreach([$list, $list] as $loopIndex => $chunk)
                    <div class="flex gap-4 md:gap-5 shrink-0" @if($loopIndex === 1) aria-hidden="true" @endif>
                        @foreach($chunk as $t)
                            <article class="w-[200px] sm:w-[280px] md:w-[300px] shrink-0 glass-panel rounded-xl md:rounded-2xl p-3 md:p-4 border border-black/5 dark:border-white/10 shadow-sm dark:shadow-none flex flex-col">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="size-6 md:size-9 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-[10px] md:text-xs uppercase border border-primary/20">
                                        {{ mb_substr($t->displayName(), 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] md:text-xs font-bold text-slate-900 dark:text-white truncate">{{ $t->displayName() }}</p>
                                        <div class="flex text-amber-500 gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-icons-round text-[10px] md:text-[14px] {{ $i <= $t->stars ? '' : 'opacity-25' }}">star</span>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[9px] md:text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-4 flex-1">"{{ $t->comment }}"</p>
                                <p class="text-[8px] md:text-[9px] text-slate-400 dark:text-slate-500 mt-2 truncate">{{ $t->product_name }}</p>
                            </article>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

@push('styles')
<style>
    @keyframes nf-marquee-x {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .nf-testimonial-marquee {
        animation: nf-marquee-x linear infinite;
        animation-duration: {{ max(25, min(90, $list->count() * 6)) }}s;
    }
    .marquee-mask {
        mask-image: linear-gradient(to right, transparent, black 4%, black 96%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 4%, black 96%, transparent);
    }
    @media (prefers-reduced-motion: reduce) {
        .nf-testimonial-marquee { animation: none; }
    }
</style>
@endpush
