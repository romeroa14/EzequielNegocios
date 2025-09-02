<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\FetchBcvRates;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Webhook para actualizar tasas del BCV
     * Puede ser llamado por servicios externos como cron-job.org, cronhooks.io, etc.
     */
    public function updateBcvRates(Request $request)
    {
        try {
            Log::info('🔄 WEBHOOK BCV INICIADO', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'headers' => $request->headers->all()
            ]);

            // Validar el webhook (opcional, para seguridad)
            $validator = Validator::make($request->all(), [
                'secret' => 'sometimes|string',
                'currency' => 'sometimes|string|in:USD,EUR,CNY,TRY,RUB,all'
            ]);

            if ($validator->fails()) {
                Log::warning('❌ WEBHOOK BCV VALIDACIÓN FALLIDA', [
                    'errors' => $validator->errors(),
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Invalid request'], 400);
            }

            // Verificar secret si está configurado
            $expectedSecret = config('services.bcv.webhook_secret');
            if ($expectedSecret && $request->input('secret') !== $expectedSecret) {
                Log::warning('❌ WEBHOOK BCV SECRET INVÁLIDO', [
                    'ip' => $request->ip(),
                    'provided_secret' => $request->input('secret')
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Ejecutar el comando para obtener las tasas
            $currency = $request->input('currency', 'all');
            $command = $currency === 'all' ? 'bcv:fetch-rates --all' : "bcv:fetch-rates {$currency}";
            
            Log::info('🚀 WEBHOOK BCV EJECUTANDO COMANDO', [
                'command' => $command,
                'currency' => $currency
            ]);

            // Ejecutar el comando de forma síncrona
            $exitCode = Artisan::call('bcv:fetch-rates', [
                'currency' => $currency === 'all' ? null : $currency,
                '--all' => $currency === 'all'
            ]);

            if ($exitCode === 0) {
                Log::info('✅ WEBHOOK BCV COMPLETADO EXITOSAMENTE', [
                    'command' => $command,
                    'exit_code' => $exitCode,
                    'timestamp' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'BCV rates updated successfully',
                    'command' => $command,
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                Log::error('❌ WEBHOOK BCV COMANDO FALLÓ', [
                    'command' => $command,
                    'exit_code' => $exitCode,
                    'timestamp' => now()
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Command execution failed',
                    'exit_code' => $exitCode
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('❌ WEBHOOK BCV ERROR NO CONTROLADO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook de salud para verificar que el sistema esté funcionando
     */
    public function healthCheck(Request $request)
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'version' => config('app.version', '1.0.0')
        ]);
    }

    /**
     * Webhook para limpiar tasas antiguas del BCV
     */
    public function cleanupBcvRates(Request $request)
    {
        try {
            Log::info('🧹 WEBHOOK LIMPIEZA BCV INICIADO', [
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            // Ejecutar comando de limpieza (si existe)
            $exitCode = Artisan::call('bcv:cleanup-old-rates');

            Log::info('✅ WEBHOOK LIMPIEZA BCV COMPLETADO', [
                'exit_code' => $exitCode,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'BCV rates cleanup completed',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ WEBHOOK LIMPIEZA BCV ERROR', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
