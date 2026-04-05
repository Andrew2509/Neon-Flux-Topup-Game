@if(filled($category->support_phone))
@php
    $display = trim($category->support_phone);
    $telHref = preg_replace('/[^\d+]/', '', preg_replace('/\s+/u', '', $display));
    if ($telHref === '') {
        $telHref = $display;
    }
    $variant = $variant ?? 'default';
    $isCompact = $variant === 'compact';
@endphp
<div @class([
    'rounded-xl border border-primary/30 bg-primary/5 dark:bg-primary/10 px-3 py-2.5 flex items-center gap-2',
    'mt-3' => $isCompact,
    'mt-4 max-w-2xl' => ! $isCompact,
])>
    <span class="material-symbols-outlined text-primary shrink-0 @if($isCompact) text-lg @else text-xl @endif">support_agent</span>
    <p @class([
        'text-slate-700 dark:text-gray-200',
        'text-[10px] leading-snug' => $isCompact,
        'text-sm' => ! $isCompact,
    ])>
        <span class="font-semibold text-slate-900 dark:text-white">Bantuan {{ $category->name }}</span>
        <span class="opacity-80 block mt-0.5">Hubungi: <a href="tel:{{ $telHref }}" class="font-mono text-primary hover:underline underline-offset-2">{{ $display }}</a></span>
    </p>
</div>
@endif
