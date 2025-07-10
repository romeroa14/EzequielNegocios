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
        'description',
        'unit_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Reglas de validación
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255|unique:product_presentations,name,' . $id,
            'description' => 'required|string|max:255',
            'unit_type' => 'required|in:kg,ton,paquete,saco,caja,unidad',
            'is_active' => 'boolean'
        ];
    }

    // Mensajes de error personalizados en español
    public static function validationMessages()
    {
        return [
            'name.required' => 'El nombre de la presentación es obligatorio',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'name.unique' => 'Esta presentación ya existe en el sistema',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no puede tener más de 255 caracteres',
            'unit_type.required' => 'El tipo de unidad es obligatorio',
            'is_active.boolean' => 'El estado debe ser verdadero o falso'
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 