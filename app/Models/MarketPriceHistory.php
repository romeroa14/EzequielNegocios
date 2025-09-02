<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'currency',
        'change_date',
        'notes',
        'changed_by',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'change_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'changed_by');
    }

    public function getPriceDifferenceAttribute()
    {
        return $this->new_price - $this->old_price;
    }

    public function getPriceChangePercentageAttribute()
    {
        if ($this->old_price == 0) return 0;
        return (($this->new_price - $this->old_price) / $this->old_price) * 100;
    }

    public function getFormattedOldPriceAttribute()
    {
        return number_format($this->old_price, 2, ',', '.') . ' ' . $this->currency;
    }

    public function getFormattedNewPriceAttribute()
    {
        return number_format($this->new_price, 2, ',', '.') . ' ' . $this->currency;
    }

    public function getFormattedPriceDifferenceAttribute()
    {
        $difference = $this->price_difference;
        $sign = $difference > 0 ? '+' : '';
        return $sign . number_format($difference, 2, ',', '.') . ' ' . $this->currency;
    }

    public function getChangeTypeAttribute()
    {
        if ($this->price_difference > 0) return 'increase';
        if ($this->price_difference < 0) return 'decrease';
        return 'stable';
    }
}
