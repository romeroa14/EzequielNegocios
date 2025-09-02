<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BcvRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_code',
        'rate',
        'fetched_at',
        'source',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'fetched_at' => 'datetime',
    ];

    /**
     * Obtener la tasa más reciente para una moneda específica
     */
    public static function getLatestRate($currencyCode = 'USD')
    {
        return static::where('currency_code', strtoupper($currencyCode))
            ->latest('fetched_at')
            ->first();
    }

    /**
     * Obtener todas las tasas más recientes
     */
    public static function getLatestRates()
    {
        return static::selectRaw('currency_code, rate, MAX(fetched_at) as fetched_at')
            ->groupBy('currency_code', 'rate')
            ->get()
            ->keyBy('currency_code');
    }

    /**
     * Convertir un precio de VES a USD
     */
    public static function convertVesToUsd($vesAmount, $currencyCode = 'USD')
    {
        $rate = static::getLatestRate($currencyCode);
        if (!$rate) {
            return null;
        }
        
        return $vesAmount / $rate->rate;
    }

    /**
     * Convertir un precio de USD a VES
     */
    public static function convertUsdToVes($usdAmount, $currencyCode = 'USD')
    {
        $rate = static::getLatestRate($currencyCode);
        if (!$rate) {
            return null;
        }
        
        return $usdAmount * $rate->rate;
    }

    /**
     * Obtener la tasa formateada
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 2, ',', '.');
    }

    /**
     * Obtener la fecha formateada
     */
    public function getFormattedFetchedAtAttribute()
    {
        return $this->fetched_at->format('d/m/Y H:i');
    }
}
