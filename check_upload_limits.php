<?php
/**
 * Script para verificar límites de subida de archivos
 * Ejecutar en producción para diagnosticar problemas
 */

// Bootstrap Laravel si está disponible
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    
    if (file_exists('bootstrap/app.php')) {
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }
}

echo "🔍 DIAGNÓSTICO DE LÍMITES DE SUBIDA\n";
echo "=====================================\n\n";

// Configuración PHP
echo "📋 CONFIGURACIÓN PHP:\n";
echo "- upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "- post_max_size: " . ini_get('post_max_size') . "\n";
echo "- max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "- max_execution_time: " . ini_get('max_execution_time') . " segundos\n";
echo "- max_input_time: " . ini_get('max_input_time') . " segundos\n";
echo "- memory_limit: " . ini_get('memory_limit') . "\n";

echo "\n";

// Configuración Laravel
if (function_exists('config')) {
    echo "📋 CONFIGURACIÓN LARAVEL:\n";
    echo "- APP_ENV: " . config('app.env') . "\n";
    echo "- Disco de almacenamiento: " . (app()->environment('production') ? 'r2' : 'public') . "\n";
    
    // Verificar disco R2
    try {
        $r2Disk = \Illuminate\Support\Facades\Storage::disk('r2');
        echo "- R2 configurado: ✅\n";
        
        // Test básico de escritura
        $testFile = 'test_' . time() . '.txt';
        $r2Disk->put($testFile, 'test content');
        
        if ($r2Disk->exists($testFile)) {
            echo "- R2 escritura: ✅\n";
            $r2Disk->delete($testFile);
        } else {
            echo "- R2 escritura: ❌\n";
        }
        
    } catch (\Exception $e) {
        echo "- R2 configurado: ❌ (" . $e->getMessage() . ")\n";
    }
}

echo "\n";

// Convertir valores a bytes para comparación
function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

$uploadMax = parseSize(ini_get('upload_max_filesize'));
$postMax = parseSize(ini_get('post_max_size'));

echo "📊 ANÁLISIS:\n";
echo "- Tamaño máximo de archivo: " . number_format($uploadMax / 1024 / 1024, 2) . " MB\n";
echo "- Tamaño máximo de POST: " . number_format($postMax / 1024 / 1024, 2) . " MB\n";

if ($uploadMax > $postMax) {
    echo "⚠️  PROBLEMA: upload_max_filesize es mayor que post_max_size\n";
}

echo "\n";

// Recomendaciones
echo "💡 RECOMENDACIONES:\n";
echo "- upload_max_filesize: 10M (mínimo)\n";
echo "- post_max_size: 12M (mayor que upload_max_filesize)\n";
echo "- max_execution_time: 300 (5 minutos)\n";
echo "- memory_limit: 256M (mínimo)\n";

echo "\n✨ Diagnóstico completado\n"; 