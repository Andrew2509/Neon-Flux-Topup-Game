<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category');
        $type = $request->input('type');

        // Filter by Type (Topup Game / Voucher Game)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Search (Name or Product Code)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $services = $query->latest()->paginate(20)->withQueryString();

        // Filter categories based on selected type
        $categories = Category::query()
            ->when($type, function($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('name')
            ->get();

        return view('admin.services', compact('services', 'categories'));
    }

    public function getSyncList()
    {
        $provider = Provider::where('name', 'like', '%Toko%')->first();
        if (!$provider) return response()->json(['error' => 'Provider not set'], 400);

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $signature = md5($memberCode . ":" . $secret);

        try {
            // Fetch Operators from Tokovoucher
            $targetTypes = [1 => 'Topup Game', 2 => 'Voucher Game'];
            $allOperators = [];

            foreach ($targetTypes as $id => $name) {
                $response = Http::timeout(10)->get("https://api.tokovoucher.net/member/produk/operator/list", [
                    'member_code' => $memberCode,
                    'signature' => $signature,
                    'id' => $id
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data'])) {
                        foreach ($data['data'] as $op) {
                            if ($op['status'] == 1) {
                                $allOperators[] = [
                                    'id' => $op['id'],
                                    'name' => $op['nama'],
                                ];
                            }
                        }
                    }
                }
            }

            return response()->json(['operators' => $allOperators]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function syncTokoVoucher(Request $request)
    {
        try {
            $operatorId = $request->input('operator_id');
            $cleanupOnly = $request->input('cleanup_only');

            // Set higher execution time for individual operator sync
            @ini_set('max_execution_time', 120);
            @set_time_limit(120);

            $params = [];
            if ($operatorId) {
                $params['--operator'] = $operatorId;
            }
            if ($cleanupOnly) {
                $params['--cleanup-only'] = true;
            }

            // Execute the Artisan command
            $exitCode = \Illuminate\Support\Facades\Artisan::call('sync:tokovoucher', $params);
            $output = \Illuminate\Support\Facades\Artisan::output();

            if ($exitCode !== 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Command failed: ' . trim($output)
                    ], 500);
                }
                return back()->with('error', 'Gagal memproses operator: ' . trim($output));
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Operator synced',
                    'output' => $output
                ]);
            }

            if (str_contains($output, 'Sukses!') || str_contains($output, 'Berhasil menarik') || str_contains($output, 'Berhasil sinkronisasi')) {
                return back()->with('success', 'Sinkronisasi selesai! ' . substr($output, strrpos(trim($output), "\n")));
            }

            return back()->with('error', 'Terdapat masalah saat sinkronisasi. Silakan cek log atau jalankan via terminal.');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Kesalahan mengeksekusi sinkronisasi: ' . $e->getMessage());
        }
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('q');
        $services = Service::with('category')
            ->where('status', 'Aktif')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get();

        return response()->json($services);
    }

    public function toggle($id)
    {
        $service = Service::findOrFail($id);
        $service->status = $service->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
        $service->save();

        return back()->with('success', "Status Layanan {$service->name} berhasil diperbarui.");
    }
}
