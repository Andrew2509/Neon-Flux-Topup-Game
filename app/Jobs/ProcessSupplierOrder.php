<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Provider;
use App\Services\FailedPermanentOrderWhatsAppNotifier;
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

    public function __construct(public Order $order)
    {}

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

        if (!$tokovoucher || !$tokovoucher->provider_id || !$tokovoucher->api_key) {
            $this->order->logStatus('Gagal: Konfigurasi supplier tidak lengkap.', 'failed_provider');
            return;
        }

        $payloadData = $this->order->payload;
        $tujuan = $payloadData['tujuan'] ?? '';
        $productCode = $payloadData['product_code'] ?? '';
        $serverId = $payloadData['server_id'] ?? '';

        $url = "https://api.tokovoucher.net/v1/transaksi";
        
        try {
            $response = Http::timeout(45)->connectTimeout(15)->get($url, [
                'ref_id' => $this->order->order_id,
                'produk' => $productCode,
                'tujuan' => $tujuan,
                'secret' => $tokovoucher->api_key,
                'member_code' => $tokovoucher->provider_id,
                'server_id' => $serverId,
            ]);

            $decoded = $response->json();
            $resultToko = is_array($decoded) ? $decoded : [];

            $tvStatus = $resultToko['status'] ?? null;
            $tvOk = $tvStatus === 1 || $tvStatus === '1' || $tvStatus === true;

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
        Log::error('Supplier Job Permanently Failed', [
            'order_id' => $this->order->order_id,
            'message' => $exception->getMessage()
        ]);

        $this->order->logStatus('Gagal: Pengiriman ke supplier gagal setelah beberapa kali percobaan. Mohon hubungi Admin.', 'failed_permanent', [
            'final_error' => $exception->getMessage()
        ]);

        try {
            app(FailedPermanentOrderWhatsAppNotifier::class)->notify($this->order->fresh(), $exception->getMessage());
        } catch (\Throwable $e) {
            Log::error('Failed permanent: notifier WA error', [
                'order_id' => $this->order->order_id,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Gabungkan penjelasan gagal dari berbagai bentuk respons TokoVoucher.
     */
    private function formatTokovoucherFailureMessage(\Illuminate\Http\Client\Response $response, array $resultToko, string $productCode, string $tujuan, string $serverId): string
    {
        $parts = [];

        if (! $response->successful()) {
            $parts[] = 'HTTP '.$response->status();
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
}
