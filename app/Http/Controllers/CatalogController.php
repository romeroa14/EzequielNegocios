<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use App\Models\ProductCategory;

class CatalogController extends Controller
{
    public function index()
    {
        $products = ProductListing::where('status', 'active')
            ->with(['seller', 'category'])
            ->paginate(12);
            
        $categories = ProductCategory::where('is_active', true)->get();
        
        return view('catalog.index', compact('products', 'categories'));
    }

    public function show(ProductListing $product)
    {
        $product->load(['seller.user', 'category']);
        $relatedProducts = ProductListing::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();
            
        return view('catalog.show', compact('product', 'relatedProducts'));
    }
} 