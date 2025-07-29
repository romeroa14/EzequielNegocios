<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Services\BcvScraperService;
use Illuminate\Http\JsonResponse;
use Exception;

class ExchangeRateController extends Controller
{
    /**
     * Obtiene la última tasa para una moneda específica
     */
    public function getRate(string $currencyCode = 'USD'): JsonResponse
    {
        try {
            $rate = ExchangeRate::getLatestRate($currencyCode);
            
            if (!$rate) {
                return response()->json([
                    'success' => false,
                    'message' => "No hay tasa disponible para $currencyCode",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'currency_code' => $rate->currency_code,
                    'rate' => $rate->rate,
                    'fetched_at' => $rate->fetched_at->toIso8601String(),
                    'source' => $rate->source,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la tasa de cambio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene todas las tasas más recientes
     */
    public function getAllRates(): JsonResponse
    {
        try {
            $rates = ExchangeRate::getLatestRates();
            
            return response()->json([
                'success' => true,
                'data' => $rates,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las tasas de cambio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fuerza una actualización de las tasas
     */
    public function forceUpdate(BcvScraperService $scraper): JsonResponse
    {
        try {
            $rates = $scraper->fetchRates();
            
            return response()->json([
                'success' => true,
                'message' => 'Tasas actualizadas exitosamente',
                'data' => $rates,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar las tasas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 