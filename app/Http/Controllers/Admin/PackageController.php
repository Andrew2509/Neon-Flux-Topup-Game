<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $search = $request->input('search');

        // We now show Categories (Operators) instead of manual Packages
        // Note: Removed 'where status Aktif' to allow toggling Nonaktif operators
        $operators = \App\Models\Category::when($type, function($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->withCount('jenis')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.packages', compact('operators', 'type', 'search'));
    }

    public function toggleOperatorStatus($id)
    {
        $operator = \App\Models\Category::findOrFail($id);
        $operator->status = $operator->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
        $operator->save();

        return response()->json([
            'success' => true,
            'status' => $operator->status,
            'message' => "Status {$operator->name} berhasil diperbarui ke {$operator->status}."
        ]);
    }

    public function showOperator($id)
    {
        $operator = \App\Models\Category::findOrFail($id);
        $jenis = \App\Models\ProductJenis::where('category_id', $id)
            ->withCount('services')
            ->get();

        return view('admin.packages.jenis', compact('operator', 'jenis'));
    }

    public function showJenis($id)
    {
        $jenis = \App\Models\ProductJenis::with('category')->findOrFail($id);
        $services = \App\Models\Service::where('product_jenis_id', $id)->get();

        return view('admin.packages.services', compact('jenis', 'services'));
    }

    public function editOperator($id)
    {
        $operator = \App\Models\Category::findOrFail($id);
        return view('admin.packages.edit_operator', compact('operator'));
    }

    public function updateOperator(Request $request, $id)
    {
        $operator = \App\Models\Category::findOrFail($id);

        $request->validate([
            'input_label' => 'required',
            'input_placeholder' => 'required',
            'has_zone' => 'required|boolean',
            'zone_label' => 'nullable|string',
            'zone_placeholder' => 'nullable|string',
        ]);

        $operator->update($request->only([
            'input_label',
            'input_placeholder',
            'has_zone',
            'zone_label',
            'zone_placeholder'
        ]));

        return redirect()->route('admin.packages.index')->with('success', 'Konfigurasi operator berhasil diperbarui.');
    }

    public function create()
    {
        // Archived logic
        return redirect()->route('admin.packages.index');
    }

    public function toggleJenisStatus($id)
    {
        $jenis = \App\Models\ProductJenis::findOrFail($id);
        $jenis->status = $jenis->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
        $jenis->save();

        return back()->with('success', "Status Jenis {$jenis->name} berhasil diperbarui.");
    }

    public function toggleServiceStatus($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $service->status = $service->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
        $service->save();

        return back()->with('success', "Status Layanan {$service->name} berhasil diperbarui.");
    }

    public function destroy(Package $package)
    {
        // Keep for legacy cleanup or delete
        $package->delete();
        return back()->with('success', 'Paket berhasil dihapus!');
    }
}
