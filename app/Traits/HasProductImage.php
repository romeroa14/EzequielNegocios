<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasProductImage
{
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // En producción usar el disco configurado en vapor.yml (s3)
        $disk = app()->environment('production') ? 's3' : 'public';
        
        // Si estamos en producción, usar la URL de S3
        if ($disk === 's3') {
            return Storage::disk($disk)->url($this->image);
        }
        
        // En desarrollo, usar la URL local
        return asset('storage/' . $this->image);
    }

    public function deleteImage(): void
    {
        if (!$this->image) {
            return;
        }
        
        $disk = app()->environment('production') ? 's3' : 'public';
        Storage::disk($disk)->delete($this->image);
    }

    protected static function bootHasProductImage(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });

        static::updating(function ($model) {
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                $disk = app()->environment('production') ? 's3' : 'public';
                Storage::disk($disk)->delete($model->getOriginal('image'));
            }
        });
    }
} 