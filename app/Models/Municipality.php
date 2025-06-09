<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'id_municipio'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
} 