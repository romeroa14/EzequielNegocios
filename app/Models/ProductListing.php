<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasListingImages;

class ProductListing extends Model
{
    use HasFactory, HasListingImages;

    protected $fillable = [
        'person_id',
        'product_id',
        'title',
        'description',
        'unit_price',
        'quantity_available',
        'quality_grade',
        'harvest_date',
        'images',
        'state_id',
        'municipality_id',
        'parish_id',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity_available' => 'integer',
        'harvest_date' => 'datetime',
        'images' => 'array',
    ];

    public static function rules($id = null)
    {
        return [
            'product_id' => 'required|exists:products,id',
            'person_id' => 'required|exists:people,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'quality_grade' => 'required|in:premium,standard,economic',
            'harvest_date' => 'required|date',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'state_id' => 'required|exists:states,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'parish_id' => 'required|exists:parishes,id',
            'status' => 'required|in:active,pending,sold_out,inactive'
        ];
    }

    public static function validationMessages()
    {
        return [
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.exists' => 'El producto seleccionado no existe.',
            'person_id.required' => 'El vendedor es obligatorio.',
            'person_id.exists' => 'El vendedor seleccionado no existe.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'unit_price.required' => 'El precio unitario es obligatorio.',
            'unit_price.numeric' => 'El precio unitario debe ser un número.',
            'unit_price.min' => 'El precio unitario debe ser mayor o igual a 0.',
            'quantity_available.required' => 'La cantidad disponible es obligatoria.',
            'quantity_available.integer' => 'La cantidad disponible debe ser un número entero.',
            'quantity_available.min' => 'La cantidad disponible debe ser mayor o igual a 0.',
            'quality_grade.required' => 'La calidad es obligatoria.',
            'quality_grade.in' => 'La calidad debe ser premium, standard o economic.',
            'harvest_date.required' => 'La fecha de cosecha es obligatoria.',
            'harvest_date.date' => 'La fecha de cosecha debe ser una fecha válida.',
            'images.array' => 'Las imágenes deben ser un arreglo.',
            'images.*.image' => 'Los archivos deben ser imágenes.',
            'images.*.max' => 'Las imágenes no pueden ser mayores a 2MB.',
            'state_id.required' => 'El estado es obligatorio.',
            'state_id.exists' => 'El estado seleccionado no existe.',
            'municipality_id.required' => 'La municipalidad es obligatoria.',
            'municipality_id.exists' => 'La municipalidad seleccionada no existe.',
            'parish_id.required' => 'El municipio es obligatorio.',
            'parish_id.exists' => 'El municipio seleccionado no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active, pending, sold_out o inactive.'
    ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
} 