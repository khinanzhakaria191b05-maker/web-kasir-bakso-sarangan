<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductOption;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::all(),
            'options' => ProductOption::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $optionIds = $data['options'] ?? [];
        unset($data['options']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->options()->sync($optionIds);

        return redirect()->route('admin.products.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product->load('options'),
            'categories' => Category::all(),
            'options' => ProductOption::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $optionIds = $data['options'] ?? [];
        unset($data['options']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->options()->sync($optionIds);

        return redirect()->route('admin.products.index')->with('success', 'Menu diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'exists:product_options,id',
        ]);
    }
}
