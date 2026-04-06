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
        $provider = \App\Models\Provider::whereRaw('LOWER(TRIM(name)) = ?', ['ipaymu'])->first();

        $this->va = trim((string) ($provider->provider_id ?? ''));
        $this->apiKey = trim((string) ($provider->api_key ?? ''));

        $mode = strtolower(trim((string) ($provider->mode ?? 'sandbox')));
        $this->baseUrl = $mode === 'sandbox'
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
        if ($this->va === '' || $this->apiKey === '') {
            Log::warning('iPaymu: VA atau API Key kosong — pastikan ada baris provider iPaymu di admin.');

            return [
                'Status' => 401,
                'Success' => false,
                'Message' => 'Kredensial iPaymu belum lengkap (VA atau API Key kosong di pengaturan provider).',
                'Data' => null,
            ];
        }

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
                'amount' => (int) round((float) $data['amount']),
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
                'price' => [(int) round((float) $data['amount'])],
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
     * Validate Callback Signature (notify / sandbox simulation).
     * iPaymu bisa mengirim signature di header X-Signature, body form-urlencoded atau JSON,
     * dan dokumentasi publik sering memakai HMAC-SHA256 atas payload terurut dengan kunci VA atau API Key.
     */
    public function validateCallback(
        array $data,
        string $receivedSignature,
        string $rawBody = '',
        ?string $requestTimestamp = null,
        ?string $requestExternalId = null
    ): bool {
        $receivedSignature = strtolower(trim($receivedSignature));

        Log::debug('iPaymu Validate Trace start:', [
            'api_key_last_4' => substr($this->apiKey, -4),
            'va' => $this->va,
            'received_sig' => $receivedSignature,
            'body_snippet' => substr($rawBody, 0, 120),
            'body_len' => strlen($rawBody),
        ]);

        if ($receivedSignature === '') {
            Log::warning('iPaymu Validation: No signature received');

            return false;
        }

        $secrets = array_values(array_unique(array_filter([
            trim($this->apiKey),
            trim($this->va),
        ])));

        if ($secrets === []) {
            return false;
        }

        $rawCandidates = array_unique(array_filter([
            $rawBody,
            $this->ipaymuSortedFormBody($data),
            $this->ipaymuSortedFormBody($data, PHP_QUERY_RFC3986),
        ]));

        // Strategy 1: HMAC-SHA256( hex(SHA256(raw_body)), secret ) — variasi body & secret
        foreach ($rawCandidates as $rb) {
            $shaHex = hash('sha256', $rb);
            $shaBin = hash('sha256', $rb, true);
            foreach ($secrets as $sec) {
                foreach ([$shaHex, $shaBin] as $inner) {
                    $gen = strtolower(hash_hmac('sha256', $inner, $sec));
                    if (hash_equals($receivedSignature, $gen)) {
                        Log::info('iPaymu Validation Success: Strategy 1 (SHA256 body HMAC)');

                        return true;
                    }
                }
            }
        }

        // Strategy 1b: timestamp + path (simulasi notify sandbox dengan X-Timestamp)
        if ($requestTimestamp !== null && $requestTimestamp !== '') {
            $path = '/api/ipaymu/callback';
            foreach ($rawCandidates as $rb) {
                $h = hash('sha256', $rb);
                $patterns = [
                    'POST:'.$path.':'.$requestTimestamp.':'.$h,
                    'POST:'.$requestTimestamp.':'.$h,
                ];
                if ($requestExternalId) {
                    $patterns[] = $requestExternalId.':'.$requestTimestamp.':'.$h;
                }
                foreach ($patterns as $p) {
                    foreach ($secrets as $sec) {
                        $gen = strtolower(hash_hmac('sha256', $p, $sec));
                        if (hash_equals($receivedSignature, $gen)) {
                            Log::info('iPaymu Validation Success: Strategy 1b (timestamp string)');

                            return true;
                        }
                    }
                }
            }
        }

        // Strategy 2: sha256(va + trx_id + status + apiKey) dan variasi status_code
        $va = (string) ($data['va'] ?? $this->va);
        $trxId = (string) ($data['trx_id'] ?? '');
        $status = (string) ($data['status'] ?? '');
        $statusCode = (string) ($data['status_code'] ?? '');

        foreach ([$status, $statusCode] as $st) {
            if ($st === '') {
                continue;
            }
            $legacyString = $va.$trxId.$st.$this->apiKey;
            $gen = strtolower(hash('sha256', $legacyString));
            if (hash_equals($receivedSignature, $gen)) {
                Log::info('iPaymu Validation Success: Strategy 2 (legacy concat)');

                return true;
            }
        }

        // Strategy 3: HMAC-SHA256(sorted JSON, secret) — kunci VA dan/atau API Key, beberapa flag JSON
        $jsonFlagSets = [
            JSON_UNESCAPED_SLASHES,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            0,
        ];

        foreach ([false, true] as $normalizeNumerics) {
            $payload = $data;
            unset($payload['signature']);
            if ($normalizeNumerics) {
                $payload = $this->normalizeIpaymuCallbackNumerics($payload);
            }
            $this->ksortRecursive($payload);

            foreach ($jsonFlagSets as $flags) {
                $jsonBody = json_encode($payload, $flags);
                if ($jsonBody === false) {
                    continue;
                }
                foreach ($secrets as $sec) {
                    $gen = strtolower(hash_hmac('sha256', $jsonBody, $sec));
                    if (hash_equals($receivedSignature, $gen)) {
                        Log::info('iPaymu Validation Success: Strategy 3 (sorted JSON HMAC)');

                        return true;
                    }
                }
            }
        }

        Log::warning('iPaymu Signature Mismatch Debug:', [
            'received' => $receivedSignature,
            'raw_body_len' => strlen($rawBody),
            'va_used' => $va,
        ]);

        return false;
    }

    private function ipaymuSortedFormBody(array $data, int $encType = PHP_QUERY_RFC1738): string
    {
        $copy = $data;
        unset($copy['signature']);
        $this->ksortRecursive($copy);

        return http_build_query($copy, '', '&', $encType);
    }

    private function ksortRecursive(array &$array): void
    {
        ksort($array, SORT_STRING);
        foreach ($array as &$v) {
            if (is_array($v)) {
                $this->ksortRecursive($v);
            }
        }
        unset($v);
    }

    /**
     * Samakan tipe angka/boolean dengan JSON notify iPaymu (string vs int) agar HMAC cocok.
     */
    private function normalizeIpaymuCallbackNumerics(array $payload): array
    {
        $intKeys = ['trx_id', 'status_code', 'transaction_status_code', 'paid_off'];
        $amountKeys = ['sub_total', 'total', 'amount', 'fee'];

        foreach ($intKeys as $k) {
            if (! array_key_exists($k, $payload) || $payload[$k] === '' || $payload[$k] === null) {
                continue;
            }
            if (is_numeric($payload[$k])) {
                $payload[$k] = (int) $payload[$k];
            }
        }
        foreach ($amountKeys as $k) {
            if (! array_key_exists($k, $payload) || $payload[$k] === '' || $payload[$k] === null) {
                continue;
            }
            if (is_numeric($payload[$k])) {
                $payload[$k] = is_string($payload[$k]) && str_contains((string) $payload[$k], '.')
                    ? (float) $payload[$k]
                    : (int) $payload[$k];
            }
        }
        if (array_key_exists('is_escrow', $payload)) {
            $payload['is_escrow'] = filter_var($payload['is_escrow'], FILTER_VALIDATE_BOOLEAN);
        }

        return $payload;
    }

}
