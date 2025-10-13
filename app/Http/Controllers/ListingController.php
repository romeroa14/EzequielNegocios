<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ListingController extends Controller
{
    public function generateSocialMediaImage($id)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($id);
            
            // Crear imagen personalizada para redes sociales (1080x1350)
            $width = 1080;
            $height = 1350;
            
            // Crear manager de imágenes
            $manager = new ImageManager(new Driver());
            
            // Crear imagen base con fondo blanco
            $image = $manager->create($width, $height);
            
            // Llenar con color de fondo
            $image->fill('#f0f9ff'); // Azul claro
            
            // Agregar imagen del producto si existe
            if ($listing->hasImages() && !empty($listing->images)) {
                try {
                    $productImage = $manager->read($listing->main_image_url);
                    $productImage->scaleDown(900, 600);
                    
                    // Posicionar imagen del producto
                    $image->place($productImage, 'top-center', 0, 80);
                } catch (\Exception $e) {
                    // Si no se puede cargar la imagen, continuar sin ella
                    \Log::warning('No se pudo cargar imagen del producto', ['error' => $e->getMessage()]);
                }
            }
            
            // Crear directorio si no existe
            $directory = storage_path('app/public/social-media-images');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Generar nombre único para el archivo
            $filename = 'social-media-' . $id . '-' . time() . '.png';
            $path = 'social-media-images/' . $filename;
            $fullPath = storage_path('app/public/' . $path);
            
            // Guardar imagen
            $image->save($fullPath);
            
            // Retornar la imagen como descarga
            return response()->download($fullPath, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error al generar imagen para redes sociales', [
                'listing_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'No se pudo generar la imagen: ' . $e->getMessage()], 500);
        }
    }
}
