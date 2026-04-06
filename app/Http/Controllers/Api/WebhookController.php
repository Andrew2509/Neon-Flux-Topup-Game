<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Provider;
use App\Services\TokovoucherPostPaymentRelay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook TokoVoucher — POST (JSON) sesuai https://docs.tokovoucher.net/webhook/post
     * atau GET (report IP/Otomax) sesuai https://docs.tokovoucher.net/webhook/get
     *
     * URL mitra: https://www.neonflux.my.id/api/tokovoucher/webhook
     * Header POST: X-TokoVoucher-Authorization = md5(MEMBER_CODE:SECRET:REF_ID)
     * Whitelist IP sumber (disarankan): 188.166.243.56 — set TOKOVOUCHER_WEBHOOK_VERIFY_IP=true bila perlu.
     */
    public function tokovoucher(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->tokovoucherReportGet($request);
        }

        return $this->tokovoucherPost($request);
    }

    private function tokovoucherPost(Request $request)
    {
        if ($request->header('X-Neonflux-Relay') === '1') {
            return $this->handlePostPaymentRelay($request);
        }

        if ($this->shouldRejectTokovoucherIp($request)) {
            Log::warning('TokoVoucher Webhook POST: IP ditolak', ['ip' => $request->ip()]);

            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        Log::info('TokoVoucher Webhook POST', [
            'headers' => [
                'X-TokoVoucher-Authorization' => $request->hasHeader('X-TokoVoucher-Authorization') ? '(present)' : null,
            ],
            'body' => $request->all(),
        ]);

        $refId = $request->input('ref_id');
        if ($refId === null || $refId === '') {
            return response()->json(['status' => 'error', 'message' => 'ref_id required'], 400);
        }

        $refId = (string) $refId;
        $statusRaw = strtolower(trim((string) $request->input('status', '')));
        if ($statusRaw === '') {
            return response()->json(['status' => 'error', 'message' => 'status required'], 400);
        }
        $sn = $request->input('sn', '');

        $provider = $this->resolveTokovoucherProvider();
        if (! $provider || ! $provider->provider_id || ! $provider->api_key) {
            return response()->json(['status' => 'error', 'message' => 'Provider TokoVoucher tidak dikonfigurasi'], 400);
        }

        $memberCode = (string) $provider->provider_id;
        $secret = (string) $provider->api_key;

        $signature = $request->header('X-TokoVoucher-Authorization', '')
            ?: $request->input('signature', '');
        $signature = is_string($signature) ? trim($signature) : '';
        $expectedSignature = md5($memberCode.':'.$secret.':'.$refId);

        if ($signature === '' || ! hash_equals($expectedSignature, $signature)) {
            Log::warning('TokoVoucher Webhook: signature tidak valid', [
                'had_header' => $request->hasHeader('X-TokoVoucher-Authorization'),
                'ref_id' => $refId,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        if (str_starts_with($refId, 'DEP-')) {
            return $this->handleDepositCallback($refId, $statusRaw, $request->all(), false);
        }

        $order = Order::where('order_id', $refId)->first();
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $trxId = (string) $request->input('trx_id', '');

        if ($statusRaw === 'sukses') {
            if ($order->status === 'success') {
                $existing = (string) data_get($order->payload, 'tokovoucher.trx_id', '');
                if ($trxId !== '' && $existing !== '' && hash_equals($existing, $trxId)) {
                    return response()->json(['status' => 'success', 'message' => 'already_processed']);
                }

                return response()->json(['status' => 'success', 'message' => 'already_success']);
            }

            $from = $order->status;
            $merged = $this->mergeTokovoucherPayload($order, $request, $refId, (string) $sn, $trxId);

            $order->update([
                'status' => 'success',
                'payload' => $merged,
            ]);

            $order->logs()->create([
                'status_from' => $from,
                'status_to' => 'success',
                'message' => 'TokoVoucher (webhook): transaksi sukses, SN diterima.',
                'payload' => [
                    'trx_id' => $trxId,
                    'produk' => $request->input('produk'),
                ],
            ]);
        } elseif ($statusRaw === 'gagal') {
            if ($order->status === 'failed') {
                return response()->json(['status' => 'success', 'message' => 'already_processed']);
            }

            $from = $order->status;
            $merged = array_merge($order->payload ?? [], [
                'tokovoucher' => array_merge($order->payload['tokovoucher'] ?? [], [
                    'ref_id' => $refId,
                    'trx_id' => $trxId,
                    'webhook_status' => 'gagal',
                    'webhook_message' => $request->input('message'),
                ]),
                'tokovoucher_webhook' => $request->all(),
            ]);

            $order->update([
                'status' => 'failed',
                'payload' => $merged,
            ]);

            $order->logs()->create([
                'status_from' => $from,
                'status_to' => 'failed',
                'message' => 'TokoVoucher (webhook): transaksi gagal — '.(string) $request->input('message', ''),
                'payload' => $request->only(['message', 'trx_id', 'produk']),
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'status tidak dikenal'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Relay internal setelah gateway bayar: HMAC, tanpa signature TokoVoucher.
     * Memicu cek status API + retry ProcessSupplierOrder (lihat TokovoucherPostPaymentRelay).
     */
    private function handlePostPaymentRelay(Request $request)
    {
        $secret = (string) config('services.tokovoucher.internal_relay_secret', '');
        if ($secret === '' || strlen($secret) < 16) {
            return response()->json(['status' => 'error', 'message' => 'Relay disabled'], 403);
        }

        $ts = $request->header('X-Neonflux-Timestamp', '');
        $sig = $request->header('X-Neonflux-Signature', '');
        if (! is_string($ts) || ! is_string($sig) || $ts === '' || $sig === '') {
            return response()->json(['status' => 'error', 'message' => 'Relay auth required'], 403);
        }

        if (! ctype_digit($ts) || abs(time() - (int) $ts) > 300) {
            return response()->json(['status' => 'error', 'message' => 'Relay timestamp invalid'], 403);
        }

        $refId = $request->input('ref_id');
        if ($refId === null || $refId === '') {
            return response()->json(['status' => 'error', 'message' => 'ref_id required'], 400);
        }
        $refId = (string) $refId;

        $expected = hash_hmac('sha256', $refId.'|'.$ts, $secret);
        if (! hash_equals($expected, $sig)) {
            Log::warning('TokoVoucher relay: signature tidak valid', ['ref_id' => $refId]);

            return response()->json(['status' => 'error', 'message' => 'Invalid relay signature'], 403);
        }

        $order = Order::where('order_id', $refId)->first();
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        TokovoucherPostPaymentRelay::run($order);

        return response()->json(['status' => 'success', 'message' => 'relay_ok'], 200);
    }

    /**
     * Report GET (Via IP / Otomax): clientid = ref_id, statuscode 1=sukses, 2=gagal.
     */
    private function tokovoucherReportGet(Request $request)
    {
        if ($this->shouldRejectTokovoucherIp($request)) {
            Log::warning('TokoVoucher Webhook GET: IP ditolak', ['ip' => $request->ip()]);

            return response('Forbidden', 403);
        }

        Log::info('TokoVoucher Webhook GET (report)', $request->query());

        $refId = (string) $request->query('clientid', '');
        if ($refId === '') {
            return response('Bad Request', 400);
        }

        $statusCode = (int) $request->query('statuscode', 0);
        $sn = (string) $request->query('sn', '');

        if (str_starts_with($refId, 'DEP-')) {
            $statusText = $statusCode === 1 ? 'sukses' : 'gagal';

            return $this->handleDepositCallback($refId, $statusText, [
                'ref_id' => $refId,
                'status' => $statusText,
                'sn' => $sn,
                'trx_id' => (string) $request->query('serverid', ''),
                'message' => (string) $request->query('msg', ''),
                'produk' => (string) $request->query('kp', ''),
                '_source' => 'get_report',
            ], true);
        }

        $order = Order::where('order_id', $refId)->first();
        if (! $order) {
            return response('Not Found', 404);
        }

        $trxId = (string) $request->query('serverid', '');

        if ($statusCode === 1) {
            if ($order->status === 'success') {
                return response('OK', 200);
            }

            $from = $order->status;
            $fakeRequest = new Request([
                'ref_id' => $refId,
                'trx_id' => $trxId,
                'produk' => $request->query('kp'),
                'message' => $request->query('msg'),
            ]);
            $merged = $this->mergeTokovoucherPayload($order, $fakeRequest, $refId, $sn, $trxId);
            $merged['tokovoucher_report_get'] = $request->query();

            $order->update([
                'status' => 'success',
                'payload' => $merged,
            ]);

            $order->logs()->create([
                'status_from' => $from,
                'status_to' => 'success',
                'message' => 'TokoVoucher (report GET): transaksi sukses.',
                'payload' => ['serverid' => $trxId, 'clientid' => $refId],
            ]);
        } elseif ($statusCode === 2) {
            if ($order->status === 'failed') {
                return response('OK', 200);
            }

            $from = $order->status;
            $merged = array_merge($order->payload ?? [], [
                'tokovoucher' => array_merge($order->payload['tokovoucher'] ?? [], [
                    'ref_id' => $refId,
                    'trx_id' => $trxId,
                    'webhook_status' => 'gagal',
                    'webhook_message' => $request->query('msg'),
                ]),
                'tokovoucher_report_get' => $request->query(),
            ]);

            $order->update([
                'status' => 'failed',
                'payload' => $merged,
            ]);

            $order->logs()->create([
                'status_from' => $from,
                'status_to' => 'failed',
                'message' => 'TokoVoucher (report GET): gagal — '.(string) $request->query('msg', ''),
            ]);
        }

        return response('OK', 200);
    }

    private function shouldRejectTokovoucherIp(Request $request): bool
    {
        if (! config('services.tokovoucher.webhook_verify_ip', false)) {
            return false;
        }

        $allowed = config('services.tokovoucher.webhook_allowed_ips', []);
        if ($allowed === [] || $allowed === null) {
            return false;
        }

        return ! in_array($request->ip(), $allowed, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function mergeTokovoucherPayload(Order $order, Request $request, string $refId, string $sn, string $trxId): array
    {
        return array_merge($order->payload ?? [], [
            'tokovoucher' => array_merge($order->payload['tokovoucher'] ?? [], [
                'sn' => $sn,
                'trx_id' => $trxId,
                'ref_id' => $refId,
                'produk' => $request->input('produk'),
                'webhook_status' => 'sukses',
            ]),
            'tokovoucher_webhook' => $request->all(),
        ]);
    }

    private function resolveTokovoucherProvider(): ?Provider
    {
        return Provider::where(function ($q) {
            $q->where('name', 'like', '%Toko%')
                ->orWhere('name', 'like', '%tokovoucher%');
        })->first();
    }

    private function handleDepositCallback(string $depositId, string $status, array $allData, bool $plainResponse = false)
    {
        $deposit = Deposit::where('deposit_id', $depositId)->first();
        if (! $deposit) {
            return $plainResponse
                ? response('Not Found', 404)
                : response()->json(['status' => 'error', 'message' => 'Deposit not found'], 404);
        }

        if ($deposit->status !== 'pending') {
            return $plainResponse
                ? response('OK', 200)
                : response()->json(['status' => 'success', 'message' => 'Already processed']);
        }

        if ($status === 'sukses') {
            DB::transaction(function () use ($deposit, $allData) {
                $deposit->update([
                    'status' => 'success',
                    'payload' => array_merge((array) $deposit->payload, ['webhook_data' => $allData]),
                ]);

                $user = $deposit->user;
                $user->increment('balance', $deposit->amount);

                Log::info("User {$user->id} balance updated via TokoVoucher webhook. New balance: {$user->balance}");
            });
        } elseif ($status === 'gagal') {
            $deposit->update([
                'status' => 'failed',
                'payload' => array_merge((array) $deposit->payload, ['webhook_data' => $allData]),
            ]);
        }

        return $plainResponse
            ? response('OK', 200)
            : response()->json(['status' => 'success'], 200);
    }
}
