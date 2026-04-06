<?php

namespace App\Services;

use App\Jobs\ProcessSupplierOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * Jalur "komunikasi" kedua setelah bayar: cek status TV + retry kirim transaksi bila masih paid.
 * Dipanggil dari POST /api/tokovoucher/webhook (header relay internal), bukan dari iPaymu langsung.
 */
class TokovoucherPostPaymentRelay
{
    public static function run(Order $order): void
    {
        $order = $order->fresh();
        if (! $order) {
            return;
        }

        Log::info('Tokovoucher relay: mulai', ['order_id' => $order->order_id, 'status' => $order->status]);

        TokovoucherOrderSync::syncPaidOrderFromTvStatusApi($order, true);

        $order->refresh();
        if ($order->status === 'success') {
            return;
        }

        if (! in_array($order->status, ['paid', 'processing', 'failed_provider'], true)) {
            return;
        }

        try {
            ProcessSupplierOrder::dispatchSync($order->fresh());
        } catch (\Throwable $e) {
            Log::warning('Tokovoucher relay: retry ProcessSupplierOrder gagal', [
                'order_id' => $order->order_id,
                'msg' => $e->getMessage(),
            ]);
            ProcessSupplierOrder::dispatch($order->fresh());
        }
    }
}
