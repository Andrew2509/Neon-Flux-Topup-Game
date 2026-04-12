<?php

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        try {
            return Cache::remember('setting_' . $key, 86400, function () use ($key, $default) {
                $setting = SiteSetting::where('key', $key)->first();
                return ($setting && !empty($setting->value)) ? $setting->value : $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('get_image_url')) {
    function get_image_url($key, $default_asset = null)
    {
        $value = get_setting($key);
        if (!$value) {
            return $default_asset ? asset($default_asset) : null;
        }

        // Check if it's base64 data
        if (str_starts_with($value, 'data:')) {
            return $value;
        }

        return asset($value);
    }
}

if (!function_exists('is_mobile_neonflux')) {
    function is_mobile_neonflux()
    {
        $userAgent = request()->header('User-Agent') ?: '';
        
        // Match logic from CatalogController::deviceType
        $isDesktopOS = (bool) preg_match('/windows|macintosh|linux(?!.*android)/i', $userAgent);
        $isPriorityTablet = (bool) preg_match('/huawei|harmony|mediapad|matepad|mate\s*pad|honor|pad|playbook|silk|kindle|pppleweb|tablet|ipad|AGS[3-9]|BAH[3-9]|KOB[3-9]|edga\/144/i', $userAgent);
        $isAndroid = (bool) preg_match('/android/i', $userAgent);
        $hasMobile = (bool) preg_match('/mobile/i', $userAgent);

        if ($isPriorityTablet || ($isAndroid && ! $hasMobile)) {
            return false; // Tablet categorized as desktop-like for the sake of 'hp' check
        }

        if (! $isDesktopOS) {
            return (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
        }

        return false;
    }
}
