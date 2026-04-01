<?php

namespace App\Services;

use App\Models\Provider;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    public function getServerKey()
    {
        $midtrans = Provider::where('name', 'like', '%Midtrans%')->first();
        return $midtrans ? $midtrans->api_key : env('MIDTRANS_SERVER_KEY');
    }

    public function configure()
    {
        $midtrans = Provider::where('name', 'like', '%Midtrans%')->first();
        
        if ($midtrans) {
            Config::$serverKey = $midtrans->api_key;
            Config::$isProduction = ($midtrans->mode === 'production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
            
            // Client key is usually stored in provider_id or another field, 
            // but for Snap redirection we mainly need serverKey.
            Config::$clientKey = $midtrans->provider_id; 
        } else {
            // Fallback to env or dummy for initial setup
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;
        }
    }

    /**
     * Verify Midtrans Signature
     */
    public function verifyNotification($requestData)
    {
        $serverKey = $this->getServerKey();
        $orderId = $requestData['order_id'] ?? '';
        $statusCode = $requestData['status_code'] ?? '';
        $grossAmount = $requestData['gross_amount'] ?? '';
        $signature = $requestData['signature'] ?? '';

        $calcSignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($signature, $calcSignature);
    }

    /**
     * Create Snap Token for redirect or popup
     */
    public function createSnapToken($params)
    {
        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create Snap URL for redirect
     */
    public function createSnapUrl($params)
    {
        try {
            return Snap::createTransaction($params)->redirect_url;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get Status of transaction
     */
    public function getStatus($orderId)
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            Log::error('Midtrans Status Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
