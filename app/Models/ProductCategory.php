<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255|unique:product_categories,name,' . $id,
            'description' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ];
    }

    public static function validationMessages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique' => 'Ya existe una categoría con este nombre.',
            'description.required' => 'La descripción es obligatoria.',
            'icon.max' => 'El icono no puede tener más de 50 caracteres.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.'
        ];
    }

    public function subcategories()
    {
        return $this->hasMany(ProductSubcategory::class, 'category_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductSubcategory::class);
    }
} 