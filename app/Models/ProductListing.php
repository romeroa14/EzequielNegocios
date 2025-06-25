<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'person_id',
        'title',
        'description',
        'unit_price',
        'quantity_available',
        'quality_grade',
        'harvest_date',
        'images',
        'location_city',
        'location_state',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity_available' => 'integer',
        'harvest_date' => 'date',
        'images' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
} 