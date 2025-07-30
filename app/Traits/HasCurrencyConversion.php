<?php

namespace App\Traits;

use App\Models\ExchangeRate;

trait HasCurrencyConversion
{
    public function getFormattedPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getBsPriceAttribute()
    {
        $rate = ExchangeRate::getLatestRate('USD');
        if (!$rate) {
            return null;
        }
        return $this->unit_price * $rate->rate;
    }

    public function getFormattedBsPriceAttribute()
    {
        $bsPrice = $this->bs_price;
        if (is_null($bsPrice)) {
            return 'Tasa no disponible';
        }
        return number_format($bsPrice, 2);
    }

    public function getCurrentRateAttribute()
    {
        $rate = ExchangeRate::getLatestRate('USD');
        return $rate ? number_format($rate->rate, 2) : null;
    }
} 