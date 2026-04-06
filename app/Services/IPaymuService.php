<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IPaymuService
{
    protected string $va;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $provider = \App\Models\Provider::forIpaymu();

        $this->va = trim((string) ($provider?->provider_id ?? ''));
        $this->apiKey = trim((string) ($provider?->api_key ?? ''));

        // Mengikuti ENVIRONMENT MODE di Admin → Provider (sandbox | production).
        $this->baseUrl = ($provider && $provider->usesProductionApi())
            ? 'https://my.ipaymu.com'
            : 'https://sandbox.ipaymu.com';

        if (! $provider) {
            Log::warning('iPaymu: tidak ada Provider dengan nama iPaymu / mengandung "ipaymu" — sandbox URL dipakai dan kredensial kosong.');
        } else {
            Log::debug('iPaymu init', [
                'provider_name' => $provider->name,
                'base_url' => $this->baseUrl,
                'uses_production_api' => $provider->usesProductionApi(),
            ]);
        }
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
            $response = $this->ipaymuHttp()->withHeaders($headers)->post($url, $body);
            $result = $response->json();

            if (! $response->successful()) {
                $http = $response->status();
                $payload = is_array($result) ? $result : [];
                $msg = $payload['Message'] ?? $payload['message'] ?? $payload['error'] ?? $payload['errors'] ?? null;
                if (is_array($msg)) {
                    $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
                }
                $msg = $msg !== null ? trim((string) $msg) : '';
                if ($msg === '') {
                    $raw = trim(strip_tags($response->body()));
                    $msg = $raw !== '' ? Str::limit($raw, 400) : '';
                }
                if ($msg === '' || strcasecmp($msg, 'Server Error') === 0 || strcasecmp($msg, 'Internal Server Error') === 0) {
                    $msg = "API iPaymu mengembalikan HTTP {$http} (gangguan di sisi iPaymu atau permintaan ditolak). Coba lagi beberapa saat, ganti channel bayar, atau hubungi support iPaymu.";
                }

                Log::warning('iPaymu HTTP non-success', [
                    'http' => $http,
                    'url' => $url,
                    'body_snippet' => Str::limit($response->body(), 1500),
                ]);

                return [
                    'Status' => $http,
                    'Success' => false,
                    'Message' => $msg,
                    'Data' => null,
                ];
            }

            if (! is_array($result)) {
                Log::error('iPaymu non-JSON response', ['snippet' => Str::limit($response->body(), 500)]);

                return [
                    'Status' => 502,
                    'Success' => false,
                    'Message' => 'Respons iPaymu tidak valid (bukan JSON). Periksa koneksi atau status layanan iPaymu.',
                    'Data' => null,
                ];
            }

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
     * Client HTTP untuk iPaymu: timeout lebih panjang + opsi IPv4-only (hindari hang di VPS tanpa route IPv6).
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function ipaymuHttp()
    {
        $cfg = config('services.ipaymu', []);
        $pending = Http::timeout((int) ($cfg['http_timeout'] ?? 90))
            ->connectTimeout((int) ($cfg['http_connect_timeout'] ?? 25));

        if (! empty($cfg['force_ipv4']) && defined('CURL_IPRESOLVE_V4')) {
            $pending = $pending->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ]);
        }

        return $pending;
    }

    /**
     * Payload notify iPaymu: gabungkan JSON mentah bila Laravel belum mem-parse (Content-Type tidak JSON, proxy, dll).
     */
    public static function parseNotifyPayload(Request $request): array
    {
        $payload = $request->all();
        $raw = $request->getContent();
        $trim = ltrim((string) $raw);
        if ($trim !== '' && $trim[0] === '{') {
            $j = json_decode($raw, true);
            if (JSON_ERROR_NONE === json_last_error() && is_array($j)) {
                $payload = array_replace($payload, $j);
            }
        }

        return $payload;
    }

    /**
     * Ambil field notify (snake_case atau camelCase).
     *
     * @param  array<int, string>  $keys
     */
    public static function notifyField(array $payload, array $keys): mixed
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $payload)) {
                continue;
            }
            $v = $payload[$k];
            if ($v !== null && $v !== '') {
                return $v;
            }
        }

        return null;
    }

    /**
     * True jika notifikasi menandakan pembayaran sukses (variasi field iPaymu v2).
     */
    public static function isNotifyPaid(array $payload): bool
    {
        $sc = self::notifyField($payload, ['status_code', 'statusCode']);
        if ($sc !== null && (int) $sc === 1) {
            return true;
        }
        $tsc = self::notifyField($payload, ['transaction_status_code', 'transactionStatusCode']);
        if ($tsc !== null && (int) $tsc === 1) {
            return true;
        }
        $po = self::notifyField($payload, ['paid_off', 'paidOff']);
        if ($po !== null && (int) $po === 1) {
            return true;
        }
        $st = strtolower((string) self::notifyField($payload, ['status', 'Status']));
        if ($st !== '' && in_array($st, ['berhasil', 'success', 'sukses', 'settlement', 'lunas'], true)) {
            return true;
        }

        return false;
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
            $response = $this->ipaymuHttp()->withHeaders($headers)->get($url);
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

        // Strategy 0: iPaymu API v2 (sama seperti request payment) — POST:VA:sha256(body):apiKey lalu HMAC-SHA256(..., apiKey)
        // Lihat contoh resmi: https://gist.githubusercontent.com/iogias/7470dbcd586df1b45613e6a9c67c717c/raw
        if ($this->va !== '' && $this->apiKey !== '') {
            $jsonBodiesForHash = [];
            foreach ([false, true] as $normalizeNumerics) {
                $payload = $data;
                unset($payload['signature']);
                if ($normalizeNumerics) {
                    $payload = $this->normalizeIpaymuCallbackNumerics($payload);
                }
                // Urutan kunci seperti diterima PHP (bisa sama dengan JSON yang ditandatangani simulator)
                foreach ([JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 0] as $jf) {
                    $j = json_encode($payload, $jf);
                    if ($j !== false) {
                        $jsonBodiesForHash[] = $j;
                    }
                }
                $this->ksortRecursive($payload);
                foreach ([JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 0] as $jf) {
                    $j = json_encode($payload, $jf);
                    if ($j !== false) {
                        $jsonBodiesForHash[] = $j;
                    }
                }
            }
            $bodyStrings = array_unique(array_filter(array_merge($rawCandidates, $jsonBodiesForHash)));

            foreach ($bodyStrings as $bodyStr) {
                $bodyHash = strtolower(hash('sha256', $bodyStr));
                foreach (['POST', 'post'] as $method) {
                    $variants = [
                        $method.':'.$this->va.':'.$bodyHash.':'.$this->apiKey,
                        $method.':'.$this->va.':'.$bodyHash,
                    ];
                    foreach ($variants as $stringToSign) {
                        $gen = strtolower(hash_hmac('sha256', $stringToSign, $this->apiKey));
                        if (hash_equals($receivedSignature, $gen)) {
                            Log::info('iPaymu Validation Success: Strategy 0 (v2 stringToSign)');

                            return true;
                        }
                    }
                }
            }
        }

        // Strategy 1: HMAC-SHA256( hex(SHA256(raw_body)), secret ) — variasi body & secret (legacy / varian)
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
