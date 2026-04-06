<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Provider;
use App\Services\FailedPermanentOrderWhatsAppNotifier;
use App\Services\TokovoucherService;
use App\Services\TopupSuccessWhatsAppNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessSupplierOrder implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $backoff = [60, 300, 600, 1200, 3600]; // Exponential backoff

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        // Skip if already fulfilled
        if ($this->order->status === 'success') {
            return;
        }

        // Jangan ubah status ke processing: pembayaran sudah 'paid' dan ditampilkan sebagai berhasil di lacak pesanan
        $this->order->logStatus('Memulai proses pengiriman ke supplier...', null);

        // Hanya akun TokoVoucher yang valid untuk api.tokovoucher.net (prioritas "Toko", bukan OR sembarang provider)
        $tokovoucher = Provider::where(function ($q) {
            $q->where('name', 'like', '%Toko%')
                ->orWhere('name', 'like', '%tokovoucher%');
        })->first();

        if (! $tokovoucher) {
            $tokovoucher = Provider::where('name', 'like', '%Digiflazz%')->first();
        }

        if (! $tokovoucher || ! $tokovoucher->provider_id || ! $tokovoucher->api_key) {
            $this->order->logStatus('Gagal: Konfigurasi supplier tidak lengkap.', 'failed_provider');

            return;
        }

        $payloadData = $this->order->payload;
        $tujuan = $payloadData['tujuan'] ?? '';
        $productCode = $payloadData['product_code'] ?? '';
        $serverId = $payloadData['server_id'] ?? '';

        $bases = array_values(array_unique(array_filter([
            config('services.tokovoucher.transaction_base', 'https://api.tokovoucher.net'),
            config('services.tokovoucher.transaction_fallback'),
        ])));

        try {
            $response = null;
            $lastTransactionPath = '/v1/transaksi';
            foreach ($bases as $idx => $base) {
                $baseTrim = rtrim((string) $base, '/');
                $path = TokovoucherService::transactionPathForBase($baseTrim);
                $lastTransactionPath = $path;
                $url = $baseTrim.$path;
                $response = $this->tokovoucherHttp()->get($url, [
                    'ref_id' => $this->order->order_id,
                    'produk' => $productCode,
                    'tujuan' => $tujuan,
                    'secret' => $tokovoucher->api_key,
                    'member_code' => $tokovoucher->provider_id,
                    'server_id' => $serverId,
                ]);

                $code = $response->status();
                $retryable = in_array($code, [502, 503, 504], true);
                if (! $retryable || $idx === count($bases) - 1) {
                    break;
                }

                Log::warning('TokoVoucher: HTTP '.$code.' pada host, mencoba host berikutnya', [
                    'host' => $base,
                    'path' => $path,
                    'order_id' => $this->order->order_id,
                ]);
            }

            $resultToko = $this->normalizeTokovoucherTransactionResponse($response, $lastTransactionPath);

            $tvStatus = $resultToko['status'] ?? null;
            $tvOk = $tvStatus === 1 || $tvStatus === '1' || $tvStatus === true
                || (is_string($tvStatus) && strcasecmp((string) $tvStatus, 'sukses') === 0);

            if ($response->successful() && $tvOk) {
                $merged = $this->order->payload ?? [];
                $merged['tokovoucher'] = [
                    'sn' => $resultToko['sn'] ?? '',
                    'ref_id' => $resultToko['ref_id'] ?? $this->order->order_id,
                    'trx_id' => $resultToko['trx_id'] ?? '',
                    'message' => $resultToko['message'] ?? '',
                ];
                $this->order->update(['payload' => $merged]);
                $this->order->logStatus('Sukses: Order berhasil dikirim ke supplier (TokoVoucher).', 'success', $resultToko);
                app(TopupSuccessWhatsAppNotifier::class)->notifyIfNeeded($this->order->fresh());
            } else {
                $msg = $this->formatTokovoucherFailureMessage($response, $resultToko, $productCode, $tujuan, $serverId);
                $this->order->logStatus("Pending: TokoVoucher — {$msg}. Akan dicoba lagi jika masih gagal.", null, $resultToko ?: ['raw' => Str::limit($response->body(), 2000)]);

                Log::warning('TokoVoucher transaksi tidak sukses', [
                    'order_id' => $this->order->order_id,
                    'http' => $response->status(),
                    'produk' => $productCode,
                    'tujuan' => $tujuan,
                    'server_id' => $serverId,
                    'body' => $resultToko ?: Str::limit($response->body(), 1000),
                ]);

                throw new \Exception('Supplier API Error: '.$msg);
            }
        } catch (\Exception $e) {
            Log::error('Supplier Job Exception', ['order_id' => $this->order->order_id, 'msg' => $e->getMessage()]);
            $this->order->logStatus('Error: Terjadi kesalahan koneksi ke supplier. Menunggu retry...', null, ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $msg = $exception->getMessage();

        // 502/503/504 = gangguan sementara di sisi TokoVoucher — bukan "voucher gagal".
        // Jangan set failed_permanent; biarkan status paid + user bisa refresh / cek status / admin retry job.
        if ($this->isTokovoucherTransientTransportError($msg)) {
            Log::warning('Supplier job habis retry karena HTTP 502/503/504 (sementara); order tetap paid', [
                'order_id' => $this->order->order_id,
                'message' => Str::limit($msg, 800),
            ]);

            $this->order->logStatus(
                'TokoVoucher sempat tidak tersedia (server sibuk/maintenance) setelah beberapa percobaan otomatis. Pembayaran Anda tetap tercatat — silakan tunggu lalu buka lagi halaman cek transaksi, atau hubungi admin bila lama tidak berubah.',
                null,
                ['transient_http' => true, 'detail' => Str::limit($msg, 600)]
            );

            return;
        }

        Log::error('Supplier Job Permanently Failed', [
            'order_id' => $this->order->order_id,
            'message' => $msg,
        ]);

        $this->order->logStatus('Gagal: Pengiriman ke supplier gagal setelah beberapa kali percobaan. Mohon hubungi Admin.', 'failed_permanent', [
            'final_error' => $msg,
        ]);

        try {
            app(FailedPermanentOrderWhatsAppNotifier::class)->notify($this->order->fresh(), $msg);
        } catch (\Throwable $e) {
            Log::error('Failed permanent: notifier WA error', [
                'order_id' => $this->order->order_id,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * JSON dari /v1/transaksi atau teks dari /trx (jalur IP).
     *
     * @return array<string, mixed>
     */
    private function normalizeTokovoucherTransactionResponse(\Illuminate\Http\Client\Response $response, string $path): array
    {
        if (str_ends_with($path, '/trx')) {
            $parsed = TokovoucherService::parseTrxIpTextResponse((string) $response->body());

            return TokovoucherService::trxIpTextToTransactionArray($parsed, $this->order->order_id);
        }

        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }

    private function isTokovoucherTransientTransportError(string $message): bool
    {
        if (preg_match('/\bHTTP\s*(502|503|504)\b/i', $message)) {
            return true;
        }

        return str_contains($message, 'Server TokoVoucher sibuk')
            || str_contains($message, 'Service Temporarily Unavailable');
    }

    /**
     * Gabungkan penjelasan gagal dari berbagai bentuk respons TokoVoucher.
     */
    private function formatTokovoucherFailureMessage(\Illuminate\Http\Client\Response $response, array $resultToko, string $productCode, string $tujuan, string $serverId): string
    {
        $parts = [];

        if (! $response->successful()) {
            $code = $response->status();
            $parts[] = 'HTTP '.$code;
            if (in_array($code, [502, 503, 504], true)) {
                $parts[] = 'Server TokoVoucher sibuk atau maintenance (coba lagi nanti; job antrian akan retry jika ada).';
            }
        }

        $textKeys = ['message', 'msg', 'error_msg', 'error', 'pesan', 'keterangan', 'errorMessage'];
        foreach ($textKeys as $key) {
            if (! empty($resultToko[$key]) && is_string($resultToko[$key])) {
                $parts[] = trim($resultToko[$key]);
                break;
            }
        }

        $st = $resultToko['status'] ?? null;
        if ($st !== null && $st !== '' && $st !== 1 && $st !== '1' && $st !== true) {
            $parts[] = 'status='.json_encode($st, JSON_UNESCAPED_UNICODE);
        }

        if (! empty($resultToko['trx_ip_raw']) && is_string($resultToko['trx_ip_raw'])) {
            $parts[] = 'Respons IP (/trx): '.Str::limit(trim($resultToko['trx_ip_raw']), 500);
        }

        if ($resultToko === [] && $response->body() !== '') {
            $parts[] = 'body: '.Str::limit(trim(strip_tags($response->body())), 400);
        } elseif ($parts === [] && $resultToko !== []) {
            $parts[] = Str::limit(json_encode($resultToko, JSON_UNESCAPED_UNICODE), 500);
        }

        if ($parts === []) {
            $parts[] = 'Respons kosong atau tidak dikenali. Periksa member_code/secret, kode produk, tujuan, dan saldo TokoVoucher.';
        }

        $hint = sprintf(
            ' [ref=%s, produk=%s, tujuan=%s, server_id=%s]',
            $this->order->order_id,
            $productCode !== '' ? $productCode : '-',
            $tujuan !== '' ? $tujuan : '-',
            $serverId !== '' ? $serverId : '-'
        );

        return implode(' — ', $parts).$hint;
    }

    /**
     * Client HTTP ke TokoVoucher; opsional paksa IPv4 agar IP publik sama dengan yang di-whitelist member.
     */
    private function tokovoucherHttp(): \Illuminate\Http\Client\PendingRequest
    {
        $req = Http::timeout(45)->connectTimeout(15);

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
