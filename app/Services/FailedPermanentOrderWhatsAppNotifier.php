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

    /**
     * User ID tampilan (bukan gabungan User+Zone). Pakai payload game_user_id bila ada;
     * untuk order lama: jika tujuan berakhiran server_id, kita pisahkan.
     */
    protected function resolveGameUserIdForMessage(array $payload): string
    {
        $raw = trim((string) ($payload['game_user_id'] ?? ''));
        if ($raw !== '') {
            return $raw;
        }

        $tujuan = (string) ($payload['tujuan'] ?? '');
        $zone = trim((string) ($payload['server_id'] ?? ''));
        if ($tujuan === '' || $zone === '') {
            return $tujuan;
        }

        return str_ends_with($tujuan, $zone)
            ? substr($tujuan, 0, -strlen($zone))
            : $tujuan;
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
        $serverId = trim((string) ($payload['server_id'] ?? ''));
        $playerNick = trim((string) ($payload['player_nickname'] ?? ''));
        $gameUserId = $this->resolveGameUserIdForMessage($payload);

        $barisUser = $gameUserId !== ''
            ? "• *User ID:* {$gameUserId}\n"
            : "• *User ID:* (tidak tercatat — mohon kirim lengkap)\n";

        $barisZone = $serverId !== ''
            ? "• *Zone / Server ID:* {$serverId}\n"
            : '';

        $namaBaris = $playerNick !== ''
            ? "• *Nama pemain (saat cek ID):* {$playerNick}\n"
            : '';

        $pesanTeknis = $technicalSummary
            ? "\n_Ringkas sistem: ".str_replace(["\n", "\r"], ' ', \Illuminate\Support\Str::limit($technicalSummary, 200))."_\n"
            : '';

        return <<<TXT
Halo 👋

Kami dari *{$site}*.

Pesanan *{$order->order_id}* (*{$productLabel}*) *tidak dapat diproses otomatis* ke supplier setelah beberapa percobaan. Biasanya karena *User ID / Zone (Server) ID* tidak cocok dengan game yang dipesan.

*Data akun yang tercatat saat ini:*
{$barisUser}{$barisZone}{$namaBaris}Jika ada kesalahan, mohon *balas pesan ini* dengan *User ID* dan *Zone / Server ID* yang benar (untuk game yang memakai Zone).

Setelah data benar kami terima di chat ini, mohon tunggu *sekitar 5 menit* untuk kami *proses ulang* pengiriman.

🌐 Lacak pesanan: {$trackUrl}
{$domain}{$pesanTeknis}
Jika sudah lunas, tim kami akan membantu menyelesaikan. Terima kasih atas pengertiannya 🙏
TXT;
    }
}
