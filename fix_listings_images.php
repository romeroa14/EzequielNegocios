<?php

/**
 * Script para migrar imágenes de ProductListing de products/ a listings/
 * Este script corrige el problema de producción donde las imágenes 
 * se están guardando en la carpeta incorrecta
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductListing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

echo "🔧 Iniciando migración de imágenes de listings...\n\n";

try {
    // Obtener todos los listings que tienen imágenes
    $listings = ProductListing::whereNotNull('images')
        ->where('images', '!=', '[]')
        ->get();

    echo "📊 Encontrados {$listings->count()} listings con imágenes\n\n";

    $migrated = 0;
    $errors = 0;
    $skipped = 0;

    foreach ($listings as $listing) {
        echo "🔍 Procesando listing ID: {$listing->id}\n";
        
        if (!$listing->images || !is_array($listing->images)) {
            echo "   ⏭️  Sin imágenes válidas, saltando...\n";
            $skipped++;
            continue;
        }

        $newImages = [];
        $hasChanges = false;

        foreach ($listing->images as $index => $imagePath) {
            echo "   🖼️  Imagen {$index}: {$imagePath}\n";
            
            // Si la imagen ya está en listings/, no necesita migración
            if (str_starts_with($imagePath, 'listings/')) {
                echo "   ✅ Ya está en listings/, no requiere migración\n";
                $newImages[] = $imagePath;
                continue;
            }

            // Si la imagen está en products/, necesita migración
            if (str_starts_with($imagePath, 'products/')) {
                $hasChanges = true;
                
                // Determinar el disco según el entorno
                $disk = app()->environment('production') ? 'r2' : 'public';
                
                // Generar nueva ruta en listings/
                $fileName = basename($imagePath);
                $newPath = 'listings/' . $fileName;
                
                echo "   🔄 Migrando de {$imagePath} a {$newPath}\n";
                
                try {
                    // Verificar si el archivo origen existe
                    if (!Storage::disk($disk)->exists($imagePath)) {
                        echo "   ❌ Archivo origen no existe: {$imagePath}\n";
                        $errors++;
                        continue;
                    }
                    
                    // Verificar si el destino ya existe
                    if (Storage::disk($disk)->exists($newPath)) {
                        echo "   ⚠️  Destino ya existe: {$newPath}, usando nombre único\n";
                        $fileName = time() . '_' . $fileName;
                        $newPath = 'listings/' . $fileName;
                    }
                    
                    // Copiar archivo
                    $fileContent = Storage::disk($disk)->get($imagePath);
                    Storage::disk($disk)->put($newPath, $fileContent);
                    
                    // Verificar que se copió correctamente
                    if (Storage::disk($disk)->exists($newPath)) {
                        echo "   ✅ Copiado exitosamente\n";
                        
                        // Eliminar archivo original
                        Storage::disk($disk)->delete($imagePath);
                        echo "   🗑️  Archivo original eliminado\n";
                        
                        $newImages[] = $newPath;
                        $migrated++;
                    } else {
                        echo "   ❌ Error al copiar archivo\n";
                        $errors++;
                        $newImages[] = $imagePath; // Mantener original si falla
                    }
                    
                } catch (\Exception $e) {
                    echo "   ❌ Error: " . $e->getMessage() . "\n";
                    $errors++;
                    $newImages[] = $imagePath; // Mantener original si falla
                }
            } else {
                // Ruta desconocida, mantener como está
                echo "   ⚠️  Ruta desconocida, manteniendo: {$imagePath}\n";
                $newImages[] = $imagePath;
            }
        }
        
        // Actualizar el listing si hubo cambios
        if ($hasChanges && !empty($newImages)) {
            $listing->update(['images' => $newImages]);
            echo "   💾 Listing actualizado con nuevas rutas\n";
        }
        
        echo "\n";
    }

    echo "🎉 Migración completada!\n";
    echo "📊 Estadísticas:\n";
    echo "   ✅ Imágenes migradas: {$migrated}\n";
    echo "   ❌ Errores: {$errors}\n";
    echo "   ⏭️  Saltados: {$skipped}\n";

} catch (\Exception $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✨ Script completado exitosamente!\n"; 