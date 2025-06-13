<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Person::where('role', 'seller')
            ->with(['user', 'listings'])
            ->paginate(12);
            
        return view('producers.producers', compact('producers'));
    }

    public function show(Person $producer)
    {
        if ($producer->role !== 'seller') {
            abort(404);
        }

        $producer->load(['user', 'listings' => function($query) {
            $query->where('status', 'active')
                  ->with('category')
                  ->orderBy('created_at', 'desc');
        }]);

        return view('producers.show', compact('producer'));
    }
} 