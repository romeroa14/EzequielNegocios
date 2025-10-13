<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function show($id)
    {
        try {
            $listing = ProductListing::with([
                'product.productCategory',
                'product.productSubcategory', 
                'product.productLine',
                'product.brand',
                'productPresentation',
                'person',
                'state',
                'municipality',
                'parish',
                'market'
            ])
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

            // Redirigir al catálogo con parámetro para abrir modal
            return redirect()->route('welcome', ['product' => $id]);
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar producto', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('welcome')->with('error', 'Producto no encontrado');
        }
    }

    public function showFriendly($producerSlug, $listingSlug, $id)
    {
        try {
            $listing = ProductListing::with([
                'product.productCategory',
                'product.productSubcategory', 
                'product.productLine',
                'product.brand',
                'productPresentation',
                'person',
                'state',
                'municipality',
                'parish',
                'market'
            ])
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

            // Verificar que los slugs coincidan (opcional, para SEO)
            $expectedProducerSlug = Str::slug($listing->person->first_name . ' ' . $listing->person->last_name);
            $expectedListingSlug = Str::slug($listing->title);

            // Redirigir al catálogo con parámetro para abrir modal
            return redirect()->route('welcome', ['product' => $id]);
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar producto amigable', [
                'producer_slug' => $producerSlug,
                'listing_slug' => $listingSlug,
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('welcome')->with('error', 'Producto no encontrado');
        }
    }

    public function showModal($id)
    {
        try {
            $listing = ProductListing::with([
                'product.productCategory',
                'product.productSubcategory', 
                'product.productLine',
                'product.brand',
                'productPresentation',
                'person',
                'state',
                'municipality',
                'parish',
                'market'
            ])
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

            // Retornar vista con modal abierto
            return view('product-modal', compact('listing'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar modal de producto', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('welcome')->with('error', 'Producto no encontrado');
        }
    }
}

