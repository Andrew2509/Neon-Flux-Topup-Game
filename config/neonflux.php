<?php

return [

    /*
    | HP & tablet memakai view folder `desktop` yang sama (Tailwind responsif).
    | Nonaktifkan dengan false bila ingin kembali ke view hp/tablet terpisah.
    */
    'unified_desktop_views' => filter_var(env('NEONFLUX_UNIFIED_DESKTOP_VIEWS', true), FILTER_VALIDATE_BOOLEAN),

];
