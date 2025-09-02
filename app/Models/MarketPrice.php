<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'currency',
        'price_date',
        'notes',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con la persona que actualizó el precio
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'updated_by');
    }

    /**
     * Scope para precios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para precios de una fecha específica
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('price_date', $date);
    }

    /**
     * Scope para precios más recientes
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('price_date', 'desc');
    }

    /**
     * Obtener el precio más reciente para un producto
     */
    public static function getLatestPriceForProduct($productId)
    {
        return static::where('product_id', $productId)
            ->active()
            ->latest()
            ->first();
    }

    /**
     * Obtener precios de una semana específica
     */
    public static function getPricesForWeek($startDate, $endDate = null)
    {
        $query = static::with(['product', 'updatedBy'])
            ->active()
            ->whereBetween('price_date', [$startDate, $endDate ?? $startDate])
            ->orderBy('price_date', 'desc')
            ->orderBy('product_id');

        return $query->get();
    }

    /**
     * Formatear precio para mostrar
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Formatear precio USD para mostrar
     */
    public function getFormattedPriceUsdAttribute()
    {
        if (isset($this->price_usd)) {
            return '$ ' . number_format($this->price_usd, 2, ',', '.');
        }
        return '-';
    }

    /**
     * Formatear precio VES equivalente para mostrar
     */
    public function getFormattedPriceVesEquivalentAttribute()
    {
        if (isset($this->price_ves_equivalent)) {
            return 'Bs. ' . number_format($this->price_ves_equivalent, 2, ',', '.');
        }
        return '-';
    }

    /**
     * Obtener conversión bidireccional
     */
    public function getConversionDisplayAttribute()
    {
        if ($this->currency === 'VES' && isset($this->price_usd)) {
            return [
                'original' => $this->formatted_price,
                'conversion' => '$ ' . number_format($this->price_usd, 2, ',', '.'),
                'rate_info' => '1 USD = Bs. ' . number_format($this->exchange_rate ?? 0, 2, ',', '.')
            ];
        } elseif ($this->currency === 'USD' && isset($this->price_ves_equivalent)) {
            return [
                'original' => $this->formatted_price,
                'conversion' => 'Bs. ' . number_format($this->price_ves_equivalent, 2, ',', '.'),
                'rate_info' => '1 USD = Bs. ' . number_format($this->exchange_rate ?? 0, 2, ',', '.')
            ];
        }
        return null;
    }

    /**
     * Obtener el nombre del producto
     */
    public function getProductNameAttribute()
    {
        return $this->product->name ?? 'Producto no encontrado';
    }

    /**
     * Obtener el nombre de quien actualizó
     */
    public function getUpdatedByNameAttribute()
    {
        return $this->updatedBy->full_name ?? 'Sistema';
    }
}
