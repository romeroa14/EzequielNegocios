<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Person extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Definición de roles
    public const ROLE_BUYER = 'buyer';
    public const ROLE_SELLER = 'seller';

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'identification_type',
        'identification_number',
        'phone',
        'address',
        'sector',
        'role',
        'company_name',
        'company_rif',
        'is_active',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the login identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'person_id');
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

    /**
     * Verifica si el usuario es un comprador
     */
    public function isBuyer(): bool
    {
        return $this->role === self::ROLE_BUYER;
    }
    
    /**
     * Verifica si el usuario es un vendedor
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Obtiene las capacidades del usuario según su rol
     */
    public function getCapabilities(): array
    {
        return match($this->role) {
            self::ROLE_BUYER => [
                'can_buy_products',
                'can_view_catalog',
                'can_review_sellers',
                'can_track_orders',
            ],
            self::ROLE_SELLER => [
                'can_list_products',
                'can_manage_inventory',
                'can_process_orders',
                'can_view_sales_stats',
                'can_receive_reviews',
            ],
            default => [],
        };
    }

    /**
     * Verifica si el usuario tiene una capacidad específica
     */
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->getCapabilities());
    }

    /**
     * Obtiene el dashboard correspondiente al rol del usuario
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            self::ROLE_BUYER => 'buyer.dashboard',
            self::ROLE_SELLER => 'seller.dashboard',
            default => 'home',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 