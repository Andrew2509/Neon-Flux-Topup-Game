<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function validateVoucher(Request $request)
    {
        $code = $request->input('code');
        $min_purchase = $request->input('amount', 0);

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan kode voucher.'
            ]);
        }

        $voucher = Voucher::where('code', $code)
            ->where('status', 'Aktif')
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid.'
            ]);
        }

        // Check expiry
        if ($voucher->expiry_date && Carbon::parse($voucher->expiry_date)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kadaluwarsa.'
            ]);
        }

        // Check quota (if -1, unlimited)
        if ($voucher->quota !== -1 && $voucher->quota <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota voucher telah habis.'
            ]);
        }

        // Check min purchase
        if ($min_purchase < $voucher->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal pembelian untuk voucher ini adalah Rp ' . number_format($voucher->min_purchase, 0, ',', '.')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'data' => [
                'code' => $voucher->code,
                'type' => $voucher->type,
                'amount' => $voucher->discount_amount
            ]
        ]);
    }
}
