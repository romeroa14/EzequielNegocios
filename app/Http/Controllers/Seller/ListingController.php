<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        return view('seller.listings.index');
    }

    public function create()
    {
        return view('seller.listings.create');
    }

    public function store(Request $request)
    {
        // Implementar lógica de creación
    }

    public function edit($id)
    {
        return view('seller.listings.edit');
    }

    public function update(Request $request, $id)
    {
        // Implementar lógica de actualización
    }

    public function destroy($id)
    {
        // Implementar lógica de eliminación
    }
} 