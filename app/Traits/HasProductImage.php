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

        // Obtener la URL del bucket de Laravel Cloud
        $s3Url = config('filesystems.disks.s3.url');
        $bucket = config('filesystems.disks.s3.bucket');
        
        // Construir la URL completa
        return "{$s3Url}/{$bucket}/{$this->image}";
    }

    public function deleteImage(): void
    {
        if (!$this->image) {
            return;
        }
        
        Storage::disk('s3')->delete($this->image);
    }

    protected static function bootHasProductImage(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });

        static::updating(function ($model) {
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                Storage::disk('s3')->delete($model->getOriginal('image'));
            }
        });
    }
} 