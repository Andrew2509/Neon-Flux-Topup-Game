<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(10);
        return view('admin.vouchers', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers',
            'discount_amount' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date',
            'status' => 'required|string|in:Aktif,Nonaktif',
        ]);

        Voucher::create($request->all());

        return redirect()->route('admin.vouchers')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function show(Voucher $voucher)
    {
        return view('admin.vouchers.show', compact('voucher'));
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'status' => 'required|string|in:Aktif,Nonaktif',
        ]);

        $voucher->update($request->all());

        return redirect()->route('admin.vouchers')->with('success', 'Data voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers')->with('success', 'Voucher berhasil dihapus.');
    }
}
