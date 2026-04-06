<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Setelah pembayaran → HTTP POST ke URL webhook sendiri (relay internal) agar sinkron TokoVoucher jalan lagi
 * setelah response callback gateway (iPaymu/dll.) selesai.
 *
 * Tanpa ShouldQueue: dengan dispatch()->afterResponse(), handle() dijalankan langsung setelah respons HTTP keluar.
 */
class RelayTokovoucherWebhookAfterPaymentJob
{
    use Dispatchable;

    public function __construct(public string $orderId) {}

    public function handle(): void
    {
        $secret = (string) config('services.tokovoucher.internal_relay_secret', '');
        if ($secret === '' || strlen($secret) < 16) {
            return;
        }

        $baseUrl = rtrim((string) config('app.url'), '/');
        $url = $baseUrl.'/api/tokovoucher/webhook';
        $ts = (string) time();
        $sig = hash_hmac('sha256', $this->orderId.'|'.$ts, $secret);

        try {
            $response = Http::timeout(35)
                ->connectTimeout(12)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-Neonflux-Relay' => '1',
                    'X-Neonflux-Timestamp' => $ts,
                    'X-Neonflux-Signature' => $sig,
                ])
                ->asJson()
                ->post($url, [
                    'ref_id' => $this->orderId,
                    'relay' => 'post_payment',
                ]);

            Log::info('Tokovoucher relay self-POST selesai', [
                'order_id' => $this->orderId,
                'http' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Tokovoucher relay self-POST gagal', [
                'order_id' => $this->orderId,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
