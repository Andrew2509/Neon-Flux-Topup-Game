<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan sinkronisasi TokoVoucher setiap 15 menit agar data harga & produk selalu up-to-date
Schedule::command('sync:tokovoucher')->everyFifteenMinutes();
