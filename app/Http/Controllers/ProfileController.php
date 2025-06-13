<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $person = Auth::guard('web')->user();
        return view('profile.edit', compact('person'));
    }

    public function update(Request $request)
    {
        $person = Auth::guard('web')->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', 'max:255', Rule::unique('people')->ignore($person->id)],
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'sector'     => 'nullable|string|max:255',
        ]);

        $person->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
