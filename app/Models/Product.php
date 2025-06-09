<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'name',
        'description',
        'sku_base',
        'unit_type',
        'image',
        'seasonal_info',
        'is_active'
    ];

    protected $casts = [
        'seasonal_info' => 'array',
        'is_active' => 'boolean'
    ];

    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class);
    }

    public function listings()
    {
        return $this->hasMany(ProductListing::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class);
    }
} 