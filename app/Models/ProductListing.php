<?php

namespace App\Models;

use App\Traits\HasProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductListing extends Model
{
    use HasFactory;
    use HasProductImage;

    protected $fillable = [
        'product_id',
        'seller_id',
        'title',
        'description',
        'unit_price',
        'quantity_available',
        'quality_grade',
        'harvest_date',
        'images',
        'location_city',
        'location_state',
        'status'
    ];

    protected $casts = [
        'seller_id' => 'integer',
        'product_id' => 'integer',
        'images' => 'array',
        'quantity_available' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'min_quantity_order' => 'integer',
        'max_quantity_order' => 'integer',
        'pickup_available' => 'boolean',
        'delivery_available' => 'boolean',
        'delivery_radius_km' => 'decimal:1',
        'harvest_date' => 'date',
        'expiry_date' => 'date',
        'featured_until' => 'datetime'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listing) {
            if (empty($listing->images) && $listing->product && $listing->product->image) {
                $listing->images = [$listing->product->image];
            }
        });
    }

    public function getMainImageAttribute()
    {
        return $this->images[0] ?? null;
    }

    public function getMainImageUrlAttribute()
    {
        return $this->main_image ? asset(Storage::disk('public')->path($this->main_image)) : null;
    }
} 