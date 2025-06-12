<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'person_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'reference_number',
        'batch_number',
        'expiry_date',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'expiry_date' => 'date'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
} 