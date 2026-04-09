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
 * @see https://docs.tokovoucher.net/transaksi/get-text (jalur IP: GET /trx, respons teks)
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
     * Cek saldo member Tokovoucher.
     * signature = md5(MEMBER_CODE:SECRET)
     *
     * @return float|null
     */
    public function checkBalance(): ?float
    {
        $provider = self::resolveTokovoucherProvider();
        if (! $provider || ! $provider->provider_id || ! $provider->api_key) {
            return null;
        }

        $memberCode = (string) $provider->provider_id;
        $secret = (string) $provider->api_key;
        $signature = md5($memberCode.':'.$secret);

        $base = rtrim((string) config('services.tokovoucher.api_base', 'https://api.tokovoucher.net'), '/');
        $url = $base.'/member';

        try {
            $response = $this->http()->get($url, [
                'member_code' => $memberCode,
                'signature' => $signature,
            ]);

            $json = $response->json();
            if (isset($json['status']) && $json['status'] == 1 && isset($json['data']['saldo'])) {
                return (float) $json['data']['saldo'];
            }

            Log::warning('TokoVoucher cek saldo: respons gagal', [
                'http' => $response->status(),
                'json' => $json,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('TokoVoucher cek saldo exception', ['msg' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>|null  $result
     */
    /**
     * Host trx-ip.tokovoucher.net memakai GET /trx (teks), bukan /v1/transaksi (JSON).
     *
     * @see https://docs.tokovoucher.net/transaksi/get-text
     */
    public static function transactionPathForBase(string $base): string
    {
        $host = strtolower((string) parse_url($base, PHP_URL_HOST));

        if ($host !== '' && str_contains($host, 'trx-ip')) {
            return '/trx';
        }

        return '/v1/transaksi';
    }

    /**
     * Parse baris respons teks jalur IP, mis.:
     * TRXID:.... REFID:.... PENDING. ...
     * TRXID:.... REFID:.... SUKSES, SN:.... ...
     * TRXID:.... REFID:.... GAGAL, pesan. ...
     *
     * @return array{raw: string, trx_id: string, ref_id: string, status: string, sn: string, message: string}
     */
    public static function parseTrxIpTextResponse(string $body): array
    {
        $body = trim($body);
        $out = [
            'raw' => $body,
            'trx_id' => '',
            'ref_id' => '',
            'status' => 'unknown',
            'sn' => '',
            'message' => '',
        ];

        if ($body === '') {
            return $out;
        }

        if (preg_match('/TRXID:([^.]*)\./i', $body, $m)) {
            $out['trx_id'] = trim($m[1]);
        }
        if (preg_match('/REFID:([^.]*)\./i', $body, $m)) {
            $out['ref_id'] = trim($m[1]);
        }

        if (preg_match('/\bPENDING\b/i', $body)) {
            $out['status'] = 'pending';
        } elseif (preg_match('/\bSUKSES\b/i', $body)) {
            $out['status'] = 'sukses';
            if (preg_match('/SN:([^\s.]+)/i', $body, $m)) {
                $out['sn'] = trim($m[1]);
            }
        } elseif (preg_match('/\bGAGAL\b/i', $body)) {
            $out['status'] = 'gagal';
            if (preg_match('/GAGAL,\s*([^.]+)\./is', $body, $m)) {
                $out['message'] = trim($m[1]);
            }
        }

        return $out;
    }

    /**
     * Samakan bentuk hasil parse teks IP ke struktur mirip JSON /v1/transaksi untuk satu alur di job.
     *
     * @param  array{raw: string, trx_id: string, ref_id: string, status: string, sn: string, message: string}  $parsed
     * @return array<string, mixed>
     */
    public static function trxIpTextToTransactionArray(array $parsed, string $fallbackRefId): array
    {
        $st = $parsed['status'] ?? 'unknown';
        $status = match ($st) {
            'sukses' => 1,
            'pending' => 'pending',
            'gagal' => 0,
            default => null,
        };

        return [
            'status' => $status,
            'sn' => $parsed['sn'] ?? '',
            'trx_id' => $parsed['trx_id'] ?? '',
            'ref_id' => ($parsed['ref_id'] ?? '') !== '' ? $parsed['ref_id'] : $fallbackRefId,
            'message' => $parsed['message'] ?? '',
            'trx_ip_text' => true,
            'trx_ip_raw' => Str::limit($parsed['raw'] ?? '', 2000),
        ];
    }

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
