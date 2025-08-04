<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ExchangeRate;

class CheckSchedulerStatus extends Command
{
    protected $signature = 'scheduler:status';
    protected $description = 'Verifica el estado del scheduler y las tasas de cambio';

    public function handle()
    {
        $this->info('🔍 VERIFICANDO ESTADO DEL SISTEMA');
        $this->newLine();
        
        // Información del entorno
        $this->info('📊 INFORMACIÓN DEL ENTORNO:');
        $this->line('Environment: ' . app()->environment());
        $this->line('Timezone: ' . config('app.timezone'));
        $this->line('Current Time: ' . now());
        $this->newLine();
        
        // Verificar tasas en la base de datos
        $this->info('💰 TASAS EN BASE DE DATOS:');
        $rates = ExchangeRate::getLatestRates();
        
        if (empty($rates)) {
            $this->error('❌ No hay tasas en la base de datos');
            Log::warning('🚨 SCHEDULER STATUS: No hay tasas en la base de datos', [
                'timestamp' => now(),
                'environment' => app()->environment()
            ]);
        } else {
            foreach ($rates as $currency => $rate) {
                $this->line("$currency: {$rate['rate']} Bs. (Actualizada: {$rate['fetched_at']})");
            }
            
            Log::info('✅ SCHEDULER STATUS: Tasas encontradas en BD', [
                'rates_count' => count($rates),
                'timestamp' => now(),
                'environment' => app()->environment()
            ]);
        }
        
        $this->newLine();
        
        // Log de estado del scheduler
        Log::info('🔍 SCHEDULER STATUS CHECK EJECUTADO', [
            'timestamp' => now(),
            'environment' => app()->environment(),
            'rates_in_db' => count($rates),
            'command' => 'scheduler:status'
        ]);
        
        $this->info('✅ Verificación completada. Revisa los logs para más detalles.');
        
        return Command::SUCCESS;
    }
}
