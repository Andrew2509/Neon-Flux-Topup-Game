<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales' => Order::where('status', 'success')->sum('total_price'),
            'active_users' => User::where('role', 'member')->where('status', 'active')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_deposits' => Deposit::where('status', 'success')->sum('amount'),
        ];

        $recent_orders = Order::with('user')->latest()->limit(5)->get();

        // Calculate Top Providers (grouped by product name prefix or simple logic)
        $top_providers = Order::select('product_name', DB::raw('count(*) as count'))
            ->groupBy('product_name')
            ->orderBy('count', 'desc')
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_providers'));
    }
}
