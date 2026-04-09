<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\Provider;
use App\Models\Service;

function checkId($userId, $zoneId, $productCode) {
    echo "Testing Check ID for User: $userId, Zone: $zoneId, Product: $productCode\n";
    
    try {
        $request = new \Illuminate\Http\Request([
            'user_id' => $userId,
            'zone_id' => $zoneId,
            'product_code' => $productCode,
            'operator_id' => '1',
            'game_slug' => 'mobile-legends'
        ]);

        // $controller = app(App\Http\Controllers\TransactionController::class);
        // We can't easily call private methods, but we can check the dependencies.
        
        $provider = Provider::where('name', 'like', '%Toko%')
            ->orWhere('name', 'like', '%tokovoucher%')
            ->orderBy('id')
            ->first();

        if (!$provider) {
            echo "Error: Provider Tokovoucher not found!\n";
            return;
        }
        echo "Found Provider: " . $provider->name . "\n";

        $service = Service::where('product_code', $productCode)
            ->where('provider', 'TokoVoucher')
            ->first();

        if (!$service) {
            echo "Error: Service for product code $productCode not found!\n";
            return;
        }
        echo "Found Service: " . $service->name . "\n";

        // Try to manually perform the inquiry steps to see where it breaks
        $refId = 'INQ'.strtoupper(bin2hex(random_bytes(6)));
        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $signature = md5($refId.':'.$memberCode.':'.$secret);

        $payload = [
            'ref_id' => $refId,
            'produk' => $productCode,
            'tujuan' => $userId,
            'server_id' => (string)($zoneId),
            'member_code' => $memberCode,
            'signature' => $signature,
        ];

        echo "Payload created. Sending request to Tokovoucher...\n";
        
        $base = config('services.tokovoucher.api_base') ?: 'https://api.tokovoucher.net';
        $url = rtrim($base, '/').'/v1/pascabayar-inq';
        
        echo "URL: $url\n";

        $response = Http::timeout(10)->post($url, $payload);
        
        echo "Response Code: " . $response->status() . "\n";
        echo "Response Body: " . $response->body() . "\n";

    } catch (\Exception $e) {
        echo "Exception Caught: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}

// Example ML test
// Needs a valid product code from the DB
$firstMlService = Service::where('provider', 'TokoVoucher')->first();
if ($firstMlService) {
    checkId('12345678', '1234', $firstMlService->product_code);
} else {
    echo "No Tokovoucher services found in DB.\n";
}
