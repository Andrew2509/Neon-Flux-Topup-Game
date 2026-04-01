<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function bankForm()
    {
        $banks = [
            ['code' => 'bca', 'name' => 'BCA'],
            ['code' => 'mandiri', 'name' => 'Mandiri'],
            ['code' => 'bni', 'name' => 'BNI'],
            ['code' => 'bri', 'name' => 'BRI'],
            ['code' => 'permata', 'name' => 'Permata'],
            ['code' => 'cimb', 'name' => 'CIMB Niaga'],
            ['code' => 'danamon', 'name' => 'Danamon'],
            ['code' => 'bsi', 'name' => 'BSI'],
        ];
        return view('admin.withdrawals.bank', compact('banks'));
    }

    public function processBank(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'tujuan' => 'required|numeric',
            'nominal' => 'required|numeric|min:10000',
        ]);

        $provider = Provider::where('name', 'LIKE', '%Toko%')->first();
        if (!$provider) {
            return back()->with('error', 'Provider TokoVoucher tidak ditemukan.');
        }

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $refId = 'WD' . time() . rand(10, 99);
        $signature = md5($memberCode . ':' . $secret . ':' . $refId);

        try {
            $response = Http::post('https://api.tokovoucher.net/v1/transfer/bank', [
                'member_code' => $memberCode,
                'ref_id' => $refId,
                'bank' => $request->bank,
                'tujuan' => $request->tujuan,
                'nominal' => (int)$request->nominal,
                'signature' => $signature,
            ]);

            $result = $response->json();

            if (isset($result['status']) && $result['status'] == 'sukses') {
                return back()->with('success', 'Transfer Bank berhasil diproses. TRX ID: ' . ($result['trx_id'] ?? '-'));
            }

            return back()->with('error', 'Gagal: ' . ($result['message'] ?? 'Unknown Error'));

        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan koneksi: ' . $e->getMessage());
        }
    }

    public function ewalletForm()
    {
        $wallets = [
            ['code' => 'OVO', 'name' => 'OVO'],
            ['code' => 'DANA', 'name' => 'DANA'],
            ['code' => 'GOPAY', 'name' => 'GoPay'],
            ['code' => 'SHOPEE', 'name' => 'ShopeePay'],
            ['code' => 'LINKAJA', 'name' => 'LinkAja'],
        ];
        return view('admin.withdrawals.ewallet', compact('wallets'));
    }

    public function processEwallet(Request $request)
    {
        $request->validate([
            'ewallet' => 'required',
            'tujuan' => 'required|numeric',
            'nominal' => 'required|numeric|min:1000',
        ]);

        $provider = Provider::where('name', 'LIKE', '%Toko%')->first();
        if (!$provider) {
            return back()->with('error', 'Provider TokoVoucher tidak ditemukan.');
        }

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;
        $refId = 'RL' . time() . rand(10, 99);
        $signature = md5($memberCode . ':' . $secret . ':' . $refId);

        try {
            $response = Http::post('https://api.tokovoucher.net/v1/transfer/ewallet', [
                'member_code' => $memberCode,
                'ref_id' => $refId,
                'ewallet' => $request->ewallet,
                'tujuan' => $request->tujuan,
                'nominal' => (int)$request->nominal,
                'signature' => $signature,
            ]);

            $result = $response->json();

            if (isset($result['status']) && $result['status'] == 'sukses') {
                return back()->with('success', 'E-Wallet Reload berhasil diproses. TRX ID: ' . ($result['trx_id'] ?? '-'));
            }

            return back()->with('error', 'Gagal: ' . ($result['message'] ?? 'Unknown Error'));

        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan koneksi: ' . $e->getMessage());
        }
    }
}
