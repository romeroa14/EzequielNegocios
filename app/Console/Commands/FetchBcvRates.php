<?php

namespace App\Console\Commands;

use App\Services\BcvScraperService;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;

class FetchBcvRates extends Command
{
    protected $signature = 'bcv:fetch-rates {currency?} {--all : Obtener todas las tasas disponibles}';
    protected $description = 'Obtiene las tasas de cambio actuales del BCV. Por defecto obtiene USD.';

    public function handle(BcvScraperService $scraper)
    {
        try {
            $currency = $this->argument('currency') ?? 'USD';
            $getAllRates = $this->option('all');

            Log::info('📊 COMANDO BCV INICIADO', [
                'currency' => $currency,
                'get_all_rates' => $getAllRates,
                'timestamp' => now(),
                'environment' => app()->environment()
            ]);

            $this->info('Obteniendo tasas del BCV...');
            
            if ($getAllRates) {
                $rates = $scraper->fetchRates();
                
                Log::info('📈 TASAS OBTENIDAS EXITOSAMENTE', [
                    'rates_count' => count($rates),
                    'rates' => $rates,
                    'timestamp' => now()
                ]);
                
                $this->info('Tasas obtenidas exitosamente:');
                foreach ($rates as $code => $rate) {
                    $this->line(sprintf(
                        "%s: %s Bs.",
                        str_pad($code, 5, ' ', STR_PAD_RIGHT),
                        number_format($rate, 8, ',', '.')
                    ));
                }
            } else {
                $currency = strtoupper($currency);
                $rate = $scraper->fetchRateForCurrency($currency);
                
                Log::info('📈 TASA INDIVIDUAL OBTENIDA', [
                    'currency' => $currency,
                    'rate' => $rate,
                    'timestamp' => now()
                ]);
                
                $this->info('Tasa obtenida exitosamente:');
                $this->line(sprintf(
                    "%s: %s Bs.",
                    str_pad($currency, 5, ' ', STR_PAD_RIGHT),
                    number_format($rate, 8, ',', '.')
                ));
            }
            
            Log::info('✅ COMANDO BCV COMPLETADO EXITOSAMENTE', [
                'timestamp' => now(),
                'status' => 'success'
            ]);
            
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('❌ COMANDO BCV FALLÓ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now(),
                'status' => 'failed'
            ]);
            
            $this->error('Error al obtener las tasas: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 