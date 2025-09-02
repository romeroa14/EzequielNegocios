<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageHelper
{
    /**
     * Obtener la URL de una imagen de producto
     */
    public static function getProductImageUrl(?string $imagePath, string $productName = ''): string
    {
        // Si no hay imagen, devolver imagen por defecto
        if (empty($imagePath)) {
            return self::getDefaultProductImage();
        }

        // Verificar si estamos en producción (usando R2)
        if (config('filesystems.default') === 'r2' || app()->environment('production')) {
            // Usar R2/Cloudflare - construir URL manualmente
            return config('filesystems.disks.r2.url') . '/' . $imagePath;
        }

        // Verificar si el enlace simbólico existe
        $publicPath = public_path('storage/' . $imagePath);
        if (file_exists($publicPath)) {
            // Usar storage local con enlace simbólico
            return asset('storage/' . $imagePath);
        }

        // Intentar usar storage directo
        if (Storage::disk('public')->exists($imagePath)) {
            // Fallback a asset directo
            return asset('storage/' . $imagePath);
        }

        // Fallback a imagen por defecto
        return self::getDefaultProductImage();
    }

    /**
     * Obtener imagen por defecto para productos
     */
    public static function getDefaultProductImage(): string
    {
        // Puedes personalizar esta imagen por defecto
        return asset('images/default-product.svg');
    }

    /**
     * Verificar si una imagen existe
     */
    public static function imageExists(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        // Verificar en R2
        if (config('filesystems.default') === 'r2' || app()->environment('production')) {
            return Storage::disk('r2')->exists($imagePath);
        }

        // Verificar en storage local
        return Storage::disk('public')->exists($imagePath);
    }

    /**
     * Obtener el tamaño de una imagen
     */
    public static function getImageSize(?string $imagePath): ?array
    {
        if (empty($imagePath)) {
            return null;
        }

        try {
            if (config('filesystems.default') === 'r2' || app()->environment('production')) {
                $contents = Storage::disk('r2')->get($imagePath);
            } else {
                $contents = Storage::disk('public')->get($imagePath);
            }

            if ($contents) {
                $imageInfo = getimagesizefromstring($contents);
                if ($imageInfo) {
                    return [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1],
                        'mime' => $imageInfo['mime']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error si es necesario
        }

        return null;
    }
}
