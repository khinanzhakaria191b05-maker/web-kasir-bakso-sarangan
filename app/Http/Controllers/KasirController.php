<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class KasirController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'name')->get();

        $products = Product::with('options')->orderBy('name')->get()->map(fn($p) => [
            'id'           => $p->id,
            'name'         => $p->name,
            'price'        => $p->price,
            'category_id'  => $p->category_id,
            'is_available' => $p->is_available,
            'image'        => $p->image ? asset('storage/' . $p->image) : null,
            'options'      => $p->options->map(fn($o) => [
                'id'    => $o->id,
                'name'  => $o->name,
                'type'  => $o->type,
                'price' => $o->price,
            ])->values(),
        ]);

        return view('kasir.index', compact('categories', 'products'));
    }
}
