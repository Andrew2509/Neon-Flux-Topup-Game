<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Service;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::with('service.category')->latest()->get();
        // For product selection in modal/create
        $services = Service::where('status', 'Aktif')->with('category')->get();
        $categories = \App\Models\Category::where('status', 'Aktif')->orderBy('name')->get();
        
        return view('admin.flash-sale', compact('flashSales', 'services', 'categories'));
    }

    public function getOperators($category_id)
    {
        $operators = \App\Models\ProductJenis::where('category_id', $category_id)
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();
            
        return response()->json($operators);
    }

    public function getServices($jenis_id)
    {
        $services = Service::where('product_jenis_id', $jenis_id)
            ->where('status', 'Aktif')
            ->select('id', 'name', 'price', 'cost')
            ->orderBy('price')
            ->get();
            
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'discount_price' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:Aktif,Nonaktif',
            'stock' => 'required|integer'
        ]);

        FlashSale::create($request->all());

        return back()->with('success', 'Flash Sale berhasil ditambahkan.');
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'discount_price' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:Aktif,Nonaktif',
            'stock' => 'required|integer'
        ]);

        $flashSale->update($request->all());

        return back()->with('success', 'Flash Sale berhasil diperbarui.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return back()->with('success', 'Flash Sale berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->status = $flashSale->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $flashSale->save();

        return back()->with('success', 'Status Flash Sale berhasil diubah.');
    }
}
