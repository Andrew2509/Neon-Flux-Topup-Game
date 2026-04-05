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
    public function validateCallback(array $data, string $receivedSignature, string $rawBody = ''): bool
    {
        Log::debug('iPaymu Validate Trace start:', [
            'api_key_last_4' => substr($this->apiKey, -4),
            'va' => $this->va,
            'received_sig' => $receivedSignature,
            'body_snippet' => substr($rawBody, 0, 100),
            'body_len' => strlen($rawBody)
        ]);

        if (empty($receivedSignature)) {
            Log::warning('iPaymu Validation: No signature received');
            return false;
        }

        // Strategy 1: Official iPaymu V2 (HMAC-SHA256 with Raw Body & API Key)
        if (!empty($rawBody)) {
            $sha256_body = hash('sha256', $rawBody);
            $generatedV2 = hash_hmac('sha256', $sha256_body, $this->apiKey);
            if (hash_equals($generatedV2, $receivedSignature)) {
                Log::info('iPaymu Validation Success: Strategy 1 (V2)');
                return true;
            }
        }


        // Strategy 2: iPaymu Official (sha256(va + trx_id + status + apiKey))
        $va = $data['va'] ?? $this->va;
        $trx_id = $data['trx_id'] ?? '';
        $status = $data['status'] ?? '';
        $legacyString = $va . $trx_id . $status . $this->apiKey;
        $generatedLegacy = hash('sha256', $legacyString);
        if (hash_equals($generatedLegacy, $receivedSignature)) {
            return true;
        }

        // Strategy 3: iPaymu HMAC with API Key and JSON Payload
        $payload = $data;
        unset($payload['signature']);
        ksort($payload);
        $jsonBody = json_encode($payload);
        $generatedHMAC = hash_hmac('sha256', $jsonBody, $this->apiKey);
        if (hash_equals($generatedHMAC, $receivedSignature)) {
            return true;
        }

        Log::warning('iPaymu Signature Mismatch Debug:', [
            'received' => $receivedSignature,
            'expected_v2' => !empty($rawBody) ? hash_hmac('sha256', hash('sha256', $rawBody), $this->apiKey) : 'N/A',
            'expected_legacy' => $generatedLegacy,
            'raw_body_len' => strlen($rawBody),
            'va_used' => $va
        ]);

        return false;
    }

}
