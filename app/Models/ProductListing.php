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
        'person_id',
        'product_id',
        'title',
        'description',
        'quantity_available',
        'unit_price',
        'wholesale_price',
        'min_quantity_order',
        'max_quantity_order',
        'quality_grade',
        'harvest_date',
        'expiry_date',
        'images',
        'location_city',
        'location_state',
        'pickup_available',
        'delivery_available',
        'delivery_radius_km',
        'status',
        'featured_until'
    ];

    protected $casts = [
        'person_id' => 'integer',
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

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function product(): BelongsTo
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