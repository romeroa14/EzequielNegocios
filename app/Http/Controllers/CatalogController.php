<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use App\Models\ProductCategory;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        // Obtener listados activos
        $listings = ProductListing::where('status', 'active')
            ->with(['seller', 'category', 'product'])
            ->get();

        // Obtener productos universales activos
        $universalProducts = Product::where('is_universal', true)
            ->with(['creator', 'category'])
            ->whereHas('creator', function ($query) {
                $query->where('is_universal', true);
            })
            ->get();

        // Combinar productos universales con listados normales
        $products = $listings->concat($universalProducts)->paginate(12);
            
        $categories = ProductCategory::where('is_active', true)->get();
        
        return view('catalog.index', compact('products', 'categories'));
    }

    public function show(ProductListing $product)
    {
        $product->load(['seller.user', 'category', 'product']);

        // Obtener productos relacionados incluyendo universales
        $relatedProducts = ProductListing::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->with(['product'])
            ->limit(4)
            ->get();

        // Agregar productos universales relacionados
        $relatedUniversal = Product::where('is_universal', true)
            ->where('category_id', $product->category_id)
            ->whereHas('creator', function ($query) {
                $query->where('is_universal', true);
            })
            ->limit(2)
            ->get();

        $relatedProducts = $relatedProducts->concat($relatedUniversal);
            
        return view('catalog.show', compact('product', 'relatedProducts'));
    }
} 