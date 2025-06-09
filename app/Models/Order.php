<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'delivery_address',
        'delivery_date',
        'notes'
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'order_person')
            ->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
} 