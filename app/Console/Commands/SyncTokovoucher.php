<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Provider;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\SiteSetting;


class SyncTokovoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tokovoucher
                            {--operator= : ID Operator spesifik untuk disinkronkan}
                            {--jenis= : ID Jenis produk spesifik untuk disinkronkan}
                            {--cleanup-only : Hanya jalankan proses pembersihan produk lama}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automasi penarikan produk TokoVoucher (Kategori, Operator, Jenis, Produk)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set higher execution time for background sync
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $this->info('Memulai sinkronisasi TokoVoucher...');

        $provider = Provider::where('name', 'like', '%Toko%')->first();
        if (!$provider) {
            $this->error('Provider (TokoVoucher) belum dikonfigurasi.');
            return Command::FAILURE;
        }

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        if (!$memberCode || !$secret) {
            $this->error('Member Code atau Secret Key belum diatur.');
            return Command::FAILURE;
        }

        $signature = md5($memberCode . ":" . $secret);
        
        // --- STEP 0: SYNC BALANCE FIRST ---
        $this->line('Menyinkronkan saldo provider...');
        try {
            $balResponse = Http::timeout(10)->get("https://api.tokovoucher.net/member", [
                'member_code' => $memberCode,
                'signature' => $signature,
            ]);

            if ($balResponse->successful()) {
                $balData = $balResponse->json();
                if (isset($balData['data']['saldo'])) {
                    $newBalance = $balData['data']['saldo'];
                    $provider->update(['balance' => $newBalance, 'status' => 'Aktif']);
                    $this->info("Saldo berhasil diperbarui: Rp " . number_format($newBalance, 0, ',', '.'));
                }
            }
        } catch (\Exception $e) {
            $this->warn('Gagal sinkron saldo: ' . $e->getMessage());
        }

        // --- CLEANUP ONLY MODE ---
        if ($this->option('cleanup-only')) {
            $this->info('Menjalankan pembersihan produk lama saja...');
            $deactivated = Service::where('provider', 'TokoVoucher')
                ->where('updated_at', '<', now()->startOfDay())
                ->update(['status' => 'Nonaktif']);
            $this->info("Berhasil menonaktifkan {$deactivated} produk lama.");
            return Command::SUCCESS;
        }

        try {
            $this->line('Mengambil daftar kategori...');
            // STEP 1: Categories (Topup & Voucher)
            $targetTypes = [1 => 'Topup Game', 2 => 'Voucher Game'];

            // STEP 2: Fetch Operators for each Type
            $operatorIdFilter = $this->option('operator');

            $opResponses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($targetTypes, $memberCode, $signature) {
                foreach ($targetTypes as $id => $name) {
                    $pool->as($id)->timeout(10)->get("https://api.tokovoucher.net/member/produk/operator/list", [
                        'member_code' => $memberCode,
                        'signature' => $signature,
                        'id' => $id
                    ]);
                }
            });

            $activeOperators = [];
            foreach ($opResponses as $catId => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $op) {
                            if ($op['status'] == 1) {
                                // Apply filter if provided
                                if ($operatorIdFilter && $op['id'] != $operatorIdFilter) {
                                    continue;
                                }

                                $activeOperators[] = [
                                    'id' => $op['id'],
                                    'name' => $op['nama'],
                                    'type' => $targetTypes[$catId],
                                    'logo' => $op['logo'] ?? null
                                ];
                            }
                        }
                    } else {
                        Log::error("Tokovoucher Sync: Invalid or empty operator data for category {$catId}", ['response' => $data]);
                    }
                } else {
                    Log::error("Tokovoucher Sync: HTTP Error for category {$catId}", [
                        'status' => $response instanceof \Illuminate\Http\Client\Response ? $response->status() : 'Unknown',
                        'body' => $response instanceof \Illuminate\Http\Client\Response ? $response->body() : 'No Body'
                    ]);
                }
            }

            if (empty($activeOperators)) {
                $this->error('Tidak ditemukan operator aktif dari TokoVoucher.');
                $this->line('Cek log untuk detail kesalahan API.');
                return Command::FAILURE;
            }

            $this->info('Mempersiapkan ' . count($activeOperators) . ' kategori/operator...');

            // --- IMMEDIATE CATEGORY POPULATION ---
            $cachedCategoryIds = [];
            foreach ($activeOperators as $op) {
                $isML = str_contains(strtolower($op['name']), 'mobile legends');

                $category = Category::updateOrCreate(
                    ['name' => $op['name']],
                    [
                        'slug' => Str::slug($op['name']),
                        'type' => $op['type'],
                        'icon' => $op['logo'] ?? 'games',
                        'status' => 'Aktif',
                        'input_label' => $op['input_label'] ?? 'User ID',
                        'input_placeholder' => $op['input_placeholder'] ?? 'Masukkan ID Pemain',
                        'has_zone' => $op['has_zone'] ?? ($isML ? 1 : 0),
                        'zone_label' => $op['zone_label'] ?? ($isML ? 'Zone ID' : null),
                        'zone_placeholder' => $op['zone_placeholder'] ?? ($isML ? 'Contoh: 1234' : null),
                        'ext_id' => $op['id'],
                    ]
                );
                $cachedCategoryIds[$op['id']] = $category->id;
            }

            $this->line('Mengambil daftar jenis produk...');

            $jenisIdFilter = $this->option('jenis');

            $jenisResponses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($activeOperators, $memberCode, $signature) {
                // Limit to first 300 to be safe
                $limitedOps = array_slice($activeOperators, 0, 300);
                foreach ($limitedOps as $op) {
                    $pool->as($op['id'])->timeout(8)->get("https://api.tokovoucher.net/member/produk/jenis/list", [
                        'member_code' => $memberCode,
                        'signature' => $signature,
                        'id' => $op['id']
                    ]);
                }
            });

            $targetJenis = [];
            foreach ($jenisResponses as $opId => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $data = $response->json();
                    if (isset($data['data'])) {
                        foreach ($data['data'] as $j) {
                            if ($j['status'] == 1) {
                                // Apply jenis filter if provided
                                if ($jenisIdFilter && $j['id'] != $jenisIdFilter) {
                                    continue;
                                }

                                // SAVE JENIS TO DATABASE
                                \App\Models\ProductJenis::updateOrCreate(
                                    ['id' => $j['id']],
                                    [
                                        'category_id' => $cachedCategoryIds[$opId] ?? null,
                                        'name' => $j['nama'],
                                        'status' => 'Aktif'
                                    ]
                                );

                                $targetJenis[] = [
                                    'id' => $j['id'],
                                    'operator_id' => $opId,
                                    'type' => collect($activeOperators)->firstWhere('id', $opId)['type']
                                ];
                            }
                        }
                    }
                }
            }

            $this->info('Mengambil data produk final (Total Jenis: ' . count($targetJenis) . ')...');

            // --- FETCH MARGINS ---
            $marginPublic = \App\Models\SiteSetting::where('key', 'margin_public')->value('value') ?? 10;
            $marginReseller = \App\Models\SiteSetting::where('key', 'margin_reseller')->value('value') ?? 5;
            $transactionFee = \App\Models\SiteSetting::where('key', 'transaction_fee')->value('value') ?? 0;
            $transactionFee = (float)str_replace(',', '', $transactionFee);

            // STEP 4: Fetch Products for each Jenis in batches of 10 with retries
            $count = 0;
            $totalJenis = count($targetJenis);
            $chunks = array_chunk($targetJenis, 10);
            $bar = $this->output->createProgressBar($totalJenis);
            $bar->start();

            foreach ($chunks as $chunk) {
                $retryCount = 0;
                $maxRetries = 2;
                $success = false;

                while ($retryCount <= $maxRetries && !$success) {
                    try {
                        $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($chunk, $memberCode, $signature) {
                            foreach ($chunk as $j) {
                                $pool->as($j['id'])->timeout(30)->get("https://api.tokovoucher.net/member/produk/list", [
                                    'member_code' => $memberCode,
                                    'signature' => $signature,
                                    'id_jenis' => $j['id']
                                ]);
                            }
                        });

                        $batchHasFailure = false;
                        foreach ($responses as $jenisId => $response) {
                            if ($response instanceof \Illuminate\Http\Client\Response) {
                                if ($response->successful()) {
                                    $resJson = $response->json();
                                    if (isset($resJson['data']) && is_array($resJson['data'])) {
                                        $jInfo = collect($targetJenis)->firstWhere('id', $jenisId);
                                        $appCategoryId = $cachedCategoryIds[$jInfo['operator_id']] ?? null;

                                        // Fallback category search
                                        if (!$appCategoryId && $jInfo) {
                                            $opName = collect($activeOperators)->firstWhere('id', $jInfo['operator_id'])['name'] ?? null;
                                            if ($opName) {
                                                $cat = Category::where('name', $opName)->first();
                                                $appCategoryId = $cat ? $cat->id : null;
                                            }
                                        }

                                        if ($appCategoryId) {
                                            $toUpsert = [];
                                            $now = now();
                                            foreach ($resJson['data'] as $produk) {
                                                if ($produk['status'] == 1) {
                                                    $calculatedPrice = Service::calculatePrice($produk['price'], $marginPublic);
                                                    $toUpsert[] = [
                                                        'product_code' => $produk['code'],
                                                        'category_id' => $appCategoryId,
                                                        'product_jenis_id' => $jenisId, // LINK TO JENIS
                                                        'type' => $jInfo['type'],
                                                        'name' => $produk['nama_produk'],
                                                        'provider' => 'TokoVoucher',
                                                        'cost' => (float)$produk['price'],
                                                        'price' => $calculatedPrice,
                                                        'status' => 'Aktif',
                                                        'updated_at' => $now,
                                                        'created_at' => $now,
                                                    ];
                                                }
                                            }

                                            if (!empty($toUpsert)) {
                                                Service::upsert($toUpsert, ['product_code'], ['category_id', 'product_jenis_id', 'type', 'name', 'cost', 'price', 'status', 'updated_at']);
                                                $count += count($toUpsert);
                                            }
                                        } else {
                                            Log::warning("Sync: Skipping Jenis {$jenisId} because Category ID not found for Operator {$jInfo['operator_id']}");
                                        }
                                    }
                                } else {
                                    $batchHasFailure = true;
                                    Log::error("Sync: HTTP Error for Jenis {$jenisId} in batch (retry {$retryCount})", ['status' => $response->status()]);
                                }
                            } else {
                                $batchHasFailure = true;
                                Log::error("Sync: Connection Error for Jenis {$jenisId} in batch (retry {$retryCount})", ['msg' => method_exists($response, 'getMessage') ? $response->getMessage() : 'Unknown Error']);
                            }
                        }

                        if (!$batchHasFailure) {
                            $success = true;
                        } else {
                            $retryCount++;
                            if ($retryCount <= $maxRetries) {
                                sleep(2); // Wait before retry
                            }
                        }
                    } catch (\Exception $e) {
                        $retryCount++;
                        Log::error("Sync Pool Exception for batch (retry {$retryCount}): " . $e->getMessage());
                        if ($retryCount <= $maxRetries) {
                            sleep(2);
                        }
                    }
                }

                // Advance bar for all items in chunk regardless of final success
                foreach($chunk as $item) {
                    $bar->advance();
                }
            }

            $bar->finish();
            $this->line(''); // new line

            // STEP 5: Cleanup - Mark products not updated today as Nonaktif
            // ONLY DO CLEANUP IF FULL SYNC (no filters)
            if (!$operatorIdFilter && !$jenisIdFilter) {
                $deactivated = Service::where('provider', 'TokoVoucher')
                    ->where('updated_at', '<', now()->startOfDay())
                    ->update(['status' => 'Nonaktif']);

                if ($deactivated > 0) {
                    $this->info("Membersihkan {$deactivated} produk lama (Set Menjadi Nonaktif).");
                }
            }

            $this->info("Sukses! Berhasil sinkronisasi " . count($activeOperators) . " operator game dan {$count} produk aktif.");
            
            // RECORD LAST SYNC
            SiteSetting::updateOrCreate(['key' => 'last_tokovoucher_sync'], ['value' => now()->toDateTimeString()]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Kesalahan sinkronisasi: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
