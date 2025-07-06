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
        
        // En producción (Laravel Cloud), usar S3
        if (app()->environment('production')) {
            return Storage::disk('s3')->url($this->image);
        }
        
        // En local, usar storage
        return asset('storage/' . $this->image);
    }

    public function deleteImage(): void
    {
        if (!$this->image) {
            return;
        }

        $disk = app()->environment('production') ? 's3' : 'public';
        
        if (Storage::disk($disk)->exists($this->image)) {
            Storage::disk($disk)->delete($this->image);
        }
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