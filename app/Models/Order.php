<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
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

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
} 