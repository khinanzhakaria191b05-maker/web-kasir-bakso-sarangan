<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        Category::create($data);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $category->update($data);
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori masih punya menu, tidak bisa dihapus.');
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
