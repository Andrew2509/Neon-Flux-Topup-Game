<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Provider;

class WebhookController extends Controller
{
    public function tokovoucher(Request $request)
    {
        // Log request for debugging
        Log::info('TokoVoucher Webhook Received:', $request->all());

        $memberCode = $request->input('member_code');
        $refId = $request->input('ref_id');
        $status = $request->input('status'); // 'sukses' or 'gagal'
        $sn = $request->input('sn');
        $signature = $request->input('signature');

        // 1. Get Provider Credentials
        $provider = \App\Models\Provider::where('name', 'LIKE', '%Toko%')->first();
        if (!$provider) {
            return response()->json(['status' => 'error', 'message' => 'Provider not found'], 400);
        }

        $secret = $provider->api_key;

        // 2. Validate Signature: md5(MEMBER_CODE:SECRET:REF_ID)
        $expectedSignature = md5($memberCode . ':' . $secret . ':' . $refId);

        if ($signature !== $expectedSignature) {
            Log::warning('TokoVoucher Webhook Invalid Signature:', [
                'expected' => $expectedSignature,
                'received' => $signature
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        // 3. Identify if it's a Deposit or an Order
        if (str_starts_with($refId, 'DEP-')) {
            return $this->handleDepositCallback($refId, $status, $request->all());
        }

        // 4. Find and Update Order
        $order = \App\Models\Order::where('order_id', $refId)->first();
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        if ($status === 'sukses') {
            $order->update([
                'status' => 'success',
                'payload' => array_merge((array)$order->payload, ['sn' => $sn, 'webhook_data' => $request->all()])
            ]);
        } else if ($status === 'gagal') {
            $order->update([
                'status' => 'failed',
                'payload' => array_merge((array)$order->payload, ['webhook_data' => $request->all()])
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleDepositCallback($depositId, $status, $allData)
    {
        $deposit = \App\Models\Deposit::where('deposit_id', $depositId)->first();
        if (!$deposit) {
            return response()->json(['status' => 'error', 'message' => 'Deposit not found'], 404);
        }

        if ($deposit->status !== 'pending') {
            return response()->json(['status' => 'success', 'message' => 'Already processed']);
        }

        if ($status === 'sukses') {
            \Illuminate\Support\Facades\DB::transaction(function () use ($deposit, $allData) {
                $deposit->update([
                    'status' => 'success',
                    'payload' => array_merge((array)$deposit->payload, ['webhook_data' => $allData])
                ]);

                // Increment User Balance
                $user = $deposit->user;
                $user->increment('balance', $deposit->amount);
                
                Log::info("User {$user->id} balance updated. New balance: {$user->balance}");
            });
        } else if ($status === 'gagal') {
            $deposit->update([
                'status' => 'failed',
                'payload' => array_merge((array)$deposit->payload, ['webhook_data' => $allData])
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
