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
            return $this->getImageUrl($image);
        }, $this->images);
    }

    /**
     * Obtiene la URL de la imagen principal
     */
    public function getMainImageUrlAttribute(): string
    {
        if ($this->hasImages()) {
            return $this->getImageUrl($this->images[0]);
        }
        return asset('images/placeholder.png');
    }

    /**
     * Obtiene la URL de una imagen específica
     */
    private function getImageUrl($imagePath): string
    {
        if (empty($imagePath)) {
            return asset('images/placeholder.png');
        }

        // Determinar el disco según el entorno
        $disk = app()->environment('production') ? 'r2' : 'public';
        
        if ($disk === 'r2') {
            // Para R2 en producción, usar la URL pública del bucket
            $publicUrl = config('filesystems.disks.r2.url');
            if (empty($publicUrl)) {
                return asset('images/placeholder.png');
            }
            
            $path = ltrim($imagePath, '/');
            return rtrim($publicUrl, '/') . '/' . $path;
        }
        
        // Para desarrollo, usar storage local
        return asset('storage/' . $imagePath);
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