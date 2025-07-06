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
            // Usar la URL pública directamente
            return url('storage/' . $this->image);
        } catch (\Exception $e) {
            Log::error('Error generando URL de imagen', [
                'error' => $e->getMessage(),
                'image' => $this->image
            ]);
            return null;
        }
    }

    public function deleteImage(): void
    {
        if (!$this->image) {
            return;
        }
        
        Storage::disk('public')->delete($this->image);
    }

    protected static function bootHasProductImage(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });

        static::updating(function ($model) {
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                Storage::disk('public')->delete($model->getOriginal('image'));
            }
        });
    }
} 