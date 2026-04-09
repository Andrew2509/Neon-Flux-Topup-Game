<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Order;
use App\Models\Deposit;
use Carbon\Carbon;

class TransactionNotificationController extends Controller
{
    public function getRecent()
    {
        $orders = Order::with('user')
            ->where('status', 'success')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => 'ord-' . $order->order_id,
                    'user' => $this->getDisplayName($order),
                    'product' => $order->product_name,
                    'message' => 'Pembelian berhasil diproses',
                    'time' => $order->updated_at->diffForHumans(),
                    'timestamp' => $order->updated_at->timestamp
                ];
            });

        $deposits = Deposit::with('user')
            ->where('status', 'success')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($deposit) {
                return [
                    'id' => 'dep-' . $deposit->id,
                    'user' => $deposit->user ? $this->maskName($deposit->user->name) : 'Guest',
                    'product' => 'Top up Saldo Rp ' . number_format($deposit->amount, 0, ',', '.'),
                    'message' => 'Top up saldo berhasil',
                    'time' => $deposit->updated_at->diffForHumans(),
                    'timestamp' => $deposit->updated_at->timestamp
                ];
            });

        $merged = $orders->concat($deposits)
            ->sortByDesc('timestamp')
            ->values()
            ->take(10);

        return response()->json($merged);
    }

    private function getDisplayName($order)
    {
        if ($order->user) {
            return $this->maskName($order->user->name);
        }

        // Try to get from payload if guest
        $payload = is_array($order->payload) ? $order->payload : json_decode($order->payload ?? '[]', true);
        $nick = data_get($payload, 'player_nickname') ?: data_get($payload, 'user_id');
        
        if ($nick) {
            return $this->maskName($nick);
        }

        return 'Guest';
    }

    private function maskName($name)
    {
        $name = (string) $name;
        if (strlen($name) <= 2) {
            return $name . str_repeat('*', 3);
        }
        return substr($name, 0, 2) . str_repeat('*', 3);
    }
}
