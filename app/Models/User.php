<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'producer', 'technician', 'support']);
    }

    // Relationships para el sistema admin
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function person()
    {
        return $this->hasOne(Person::class);
    }

    public function reviews()
    {
        return $this->hasMany(UserReview::class, 'reviewed_user_id');
    }

    public function givenReviews()
    {
        return $this->hasMany(UserReview::class, 'reviewer_id');
    }

    // Helpers para roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isProducer(): bool
    {
        return $this->role === 'producer';
    }
    
    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }
    
    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    // Relationships
    public function productListings()
    {
        return $this->hasMany(ProductListing::class);
    }

    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }
}
