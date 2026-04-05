<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->paginate(24)->withQueryString();

        return view('admin.categories', compact('categories'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'support_phone' => 'nullable|string|max:64',
            'input_label' => 'nullable|string|max:120',
            'input_placeholder' => 'nullable|string|max:120',
            'zone_label' => 'nullable|string|max:120',
            'zone_placeholder' => 'nullable|string|max:120',
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories', ['type' => $category->type])
            ->with('success', 'Kategori '.$category->name.' berhasil diperbarui.');
    }
}
