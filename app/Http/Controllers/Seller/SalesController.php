<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        return view('seller.sales.index');
    }

    public function show($id)
    {
        return view('seller.sales.show');
    }

    public function update(Request $request, $id)
    {
        // Implementar lógica de actualización de estado de venta
    }
} 