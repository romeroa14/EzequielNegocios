<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function rules($id = null)
    {
        return [
            'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255|unique:product_subcategories,name,' . $id . ',id,product_category_id,' . request('product_category_id'),
            'description' => 'required|string',
            'is_active' => 'boolean'
        ];
    }

    public static function validationMessages()
    {
        return [
            'product_category_id.required' => 'La categoría es obligatoria.',
            'product_category_id.exists' => 'La categoría seleccionada no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique' => 'Ya existe una subcategoría con este nombre en la categoría seleccionada.',
            'description.required' => 'La descripción es obligatoria.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.'
        ];
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_subcategory_id');
    }
}
