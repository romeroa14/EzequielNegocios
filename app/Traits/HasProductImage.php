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

        // Para Cloudflare R2 en Laravel Cloud, usamos directamente la URL configurada
        $baseUrl = config('filesystems.disks.s3.url');
        
        // Construir la URL completa
        return "{$baseUrl}/{$this->image}";
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