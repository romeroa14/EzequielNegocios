<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ListingController extends Controller
{
    public function generateSocialMediaImage($id)
    {
        try {
            Log::info('Iniciando generación de imagen PNG', ['listing_id' => $id]);
            
            $listing = ProductListing::with(['product', 'person'])->findOrFail($id);
            Log::info('Listing encontrado', ['listing_title' => $listing->title]);
            
            // Crear directorio si no existe
            $directory = storage_path('app/public/social-media-images');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Generar nombre único para el archivo
            $filename = 'social-media-' . $id . '-' . time() . '.png';
            $path = 'social-media-images/' . $filename;
            $fullPath = storage_path('app/public/' . $path);
            
            // Crear imagen PNG 1080x1350
            $manager = new ImageManager(new Driver());
            $image = $manager->create(1080, 1350);
            
            // Fondo azul claro
            $image->fill('#f0f9ff');
            
            // Agregar imagen del producto si existe
            if ($listing->hasImages() && !empty($listing->images)) {
                try {
                    $productImage = $manager->read($listing->main_image_url);
                    $productImage->scaleDown(1000, 600);
                    $image->place($productImage, 'top-center', 0, 50);
                } catch (\Exception $e) {
                    Log::warning('No se pudo cargar imagen del producto', ['error' => $e->getMessage()]);
                }
            }
            
            // Agregar rectángulo blanco para el texto
            $image->drawRectangle(50, 700, 1030, 1300, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.95)');
                $draw->border(2, '#e5e7eb');
            });
            
            // Agregar texto
            $image->text($listing->title, 540, 750, function($font) {
                $font->size(48);
                $font->color('#1f2937');
                $font->align('center');
            });
            
            $image->text($listing->formatted_price, 540, 820, function($font) {
                $font->size(36);
                $font->color('#059669');
                $font->align('center');
            });
            
            $image->text($listing->formatted_presentation, 540, 870, function($font) {
                $font->size(24);
                $font->color('#6b7280');
                $font->align('center');
            });
            
            $image->text("📍 " . $listing->location, 540, 920, function($font) {
                $font->size(20);
                $font->color('#6b7280');
                $font->align('center');
            });
            
            $image->text("👤 " . $listing->person->name, 540, 970, function($font) {
                $font->size(20);
                $font->color('#6b7280');
                $font->align('center');
            });
            
            $image->text("EZEQUIELNEGOCIOS.COM", 540, 1100, function($font) {
                $font->size(28);
                $font->color('#1f2937');
                $font->align('center');
            });
            
            $image->text("#EzequielNegocios #Agricultura #Venezuela", 540, 1200, function($font) {
                $font->size(18);
                $font->color('#3b82f6');
                $font->align('center');
            });
            
            // Guardar imagen
            $image->save($fullPath);
            
            Log::info('Imagen PNG generada exitosamente', [
                'listing_id' => $id,
                'filename' => $filename,
                'file_size' => filesize($fullPath)
            ]);
            
            // Retornar la imagen como descarga
            return response()->download($fullPath, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error al generar imagen PNG', [
                'listing_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'No se pudo generar la imagen: ' . $e->getMessage()], 500);
        }
    }

    public function previewSocialMediaImage($id)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($id);
            
            // Retornar la vista HTML directamente para preview
            return view('social-media-image', compact('listing'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar preview de imagen', [
                'listing_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'No se pudo mostrar el preview: ' . $e->getMessage()], 500);
        }
    }

    public function generateShareLink($id)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($id);
            
            // Crear URL de compartir con información del producto
            $shareUrl = route('market.index') . '?product=' . $listing->product_id . '&listing=' . $id;
            
            // Mensaje para compartir
            $message = "🌱 *" . $listing->title . "*\n\n";
            $message .= "💰 *Precio:* " . $listing->formatted_price . "\n";
            $message .= "📦 *Presentación:* " . $listing->formatted_presentation . "\n";
            $message .= "📍 *Ubicación:* " . $listing->location . "\n";
            $message .= "👤 *Vendedor:* " . $listing->person->name . "\n\n";
            $message .= "🔗 *Ver más detalles:* " . $shareUrl . "\n\n";
            $message .= "#EzequielNegocios #Agricultura #Venezuela";
            
            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'whatsapp_url' => 'https://wa.me/?text=' . urlencode($message),
                'facebook_url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl),
                'twitter_url' => 'https://twitter.com/intent/tweet?text=' . urlencode("🌱 " . $listing->title . " - " . $listing->formatted_price . " en EzequielNegocios") . '&url=' . urlencode($shareUrl),
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al generar link de compartir', [
                'listing_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'No se pudo generar el link de compartir: ' . $e->getMessage()], 500);
        }
    }
}
