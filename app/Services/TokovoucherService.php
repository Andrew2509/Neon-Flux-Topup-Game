<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Klien API TokoVoucher — cek status transaksi.
 *
 * @see https://docs.tokovoucher.net/cek-status/post
 * @see https://docs.tokovoucher.net/cek-status/get (GET ekuivalen; kami pakai POST JSON)
 */
class TokovoucherService
{
    public static function resolveTokovoucherProvider(): ?Provider
    {
        return Provider::where(function ($q) {
            $q->where('name', 'like', '%Toko%')
                ->orWhere('name', 'like', '%tokovoucher%');
        })->first();
    }

    /**
     * POST /v1/transaksi/status — signature md5(MEMBER_CODE:SECRET:REF_ID).
     *
     * @return array<string, mixed>|null
     */
    public function checkTransactionStatus(string $refId): ?array
    {
        $refId = trim($refId);
        if ($refId === '') {
            return null;
        }

        $provider = self::resolveTokovoucherProvider();
        if (! $provider || ! $provider->provider_id || ! $provider->api_key) {
            return null;
        }

        $memberCode = (string) $provider->provider_id;
        $secret = (string) $provider->api_key;
        $signature = md5($memberCode.':'.$secret.':'.$refId);

        $base = rtrim((string) config('services.tokovoucher.api_base', 'https://api.tokovoucher.net'), '/');
        $url = $base.'/v1/transaksi/status';

        $body = [
            'ref_id' => $refId,
            'member_code' => $memberCode,
            'signature' => $signature,
        ];

        try {
            $response = $this->http()->asJson()->acceptJson()->post($url, $body);
            $json = $response->json();
            if (! is_array($json)) {
                Log::warning('TokoVoucher cek status: respons bukan JSON', [
                    'ref_id' => $refId,
                    'http' => $response->status(),
                    'snippet' => Str::limit($response->body(), 400),
                ]);

                return null;
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('TokoVoucher cek status exception', ['ref_id' => $refId, 'msg' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>|null  $result
     */
    public static function parseStatusResult(?array $result): ?string
    {
        if ($result === null) {
            return null;
        }

        if (array_key_exists('error_msg', $result) && ! empty($result['error_msg'])) {
            return 'error';
        }

        $st = $result['status'] ?? null;
        if ($st === 0 || $st === '0') {
            return 'error';
        }

        if (is_string($st)) {
            return strtolower(trim($st));
        }

        return null;
    }

    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        $req = Http::timeout(30)->connectTimeout(12);

        if (config('services.tokovoucher.force_ipv4') && defined('CURL_IPRESOLVE_V4') && defined('CURLOPT_IPRESOLVE')) {
            $req = $req->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ]);
        }

        return $req;
    }
}
