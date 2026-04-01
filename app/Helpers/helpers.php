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
