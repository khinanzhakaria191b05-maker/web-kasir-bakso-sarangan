<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOption;
use Illuminate\Http\Request;

class ProductOptionController extends Controller
{
    public function index()
    {
        $options = ProductOption::withCount('products')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('admin.options.index', compact('options'));
    }

    public function store(Request $request)
    {
        ProductOption::create($this->validated($request));
        return back()->with('success', 'Opsi ditambahkan.');
    }

    public function update(Request $request, ProductOption $option)
    {
        $option->update($this->validated($request));
        return back()->with('success', 'Opsi diperbarui.');
    }

    public function destroy(ProductOption $option)
    {
        $option->delete(); // tautan ke menu ikut terhapus otomatis
        return back()->with('success', 'Opsi dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'type'  => 'required|in:varian,topping',
            'price' => 'nullable|integer|min:0',
        ]);

        // Varian selalu gratis
        $data['price'] = $data['type'] === 'varian' ? 0 : ($data['price'] ?? 0);

        return $data;
    }
}
