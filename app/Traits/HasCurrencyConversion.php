<?php

namespace App\Traits;

use App\Models\ExchangeRate;

trait HasCurrencyConversion
{
    /**
     * Obtiene el precio siempre en USD (conversión automática si está en VES)
     */
    public function getUsdPriceAttribute()
    {
        if ($this->currency_type === 'USD') {
            return $this->unit_price;
        }
        
        // Si está en VES, convertir a USD
        $rate = ExchangeRate::getLatestRate('USD');
        if (!$rate) {
            return $this->unit_price; // Fallback si no hay tasa
        }
        
        return $this->unit_price / $rate->rate;
    }

    /**
     * Obtiene el precio siempre en VES/Bs.D (conversión automática si está en USD)
     */
    public function getBsPriceAttribute()
    {
        if ($this->currency_type === 'VES') {
            return $this->unit_price;
        }
        
        // Si está en USD, convertir a VES
        $rate = ExchangeRate::getLatestRate('USD');
        if (!$rate) {
            return null;
        }
        
        return $this->unit_price * $rate->rate;
    }

    /**
     * Precio formateado en la moneda original
     */
    public function getFormattedPriceAttribute()
    {
        $symbol = $this->currency_type === 'USD' ? '$' : 'Bs.D ';
        return $symbol . number_format($this->unit_price, 2);
    }

    /**
     * Precio formateado en USD
     */
    public function getFormattedUsdPriceAttribute()
    {
        return '$' . number_format($this->usd_price, 2);
    }

    /**
     * Precio formateado en Bs.D
     */
    public function getFormattedBsPriceAttribute()
    {
        $bsPrice = $this->bs_price;
        if (is_null($bsPrice)) {
            return 'Tasa no disponible';
        }
        return 'Bs.D ' . number_format($bsPrice, 2);
    }

    public function getCurrentRateAttribute()
    {
        $rate = ExchangeRate::getLatestRate('USD');
        return $rate ? number_format($rate->rate, 2) : null;
    }

    public static function getUsdRate()
    {
        $rate = ExchangeRate::getLatestRate('USD');
        return [
            'rate' => $rate ? number_format($rate->rate, 2) : null,
            'fetched_at' => $rate ? $rate->fetched_at->format('d/m/Y h:i A') : null
        ];
    }

    public static function getEurRate()
    {
        $rate = ExchangeRate::getLatestRate('EUR');
        return [
            'rate' => $rate ? number_format($rate->rate, 2) : null,
            'fetched_at' => $rate ? $rate->fetched_at->format('d/m/Y h:i A') : null
        ];
    }
} 