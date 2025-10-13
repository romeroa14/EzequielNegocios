<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\ProductListing;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Person::whereHas('productListings', function($query) {
            $query->where('status', 'active');
        })->paginate(12);
            
        return view('producers.index', compact('producers'));
    }

    public function show($id)
    {
        // Buscar el productor por ID con sus relaciones
        $producer = Person::with(['state', 'municipality', 'parish'])->findOrFail($id);
        
        $listings = ProductListing::where('person_id', $producer->id)
            ->where('status', 'active')
            ->with(['product', 'state', 'municipality', 'parish'])
            ->paginate(12);

        return view('producers.show', [
            'producer' => $producer,
            'listings' => $listings
        ]);
    }

    public function contact(Request $request, $id)
    {
        // Buscar el productor por ID
        $producer = Person::findOrFail($id);
        
        // Aquí puedes implementar la lógica de contacto
        // Por ejemplo, enviar un email o redireccionar a WhatsApp
        return back()->with('success', 'Mensaje enviado correctamente');
    }
} 