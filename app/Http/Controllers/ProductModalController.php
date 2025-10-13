<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Log;

class ProductModalController extends Controller
{
    /**
     * Mostrar el catálogo con un producto específico abierto en modal
     */
    public function show($productId)
    {
        try {
            Log::info('ProductModalController: Opening product modal', ['product_id' => $productId]);
            
            // Verificar que el producto existe y está activo
            $listing = ProductListing::with([
                'product.productCategory',
                'product.productSubcategory', 
                'product.productLine',
                'product.brand',
                'productPresentation',
                'person',
                'state',
                'municipality',
                'parish'
            ])
            ->where('id', $productId)
            ->where('status', 'active')
            ->firstOrFail();

            Log::info('ProductModalController: Product found', [
                'product_id' => $listing->id,
                'title' => $listing->title
            ]);

            // Obtener las tasas de cambio BCV
            $exchangeRates = [
                'usd' => [
                    'rate' => \App\Models\ExchangeRate::where('currency_code', 'USD')->latest()->first()?->rate ?? null
                ],
                'eur' => [
                    'rate' => \App\Models\ExchangeRate::where('currency_code', 'EUR')->latest()->first()?->rate ?? null
                ]
            ];

            // Retornar la vista independiente del producto
            return view('product-modal', compact('listing'));
            
        } catch (\Exception $e) {
            Log::error('ProductModalController: Error opening product modal', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            
            // Si hay error, redirigir al catálogo normal
            return redirect()->route('catalogo')->with('error', 'Producto no encontrado');
        }
    }
}