<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parish extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'municipality_id',
        'id_municipio'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
} 