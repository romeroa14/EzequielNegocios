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
        'product_category_id',
        'product_subcategory_id',
        'product_line_id',
        'brand_id',
        'product_presentation_id',
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
            'product_category_id' => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'product_line_id' => 'required|exists:product_lines,id',
            'brand_id' => 'required|exists:brands,id',
            'product_presentation_id' => 'required|exists:product_presentations,id',
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
} 