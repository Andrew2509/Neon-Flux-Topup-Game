<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Satu kali kirim WA ke pelanggan saat top-up sukses (TokoVoucher selesai).
 */
class TopupSuccessWhatsAppNotifier
{
    public function notifyIfNeeded(Order $order): void
    {
        $order->refresh();
        $payload = $order->payload ?? [];

        if ($order->status !== 'success') {
            return;
        }

        if (! empty($payload['wa_topup_success_sent_at'])) {
            return;
        }

        $waDigits = $this->resolveWhatsAppNumber($order, $payload);
        if ($waDigits === null || strlen($waDigits) < 10) {
            return;
        }

        $wa = app(WhatsAppService::class);
        if (! $wa->isConfigured()) {
            return;
        }

        $message = $this->buildMessage($order, $payload);
        $result = $wa->sendMessage($waDigits, $message);

        if (! empty($result['success'])) {
            $payload['wa_topup_success_sent_at'] = now()->toIso8601String();
            $order->update(['payload' => $payload]);
            Log::info('Topup sukses: WA terkirim ke pelanggan', ['order_id' => $order->order_id]);
        } else {
            Log::warning('Topup sukses: gagal kirim WA', [
                'order_id' => $order->order_id,
                'error' => $result['error'] ?? 'unknown',
            ]);
        }
    }

    protected function resolveWhatsAppNumber(Order $order, array $payload): ?string
    {
        if (! empty($payload['customer_whatsapp'])) {
            return preg_replace('/\D/', '', (string) $payload['customer_whatsapp']);
        }

        $order->loadMissing('user');
        $user = $order->user;
        if ($user && ! empty($user->phone)) {
            return preg_replace('/\D/', '', (string) $user->phone);
        }

        return null;
    }

    protected function buildMessage(Order $order, array $payload): string
    {
        $site = function_exists('get_setting') ? get_setting('site_name', config('app.name', 'Neon Flux')) : config('app.name', 'Neon Flux');
        $sn = trim((string) data_get($payload, 'tokovoucher.sn', ''));
        $trxTv = trim((string) data_get($payload, 'tokovoucher.trx_id', ''));

        try {
            $root = (string) config('app.url');
            if ($root !== '') {
                URL::forceRootUrl($root);
            }
            $trackUrl = route('track.order', ['order_id' => $order->order_id], true);
        } catch (\Throwable) {
            $trackUrl = rtrim((string) config('app.url'), '/').'/track?order_id='.urlencode((string) $order->order_id);
        }

        $product = $order->product_name ?? 'Produk';
        $snLine = $sn !== '' ? "• *SN / Bukti:* {$sn}\n" : '';
        $trxLine = $trxTv !== '' ? "• *Trx TokoVoucher:* {$trxTv}\n" : '';

        return <<<TXT
🎉 *Pembayaran berhasil & top-up selesai!*

Halo, pesanan Anda di *{$site}* sudah diproses.

• *Order:* {$order->order_id}
• *Produk:* {$product}
{$snLine}{$trxLine}
🌐 Lacak kapan saja: {$trackUrl}

Terima kasih sudah bertransaksi! ✨
TXT;
    }
}
