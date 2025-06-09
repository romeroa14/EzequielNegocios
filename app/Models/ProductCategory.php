<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function subcategories()
    {
        return $this->hasMany(ProductSubcategory::class, 'category_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductSubcategory::class);
    }
} 