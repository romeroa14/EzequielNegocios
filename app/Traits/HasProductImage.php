<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait HasProductImage
{
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        try {
            // Determinar el disco a usar
            $disk = app()->environment('production') ? 'r2' : 'public';
            
            Log::info('Generando URL de imagen', [
                'ambiente' => app()->environment(),
                'disco' => $disk,
                'imagen_path' => $this->image,
                'r2_config' => [
                    'url' => config('filesystems.disks.r2.url'),
                    'bucket' => config('filesystems.disks.r2.bucket'),
                    'endpoint' => config('filesystems.disks.r2.endpoint'),
                ]
            ]);

            if ($disk === 'r2') {
                // Para R2, intentar obtener la URL directamente del Storage primero
                try {
                    $url = Storage::disk('r2')->url($this->image);
                    Log::info('URL generada por Storage', ['url' => $url]);
                    return $url;
                } catch (\Exception $e) {
                    Log::warning('No se pudo generar URL con Storage::url, usando URL manual', [
                        'error' => $e->getMessage()
                    ]);
                    
                    // Fallback: construir la URL manualmente
                    $baseUrl = rtrim(config('filesystems.disks.r2.url'), '/');
                    $path = ltrim($this->image, '/');
                    $url = "{$baseUrl}/{$path}";
                    
                    Log::info('URL generada manualmente', [
                        'base_url' => $baseUrl,
                        'path' => $path,
                        'url_final' => $url
                    ]);
                    
                    // Verificar si el archivo existe
                    $exists = Storage::disk('r2')->exists($this->image);
                    Log::info('Verificación de existencia de archivo', [
                        'exists' => $exists,
                        'path' => $this->image
                    ]);
                    
                    return $url;
                }
            }
            
            // Para desarrollo, usar la URL pública local
            $url = url('storage/' . $this->image);
            Log::info('URL generada para local', ['url' => $url]);
            return $url;
        } catch (\Exception $e) {
            Log::error('Error generando URL de imagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'image' => $this->image,
                'disk' => $disk ?? 'unknown'
            ]);
            return null;
        }
    }

    public function deleteImage(): void
    {
        if (!$this->image) {
            return;
        }
        
        $disk = app()->environment('production') ? 'r2' : 'public';
        try {
            $exists = Storage::disk($disk)->exists($this->image);
            Log::info('Intentando eliminar imagen', [
                'disk' => $disk,
                'path' => $this->image,
                'exists' => $exists
            ]);
            
            if ($exists) {
                Storage::disk($disk)->delete($this->image);
                Log::info('Imagen eliminada correctamente');
            }
        } catch (\Exception $e) {
            Log::error('Error eliminando imagen', [
                'error' => $e->getMessage(),
                'disk' => $disk,
                'path' => $this->image
            ]);
        }
    }

    protected static function bootHasProductImage(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });

        static::updating(function ($model) {
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                $disk = app()->environment('production') ? 'r2' : 'public';
                try {
                    $oldImage = $model->getOriginal('image');
                    $exists = Storage::disk($disk)->exists($oldImage);
                    Log::info('Verificando imagen anterior antes de actualizar', [
                        'disk' => $disk,
                        'old_image' => $oldImage,
                        'exists' => $exists
                    ]);
                    
                    if ($exists) {
                        Storage::disk($disk)->delete($oldImage);
                        Log::info('Imagen anterior eliminada correctamente');
                    }
                } catch (\Exception $e) {
                    Log::error('Error eliminando imagen anterior', [
                        'error' => $e->getMessage(),
                        'disk' => $disk,
                        'old_image' => $oldImage
                    ]);
                }
            }
        });
    }
} 