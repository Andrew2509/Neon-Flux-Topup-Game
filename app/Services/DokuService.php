<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $provider = \App\Models\Provider::where('name', 'DOKU')->first();

        $this->clientId  = $provider->provider_id ?? '';
        $this->secretKey = $provider->api_key ?? '';

        $mode = $provider->mode ?? 'sandbox';
        $this->baseUrl = $mode === 'production'
            ? 'https://api.doku.com'
            : 'https://api-sandbox.doku.com';
    }

    /**
     * Create a DOKU Checkout payment session.
     * Returns the full API response array including payment.url.
     */
    public function createCheckoutPayment(array $data): array
    {
        $requestTarget = '/checkout/v1/payment';
        $url = $this->baseUrl . $requestTarget;

        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z'); // ISO 8601 UTC+0

        // Build request body
        $body = [
            'order' => [
                'amount'         => (int) $data['amount'],
                'invoice_number' => $data['orderId'],
                'currency'       => 'IDR',
                'callback_url'   => $data['returnUrl'] ?? url('/'),
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 minutes
            ],
            'customer' => [
                'name'  => $data['name'] ?? 'Guest',
                'email' => $data['email'] ?? 'guest@neonflux.my.id',
                'phone' => $data['phone'] ?? '081122334455',
            ],
        ];

        // If payment method types specified, filter checkout page
        if (!empty($data['paymentMethodTypes'])) {
            $body['payment']['payment_method_types'] = $data['paymentMethodTypes'];
        }

        // Override notification URL to ensure DOKU sends callback to our endpoint
        $body['additional_info'] = [
            'override_notification_url' => $data['notifyUrl'] ?? url('/api/doku/callback'),
        ];

        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);

        // Generate signature
        $signature = $this->generateSignature(
            $this->clientId,
            $requestId,
            $timestamp,
            $requestTarget,
            $jsonBody,
            $this->secretKey
        );

        $headers = [
            'Client-Id'         => $this->clientId,
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature'         => $signature,
            'Content-Type'      => 'application/json',
        ];

        try {
            Log::info('DOKU Checkout Request:', [
                'url'     => $url,
                'headers' => array_merge($headers, ['Signature' => '***HIDDEN***']),
                'body'    => $body,
            ]);

            $response = Http::withHeaders($headers)
                ->withBody($jsonBody, 'application/json')
                ->post($url);

            $result = $response->json();

            Log::info('DOKU Checkout Response:', [
                'status' => $response->status(),
                'body'   => $result,
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status'  => $response->status(),
                    'message' => $result['message'] ?? $response->body(),
                    'raw'     => $result,
                ];
            }

            return [
                'success'    => true,
                'paymentUrl' => $result['response']['payment']['url'] ?? null,
                'tokenId'    => $result['response']['payment']['token_id'] ?? null,
                'sessionId'  => $result['response']['order']['session_id'] ?? null,
                'raw'        => $result,
            ];
        } catch (\Exception $e) {
            Log::error('DOKU Checkout Exception:', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate DOKU Non-SNAP Signature for request header.
     *
     * Algorithm:
     * 1. Digest = Base64(SHA-256(body))
     * 2. Components = "Client-Id:{id}\nRequest-Id:{rid}\nRequest-Timestamp:{ts}\nRequest-Target:{target}\nDigest:{digest}"
     * 3. Signature = "HMACSHA256=" + Base64(HMAC-SHA256(components, secretKey))
     */
    public function generateSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $requestTarget,
        string $body,
        string $secretKey
    ): string {
        // Step 1: Generate Digest
        $digest = base64_encode(hash('sha256', $body, true));

        // Step 2: Build component string (newline-separated)
        $components = implode("\n", [
            "Client-Id:{$clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$timestamp}",
            "Request-Target:{$requestTarget}",
            "Digest:{$digest}",
        ]);

        // Step 3: HMAC-SHA256 and prepend prefix
        $hmac = base64_encode(hash_hmac('sha256', $components, $secretKey, true));

        return "HMACSHA256={$hmac}";
    }

    /**
     * Validate notification signature from DOKU.
     *
     * DOKU sends:
     *   Header: Client-Id, Request-Id, Request-Timestamp, Signature
     *   Body: JSON notification payload
     *
     * We reconstruct the signature using our secret key and compare.
     */
    public function validateNotificationSignature(Request $request): bool
    {
        $clientId       = $request->header('Client-Id', '');
        $requestId      = $request->header('Request-Id', '');
        $timestamp       = $request->header('Request-Timestamp', '');
        $receivedSig    = $request->header('Signature', '');
        $rawBody        = $request->getContent();

        if (empty($receivedSig) || empty($clientId) || empty($rawBody)) {
            Log::warning('DOKU Notification: Missing required headers', [
                'has_signature' => !empty($receivedSig),
                'has_client_id' => !empty($clientId),
                'has_body'      => !empty($rawBody),
            ]);
            return false;
        }

        // Verify Client-Id matches ours
        if ($clientId !== $this->clientId) {
            Log::warning('DOKU Notification: Client-Id mismatch', [
                'received' => $clientId,
                'expected' => $this->clientId,
            ]);
            return false;
        }

        // Request-Target is the path of our notification URL
        $requestTarget = '/api/doku/callback';

        // Generate expected signature
        $expectedSig = $this->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $requestTarget,
            $rawBody,
            $this->secretKey
        );

        $isValid = hash_equals($expectedSig, $receivedSig);

        if (!$isValid) {
            Log::error('DOKU Notification: Signature mismatch', [
                'received' => $receivedSig,
                'expected' => $expectedSig,
                'client_id' => $clientId,
                'request_target' => $requestTarget,
            ]);
        }

        return $isValid;
    }
}
