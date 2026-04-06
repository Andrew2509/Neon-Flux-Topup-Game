<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    private function deviceType()
    {
        $userAgent = request()->header('User-Agent') ?: '';
        
        if (request()->has('force_device')) {
            $force = request('force_device');
            if ($force === 'clear' || $force === 'reset') {
                session()->forget('forced_device');
            } else {
                session(['forced_device' => $force]);
            }
        }
        
        if (session()->has('forced_device')) {
            return session('forced_device');
        }

        $isDesktopOS = (bool) preg_match('/windows|macintosh|linux(?!.*android)/i', $userAgent);
        $isPriorityTablet = (bool) preg_match('/huawei|harmony|mediapad|matepad|mate\s*pad|honor|pad|playbook|silk|kindle|pppleweb|tablet|ipad|AGS[3-9]|BAH[3-9]|KOB[3-9]|edga\/144/i', $userAgent);
        
        $isAndroid = (bool) preg_match('/android/i', $userAgent);
        $hasMobile = (bool) preg_match('/mobile/i', $userAgent);
        
        if ($isPriorityTablet || ($isAndroid && !$hasMobile)) {
            return 'tablet';
        }

        $result = 'desktop';
        if (!$isDesktopOS) {
            $isMobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
            if ($isMobile) {
                $result = 'hp';
            }
        }

        return $result;
    }

    public function syaratKetentuan()
    {
        $view = $this->deviceType() . '.syarat-ketentuan';
        
        // Fallback for tablet/desktop if not exists
        if (!view()->exists($view)) {
            $view = 'hp.syarat-ketentuan';
        }

        return view($view);
    }

    public function kebijakanPrivasi()
    {
        $view = $this->deviceType() . '.kebijakan-privasi';
        
        // Fallback for tablet/desktop if not exists
        if (!view()->exists($view)) {
            $view = 'hp.kebijakan-privasi';
        }

        return view($view);
    }

    public function kebijakanRefund()
    {
        $view = $this->deviceType() . '.kebijakan-refund';

        if (!view()->exists($view)) {
            $view = 'hp.kebijakan-refund';
        }

        return view($view);
    }

    public function faq()
    {
        $view = $this->deviceType() . '.faq';

        if (!view()->exists($view)) {
            $view = 'hp.faq';
        }

        return view($view);
    }

    public function caraOrder()
    {
        $view = $this->deviceType() . '.cara-order';
        
        // Fallback for tablet/desktop if not exists
        if (!view()->exists($view)) {
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

        $view = $this->deviceType() . '.leaderboard';
        
        // Fallback for tablet/desktop if not exists
        if (!view()->exists($view)) {
            $view = 'hp.leaderboard';
        }

        return view($view, compact('topSpenders'));
    }
}
