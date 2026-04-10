<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Rating;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function deviceType()
    {
        $userAgent = request()->header('User-Agent') ?: '';

        // 1. Session Factor (Allow user to force via URL ?force_device=tablet)
        if (request()->has('force_device')) {
            $force = request('force_device');
            if ($force === 'clear' || $force === 'reset') {
                session()->forget('forced_device');
            } else {
                session(['forced_device' => $force]);
            }
        }

        if (session()->has('forced_device')) {
            $forced = session('forced_device');
            view()->share('debugUA', $userAgent);
            view()->share('debugDevice', $forced.' (forced)');

            return $forced;
        }

        view()->share('debugUA', $userAgent);

        if (config('neonflux.unified_desktop_views', true)) {
            view()->share('debugDevice', 'desktop (unified)');

            return 'desktop';
        }

        // 2. Is this a known Desktop OS? (High confidence skip for Mobile/Tablet checks)
        // If it's Windows, Mac (not iPad), or Linux (not Android), it's Desktop.
        $isDesktopOS = (bool) preg_match('/windows|macintosh|linux(?!.*android)/i', $userAgent);

        // 3. Tablet Check (Huawei, iPad, Pro-specific UAs)
        $isPriorityTablet = (bool) preg_match('/huawei|harmony|mediapad|matepad|mate\s*pad|honor|pad|playbook|silk|kindle|pppleweb|tablet|ipad|AGS[3-9]|BAH[3-9]|KOB[3-9]|edga\/144/i', $userAgent);

        $isAndroid = (bool) preg_match('/android/i', $userAgent);
        $hasMobile = (bool) preg_match('/mobile/i', $userAgent);

        // 4. Tablet conditions
        if ($isPriorityTablet || ($isAndroid && ! $hasMobile)) {
            view()->share('debugDevice', 'tablet');

            return 'tablet';
        }

        // 5. Generic Mobile Patterns - Only if NOT Desktop OS
        $result = 'desktop'; // Default
        if (! $isDesktopOS) {
            $isMobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
            if ($isMobile) {
                $result = 'hp';
            }
        }

        view()->share('debugDevice', $result);

        return $result;
    }

    public function index()
    {
        $popular = Category::where('status', 'Aktif')
            ->where(function ($q) {
                $q->whereDoesntHave('providerCategory')
                    ->orWhereHas('providerCategory', function ($q) {
                        $q->where('status', 'Aktif');
                    });
            })
            ->whereHas('services', function ($q) {
                $q->where('status', 'Aktif');
            })
            ->withCount(['services' => function ($q) {
                $q->where('status', 'Aktif');
            }])
            ->orderBy('services_count', 'desc')
            ->take(8)
            ->get();

        $categories = Category::where('status', 'Aktif')
            ->where(function ($q) {
                $q->whereDoesntHave('providerCategory')
                    ->orWhereHas('providerCategory', function ($q) {
                        $q->where('status', 'Aktif');
                    });
            })
            ->whereHas('services', function ($q) {
                $q->where('status', 'Aktif');
            })
            ->orderBy('name', 'asc')
            ->get();

        // 1. Define UI Groups for Tabs
        $groupMaps = [
            'topup' => ['types' => ['Topup Game', 'Topup Game (Global)'], 'name' => 'Top Up Games', 'icon' => 'videogame_asset'],
            'joki' => ['types' => ['Joki'], 'name' => 'Joki MLBB', 'icon' => 'military_tech'],
            'voucher' => ['types' => ['Voucher Game', 'Voucher Data'], 'name' => 'Voucher', 'icon' => 'confirmation_number'],
            'pulsa' => ['types' => ['Pulsa', 'Paket Data', 'Telpon & SMS', 'Pulsa Transfer'], 'name' => 'Pulsa & Data', 'icon' => 'signal_cellular_alt'],
            'streaming' => ['types' => ['Hiburan', 'TV', 'Lainnya'], 'name' => 'Hiburan', 'icon' => 'live_tv'],
        ];

        // 2. Determine which groups have active categories
        $activeGroups = [];
        $categoryTypes = $categories->pluck('type')->unique()->toArray();
        $categoryNames = $categories->pluck('name')->toArray();

        foreach ($groupMaps as $key => $config) {
            $hasItems = false;
            // Check by type
            foreach ($config['types'] as $t) {
                if (in_array($t, $categoryTypes)) {
                    $hasItems = true;
                    break;
                }
            }
            // Special check for Joki in name if type mismatch
            if ($key === 'joki' && ! $hasItems) {
                foreach ($categoryNames as $n) {
                    if (stripos($n, 'Joki') !== false) {
                        $hasItems = true;
                        break;
                    }
                }
            }

            if ($hasItems) {
                $activeGroups[$key] = $config;
            }
        }

        $totalCategories = Category::where('status', 'Aktif')->count();

        // 3. Fetch Sliders
        $sliders = \App\Models\Slider::where('status', 'Aktif')->latest()->get();

        $testimonials = Rating::query()
            ->where('is_visible', true)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->with('user')
            ->latest()
            ->limit(40)
            ->get();

        $view = $this->deviceType().'.neonflux.topup';

        return view($view, compact('popular', 'categories', 'totalCategories', 'activeGroups', 'sliders', 'testimonials'));
    }


    public function showTopup($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('status', 'Aktif')
            ->where(function ($q) {
                $q->whereDoesntHave('providerCategory')
                    ->orWhereHas('providerCategory', function ($q) {
                        $q->where('status', 'Aktif');
                    });
            })
            ->firstOrFail();

        $services = \App\Models\Service::where('category_id', $category->id)
            ->where('status', 'Aktif')
            ->where(function ($q) {
                $q->whereNull('product_jenis_id')
                    ->orWhereHas('productJenis', function ($q) {
                        $q->where('status', 'Aktif');
                    });
            })
            ->orderBy('price', 'asc')
            ->get();

        // [Filter Tokovoucher Balance]
        // Menghindari user membeli produk yang melebihi batas aman saldo provider.
        $tokovoucherProvider = \App\Models\Provider::forTokovoucher();
        if ($tokovoucherProvider && $tokovoucherProvider->status === 'Aktif') {
            $limit = $tokovoucherProvider->getSafeNominalLimit();
            $services = $services->filter(function ($service) use ($limit) {
                // Hanya filter layanan yang berasal dari Tokovoucher
                if (str_contains(strtolower($service->provider), 'tokovoucher')) {
                    return $service->price <= $limit;
                }
                return true;
            });
        }

        $activeJenis = \App\Models\ProductJenis::where('category_id', $category->id)
            ->where('status', 'Aktif')
            ->orderBy('id', 'asc')
            ->get();

        $paymentMethods = \App\Models\PaymentMethod::where('status', 'Aktif')->get();
        // Group payments by type (e.g., E-Wallet, Pulsa, VA)
        $groupedPayments = $paymentMethods->groupBy('type');

        // Use specific view based on device type
        $viewFolder = $this->deviceType();
        $viewPath = "{$viewFolder}.neonflux.topupgame.{$slug}";

        if (! view()->exists($viewPath)) {
            $viewPath = "{$viewFolder}.neonflux.topupgame.generic";
        }

        return view($viewPath, compact('category', 'services', 'groupedPayments', 'activeJenis'));
    }
}
