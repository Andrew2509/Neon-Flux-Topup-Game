<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class FailedPermanentOrderWhatsAppNotifier
{
    /**
     * Kirim WA ke pelanggan saat order gagal permanen (supplier), minta konfirmasi ID game.
     */
    public function notify(Order $order, ?string $technicalSummary = null): void
    {
        $order->refresh();
        $payload = $order->payload ?? [];

        if (! empty($payload['failed_permanent_wa_sent_at'])) {
            return;
        }

        $waDigits = $this->resolveWhatsAppNumber($order, $payload);
        if ($waDigits === null || $waDigits === '') {
            Log::warning('Failed permanent: tidak ada nomor WA pelanggan untuk notifikasi', [
                'order_id' => $order->order_id,
            ]);
            return;
        }

        $wa = app(WhatsAppService::class);
        if (! $wa->isConfigured()) {
            Log::warning('Failed permanent: ORBIT_WA / provider WA belum dikonfigurasi, skip kirim pesan', [
                'order_id' => $order->order_id,
            ]);
            return;
        }

        $message = $this->buildMessage($order, $payload, $technicalSummary);

        $result = $wa->sendMessage($waDigits, $message);

        if (! empty($result['success'])) {
            $payload['failed_permanent_wa_sent_at'] = now()->toIso8601String();
            $order->update(['payload' => $payload]);
            Log::info('Failed permanent: WA terkirim ke pelanggan', ['order_id' => $order->order_id]);
        } else {
            Log::error('Failed permanent: gagal kirim WA', [
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

    protected function buildMessage(Order $order, array $payload, ?string $technicalSummary): string
    {
        $site = function_exists('get_setting') ? get_setting('site_name', config('app.name', 'Toko')) : config('app.name', 'Toko');
        $domain = parse_url(config('app.url', ''), PHP_URL_HOST) ?: (string) config('app.url');

        try {
            $root = (string) config('app.url');
            if ($root !== '') {
                URL::forceRootUrl($root);
            }
            $trackUrl = route('track.order', ['order_id' => $order->order_id], true);
        } catch (\Throwable) {
            $trackUrl = rtrim((string) config('app.url'), '/').'/track?order_id='.urlencode((string) $order->order_id);
        }

        $productLabel = $order->product_name ?? 'Produk';
        $tujuan = $payload['tujuan'] ?? '';
        $serverId = $payload['server_id'] ?? '';

        $idBaris = $tujuan !== ''
            ? "• *ID / tujuan saat ini:* {$tujuan}".($serverId !== '' ? " (Zone/Server: {$serverId})" : '')
            : '• *ID / tujuan saat ini:* (tidak tercatat — mohon kirim lengkap)';

        $pesanTeknis = $technicalSummary
            ? "\n_Ringkas sistem: ".str_replace(["\n", "\r"], ' ', \Illuminate\Support\Str::limit($technicalSummary, 200))."_\n"
            : '';

        return <<<TXT
Halo 👋

Kami dari *{$site}*.

Pesanan *{$order->order_id}* (*{$productLabel}*) *tidak dapat diproses otomatis* ke supplier setelah beberapa percobaan. Biasanya karena *User ID / Zone (Server) ID* tidak cocok dengan game yang dipesan.

Mohon *balas pesan ini* dengan data akun yang *benar* sesuai game Anda, contoh:
{$idBaris}
• User ID / ID pemain (dan Zone ID jika game membutuhkan)

Setelah data benar kami terima di chat ini, mohon tunggu *sekitar 5 menit* untuk kami *proses ulang* pengiriman.

🌐 Lacak pesanan: {$trackUrl}
{$domain}{$pesanTeknis}
Jika sudah lunas, tim kami akan membantu menyelesaikan. Terima kasih atas pengertiannya 🙏
TXT;
    }
}
