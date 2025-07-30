<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasListingImages
{
    /**
     * Obtiene las URLs de las imágenes
     */
    public function getImagesUrlAttribute(): array
    {
        if (!$this->hasImages()) {
            return [];
        }

        return array_map(function($image) {
            return asset('storage/' . $image);
        }, $this->images);
    }

    /**
     * Obtiene la URL de la imagen principal
     */
    public function getMainImageUrlAttribute(): string
    {
        if ($this->hasImages()) {
            return asset('storage/' . $this->images[0]);
        }
        return asset('images/placeholder.png');
    }

    /**
     * Obtiene todas las URLs de las imágenes
     */
    public function getAllImagesUrlAttribute(): array
    {
        return $this->images_url;
    }

    /**
     * Obtiene el número de imágenes
     */
    public function getImagesCountAttribute(): int
    {
        return $this->hasImages() ? count($this->images) : 0;
    }

    /**
     * Elimina las imágenes físicas
     */
    public function deleteImages(): void
    {
        if ($this->hasImages()) {
            foreach ($this->images as $image) {
                $disk = app()->environment('production') ? 'r2' : 'public';
                if (Storage::disk($disk)->exists($image)) {
                    Storage::disk($disk)->delete($image);
                }
            }
        }
    }
} 