<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // 'location',
        'category',
        'photo',
        'state_id',
        'municipality_id',
        'parish_id',
        'latitude',
        'longitude',
    ];

    public function productListings(): HasMany
    {
        return $this->hasMany(ProductListing::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }
}


