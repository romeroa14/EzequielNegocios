<?php

namespace App\Models;

use App\Traits\HasProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    use HasProductImage;

    protected $appends = ['image_url'];

    protected $fillable = [
        'person_id',
        'category_id',
        'subcategory_id',
        'name',
        'description',
        'sku_base',
        'unit_type',
        'image',
        'seasonal_info',
        'is_active'
    ];

    protected $casts = [
        'seasonal_info' => 'array',
        'is_active' => 'boolean'
    ];

    public static function rules($id = null)
    {
        return [
            'person_id' => 'required|exists:people,id',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sku_base' => 'required|string|max:50|unique:products,sku_base,' . $id,
            'unit_type' => 'required|in:kg,ton,saco,caja,unidad',
            'image' => 'nullable|image|max:2048',
            'seasonal_info' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }

    public static function validationMessages()
    {
        return [
            'person_id.required' => 'El vendedor es obligatorio.',
            'person_id.exists' => 'El vendedor seleccionado no existe.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'subcategory_id.required' => 'La subcategoría es obligatoria.',
            'subcategory_id.exists' => 'La subcategoría seleccionada no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'sku_base.required' => 'El SKU base es obligatorio.',
            'sku_base.max' => 'El SKU base no puede tener más de 50 caracteres.',
            'sku_base.unique' => 'Este SKU base ya está en uso.',
            'unit_type.required' => 'El tipo de unidad es obligatorio.',
            'unit_type.in' => 'El tipo de unidad debe ser kg, ton, saco, caja o unidad.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no puede ser mayor a 2MB.',
            'seasonal_info.array' => 'La información estacional debe ser un arreglo.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.'
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class);
    }

    public function listings()
    {
        return $this->hasMany(ProductListing::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class);
    }
} 