<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
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
        @ini_set('max_execution_time', 300);
        
        $provider = Provider::where('name', 'like', '%Toko%')->first();
        if (!$provider) return response()->json(['status' => 0, 'error_msg' => 'Provider not set']);

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $signature = md5($memberCode . ":" . $secret);

        try {
            // 1. Fetch Operators for this Category
            $response = Http::timeout(20)->get("https://api.tokovoucher.net/member/produk/operator/list", [
                'member_code' => $memberCode,
                'signature' => $signature,
                'id' => $id
            ]);

            if (!$response->successful()) throw new \Exception("HTTP Error: " . $response->status());
            
            $data = $response->json();
            if (!isset($data['data'])) throw new \Exception($data['error_msg'] ?? "Invalid operator data");

            $activeOperators = collect($data['data'])->where('status', 1);
            if ($activeOperators->isEmpty()) {
                return response()->json(['status' => 1, 'message' => 'Tidak ada operator aktif untuk kategori ini.']);
            }

            // 2. Fetch Pricing Settings
            $marginPublic = \App\Models\SiteSetting::where('key', 'margin_public')->value('value') ?? 10;
            $transactionFee = \App\Models\SiteSetting::where('key', 'transaction_fee')->value('value') ?? 0;
            $transactionFee = (float)str_replace(',', '', $transactionFee);

            $opCount = 0;
            $jenisCount = 0;
            $productCount = 0;

            foreach ($activeOperators as $op) {
                // Update or Create Local Category (Game/Operator)
                $category = \App\Models\Category::updateOrCreate(
                    ['name' => $op['nama']],
                    [
                        'slug' => \Illuminate\Support\Str::slug($op['nama']),
                        'type' => $id == 1 ? 'Topup Game' : ($id == 2 ? 'Voucher Game' : 'Lainnya'),
                        'icon' => $op['logo'] ?? 'games',
                        'status' => 'Aktif',
                        'ext_id' => $op['id'],
                        'category_ext_id' => $id,
                    ]
                );
                $opCount++;

                // 3. Fetch Jenis for this Operator
                $jResp = Http::timeout(15)->get("https://api.tokovoucher.net/member/produk/jenis/list", [
                    'member_code' => $memberCode,
                    'signature' => $signature,
                    'id' => $op['id']
                ]);

                if ($jResp->successful()) {
                    $jData = $jResp->json();
                    if (isset($jData['data'])) {
                        foreach ($jData['data'] as $j) {
                            if ($j['status'] != 1) continue;

                            // Create Jenis
                            \App\Models\ProductJenis::updateOrCreate(
                                ['id' => $j['id']],
                                [
                                    'category_id' => $category->id,
                                    'name' => $j['nama'],
                                    'status' => 'Aktif'
                                ]
                            );
                            $jenisCount++;

                            // 4. Fetch Products for this Jenis
                            $pResp = Http::timeout(15)->get("https://api.tokovoucher.net/member/produk/list", [
                                'member_code' => $memberCode,
                                'signature' => $signature,
                                'id_jenis' => $j['id']
                            ]);

                            if ($pResp->successful()) {
                                $pData = $pResp->json();
                                if (isset($pData['data'])) {
                                    $toUpsert = [];
                                    $now = now();
                                    foreach ($pData['data'] as $produk) {
                                        if ($produk['status'] != 1) continue;

                                        $calculatedPrice = \App\Models\Service::calculatePrice($produk['price'], $marginPublic);
                                        $toUpsert[] = [
                                            'product_code' => $produk['code'],
                                            'category_id' => $category->id,
                                            'product_jenis_id' => $j['id'],
                                            'type' => $category->type,
                                            'name' => $produk['nama_produk'],
                                            'provider' => 'TokoVoucher',
                                            'cost' => (float)$produk['price'],
                                            'price' => $calculatedPrice,
                                            'status' => 'Aktif',
                                            'updated_at' => $now,
                                            'created_at' => $now,
                                        ];
                                    }

                                    if (!empty($toUpsert)) {
                                        \App\Models\Service::upsert($toUpsert, ['product_code'], ['category_id', 'product_jenis_id', 'type', 'name', 'cost', 'price', 'status', 'updated_at']);
                                        $productCount += count($toUpsert);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return response()->json([
                'status' => 1,
                'message' => "Sinkronisasi selesai! Berhasil memperbarui {$opCount} operator, {$jenisCount} jenis, dan {$productCount} produk."
            ]);

        } catch (\Exception $e) {
            Log::error("Targeted Sync Error: " . $e->getMessage());
            return response()->json(['status' => 0, 'error_msg' => "Sinkronisasi gagal: " . $e->getMessage()]);
        }
    }
}
