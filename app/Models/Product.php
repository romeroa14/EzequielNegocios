<?php

namespace App\Models;

use App\Traits\HasProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    use HasProductImage;

    protected $fillable = [
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function subcategory(): BelongsTo
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