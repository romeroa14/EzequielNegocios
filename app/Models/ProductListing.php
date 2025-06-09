<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'description',
        'quantity_available',
        'unit_price',
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
        'images' => 'array',
        'harvest_date' => 'date',
        'expiry_date' => 'date',
        'pickup_available' => 'boolean',
        'delivery_available' => 'boolean',
        'featured_until' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
} 