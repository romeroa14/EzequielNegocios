<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'people';

    protected $fillable = [
        'identification_type',
        'identification_number',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'country_id',
        'state_id',
        'municipality_id',
        'parish_id',
        'sector',
        'role',
        'company_name',
        'company_rif',
        'is_verified',
        'verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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

    public function productListings(): HasMany
    {
        return $this->hasMany(ProductListing::class, 'person_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_person')
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(UserReview::class, 'reviewed_id');
    }

    public function givenReviews(): HasMany
    {
        return $this->hasMany(UserReview::class, 'reviewer_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->role === 'company') {
            return $this->company_name;
        }
        return $this->full_name;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->sector,
            optional($this->municipality)->name,
            optional($this->state)->name,
            optional($this->country)->name
        ]);
        
        return implode(', ', $parts);
    }

    // Helpers para roles del frontend
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }
    
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }
    
    // Atributo para el nombre de usuario en autenticación
    public function getAuthIdentifierName()
    {
        return 'email';
    }
} 