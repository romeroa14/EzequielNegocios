<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $person = $user->person;

        // Si no existe persona, puedes crearla aquí o mostrar un mensaje en la vista
        if (!$person) {
            // Opcional: crear persona automáticamente
            // $person = $user->person()->create([]);
        }

        return view('profile.edit', compact('user', 'person'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $person = $user->person;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'sector'     => 'nullable|string|max:255',
        ]);

        // Actualiza usuario
        $user->email = $validated['email'];
        $user->save();

        // Actualiza persona (si existe)
        if ($person) {
            $person->first_name = $validated['first_name'];
            $person->last_name  = $validated['last_name'];
            $person->phone      = $validated['phone'] ?? null;
            $person->address    = $validated['address'] ?? null;
            $person->sector     = $validated['sector'] ?? null;
            $person->save();
        }

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
