<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Deposit;
use App\Services\TokovoucherService;
use Illuminate\Support\Facades\Cache;
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

            // Detail Counts for new cards
            'count_success' => Order::where('status', 'success')->count(),
            'count_pending' => Order::where('status', 'pending')->count(),
            'count_failed' => Order::where('status', 'failed')->count(),

            // Period Stats
            'today_orders' => Order::where('status', 'success')->whereDate('created_at', now())->count(),
            'today_revenue' => Order::where('status', 'success')->whereDate('created_at', now())->sum('total_price'),
            
            'yesterday_orders' => Order::where('status', 'success')->whereDate('created_at', now()->subDay())->count(),
            'yesterday_revenue' => Order::where('status', 'success')->whereDate('created_at', now()->subDay())->sum('total_price'),

            'this_month_orders' => Order::where('status', 'success')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count(),
            'this_month_revenue' => Order::where('status', 'success')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_price'),

            'last_month_orders' => Order::where('status', 'success')->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->count(),
            'last_month_revenue' => Order::where('status', 'success')->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->sum('total_price'),
        ];

        // Calculate Growth Percentages
        $calcGrowth = function ($current, $previous) {
            if ($previous <= 0) return $current > 0 ? 100 : 0;
            return (($current - $previous) / $previous) * 100;
        };

        $stats['growth_today'] = $calcGrowth($stats['today_orders'], $stats['yesterday_orders']);
        $stats['growth_month'] = $calcGrowth($stats['this_month_orders'], $stats['last_month_orders']);

        // Get Tokovoucher Balance (Cached for 5 minutes)
        $tokovoucher_balance = Cache::remember('tokovoucher_balance', 300, function () {
            try {
                return (new TokovoucherService())->checkBalance();
            } catch (\Exception $e) {
                return null;
            }
        });

        $recent_orders = Order::with('user')->latest()->limit(5)->get();

        // Calculate Top Providers (grouped by product name prefix or simple logic)
        $top_providers = Order::select('product_name', DB::raw('count(*) as count'))
            ->groupBy('product_name')
            ->orderBy('count', 'desc')
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_providers', 'tokovoucher_balance'));
    }
}
