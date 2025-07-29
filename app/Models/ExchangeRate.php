<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_code',
        'rate',
        'source',
        'fetched_at',
        'is_valid',
        'error_message',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'fetched_at' => 'datetime',
        'is_valid' => 'boolean',
    ];

    /**
     * Obtiene la última tasa válida para una moneda
     */
    public static function getLatestRate(string $currencyCode = 'USD'): ?self
    {
        return static::where('currency_code', $currencyCode)
            ->where('is_valid', true)
            ->latest('fetched_at')
            ->first();
    }

    /**
     * Obtiene todas las tasas válidas más recientes
     */
    public static function getLatestRates(): array
    {
        return static::where('is_valid', true)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('exchange_rates')
                    ->where('is_valid', true)
                    ->groupBy('currency_code');
            })
            ->get()
            ->keyBy('currency_code')
            ->toArray();
    }

    /**
     * Limpia los registros antiguos cuando hay un cambio de tasa
     */
    public static function cleanOldRates(array $newRates): void
    {
        try {
            DB::beginTransaction();

            foreach ($newRates as $currencyCode => $rate) {
                // Obtener la última tasa válida para la moneda
                $lastRate = self::where('currency_code', $currencyCode)
                    ->where('is_valid', true)
                    ->latest('fetched_at')
                    ->first();

                // Si hay un cambio en la tasa o no hay tasa previa
                if (!$lastRate || $lastRate->rate != $rate) {
                    // Eliminar todos los registros anteriores para esta moneda
                    self::where('currency_code', $currencyCode)
                        ->delete();
                    
                    Log::info("Registros antiguos eliminados para {$currencyCode} debido a cambio de tasa", [
                        'old_rate' => $lastRate?->rate,
                        'new_rate' => $rate
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al limpiar registros antiguos: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica si la tasa ha cambiado
     */
    public static function hasRateChanged(string $currencyCode, float $newRate): bool
    {
        $lastRate = self::where('currency_code', $currencyCode)
            ->where('is_valid', true)
            ->latest('fetched_at')
            ->first();

        return !$lastRate || $lastRate->rate != $newRate;
    }
} 