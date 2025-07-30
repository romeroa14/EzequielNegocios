<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Traits\HasListingImages;
use App\Traits\HasCurrencyConversion;

class ProductListing extends Model
{
    use HasFactory;
    use HasCurrencyConversion;

    protected $fillable = [
        'person_id',
        'product_id',
        'title',
        'description',
        'unit_price',
        'quality_grade',
        'harvest_date',
        'images',
        'product_presentation_id',
        'presentation_quantity',
        'state_id',
        'municipality_id',
        'parish_id',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'harvest_date' => 'datetime',
        'images' => 'array',
        'presentation_quantity' => 'decimal:2',
    ];

    protected $appends = [
        'images_url',
        'main_image_url',
        'all_images_url',
        'images_count',
        'formatted_presentation'
    ];

    /**
     * Verifica si el listing tiene imágenes
     */
    public function hasImages(): bool
    {
        return !empty($this->images) && is_array($this->images);
    }

    /**
     * Obtiene la URL de la primera imagen
     */
    public function getFirstImageUrlAttribute(): string
    {
        if ($this->hasImages()) {
            return asset('storage/' . $this->images[0]);
        }
        return asset('images/placeholder.png');
    }

    public static function rules($id = null)
    {
        return [
            'product_id' => 'required|exists:products,id',
            'person_id' => 'required|exists:people,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'quality_grade' => 'required|in:premium,standard,economic',
            'harvest_date' => 'required|date',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'product_presentation_id' => 'required|exists:product_presentations,id',
            'presentation_quantity' => 'required|numeric|min:0.01',
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
            'quality_grade.required' => 'La calidad es obligatoria.',
            'quality_grade.in' => 'La calidad debe ser premium, standard o economic.',
            'harvest_date.required' => 'La fecha de cosecha es obligatoria.',
            'harvest_date.date' => 'La fecha de cosecha debe ser una fecha válida.',
            'images.array' => 'Las imágenes deben ser un arreglo.',
            'images.*.image' => 'Los archivos deben ser imágenes.',
            'images.*.max' => 'Las imágenes no pueden ser mayores a 2MB.',
            'product_presentation_id.required' => 'La presentación es obligatoria.',
            'product_presentation_id.exists' => 'La presentación seleccionada no existe.',
            'presentation_quantity.required' => 'La cantidad es obligatoria.',
            'presentation_quantity.numeric' => 'La cantidad debe ser un número.',
            'presentation_quantity.min' => 'La cantidad debe ser mayor a 0.',
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function productPresentation(): BelongsTo
    {
        return $this->belongsTo(ProductPresentation::class);
    }

    public function getFormattedPresentationAttribute(): string
    {
        if (!$this->productPresentation) {
            return '';
        }

        return "{$this->presentation_quantity} {$this->productPresentation->unit_type}";
    }

    /**
     * Obtiene la ubicación formateada
     */
    public function getLocationAttribute(): string
    {
        $parts = [];
        
        if ($this->parish) {
            $parts[] = $this->parish->name;
        }
        if ($this->municipality) {
            $parts[] = $this->municipality->name;
        }
        if ($this->state) {
            $parts[] = $this->state->name;
        }
        
        return implode(', ', $parts);
    }

    /**
     * Obtiene la ubicación corta (solo estado y municipio)
     */
    public function getShortLocationAttribute(): string
    {
        $parts = [];
        
        if ($this->municipality) {
            $parts[] = $this->municipality->name;
        }
        if ($this->state) {
            $parts[] = $this->state->name;
        }
        
        return implode(', ', $parts);
    }
} 