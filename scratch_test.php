<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\IPaymuService;

try {
    echo "Initializing IPaymuService...\n";
    $provider = \App\Models\Provider::forIpaymu();
    if ($provider) {
        echo "Found Provider: " . $provider->name . " (ID: " . $provider->id . ")\n";
        echo "VA: " . $provider->provider_id . "\n";
        echo "API Key Prefix: " . substr($provider->api_key, 0, 8) . "...\n";
    } else {
        echo "Provider iPaymu NOT FOUND in database!\n";
        echo "Listing all providers:\n";
        foreach (\App\Models\Provider::all() as $p) {
            echo "- " . $p->name . " (status: " . $p->status . ")\n";
        }
    }
    
    $service = new IPaymuService();
    echo "Service initialized.\n";

    echo "Attempting to create a test payment...\n";
    $testPayload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'amount' => 10000,
        'notifyUrl' => 'https://example.com/notify',
        'returnUrl' => 'https://example.com/return',
        'cancelUrl' => 'https://example.com/cancel',
        'referenceId' => 'TEST-' . time(),
        'paymentMethod' => 'va',
        'paymentChannel' => 'bri',
        'product' => ['Test Product'],
        'qty' => [1],
        'price' => [10000]
    ];
    
    $res = $service->createPayment($testPayload);
    echo "Response received:\n";
    print_r($res);
    
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
