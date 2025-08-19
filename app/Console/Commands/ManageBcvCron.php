<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageBcvCron extends Command
{
    protected $signature = 'bcv:cron {action : install|check|remove|test|logs}';
    protected $description = 'Gestiona el cronjob para obtener tasas BCV cada 12 horas';

    public function handle()
    {
        $action = $this->argument('action');
        $projectDir = base_path();
        $logFile = '/var/log/bcv_rates.log';

        Log::info('🔄 GESTIONANDO CRONJOB BCV', [
            'action' => $action,
            'project_dir' => $projectDir,
            'timestamp' => now()
        ]);

        switch ($action) {
            case 'install':
                $this->installCronjob($projectDir, $logFile);
                break;
            case 'check':
                $this->checkCronjob();
                break;
            case 'remove':
                $this->removeCronjob();
                break;
            case 'test':
                $this->testCommand();
                break;
            case 'logs':
                $this->showLogs($logFile);
                break;
            default:
                $this->error("Acción no válida: $action");
                $this->info("Acciones disponibles: install, check, remove, test, logs");
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function installCronjob($projectDir, $logFile)
    {
        $this->info('📅 Instalando cronjob para tasas BCV...');

        // Crear archivo de log si no existe
        if (!file_exists($logFile)) {
            exec("sudo touch $logFile");
            exec("sudo chown www-data:www-data $logFile");
            exec("sudo chmod 644 $logFile");
            $this->info("✅ Archivo de log creado: $logFile");
        }

        // Crear el cronjob
        $cronJob = "0 */12 * * * cd $projectDir && php artisan bcv:fetch-rates --all >> $logFile 2>&1";
        
        // Obtener cronjobs existentes
        $existingCrons = shell_exec('crontab -l 2>/dev/null') ?: '';
        
        // Verificar si ya existe el cronjob
        if (strpos($existingCrons, 'bcv:fetch-rates') !== false) {
            $this->warn('⚠️ El cronjob ya está instalado');
            return;
        }

        // Crear archivo temporal con el nuevo cronjob
        $tempFile = tempnam(sys_get_temp_dir(), 'cron');
        file_put_contents($tempFile, $existingCrons . "\n# BCV Rates Cronjob - Instalado el " . now() . "\n$cronJob\n");

        // Instalar el cronjob
        exec("crontab $tempFile");
        unlink($tempFile);

        $this->info('✅ Cronjob instalado exitosamente');
        $this->info('📋 El comando se ejecutará cada 12 horas');
        $this->info('📝 Los logs se guardarán en: ' . $logFile);

        Log::info('✅ CRONJOB BCV INSTALADO', [
            'cronjob' => $cronJob,
            'timestamp' => now()
        ]);
    }

    private function checkCronjob()
    {
        $this->info('🔍 Verificando cronjobs instalados...');
        
        $crons = shell_exec('crontab -l 2>/dev/null');
        
        if ($crons) {
            $this->info('Cronjobs actuales:');
            $this->line($crons);
            
            if (strpos($crons, 'bcv:fetch-rates') !== false) {
                $this->info('✅ Cronjob BCV encontrado');
            } else {
                $this->warn('⚠️ Cronjob BCV no encontrado');
            }
        } else {
            $this->warn('No hay cronjobs instalados');
        }
    }

    private function removeCronjob()
    {
        $this->info('🗑️ Removiendo cronjob BCV...');
        
        $crons = shell_exec('crontab -l 2>/dev/null') ?: '';
        
        if (strpos($crons, 'bcv:fetch-rates') !== false) {
            // Remover líneas que contengan bcv:fetch-rates
            $lines = explode("\n", $crons);
            $filteredLines = array_filter($lines, function($line) {
                return strpos($line, 'bcv:fetch-rates') === false && !empty(trim($line));
            });
            
            $tempFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tempFile, implode("\n", $filteredLines) . "\n");
            
            exec("crontab $tempFile");
            unlink($tempFile);
            
            $this->info('✅ Cronjob BCV removido');
        } else {
            $this->warn('⚠️ Cronjob BCV no encontrado');
        }
    }

    private function testCommand()
    {
        $this->info('🧪 Probando el comando BCV...');
        
        try {
            $this->call('bcv:fetch-rates', ['--all' => true]);
            $this->info('✅ Comando BCV funcionando correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error al ejecutar el comando BCV: ' . $e->getMessage());
        }
    }

    private function showLogs($logFile)
    {
        $this->info('📋 Mostrando últimos logs...');
        
        if (file_exists($logFile)) {
            $logs = shell_exec("tail -20 $logFile");
            if ($logs) {
                $this->line($logs);
            } else {
                $this->warn('El archivo de log está vacío');
            }
        } else {
            $this->warn("No se encontró el archivo de log: $logFile");
        }
    }
}
