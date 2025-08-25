<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetupLaravelCloudCron extends Command
{
    protected $signature = 'laravel-cloud:setup-cron {action : install|check|remove|test}';
    protected $description = 'Configura el cronjob para Laravel Cloud (producción)';

    public function handle()
    {
        $action = $this->argument('action');

        Log::info('🔄 CONFIGURANDO CRONJOB PARA LARAVEL CLOUD', [
            'action' => $action,
            'environment' => app()->environment(),
            'timestamp' => now()
        ]);

        switch ($action) {
            case 'install':
                $this->installCronjob();
                break;
            case 'check':
                $this->checkCronjob();
                break;
            case 'remove':
                $this->removeCronjob();
                break;
            case 'test':
                $this->testScheduler();
                break;
            default:
                $this->error("Acción no válida: $action");
                $this->info("Acciones disponibles: install, check, remove, test");
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function installCronjob()
    {
        $this->info('📅 Configurando cronjob para Laravel Cloud...');
        
        // En Laravel Cloud, solo necesitamos un cronjob que ejecute el scheduler cada minuto
        $cronJob = "* * * * * cd " . base_path() . " && php artisan schedule:run >> /dev/null 2>&1";
        
        // Obtener cronjobs existentes
        $existingCrons = shell_exec('crontab -l 2>/dev/null') ?: '';
        
        // Verificar si ya existe el cronjob del scheduler
        if (strpos($existingCrons, 'schedule:run') !== false) {
            $this->warn('⚠️ El cronjob del scheduler ya está instalado');
            $this->info('El scheduler de Laravel ya está configurado para ejecutar:');
            $this->info('- Comando BCV cada hora');
            $this->info('- Backup diario a las 7:00 AM');
            $this->info('- Heartbeat cada 10 minutos');
            return;
        }

        // Crear archivo temporal con el nuevo cronjob
        $tempFile = tempnam(sys_get_temp_dir(), 'cron');
        file_put_contents($tempFile, $existingCrons . "\n# Laravel Scheduler - Instalado el " . now() . "\n$cronJob\n");

        // Instalar el cronjob
        exec("crontab $tempFile");
        unlink($tempFile);

        $this->info('✅ Cronjob del scheduler instalado exitosamente');
        $this->info('📋 El scheduler se ejecutará cada minuto');
        $this->info('🔄 Comandos programados:');
        $this->info('   - BCV Rates: cada hora');
        $this->info('   - Backup diario: 7:00 AM');
        $this->info('   - Heartbeat: cada 10 minutos');

        Log::info('✅ CRONJOB LARAVEL CLOUD INSTALADO', [
            'cronjob' => $cronJob,
            'timestamp' => now()
        ]);
    }

    private function checkCronjob()
    {
        $this->info('🔍 Verificando cronjobs en Laravel Cloud...');
        
        $crons = shell_exec('crontab -l 2>/dev/null');
        
        if ($crons) {
            $this->info('Cronjobs actuales:');
            $this->line($crons);
            
            if (strpos($crons, 'schedule:run') !== false) {
                $this->info('✅ Cronjob del scheduler encontrado');
                $this->info('📋 Comandos programados en app/Console/Kernel.php:');
                $this->info('   - update-exchange-rates: cada hora');
                $this->info('   - daily-rates-backup: 7:00 AM');
                $this->info('   - scheduler-heartbeat: cada 10 minutos');
            } else {
                $this->warn('⚠️ Cronjob del scheduler no encontrado');
                $this->info('Ejecuta: php artisan laravel-cloud:setup-cron install');
            }
        } else {
            $this->warn('No hay cronjobs instalados');
            $this->info('Ejecuta: php artisan laravel-cloud:setup-cron install');
        }
    }

    private function removeCronjob()
    {
        $this->info('🗑️ Removiendo cronjob del scheduler...');
        
        $crons = shell_exec('crontab -l 2>/dev/null') ?: '';
        
        if (strpos($crons, 'schedule:run') !== false) {
            // Remover líneas que contengan schedule:run
            $lines = explode("\n", $crons);
            $filteredLines = array_filter($lines, function($line) {
                return strpos($line, 'schedule:run') === false && !empty(trim($line));
            });
            
            $tempFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tempFile, implode("\n", $filteredLines) . "\n");
            
            exec("crontab $tempFile");
            unlink($tempFile);
            
            $this->info('✅ Cronjob del scheduler removido');
        } else {
            $this->warn('⚠️ Cronjob del scheduler no encontrado');
        }
    }

    private function testScheduler()
    {
        $this->info('🧪 Probando el scheduler de Laravel...');
        
        try {
            // Ejecutar el scheduler manualmente
            $this->call('schedule:run');
            $this->info('✅ Scheduler ejecutado correctamente');
            
            // Verificar los logs recientes
            $this->info('📋 Verificando logs recientes...');
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                $logs = shell_exec("tail -10 $logFile | grep -E '(HEARTBEAT|BCV|TASAS)'");
                if ($logs) {
                    $this->line($logs);
                } else {
                    $this->warn('No se encontraron logs recientes del scheduler');
                }
            } else {
                $this->warn('No se encontró el archivo de log');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error al ejecutar el scheduler: ' . $e->getMessage());
        }
    }
}
