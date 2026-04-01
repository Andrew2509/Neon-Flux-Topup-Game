<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentMethod::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $payments = $query->paginate(10)->withQueryString();
        
        return view('admin.payments', compact('payments'));
    }

    public function syncDuitku()
    {
        $provider = Provider::where('name', 'LIKE', '%Duitku%')->first();

        if (!$provider) {
            return back()->with('error', 'Provider Duitku tidak ditemukan. Silakan tambahkan provider Duitku terlebih dahulu.');
        }

        $merchantCode = $provider->provider_id;
        $apiKey = $provider->api_key;
        $datetime = date('Y-m-d H:i:s');
        $amount = 10000; // Standar inquiry
        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);

        // Auto-detect Sandbox vs Production
        // 1. Check if name contains "Sandbox"
        // 2. Check if Merchant Code starts with 'D' (common for Duitku Sandbox)
        $isSandbox = str_contains(strtolower($provider->name), 'sandbox') || str_starts_with(strtoupper($merchantCode), 'D');

        $url = $isSandbox
            ? 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            : 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

        try {
            Log::info('Duitku Sync Request:', [
                'url' => $url,
                'isSandbox' => $isSandbox,
                'merchantcode' => $merchantCode,
                'datetime' => $datetime,
                'signature' => $signature,
                'env' => config('app.env')
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'merchantcode' => $merchantCode,
                'amount' => $amount,
                'datetime' => $datetime,
                'signature' => $signature,
            ]);

            Log::info('Duitku Sync Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['paymentFee']) && is_array($result['paymentFee'])) {
                    $count = 0;
                    foreach ($result['paymentFee'] as $item) {
                        PaymentMethod::updateOrCreate(
                            ['code' => $item['paymentMethod']],
                            [
                                'name' => $item['paymentName'],
                                'type' => $this->determineType($item['paymentMethod']),
                                'image' => $item['paymentImage'],
                                'fee' => $item['totalFee'],
                                'status' => 'Aktif',
                                'provider' => 'Duitku'
                            ]
                        );
                        $count++;
                    }
                    return back()->with('success', "Berhasil menyinkronkan $count metode pembayaran dari Duitku.");
                }
            }

            $errorDetail = $response->json();
            $msg = $errorDetail['responseMessage'] ?? $errorDetail['Message'] ?? 'Unknown Error';
            return back()->with('error', 'Gagal mendapatkan data dari Duitku (' . $response->status() . '): ' . $msg);

        } catch (\Exception $e) {
            Log::error('Duitku Sync Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan koneksi: ' . $e->getMessage());
        }
    }

    public function syncIPaymu()
    {
        try {
            $ipaymuService = new \App\Services\IPaymuService();
            $result = $ipaymuService->getPaymentChannels();

            if (isset($result['Status']) && $result['Status'] == 200) {
                $count = 0;
                foreach ($result['Data'] as $group) {
                    $channels = $group['Channels'] ?? [];
                    foreach ($channels as $channel) {
                        PaymentMethod::updateOrCreate(
                            ['code' => $channel['Code']],
                            [
                                'name' => $channel['Name'],
                                'type' => $this->determineIPaymuType($channel['Code'], $group['Code'] ?? null),
                                'image' => $channel['Logo'] ?? null,
                                'fee' => 0, // iPaymu doesn't always provide fee in this API, default to 0
                                'status' => 'Aktif',
                                'provider' => 'iPaymu'
                            ]
                        );
                        $count++;
                    }
                }
                return back()->with('success', "Berhasil menyinkronkan $count metode pembayaran dari iPaymu.");
            }

            $msg = $result['Message'] ?? 'Unknown Error';
            return back()->with('error', 'Gagal mendapatkan data dari iPaymu: ' . $msg);

        } catch (\Exception $e) {
            Log::error('iPaymu Sync Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan koneksi: ' . $e->getMessage());
        }
    }

    public function syncMidtrans()
    {
        $channels = [
            ['code' => 'MIDTRANS_QRIS', 'name' => 'Midtrans QRIS (Gopay, ShopeePay, Dana)', 'type' => 'qris', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/qris.png'],
            ['code' => 'MIDTRANS_BCA_VA', 'name' => 'Midtrans BCA Virtual Account', 'type' => 'bank', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/bca.png'],
            ['code' => 'MIDTRANS_BNI_VA', 'name' => 'Midtrans BNI Virtual Account', 'type' => 'bank', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/bni.png'],
            ['code' => 'MIDTRANS_BRI_VA', 'name' => 'Midtrans BRI Virtual Account', 'type' => 'bank', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/bri.png'],
            ['code' => 'MIDTRANS_MANDIRI_VA', 'name' => 'Midtrans Mandiri Virtual Account', 'type' => 'bank', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/mandiri.png'],
            ['code' => 'MIDTRANS_PERMATA_VA', 'name' => 'Midtrans Permata Virtual Account', 'type' => 'bank', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/permata_bank.png'],
            ['code' => 'MIDTRANS_ALFAMART', 'name' => 'Midtrans Alfamart', 'type' => 'retail', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/alfamart.png'],
            ['code' => 'MIDTRANS_INDOMARET', 'name' => 'Midtrans Indomaret', 'type' => 'retail', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/indomaret.png'],
            ['code' => 'MIDTRANS_GOPAY', 'name' => 'Midtrans GoPay', 'type' => 'ewallet', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/gopay.png'],
            ['code' => 'MIDTRANS_SHOPEEPAY', 'name' => 'Midtrans ShopeePay', 'type' => 'ewallet', 'image' => 'https://raw.githubusercontent.com/veritrans/logo/master/logo/shopeepay.png'],
        ];

        $count = 0;
        foreach ($channels as $channel) {
            PaymentMethod::updateOrCreate(
                ['code' => $channel['code']],
                [
                    'name' => $channel['name'],
                    'type' => $channel['type'],
                    'image' => $channel['image'],
                    'fee' => 0,
                    'status' => 'Aktif',
                    'provider' => 'Midtrans'
                ]
            );
            $count++;
        }

        return back()->with('success', "Berhasil menambahkan $count metode pembayaran standar Midtrans.");
    }

    private function determineIPaymuType($code, $categoryCode = null)
    {
        if ($categoryCode) {
            $categoryCode = strtolower($categoryCode);
            if ($categoryCode === 'va') return 'bank';
            if ($categoryCode === 'cstore') return 'retail';
            if ($categoryCode === 'ewallet') return 'ewallet';
            if ($categoryCode === 'qris') return 'qris';
        }

        $code = strtolower($code);
        if (in_array($code, ['bca', 'bni', 'mandiri', 'permata', 'bri', 'cimb', 'bag', 'bsi', 'danamon', 'bmi', 'btn'])) return 'bank';
        if (in_array($code, ['ovo', 'shopeepay', 'linkaja', 'dana'])) return 'ewallet';
        if (str_contains($code, 'qris')) return 'qris';
        if (in_array($code, ['alfamart', 'indomaret'])) return 'retail';
        return 'bank'; // Safe fallback
    }

    private function determineType($code)
    {
        $code = strtoupper($code);
        if (str_contains($code, 'VA') || in_array($code, ['BT', 'B1', 'A1', 'I1', 'M1', 'M2'])) return 'bank';
        if (in_array($code, ['OV', 'SP', 'DA', 'LA', 'GQ', 'GC'])) return 'ewallet';
        if ($code == 'DQ') return 'qris';
        if (in_array($code, ['FT', 'IR', 'AL'])) return 'retail';
        return 'other';
    }

    public function create()
    {
        return view('admin.payments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods',
            'type' => 'required|string|in:bank,ewallet,qris,retail',
            'fee' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:255',
            'status' => 'required|string|in:Aktif,Nonaktif',
            'provider' => 'required|string|max:50',
        ]);

        PaymentMethod::create($request->all());

        return redirect()->route('admin.payments')->with('success', 'Metode Pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $payment)
    {
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, PaymentMethod $payment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $payment->id,
            'type' => 'required|string|in:bank,ewallet,qris,retail',
            'fee' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:255',
            'status' => 'required|string|in:Aktif,Nonaktif',
            'provider' => 'required|string|max:50',
        ]);

        $payment->update($request->all());

        return redirect()->route('admin.payments')->with('success', 'Metode Pembayaran berhasil diperbarui.');
    }

    public function toggle(PaymentMethod $payment)
    {
        $newStatus = ($payment->status === 'Aktif') ? 'Nonaktif' : 'Aktif';
        $payment->update(['status' => $newStatus]);

        return back()->with('success', "Status {$payment->name} diubah menjadi {$newStatus}.");
    }

    public function destroy(PaymentMethod $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments')->with('success', 'Metode Pembayaran berhasil dihapus.');
    }
}
