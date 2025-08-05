#!/usr/bin/env php
<?php

/**
 * Script independiente para actualizar tasas de cambio
 * Este script puede ejecutarse directamente con cron sin depender del scheduler de Laravel
 */

// Incluir el autoloader de Laravel
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\BcvScraperService;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ExchangeRateUpdater
{
    private $logFile;
    
    public function __construct()
    {
        $this->logFile = __DIR__ . '/../storage/logs/exchange_rate_updater.log';
    }
    
    /**
     * Ejecutar la actualización de tasas
     */
    public function run()
    {
        $this->log("=== Iniciando actualización de tasas de cambio ===");
        $this->log("Fecha: " . date('Y-m-d H:i:s'));
        
        try {
            // Método 1: Intentar usar la API interna primero
            $this->log("Intentando actualizar via API interna...");
            $success = $this->updateViaInternalApi();
            
            if (!$success) {
                // Método 2: Usar el scraper directamente
                $this->log("API interna falló, usando scraper directo...");
                $this->updateViaDirectScraper();
            }
            
            $this->log("=== Actualización completada exitosamente ===");
            
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            $this->log("Trace: " . $e->getTraceAsString());
            
            // Enviar notificación de error si es necesario
            $this->notifyError($e);
        }
    }
    
    /**
     * Actualizar via API interna
     */
    private function updateViaInternalApi(): bool
    {
        try {
            $baseUrl = env('APP_URL', 'http://localhost');
            $response = Http::timeout(30)->post($baseUrl . '/api/exchange-rates/force-update');
            
            if ($response->successful()) {
                $data = $response->json();
                $this->log("API interna exitosa: " . json_encode($data));
                return true;
            } else {
                $this->log("API interna falló con código: " . $response->status());
                return false;
            }
            
        } catch (Exception $e) {
            $this->log("Error en API interna: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar usando el scraper directamente
     */
    private function updateViaDirectScraper()
    {
        $scraper = new BcvScraperService();
        
        // Obtener tasas actuales de la base de datos
        $currentUsd = ExchangeRate::getLatestRate('USD');
        $currentEur = ExchangeRate::getLatestRate('EUR');
        
        $this->log("Tasas actuales - USD: " . ($currentUsd ? $currentUsd->rate : 'N/A') . 
                   ", EUR: " . ($currentEur ? $currentEur->rate : 'N/A'));
        
        // Obtener nuevas tasas
        $newRates = $scraper->fetchRates();
        
        if (empty($newRates)) {
            throw new Exception("No se pudieron obtener nuevas tasas del BCV");
        }
        
        $this->log("Nuevas tasas obtenidas: " . json_encode($newRates));
        
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
            $this->log("Se detectaron cambios en las tasas, actualizando...");
        } else {
            $this->log("No hay cambios en las tasas");
        }
    }
    
    /**
     * Verificar el estado de las tasas
     */
    public function checkRatesStatus()
    {
        $usdRate = ExchangeRate::getLatestRate('USD');
        $eurRate = ExchangeRate::getLatestRate('EUR');
        
        $status = [
            'timestamp' => date('Y-m-d H:i:s'),
            'usd' => $usdRate ? [
                'rate' => $usdRate->rate,
                'fetched_at' => $usdRate->fetched_at->format('Y-m-d H:i:s'),
                'hours_old' => $usdRate->fetched_at->diffInHours(now())
            ] : null,
            'eur' => $eurRate ? [
                'rate' => $eurRate->rate,
                'fetched_at' => $eurRate->fetched_at->format('Y-m-d H:i:s'),
                'hours_old' => $eurRate->fetched_at->diffInHours(now())
            ] : null
        ];
        
        $this->log("Estado actual de las tasas: " . json_encode($status, JSON_PRETTY_PRINT));
        
        return $status;
    }
    
    /**
     * Escribir en el log
     */
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        // Escribir al archivo de log
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // También mostrar en consola
        echo $logMessage;
    }
    
    /**
     * Notificar error (puedes personalizar esto)
     */
    private function notifyError($exception)
    {
        // Aquí puedes agregar notificaciones por email, Slack, etc.
        $this->log("NOTIFICACIÓN DE ERROR ENVIADA");
    }
}

// Ejecutar el script
if (php_sapi_name() === 'cli') {
    $updater = new ExchangeRateUpdater();
    
    // Verificar argumentos de línea de comandos
    $command = $argv[1] ?? 'update';
    
    switch ($command) {
        case 'update':
            $updater->run();
            break;
            
        case 'status':
            echo "=== Estado de las Tasas de Cambio ===\n";
            $status = $updater->checkRatesStatus();
            break;
            
        case 'help':
            echo "Uso: php update_exchange_rates.php [comando]\n";
            echo "Comandos disponibles:\n";
            echo "  update  - Actualizar las tasas de cambio (por defecto)\n";
            echo "  status  - Mostrar el estado actual de las tasas\n";
            echo "  help    - Mostrar esta ayuda\n";
            break;
            
        default:
            echo "Comando desconocido: $command\n";
            echo "Usa 'help' para ver los comandos disponibles.\n";
            exit(1);
    }
} 