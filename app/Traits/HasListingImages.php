<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait HasListingImages
{
    public function getImagesUrlAttribute(): array
    {
        if (empty($this->images)) {
            return [];
        }
        
        try {
            // Determinar el disco a usar basado en el entorno
            $disk = app()->environment('production') ? 'r2' : 'public';
            
            $urls = [];
            foreach ($this->images as $image) {
                if ($disk === 'r2') {
                    // Para R2, usar la URL pública del bucket
                    $publicUrl = config('filesystems.disks.r2.url');
                    if (empty($publicUrl)) {
                        Log::error('URL pública de R2 no configurada');
                        continue;
                    }
                    
                    $path = ltrim($image, '/');
                    $url = rtrim($publicUrl, '/') . '/' . $path;
                    
                    Log::info('URL generada para R2', [
                        'public_url' => $publicUrl,
                        'path' => $path,
                        'url_final' => $url
                    ]);
                    
                    $urls[] = $url;
                } else {
                    // Para desarrollo, usar la URL pública local
                    $urls[] = url('storage/' . $image);
                }
            }
            
            Log::info('URLs generadas para listing', ['urls' => $urls]);
            return $urls;
        } catch (\Exception $e) {
            Log::error('Error generando URLs de imágenes', [
                'error' => $e->getMessage(),
                'images' => $this->images
            ]);
            return [];
        }
    }

    /**
     * Obtiene la URL de la imagen principal (primera imagen)
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $urls = $this->images_url;
        return !empty($urls) ? $urls[0] : null;
    }

    /**
     * Obtiene todas las URLs de las imágenes
     */
    public function getAllImagesUrlAttribute(): array
    {
        return $this->images_url;
    }

    /**
     * Verifica si tiene imágenes
     */
    public function hasImages(): bool
    {
        return !empty($this->images) && count($this->images) > 0;
    }

    /**
     * Obtiene el número total de imágenes
     */
    public function getImagesCountAttribute(): int
    {
        return is_array($this->images) ? count($this->images) : 0;
    }

    public function deleteImages(): void
    {
        if (empty($this->images)) {
            return;
        }
        
        $disk = app()->environment('production') ? 'r2' : 'public';
        
        foreach ($this->images as $image) {
            try {
                $exists = Storage::disk($disk)->exists($image);
                Log::info('Intentando eliminar imagen de listing', [
                    'disk' => $disk,
                    'path' => $image,
                    'exists' => $exists
                ]);
                
                if ($exists) {
                    Storage::disk($disk)->delete($image);
                    Log::info('Imagen de listing eliminada correctamente');
                }
            } catch (\Exception $e) {
                Log::error('Error eliminando imagen de listing', [
                    'error' => $e->getMessage(),
                    'disk' => $disk,
                    'path' => $image
                ]);
            }
        }
    }

    /**
     * Agrega una nueva imagen a la lista
     */
    public function addImage(string $imagePath): void
    {
        $images = $this->images ?? [];
        $images[] = $imagePath;
        $this->update(['images' => $images]);
    }

    /**
     * Elimina una imagen específica de la lista
     */
    public function removeImage(string $imagePath): void
    {
        $images = $this->images ?? [];
        $key = array_search($imagePath, $images);
        
        if ($key !== false) {
            // Eliminar el archivo físico
            $disk = app()->environment('production') ? 'r2' : 'public';
            try {
                if (Storage::disk($disk)->exists($imagePath)) {
                    Storage::disk($disk)->delete($imagePath);
                }
            } catch (\Exception $e) {
                Log::error('Error eliminando imagen', [
                    'error' => $e->getMessage(),
                    'path' => $imagePath
                ]);
            }
            
            // Remover de la lista
            unset($images[$key]);
            $this->update(['images' => array_values($images)]);
        }
    }

    protected static function bootHasListingImages(): void
    {
        static::deleting(function ($model) {
            $model->deleteImages();
        });

        static::updating(function ($model) {
            if ($model->isDirty('images')) {
                $oldImages = $model->getOriginal('images') ?? [];
                $disk = app()->environment('production') ? 'r2' : 'public';
                
                foreach ($oldImages as $oldImage) {
                    try {
                        $exists = Storage::disk($disk)->exists($oldImage);
                        Log::info('Verificando imagen anterior antes de actualizar', [
                            'disk' => $disk,
                            'old_image' => $oldImage,
                            'exists' => $exists
                        ]);
                        
                        if ($exists) {
                            Storage::disk($disk)->delete($oldImage);
                            Log::info('Imagen anterior de listing eliminada correctamente');
                        }
                    } catch (\Exception $e) {
                        Log::error('Error eliminando imagen anterior de listing', [
                            'error' => $e->getMessage(),
                            'disk' => $disk,
                            'old_image' => $oldImage
                        ]);
                    }
                }
            }
        });
    }
} 