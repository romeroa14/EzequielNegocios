<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'product_category_id',
        'product_subcategory_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Reglas de validación
    public static $rules = [
        'name' => 'required|string|max:255|unique:product_lines,name',
        'description' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'product_category_id' => 'required|exists:product_categories,id',
        'product_subcategory_id' => 'required|exists:product_subcategories,id'
    ];

    // Mensajes de error personalizados en español
    public static $messages = [
        'name.required' => 'El nombre de la línea es obligatorio',
        'name.string' => 'El nombre debe ser texto',
        'name.max' => 'El nombre no puede tener más de 255 caracteres',
        'name.unique' => 'Esta línea ya existe en el sistema',
        'description.string' => 'La descripción debe ser texto',
        'description.max' => 'La descripción no puede tener más de 255 caracteres',
        'product_category_id.required' => 'La categoría es obligatoria',
        'product_category_id.exists' => 'La categoría seleccionada no existe',
        'product_subcategory_id.required' => 'La subcategoría es obligatoria',
        'product_subcategory_id.exists' => 'La subcategoría seleccionada no existe'
    ];

    // Relaciones
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
} 