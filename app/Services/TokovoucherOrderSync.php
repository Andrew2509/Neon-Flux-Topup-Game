<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sinkron order dari API cek status TokoVoucher (dipakai halaman track + relay internal).
 */
class TokovoucherOrderSync
{
    /**
     * @see https://docs.tokovoucher.net/cek-status/post
     *
     * @param  bool  $ignoreThrottle  true untuk relay setelah bayar (tanpa tunggu cache 12s).
     */
    public static function syncPaidOrderFromTvStatusApi(?Order $order, bool $ignoreThrottle = false): void
    {
        if (! $order) {
            return;
        }

        if (! in_array($order->status, ['paid', 'processing', 'failed_provider'], true)) {
            return;
        }

        if (! TokovoucherService::resolveTokovoucherProvider()) {
            return;
        }

        $throttleKey = 'tv_tx_status:'.$order->order_id;
        if (! $ignoreThrottle && Cache::has($throttleKey)) {
            return;
        }

        $svc = new TokovoucherService;
        $res = $svc->checkTransactionStatus($order->order_id);
        $parsed = TokovoucherService::parseStatusResult($res);

        if ($parsed === null || $parsed === 'error') {
            if ($parsed === 'error') {
                Log::info('TokoVoucher cek status: respons error', [
                    'order_id' => $order->order_id,
                    'error_msg' => $res['error_msg'] ?? null,
                ]);
            }
            if (! $ignoreThrottle) {
                Cache::put($throttleKey, 1, 12);
            }

            return;
        }

        if ($parsed === 'pending') {
            if (! $ignoreThrottle) {
                Cache::put($throttleKey, 1, 12);
            }

            return;
        }

        if ($parsed === 'sukses' && is_array($res)) {
            try {
                DB::transaction(function () use ($order, $res) {
                    $o = Order::whereKey($order->id)->lockForUpdate()->first();
                    if (! $o || ! in_array($o->status, ['paid', 'processing', 'failed_provider'], true)) {
                        return;
                    }
                    $from = $o->status;
                    $merged = array_merge($o->payload ?? [], [
                        'tokovoucher' => array_merge($o->payload['tokovoucher'] ?? [], [
                            'sn' => (string) ($res['sn'] ?? ''),
                            'trx_id' => (string) ($res['trx_id'] ?? ''),
                            'ref_id' => (string) ($res['ref_id'] ?? $o->order_id),
                            'message' => (string) ($res['message'] ?? ''),
                            'produk' => $res['produk'] ?? null,
                            'via_status_api' => true,
                        ]),
                    ]);
                    $o->update(['status' => 'success', 'payload' => $merged]);
                    $o->logs()->create([
                        'status_from' => $from,
                        'status_to' => 'success',
                        'message' => 'TokoVoucher: sukses (sinkron API cek status).',
                        'payload' => ['trx_id' => $res['trx_id'] ?? null],
                    ]);
                });
                Log::info('Order diperbarui dari TokoVoucher cek status', ['order_id' => $order->order_id]);
            } catch (\Throwable $e) {
                Log::warning('TokovoucherOrderSync (sukses) gagal', [
                    'order_id' => $order->order_id,
                    'msg' => $e->getMessage(),
                ]);
            }

            return;
        }

        if ($parsed === 'gagal' && is_array($res)) {
            try {
                DB::transaction(function () use ($order, $res) {
                    $o = Order::whereKey($order->id)->lockForUpdate()->first();
                    if (! $o || ! in_array($o->status, ['paid', 'processing', 'failed_provider'], true)) {
                        return;
                    }
                    $from = $o->status;
                    $merged = array_merge($o->payload ?? [], [
                        'tokovoucher' => array_merge($o->payload['tokovoucher'] ?? [], [
                            'trx_id' => (string) ($res['trx_id'] ?? ''),
                            'status_api' => 'gagal',
                            'message' => (string) ($res['message'] ?? ''),
                        ]),
                    ]);
                    $o->update(['status' => 'failed', 'payload' => $merged]);
                    $o->logs()->create([
                        'status_from' => $from,
                        'status_to' => 'failed',
                        'message' => 'TokoVoucher: gagal (API cek status). '.(string) ($res['message'] ?? ''),
                        'payload' => ['trx_id' => $res['trx_id'] ?? null],
                    ]);
                });
            } catch (\Throwable $e) {
                Log::warning('TokovoucherOrderSync (gagal) error', [
                    'order_id' => $order->order_id,
                    'msg' => $e->getMessage(),
                ]);
            }

            return;
        }

        if (! $ignoreThrottle) {
            Cache::put($throttleKey, 1, 12);
        }
    }
}
