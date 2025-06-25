<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $person = Auth::user(); // El vendedor autenticado
    
        // Cantidad de productos creados por este vendedor
        $productsCount = \App\Models\Product::where('person_id', $person->id)->count();
    
        // Cantidad de publicaciones (listings) de este vendedor
        $listingsCount = \App\Models\ProductListing::where('person_id', $person->id)->count();
    
        return view('seller.dashboard', compact('productsCount', 'listingsCount'));
    }
} 