<?php

namespace App\Console\Commands;

use App\Services\BcvScraperService;
use Illuminate\Console\Command;
use Exception;

class FetchBcvRates extends Command
{
    protected $signature = 'bcv:fetch-rates {currency?} {--all : Obtener todas las tasas disponibles}';
    protected $description = 'Obtiene las tasas de cambio actuales del BCV. Por defecto obtiene USD.';

    public function handle(BcvScraperService $scraper)
    {
        try {
            $currency = $this->argument('currency') ?? 'USD';
            $getAllRates = $this->option('all');

            $this->info('Obteniendo tasas del BCV...');
            
            if ($getAllRates) {
                $rates = $scraper->fetchRates();
                
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
                
                $this->info('Tasa obtenida exitosamente:');
                $this->line(sprintf(
                    "%s: %s Bs.",
                    str_pad($currency, 5, ' ', STR_PAD_RIGHT),
                    number_format($rate, 8, ',', '.')
                ));
            }
            
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error al obtener las tasas: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 