<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSupplierOrder;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%$search%")
                  ->orWhere('product_name', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();
        
        return view('admin.orders', compact('orders'));
    }

    /**
     * Tombol admin «Kirim Diamond»: memanggil API TokoVoucher (/v1/transaksi) untuk pesanan paid.
     * Jika status sudah success tanpa klik, artinya ProcessSupplierOrder otomatis dari antrian sudah jalan.
     */
    public function fulfillTokovoucher(Request $request, Order $order)
    {
        if ($order->status !== 'paid') {
            $msg = 'Kirim Diamond hanya untuk status Paid. Status saat ini: '.$order->status.'.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        try {
            ProcessSupplierOrder::dispatchSync($order);
            $order->refresh();

            if ($order->status === 'success') {
                $message = 'Berhasil: diamond/top-up terkirim lewat TokoVoucher untuk '.$order->order_id.'.';
            } else {
                $message = 'Permintaan selesai tetapi status masih '.$order->status.'. Cek log atau coba lagi.';
            }

            return $request->expectsJson()
                ? response()->json([
                    'success' => true,
                    'message' => $message,
                    'order_status' => $order->status,
                ])
                : back()->with('success', $message);
        } catch (\Throwable $e) {
            $order->refresh();
            $message = 'Gagal memanggil TokoVoucher: '.$e->getMessage();

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                    'order_status' => $order->status,
                ], 500)
                : back()->with('error', $message);
        }
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id'
        ]);

        Order::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Pesanan terpilih berhasil dihapus.');
    }

    public function destroyAll()
    {
        Order::query()->delete();
        return back()->with('success', 'Semua data pesanan berhasil dikosongkan.');
    }
}
