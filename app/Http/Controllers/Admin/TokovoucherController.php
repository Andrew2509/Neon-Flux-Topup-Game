<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Jobs\SyncTokovoucherCategoryJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokovoucherController extends Controller
{
    public function categories()
    {
        $provider = Provider::where('name', 'like', '%Toko%')->first();
        
        if (!$provider) {
            return back()->with('error', 'Provider TokoVoucher belum dikonfigurasi.');
        }

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $signature = md5($memberCode . ":" . $secret);

        try {
            $response = Http::timeout(15)->get("https://api.tokovoucher.net/member/produk/category/list", [
                'member_code' => $memberCode,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['status']) && $result['status'] == 1) {
                    $apiCategories = $result['data'];
                    
                    // Sync with local DB to persist status
                    foreach ($apiCategories as $cat) {
                        $pc = \App\Models\ProviderCategory::updateOrCreate(
                            ['provider' => 'TokoVoucher', 'ext_id' => $cat['id']],
                            ['name' => $cat['nama']]
                        );

                        // Auto-link existing categories if not linked yet
                        // This helps migration for existing data on Vercel
                        \App\Models\Category::whereNull('category_ext_id')
                            ->where(function($q) use ($cat) {
                                $q->where('type', $cat['nama'])
                                  ->orWhere('name', $cat['nama']);
                            })
                            ->update(['category_ext_id' => $cat['id']]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Tokovoucher Categories Sync Error: ' . $e->getMessage());
        }

        $categories = \App\Models\ProviderCategory::where('provider', 'TokoVoucher')->get();
        return view('admin.tokovoucher.categories', compact('categories'));
    }

    public function toggleProviderCategory($id)
    {
        $cat = \App\Models\ProviderCategory::findOrFail($id);
        $cat->status = ($cat->status == 'Aktif' ? 'Nonaktif' : 'Aktif');
        $cat->save();

        return response()->json([
            'status' => 1,
            'new_status' => $cat->status,
            'message' => "Kategori {$cat->name} berhasil diubah menjadi {$cat->status}."
        ]);
    }
    public function syncCategory($id)
    {
        $provider = Provider::where('name', 'like', '%Toko%')->first();
        if (! $provider) {
            return response()->json(['status' => 0, 'error_msg' => 'Provider TokoVoucher belum diatur.']);
        }

        // Jalankan di background agar nginx/proxy tidak 504 (sinkron bisa >1–5 menit)
        SyncTokovoucherCategoryJob::dispatch((int) $id);

        return response()->json([
            'status' => 1,
            'async' => true,
            'message' => 'Sinkronisasi dimulai di background (1–5 menit). Tunggu sebentar lalu muat ulang halaman. Pastikan queue worker (laravel-queue) aktif di server.',
        ]);
    }
}
