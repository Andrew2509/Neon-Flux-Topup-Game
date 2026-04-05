{{-- Logo & nama situs: sama sumbernya dengan navbar (pengaturan admin site_logo) --}}
@php
    $marginBottom = $marginBottom ?? 'mb-6';
@endphp
<div class="flex items-center gap-3 {{ $marginBottom }}">
    @if ($logo = get_image_url('site_logo'))
        <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'NEON FLUX') }}" class="h-8 w-auto max-w-[150px] object-contain object-left shrink-0">
    @else
        <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20 shrink-0">
            <svg class="size-5" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V44" fill="currentColor" fill-rule="evenodd"></path>
            </svg>
        </div>
    @endif
    <h2 class="text-white text-2xl font-black tracking-tighter">{{ get_setting('site_name', 'NEON FLUX') }}</h2>
</div>
