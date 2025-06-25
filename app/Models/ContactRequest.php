<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_listing_id',
    ];

    public function buyer()
    {
        return $this->belongsTo(Person::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Person::class, 'seller_id');
    }

    public function productListing()
    {
        return $this->belongsTo(ProductListing::class);
    }
} 