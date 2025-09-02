<?php

namespace App\Models;

use App\Traits\HasProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    use HasProductImage;

    protected $appends = ['image_url', 'final_quantity'];

    protected $fillable = [
        'person_id',
        'product_category_id',
        'product_subcategory_id',
        'product_line_id',
        'brand_id',
        'product_presentation_id',
        'name',
        'description',
        'sku_base',
        'custom_quantity',
        'image',
        'seasonal_info',
        'is_active',
        'creator_user_id',
        'is_universal'
    ];

    protected $casts = [
        'seasonal_info' => 'array',
        'is_active' => 'boolean',
        'custom_quantity' => 'decimal:2',
        'is_universal' => 'boolean'
    ];

    public static function rules($id = null)
    {
        return [
            'person_id' => 'nullable|exists:people,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'product_line_id' => 'required|exists:product_lines,id',
            'brand_id' => 'required|exists:brands,id',
            'product_presentation_id' => 'required|exists:product_presentations,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku_base' => 'required|string|max:50|unique:products,sku_base,' . $id,
            'custom_quantity' => 'nullable|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
            'seasonal_info' => 'nullable|array',
            'is_active' => 'boolean',
            'creator_user_id' => 'nullable|exists:users,id',
            'is_universal' => 'boolean'
        ];
    }

    /**
     * Reglas de validación condicionales
     */
    public static function conditionalRules($data)
    {
        $rules = self::rules();
        
        // Si el producto es universal, person_id no es requerido
        if (isset($data['is_universal']) && $data['is_universal']) {
            $rules['person_id'] = 'nullable|exists:people,id';
        } else {
            // Si no es universal, person_id es requerido
            $rules['person_id'] = 'required|exists:people,id';
        }
        
        return $rules;
    }

    public static function validationMessages()
    {
        return [
            'person_id.required' => 'El vendedor es obligatorio.',
            'person_id.exists' => 'El vendedor seleccionado no existe.',
            'product_category_id.required' => 'La categoría es obligatoria.',
            'product_category_id.exists' => 'La categoría seleccionada no existe.',
            'product_subcategory_id.required' => 'La subcategoría es obligatoria.',
            'product_subcategory_id.exists' => 'La subcategoría seleccionada no existe.',
            'product_line_id.required' => 'La línea de producto es obligatoria.',
            'product_line_id.exists' => 'La línea de producto seleccionada no existe.',
            'brand_id.required' => 'La marca es obligatoria.',
            'brand_id.exists' => 'La marca seleccionada no existe.',
            'product_presentation_id.required' => 'La presentación es obligatoria.',
            'product_presentation_id.exists' => 'La presentación seleccionada no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'sku_base.required' => 'El SKU base es obligatorio.',
            'sku_base.max' => 'El SKU base no puede tener más de 50 caracteres.',
            'sku_base.unique' => 'Este SKU base ya está en uso.',
            'custom_quantity.numeric' => 'La cantidad personalizada debe ser un número.',
            'custom_quantity.min' => 'La cantidad personalizada debe ser mayor a 0.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no puede ser mayor a 2MB.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            'creator_user_id.exists' => 'El usuario creador no existe.',
            'is_universal.boolean' => 'El campo universal debe ser verdadero o falso.'
        ];
    }

    // Relaciones
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function productSubcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    public function productLine(): BelongsTo
    {
        return $this->belongsTo(ProductLine::class, 'product_line_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function productPresentation(): BelongsTo
    {
        return $this->belongsTo(ProductPresentation::class, 'product_presentation_id');
    }

    public function listings()
    {
        return $this->hasMany(ProductListing::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function marketPrices(): HasMany
    {
        return $this->hasMany(MarketPrice::class);
    }

    public function marketPriceHistory(): HasMany
    {
        return $this->hasMany(MarketPriceHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    // Accessors
    public function getFinalQuantityAttribute()
    {
        return $this->custom_quantity ?? $this->productPresentation->quantity;
    }

    // Helper para obtener la descripción completa de la cantidad
    public function getQuantityDescription()
    {
        $quantity = $this->final_quantity;
        $unit = $this->productPresentation->unit_type;
        $presentationName = $this->productPresentation->name;
        
        if ($this->custom_quantity) {
            return "{$quantity} {$unit} ({$presentationName})";
        }
        
        return $presentationName;
    }

    // Scopes
    public function scopeUniversal($query)
    {
        return $query->where('is_universal', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 