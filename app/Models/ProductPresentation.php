<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPresentation extends Model
{
    use HasFactory;

    protected $table = 'product_presentations';

    protected $fillable = [
        'name',
        'description'
    ];

    // Reglas de validación
    public static $rules = [
        'name' => 'required|string|max:255|unique:products_presentation,name',
        'description' => 'required|string|max:255'
    ];

    // Mensajes de error personalizados en español
    public static $messages = [
        'name.required' => 'El nombre de la presentación es obligatorio',
        'name.string' => 'El nombre debe ser texto',
        'name.max' => 'El nombre no puede tener más de 255 caracteres',
        'name.unique' => 'Esta presentación ya existe en el sistema',
        'description.required' => 'La descripción es obligatoria',
        'description.string' => 'La descripción debe ser texto',
        'description.max' => 'La descripción no puede tener más de 255 caracteres'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 