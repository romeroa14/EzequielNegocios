<?php

namespace App\Handlers;

use App\Services\BcvScraperService;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRatesHandler
{
    public function __invoke($event, $context)
    {
        Log::info('=== Iniciando actualización automática de tasas BCV (Vapor) ===');
        
        try {
            $scraper = new BcvScraperService();
            
            // Obtener tasas actuales
            $currentUsd = ExchangeRate::getLatestRate('USD');
            $currentEur = ExchangeRate::getLatestRate('EUR');
            
            Log::info('Tasas actuales', [
                'usd' => $currentUsd?->rate,
                'eur' => $currentEur?->rate
            ]);
            
            // Obtener nuevas tasas
            $newRates = $scraper->fetchRates();
            
            if (empty($newRates)) {
                Log::warning('No se pudieron obtener nuevas tasas del BCV');
                return [
                    'statusCode' => 500,
                    'body' => json_encode(['error' => 'No se pudieron obtener tasas'])
                ];
            }
            
            Log::info('Nuevas tasas obtenidas', $newRates);
            
            // Verificar si hay cambios
            $hasChanges = false;
            foreach ($newRates as $currencyCode => $rate) {
                $current = ExchangeRate::getLatestRate($currencyCode);
                if (!$current || $current->rate != $rate) {
                    $hasChanges = true;
                    break;
                }
            }
            
            if ($hasChanges) {
                Log::info('Se detectaron cambios en las tasas, actualizando base de datos...');
                
                // Las tasas se guardan automáticamente en fetchRates()
                Log::info('Tasas actualizadas exitosamente');
                
                return [
                    'statusCode' => 200,
                    'body' => json_encode([
                        'success' => true,
                        'message' => 'Tasas actualizadas',
                        'rates' => $newRates
                    ])
                ];
            } else {
                Log::info('No hay cambios en las tasas');
                
                return [
                    'statusCode' => 200,
                    'body' => json_encode([
                        'success' => true,
                        'message' => 'Sin cambios en las tasas',
                        'rates' => $newRates
                    ])
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error actualizando tasas BCV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'statusCode' => 500,
                'body' => json_encode([
                    'error' => 'Error interno',
                    'message' => $e->getMessage()
                ])
            ];
        }
    }
} 