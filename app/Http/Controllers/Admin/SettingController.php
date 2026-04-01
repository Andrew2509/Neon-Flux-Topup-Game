<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    private function resizeImage($file, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) return null;

        $mime = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Create image from file
        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($file->getRealPath()); break;
            case 'image/png':  $src = imagecreatefrompng($file->getRealPath());  break;
            case 'image/webp': $src = imagecreatefromwebp($file->getRealPath()); break;
            default: return null;
        }

        if (!$src) return null;

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        if ($ratio < 1) {
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);

            $dst = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG/WebP
            if ($mime == 'image/png' || $mime == 'image/webp') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $src = $dst;
        }

        // Output to buffer
        ob_start();
        switch ($mime) {
            case 'image/jpeg': imagejpeg($src, null, 80); break;
            case 'image/png':  imagepng($src, null, 6); break; // PNG quality 0-9
            case 'image/webp': imagewebp($src, null, 80); break;
        }
        $data = ob_get_clean();
        imagedestroy($src);

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', 'site_logo', 'site_favicon']);
        Log::info('Settings Update Attempt:', $inputs);

        // Detect if margin or fee settings were updated
        $marginKeys = ['margin_public', 'margin_reseller', 'transaction_fee'];
        $shouldRecalculate = false;
        foreach ($marginKeys as $mKey) {
            if ($request->has($mKey)) {
                $shouldRecalculate = true;
                break;
            }
        }

        foreach ($inputs as $key => $value) {
            // Ensure value is not null to avoid DB integrity constraint violation
            $safeValue = $value ?? '';
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $safeValue]);
            Cache::forget('setting_' . $key); // Clear specific key cache
        }
        
        // Trigger price recalculation and fee sync if margins or fees changed
        if ($shouldRecalculate) {
            try {
                \App\Models\Service::recalculateAllPrices();
                
                // Also update all payment methods with the new transaction_fee
                if ($request->has('transaction_fee')) {
                    $newFee = (float)str_replace(',', '', $request->transaction_fee);
                    \App\Models\PaymentMethod::where('status', 'Aktif')->update(['fee' => $newFee]);
                    Log::info('Payment Method Fees synced from Settings Update');
                }
                
                Log::info('Automatic Price Recalculation Triggered from Settings Update');
            } catch (\Exception $e) {
                Log::error('Settings Sync Error: ' . $e->getMessage());
            }
        }

        // Handle File Uploads (Base64 Database Storage with Compression)
        try {
            if ($request->hasFile('site_logo')) {
                $logoBase64 = $this->resizeImage($request->file('site_logo'), 800, 200);
                if ($logoBase64) {
                    SiteSetting::updateOrCreate(['key' => 'site_logo'], ['value' => $logoBase64]);
                    Cache::forget('setting_site_logo');
                }
            }

            if ($request->hasFile('site_favicon')) {
                $faviconBase64 = $this->resizeImage($request->file('site_favicon'), 64, 64);
                if ($faviconBase64) {
                    SiteSetting::updateOrCreate(['key' => 'site_favicon'], ['value' => $faviconBase64]);
                    Cache::forget('setting_site_favicon');
                }
            }
        } catch (\Exception $e) {
            Log::error('Settings Upload Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses gambar: ' . $e->getMessage());
        }

        // Clear everything just in case
        Cache::flush();

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
