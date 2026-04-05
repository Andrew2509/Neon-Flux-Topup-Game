{{--
    Hindari 500 ViteManifestNotFoundException di production bila belum npm run build.
    Tetap jalankan `npm ci && npm run build` di server agar Tailwind + JS dari Vite tersedia.
--}}
@php
    $entries = $entries ?? ['resources/css/app.css', 'resources/js/app.js'];
    $viteOk = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
@if ($viteOk)
    @vite($entries)
@endif
