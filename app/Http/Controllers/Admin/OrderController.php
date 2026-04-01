<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
