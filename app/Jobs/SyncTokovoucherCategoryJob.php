<?php

namespace App\Jobs;

use App\Models\Provider;
use App\Models\ProductJenis;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncTokovoucherCategoryJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    /** Proses bisa banyak request ke API TokoVoucher — naikkan jika perlu */
    public int $timeout = 900;

    public function __construct(public int $categoryExtId) {}

    public function handle(): void
    {
        @ini_set('max_execution_time', '900');

        $id = $this->categoryExtId;

        $provider = Provider::where('name', 'like', '%Toko%')->first();
        if (!$provider) {
            Log::warning('SyncTokovoucherCategoryJob: provider tidak ada', ['category_ext_id' => $id]);
            return;
        }

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $signature = md5($memberCode . ':' . $secret);

        $response = Http::timeout(30)->get('https://api.tokovoucher.net/member/produk/operator/list', [
            'member_code' => $memberCode,
            'signature' => $signature,
            'id' => $id,
        ]);

        if (! $response->successful()) {
            throw new \Exception('HTTP Error operator/list: '.$response->status());
        }

        $data = $response->json();
        if (! isset($data['data'])) {
            throw new \Exception($data['error_msg'] ?? 'Invalid operator data');
        }

        $activeOperators = collect($data['data'])->where('status', 1);
        if ($activeOperators->isEmpty()) {
            Log::info('SyncTokovoucherCategoryJob: tidak ada operator aktif', ['category_ext_id' => $id]);

            return;
        }

        $marginPublic = SiteSetting::where('key', 'margin_public')->value('value') ?? 10;

        $opCount = 0;
        $jenisCount = 0;
        $productCount = 0;

        foreach ($activeOperators as $op) {
            $category = \App\Models\Category::updateOrCreate(
                ['name' => $op['nama']],
                [
                    'slug' => Str::slug($op['nama']),
                    'type' => $id == 1 ? 'Topup Game' : ($id == 2 ? 'Voucher Game' : 'Lainnya'),
                    'icon' => $op['logo'] ?? 'games',
                    'status' => 'Aktif',
                    'ext_id' => $op['id'],
                    'category_ext_id' => $id,
                ]
            );
            $opCount++;

            $jResp = Http::timeout(25)->get('https://api.tokovoucher.net/member/produk/jenis/list', [
                'member_code' => $memberCode,
                'signature' => $signature,
                'id' => $op['id'],
            ]);

            if (! $jResp->successful()) {
                continue;
            }

            $jData = $jResp->json();
            if (! isset($jData['data'])) {
                continue;
            }

            foreach ($jData['data'] as $j) {
                if ($j['status'] != 1) {
                    continue;
                }

                ProductJenis::updateOrCreate(
                    ['id' => $j['id']],
                    [
                        'category_id' => $category->id,
                        'name' => $j['nama'],
                        'status' => 'Aktif',
                    ]
                );
                $jenisCount++;

                $pResp = Http::timeout(25)->get('https://api.tokovoucher.net/member/produk/list', [
                    'member_code' => $memberCode,
                    'signature' => $signature,
                    'id_jenis' => $j['id'],
                ]);

                if (! $pResp->successful()) {
                    continue;
                }

                $pData = $pResp->json();
                if (! isset($pData['data'])) {
                    continue;
                }

                $toUpsert = [];
                $now = now();
                foreach ($pData['data'] as $produk) {
                    if ($produk['status'] != 1) {
                        continue;
                    }

                    $calculatedPrice = Service::calculatePrice($produk['price'], $marginPublic);
                    $toUpsert[] = [
                        'product_code' => $produk['code'],
                        'category_id' => $category->id,
                        'product_jenis_id' => $j['id'],
                        'type' => $category->type,
                        'name' => $produk['nama_produk'],
                        'provider' => 'TokoVoucher',
                        'cost' => (float) $produk['price'],
                        'price' => $calculatedPrice,
                        'status' => 'Aktif',
                        'updated_at' => $now,
                        'created_at' => $now,
                    ];
                }

                if ($toUpsert !== []) {
                    Service::upsert($toUpsert, ['product_code'], ['category_id', 'product_jenis_id', 'type', 'name', 'cost', 'price', 'status', 'updated_at']);
                    $productCount += count($toUpsert);
                }
            }
        }

        Log::info('SyncTokovoucherCategoryJob selesai', [
            'category_ext_id' => $id,
            'operators' => $opCount,
            'jenis' => $jenisCount,
            'products' => $productCount,
        ]);
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('SyncTokovoucherCategoryJob gagal', [
            'category_ext_id' => $this->categoryExtId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
