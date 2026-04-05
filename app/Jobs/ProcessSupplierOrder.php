<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Provider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $response = Http::get($url, [
                'ref_id' => $this->order->order_id,
                'produk' => $productCode,
                'tujuan' => $tujuan,
                'secret' => $tokovoucher->api_key,
                'member_code' => $tokovoucher->provider_id,
                'server_id' => $serverId,
            ]);

            $resultToko = $response->json() ?? [];

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
                $msg = $resultToko['message'] ?? 'Error dari supplier.';
                $this->order->logStatus("Pending: Supplier mengembalikan error: {$msg}. Akan dicoba lagi sesuai jadwal.", null, $resultToko);
                
                // Fail the job so it retries
                throw new \Exception("Supplier API Error: {$msg}");
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
    }
}
