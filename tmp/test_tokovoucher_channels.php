<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Provider;
use Illuminate\Support\Facades\Http;

$provider = Provider::where('name', 'like', '%Toko%')->first();
if (!$provider) {
    echo "Provider not found\n";
    exit;
}

$params = [
    'member_code' => $provider->provider_id,
    'secret' => $provider->api_key
];

$url = 'https://api.tokovoucher.net/v1/deposit/operator?' . http_build_query($params);
$response = Http::get($url);

echo json_encode($response->json(), JSON_PRETTY_PRINT);
