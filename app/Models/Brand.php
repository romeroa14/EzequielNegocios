<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'is_active',
        'product_line_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Reglas de validación
    public static $rules = [
        'name' => 'required|string|max:255|unique:brands,name',
        'description' => 'nullable|string|max:255',
        'logo_url' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'product_line_id' => 'required|exists:product_lines,id'
    ];

    // Mensajes de error personalizados en español
    public static $messages = [
        'name.required' => 'El nombre de la marca es obligatorio',
        'name.string' => 'El nombre debe ser texto',
        'name.max' => 'El nombre no puede tener más de 255 caracteres',
        'name.unique' => 'Esta marca ya existe en el sistema',
        'description.string' => 'La descripción debe ser texto',
        'description.max' => 'La descripción no puede tener más de 255 caracteres',
        'logo_url.string' => 'La URL del logo debe ser texto',
        'logo_url.max' => 'La URL del logo no puede tener más de 255 caracteres',
        'product_line_id.required' => 'La línea de producto es obligatoria',
        'product_line_id.exists' => 'La línea de producto seleccionada no existe'
    ];

    // Relaciones
    public function productLine(): BelongsTo
    {
        return $this->belongsTo(ProductLine::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
} 