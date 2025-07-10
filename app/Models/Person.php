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
        'state_id',
        'municipality_id',
        'parish_id',
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

    public static function rules($id = null)
    {
        $unique_email = $id ? 'unique:people,email,' . $id : 'unique:people,email';
        $unique_identification = $id ? 'unique:people,identification_number,' . $id : 'unique:people,identification_number';

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|' . $unique_email,
            'password' => $id ? 'nullable|min:8' : 'required|min:8',
            'identification_type' => 'required|in:V,E,J,G',
            'identification_number' => 'required|string|' . $unique_identification,
            'state_id' => 'required|exists:states,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'parish_id' => 'required|exists:parishes,id',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
            'role' => 'required|in:buyer,seller',
            'company_name' => 'required_if:identification_type,J,G|nullable|string|max:255',
            'company_rif' => 'required_if:identification_type,J,G|nullable|string|max:20',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public static function validationMessages()
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'identification_type.required' => 'El tipo de identificación es obligatorio.',
            'identification_type.in' => 'El tipo de identificación debe ser V, E, J o G.',
            'identification_number.required' => 'El número de identificación es obligatorio.',
            'identification_number.unique' => 'Este número de identificación ya está registrado.',
            'state_id.required' => 'El estado es obligatorio.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
            'municipality_id.required' => 'El municipio es obligatorio.',
            'municipality_id.exists' => 'El municipio seleccionado no es válido.',
            'parish_id.required' => 'La parroquia es obligatoria.',
            'parish_id.exists' => 'La parroquia seleccionada no es válida.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'address.required' => 'La dirección es obligatoria.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            'sector.max' => 'El sector no puede tener más de 255 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol debe ser comprador o vendedor.',
            'company_name.required_if' => 'El nombre de la empresa es obligatorio para personas jurídicas.',
            'company_name.max' => 'El nombre de la empresa no puede tener más de 255 caracteres.',
            'company_rif.required_if' => 'El RIF es obligatorio para personas jurídicas.',
            'company_rif.max' => 'El RIF no puede tener más de 20 caracteres.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            'is_verified.boolean' => 'La verificación debe ser verdadero o falso.',
        ];
    }

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
            self::ROLE_BUYER => 'catalog',
            self::ROLE_SELLER => 'seller.dashboard',
            default => 'home',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 