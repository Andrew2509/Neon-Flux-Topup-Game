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

        // Mengikuti ENVIRONMENT MODE di Admin → Provider (sandbox | production).
        $url = $provider->usesProductionApi()
            ? 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            : 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

        try {
            Log::info('Duitku Sync Request:', [
                'url' => $url,
                'uses_production_api' => $provider->usesProductionApi(),
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
                    $groupCode = strtolower($group['Code'] ?? '');
                    $groupName = strtolower($group['Name'] ?? '');
                    $channels = $group['Channels'] ?? [];
                    
                    foreach ($channels as $channel) {
                        $code = $channel['Code'];
                        
                        // Determinate Fee from iPaymu Pricing (Standard Settlement)
                        $fee = 0;
                        if ($groupCode === 'qris' || str_contains($groupName, 'qris')) {
                            $fee = '0.7%';
                        } elseif ($groupCode === 'va' || str_contains($groupName, 'virtual account')) {
                            $fee = 3500;
                        } elseif (in_array($groupCode, ['cstore', 'retail']) || str_contains($groupName, 'convenience store')) {
                            $fee = 4000;
                        } elseif ($groupCode === 'ewallet' || str_contains($groupName, 'ewallet')) {
                            $fee = '3.5%';
                        } elseif (str_contains($groupCode, 'creditcard') || str_contains($groupName, 'credit card')) {
                            $fee = '2.5%+2000';
                        } elseif (in_array($groupCode, ['debit', 'direct_debit']) || str_contains($groupName, 'debit')) {
                            $fee = '1.4%+2000';
                        }

                        PaymentMethod::updateOrCreate(
                            ['code' => $code],
                            [
                                'name' => $channel['Name'],
                                'type' => $this->determineIPaymuType($code, $groupCode),
                                'image' => $channel['Logo'] ?? null,
                                'fee' => $fee,
                                'status' => 'Aktif',
                                'provider' => 'iPaymu'
                            ]
                        );
                        $count++;
                    }
                }
                return back()->with('success', "Berhasil menyinkronkan $count metode pembayaran dari iPaymu dengan fee terbaru.");
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

    public function syncDoku()
    {
        $provider = Provider::where('name', 'LIKE', '%DOKU%')->first();

        if (!$provider) {
            return back()->with('error', 'Provider DOKU tidak ditemukan. Silakan tambahkan provider DOKU terlebih dahulu di Manajemen Provider.');
        }

        $channels = [
            // QRIS
            ['code' => 'DOKU_QRIS', 'name' => 'DOKU QRIS (Semua E-Wallet)', 'type' => 'qris', 'image' => 'https://images.tokopedia.net/img/toppay/sprites/qris-logo-2.png'],
            // Virtual Account
            ['code' => 'DOKU_VA_BCA', 'name' => 'DOKU BCA Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/200px-Bank_Central_Asia.svg.png'],
            ['code' => 'DOKU_VA_BNI', 'name' => 'DOKU BNI Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/200px-BNI_logo.svg.png'],
            ['code' => 'DOKU_VA_BRI', 'name' => 'DOKU BRI Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/200px-BANK_BRI_logo.svg.png'],
            ['code' => 'DOKU_VA_MANDIRI', 'name' => 'DOKU Mandiri Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/200px-Bank_Mandiri_logo_2016.svg.png'],
            ['code' => 'DOKU_VA_PERMATA', 'name' => 'DOKU Permata Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/id/thumb/d/d4/PermataBank_logo.svg/200px-PermataBank_logo.svg.png'],
            ['code' => 'DOKU_VA_CIMB', 'name' => 'DOKU CIMB Niaga Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/CIMB_Niaga_logo.svg/200px-CIMB_Niaga_logo.svg.png'],
            ['code' => 'DOKU_VA_DANAMON', 'name' => 'DOKU Danamon Virtual Account', 'type' => 'bank', 'image' => 'https://upload.wikimedia.org/wikipedia/id/thumb/5/5b/Bank_Danamon_logo.svg/200px-Bank_Danamon_logo.svg.png'],
            // E-Wallet
            ['code' => 'DOKU_SHOPEEPAY', 'name' => 'DOKU ShopeePay', 'type' => 'ewallet', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Shopee.svg/200px-Shopee.svg.png'],
            ['code' => 'DOKU_OVO', 'name' => 'DOKU OVO', 'type' => 'ewallet', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/200px-Logo_ovo_purple.svg.png'],
            ['code' => 'DOKU_DANA', 'name' => 'DOKU DANA', 'type' => 'ewallet', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Dana_logo.svg/200px-Dana_logo.svg.png'],
            ['code' => 'DOKU_LINKAJA', 'name' => 'DOKU LinkAja', 'type' => 'ewallet', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/LinkAja.svg/200px-LinkAja.svg.png'],
            // Retail
            ['code' => 'DOKU_ALFAMART', 'name' => 'DOKU Alfamart', 'type' => 'retail', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Alfamart_logo.svg/200px-Alfamart_logo.svg.png'],
            ['code' => 'DOKU_INDOMARET', 'name' => 'DOKU Indomaret', 'type' => 'retail', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Indomaret.svg/200px-Indomaret.svg.png'],
            // Credit Card
            ['code' => 'DOKU_CC', 'name' => 'DOKU Credit Card (Visa/MC)', 'type' => 'other', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png'],
        ];

        $count = 0;
        foreach ($channels as $channel) {
            PaymentMethod::updateOrCreate(
                ['code' => $channel['code']],
                [
                    'name'     => $channel['name'],
                    'type'     => $channel['type'],
                    'image'    => $channel['image'],
                    'fee'      => 0,
                    'status'   => 'Aktif',
                    'provider' => 'DOKU'
                ]
            );
            $count++;
        }

        return back()->with('success', "Berhasil menambahkan $count metode pembayaran DOKU Checkout.");
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
            'fee' => 'required|string|max:50',
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
            'fee' => 'required|string|max:50',
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
