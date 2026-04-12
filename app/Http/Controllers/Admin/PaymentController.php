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
