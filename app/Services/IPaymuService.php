<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IPaymuService
{
    protected string $va;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $provider = \App\Models\Provider::where('name', 'iPaymu')->first();
        
        $this->va = $provider->provider_id ?? '';
        $this->apiKey = $provider->api_key ?? '';
        $this->baseUrl = ($provider->mode ?? 'sandbox') === 'sandbox' 
            ? 'https://sandbox.ipaymu.com' 
            : 'https://my.ipaymu.com';
    }

    /**
     * Create Redirect Payment
     */
    /**
     * Create iPaymu Payment (Redirect or Direct)
     */
    public function createPayment(array $data)
    {
        $isDirect = !empty($data['paymentChannel']);
        $path = $isDirect ? '/api/v2/payment/direct' : '/api/v2/payment';
        $url = $this->baseUrl . $path;

        if ($isDirect) {
            // Map internal types to iPaymu paymentMethod types
            $methodMap = [
                'bank' => 'va',
                'retail' => 'cstore',
                'ewallet' => 'ewallet',
                'qris' => 'qris',
                'other' => 'va'
            ];
            $ipaymuMethod = $methodMap[$data['paymentMethod']] ?? $data['paymentMethod'] ?? 'va';

            // Body structure for Direct Payment
            $body = [
                'name' => $data['name'] ?? 'Guest',
                'email' => $data['email'] ?? 'guest@princepay.com',
                'phone' => $data['phone'] ?? '081122334455',
                'amount' => (float) $data['amount'],
                'notifyUrl' => $data['notifyUrl'],
                'returnUrl' => $data['returnUrl'],
                'cancelUrl' => $data['cancelUrl'],
                'referenceId' => $data['orderId'],
                'paymentMethod' => $ipaymuMethod,
                'paymentChannel' => $data['paymentChannel'],
            ];
        } else {
            // Body structure for Redirect Payment
            $body = [
                'product' => [$data['product']],
                'qty' => [1],
                'price' => [(float) $data['amount']],
                'returnUrl' => $data['returnUrl'],
                'cancelUrl' => $data['cancelUrl'],
                'notifyUrl' => $data['notifyUrl'],
                'referenceId' => $data['orderId'],
            ];
        }

        $headers = $this->generateHeaders($body, 'POST');

        try {
            Log::info('iPaymu Request:', ['url' => $url, 'headers' => $headers, 'body' => $body]);
            $response = Http::withHeaders($headers)->post($url, $body);
            $result = $response->json();
            Log::info('iPaymu Response:', ['res' => $result]);
            return $result;
        } catch (\Exception $e) {
            Log::error('IPaymu API Error:', ['msg' => $e->getMessage()]);
            return ['status' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate iPaymu Auth Headers
     */
    /**
     * Generate iPaymu Auth Headers
     */
    private function generateHeaders(array $body, string $method): array
    {
        $timestamp = date('YmdHis');
        // iPaymu expects empty body to be {} in signature calculation
        $jsonBody = empty($body) ? '{}' : json_encode($body, JSON_UNESCAPED_SLASHES);
        $bodyHash = strtolower(hash('sha256', $jsonBody));
        
        // Final signature structure based on official sample
        $stringToSign = strtoupper($method) . ':' . $this->va . ':' . $bodyHash . ':' . $this->apiKey;
        $signature = hash_hmac('sha256', $stringToSign, $this->apiKey);

        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'va' => $this->va,
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    /**
     * Get Available Payment Channels
     */
    public function getPaymentChannels()
    {
        $url = $this->baseUrl . '/api/v2/payment-channels';
        $body = []; // Empty body for GET-like POST in iPaymu channels API
        
        $headers = $this->generateHeaders($body, 'GET');

        try {
            // iPaymu recommends GET for channels
            $response = Http::withHeaders($headers)->get($url);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('IPaymu Channels API Error:', ['msg' => $e->getMessage()]);
            return ['status' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * Validate Callback Signature
     */
    public function validateCallback(array $data, string $receivedSignature): bool
    {
        if (empty($receivedSignature)) {
            return false;
        }

        // 1. Remove signature from data
        $payload = $data;
        unset($payload['signature']);

        // 2. Sort by keys ascending (ksort)
        ksort($payload);

        // 3. Generate JSON string
        $jsonBody = json_encode($payload);

        // 4. Generate HMAC-SHA256 using Merchant VA as Secret Key
        $calculatedSignature = hash_hmac('sha256', $jsonBody, $this->va);

        return hash_equals($calculatedSignature, $receivedSignature);
    }
}
