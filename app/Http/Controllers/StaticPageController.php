<?php

namespace App\Http\Controllers;

class StaticPageController extends Controller
{
    private function deviceType(): string
    {
        return app(CatalogController::class)->deviceType();
    }

    public function syaratKetentuan()
    {
        $view = $this->deviceType().'.syarat-ketentuan';

        // Fallback for tablet/desktop if not exists
        if (! view()->exists($view)) {
            $view = 'hp.syarat-ketentuan';
        }

        return view($view);
    }

    public function kebijakanPrivasi()
    {
        $view = $this->deviceType().'.kebijakan-privasi';

        // Fallback for tablet/desktop if not exists
        if (! view()->exists($view)) {
            $view = 'hp.kebijakan-privasi';
        }

        return view($view);
    }

    public function kebijakanRefund()
    {
        $view = $this->deviceType().'.kebijakan-refund';

        if (! view()->exists($view)) {
            $view = 'hp.kebijakan-refund';
        }

        return view($view);
    }

    public function faq()
    {
        $view = $this->deviceType().'.faq';

        if (! view()->exists($view)) {
            $view = 'hp.faq';
        }

        return view($view);
    }

    public function caraOrder()
    {
        $view = $this->deviceType().'.cara-order';

        // Fallback for tablet/desktop if not exists
        if (! view()->exists($view)) {
            $view = 'hp.cara-order';
        }

        return view($view);
    }

    public function leaderboard()
    {
        $topSpenders = \App\Models\Order::where('status', 'success')
            ->whereNotNull('user_id')
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('SUM(total_price) as total_spent'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as total_orders'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->take(10)
            ->with('user')
            ->get();

        $view = $this->deviceType().'.leaderboard';

        // Fallback for tablet/desktop if not exists
        if (! view()->exists($view)) {
            $view = 'hp.leaderboard';
        }

        return view($view, compact('topSpenders'));
    }
}
