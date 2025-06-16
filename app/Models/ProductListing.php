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
        'person_id',
        'price',
        'available_quantity',
        'minimum_order_quantity',
        'maximum_order_quantity',
        'delivery_time',
        'is_active',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'minimum_order_quantity' => 'decimal:2',
        'maximum_order_quantity' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
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

    // Estados posibles del listing
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_SOLD_OUT = 'sold_out';
    public const STATUS_ARCHIVED = 'archived';
} 