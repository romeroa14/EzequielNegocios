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
                'imagen' => $this->image,
                'r2_config' => [
                    'url' => config('filesystems.disks.r2.url'),
                    'bucket' => config('filesystems.disks.r2.bucket'),
                    'endpoint' => config('filesystems.disks.r2.endpoint'),
                ]
            ]);

            if ($disk === 'r2') {
                // Para R2, construir la URL usando el endpoint configurado
                $url = rtrim(config('filesystems.disks.r2.url'), '/') . '/' . ltrim($this->image, '/');
                Log::info('URL generada para R2', ['url' => $url]);
                return $url;
            }
            
            // Para desarrollo, usar la URL pública local
            $url = url('storage/' . $this->image);
            Log::info('URL generada para local', ['url' => $url]);
            return $url;
        } catch (\Exception $e) {
            Log::error('Error generando URL de imagen', [
                'error' => $e->getMessage(),
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
        Storage::disk($disk)->delete($this->image);
    }

    protected static function bootHasProductImage(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });

        static::updating(function ($model) {
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                $disk = app()->environment('production') ? 'r2' : 'public';
                Storage::disk($disk)->delete($model->getOriginal('image'));
            }
        });
    }
} 