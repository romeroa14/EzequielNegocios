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

        // Obtener la URL base del bucket S3
        $s3BaseUrl = config('filesystems.disks.s3.url', 'https://sistemacompraventa-master-mrqu8y.laravel.cloud/storage');
        
        // Construir la URL completa
        return $s3BaseUrl . '/' . $this->image;
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