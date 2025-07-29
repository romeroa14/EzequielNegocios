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

    public function show(Person $producer)
    {
        $listings = ProductListing::where('person_id', $producer->id)
            ->where('status', 'active')
            ->with(['product', 'state', 'municipality', 'parish'])
            ->paginate(12);

        return view('producers.show', [
            'producer' => $producer,
            'listings' => $listings
        ]);
    }

    public function contact(Request $request, Person $producer)
    {
        // Aquí puedes implementar la lógica de contacto
        // Por ejemplo, enviar un email o redireccionar a WhatsApp
        return back()->with('success', 'Mensaje enviado correctamente');
    }
} 