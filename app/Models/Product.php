<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'image', 'is_available'];

    protected $casts = ['is_available' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->belongsToMany(ProductOption::class, 'product_product_option');
    }
}
