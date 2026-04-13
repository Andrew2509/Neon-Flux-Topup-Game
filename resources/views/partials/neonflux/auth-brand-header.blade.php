{{-- Logo & nama situs: sama sumbernya dengan navbar (pengaturan admin site_logo) --}}
@php
    $marginBottom = $marginBottom ?? 'mb-6';
@endphp
<div class="flex flex-col items-center justify-center {{ $marginBottom }}">
    @if ($logo = get_image_url('site_logo'))
        <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'NEON FLUX') }}" class="h-28 w-auto max-w-[250px] object-contain shrink-0">
    @else
        <div class="size-16 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20 shrink-0">
            <svg class="size-10" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V44" fill="currentColor" fill-rule="evenodd"></path>
            </svg>
        </div>
    @endif
</div>
