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
            
            

            if ($disk === 'r2') {
                // Para R2, usar la URL pública del bucket
                $publicUrl = config('filesystems.disks.r2.url');
                if (empty($publicUrl)) {
                    Log::error('URL pública de R2 no configurada');
                    return null;
                }
                
                $path = ltrim($this->image, '/');
                $url = rtrim($publicUrl, '/') . '/' . $path;
                
                Log::info('URL generada para R2', [
                    'public_url' => $publicUrl,
                    'path' => $path,
                    'url_final' => $url
                ]);
                
                return $url;
            }
            
            // Para desarrollo, usar la URL pública local
            $url = url('storage/' . $this->image);
            Log::info('URL generada para local', ['url' => $url]);
            return $url;
        } catch (\Exception $e) {
            
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