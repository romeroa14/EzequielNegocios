<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'is_universal',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_universal' => 'boolean',
    ];

    // Constantes para roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PRODUCER = 'producer';
    public const ROLE_TECHNICIAN = 'technician';
    public const ROLE_SUPPORT = 'support';

    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => $id ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required|in:admin,producer,technician,support',
            'is_active' => 'boolean',
            'is_universal' => 'boolean',
        ];
    }

    public static function validationMessages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol debe ser admin, producer, technician o support.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            'is_universal.boolean' => 'El indicador de productor universal debe ser verdadero o falso.',
        ];
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PRODUCER, self::ROLE_TECHNICIAN, self::ROLE_SUPPORT]);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the user is a producer.
     */
    public function isProducer(): bool
    {
        return $this->role === self::ROLE_PRODUCER;
    }

    /**
     * Check if the user is a universal producer.
     */
    public function isUniversalProducer(): bool
    {
        return $this->role === self::ROLE_PRODUCER && $this->is_universal;
    }

    /**
     * Check if the user is a technician.
     */
    public function isTechnician(): bool
    {
        return $this->role === self::ROLE_TECHNICIAN;
    }

    /**
     * Check if the user is support.
     */
    public function isSupport(): bool
    {
        return $this->role === self::ROLE_SUPPORT;
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

    /**
     * Productos universales
     */
    public function universalProducts()
    {
        return $this->hasMany(Product::class, 'creator_user_id')->where('is_universal', true);
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
