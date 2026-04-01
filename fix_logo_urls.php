<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentMethod;

$count = 0;
$targetDomain = 'https://neonflux.dpdns.org/';

foreach (PaymentMethod::all() as $pm) {
    if (str_starts_with($pm->image, $targetDomain)) {
        $newImage = str_replace($targetDomain, '/', $pm->image);
        $pm->update(['image' => $newImage]);
        echo "Updated {$pm->code}: {$pm->image} -> {$newImage}\n";
        $count++;
    }
}

echo "Total updated: $count\n";
