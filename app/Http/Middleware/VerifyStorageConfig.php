<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VerifyStorageConfig
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production')) {
            try {
                $config = config('filesystems.disks.r2');
                Log::info('Configuración de R2', [
                    'bucket' => $config['bucket'] ?? 'no-bucket',
                    'url' => $config['url'] ?? 'no-url',
                    'endpoint' => $config['endpoint'] ?? 'no-endpoint',
                    'key_exists' => isset($config['key']),
                    'secret_exists' => isset($config['secret']),
                ]);

                // Verificar que el disco existe y está configurado
                $disk = Storage::disk('r2');
                Log::info('Disco R2 inicializado correctamente');

            } catch (\Exception $e) {
                Log::error('Error en configuración de almacenamiento', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $next($request);
    }
} 