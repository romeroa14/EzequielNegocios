<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These schedules are used to run the console commands.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Actualizar tasas de cambio cada hora
        $schedule->call(function () {
            Log::info('🚀 INICIANDO actualización automática de tasas de cambio', [
                'timestamp' => now(),
                'environment' => app()->environment(),
                'scheduled_task' => 'hourly_rates_update'
            ]);
            
            try {
                Artisan::call('bcv:fetch-rates', ['--all' => true]);
                
                Log::info('✅ ÉXITO: Tasas de cambio actualizadas automáticamente', [
                    'timestamp' => now(),
                    'scheduled_task' => 'hourly_rates_update',
                    'status' => 'success'
                ]);
            } catch (\Exception $e) {
                Log::error('❌ ERROR: Falló la actualización automática de tasas', [
                    'timestamp' => now(),
                    'scheduled_task' => 'hourly_rates_update',
                    'error' => $e->getMessage(),
                    'status' => 'failed'
                ]);
            }
        })->hourly()->name('update-exchange-rates');
        
        // Backup diario con logs detallados
        $schedule->call(function () {
            Log::info('🌅 INICIANDO backup diario de tasas de cambio', [
                'timestamp' => now(),
                'environment' => app()->environment(),
                'scheduled_task' => 'daily_rates_backup'
            ]);
            
            try {
                Artisan::call('bcv:fetch-rates', ['--all' => true]);
                
                Log::info('✅ ÉXITO: Backup diario de tasas completado', [
                    'timestamp' => now(),
                    'scheduled_task' => 'daily_rates_backup',
                    'status' => 'success'
                ]);
            } catch (\Exception $e) {
                Log::error('❌ ERROR: Falló el backup diario de tasas', [
                    'timestamp' => now(),
                    'scheduled_task' => 'daily_rates_backup',
                    'error' => $e->getMessage(),
                    'status' => 'failed'
                ]);
            }
        })->dailyAt('07:00')->name('daily-rates-backup');
        
        // Log de heartbeat cada 10 minutos para verificar que el scheduler funciona
        $schedule->call(function () {
            Log::info('💓 HEARTBEAT: Scheduler funcionando correctamente', [
                'timestamp' => now(),
                'environment' => app()->environment(),
                'scheduled_task' => 'scheduler_heartbeat'
            ]);
        })->everyTenMinutes()->name('scheduler-heartbeat');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 