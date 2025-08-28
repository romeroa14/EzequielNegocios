<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\Person $person */
        $person = Auth::guard('person')->user();
        
        if (!$person) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder al perfil.');
        }
        
        $states = State::where('country_id', 296)->get();
        
        // Solo obtener municipalities y parishes si el usuario tiene state_id y municipality_id
        $municipalities = collect();
        $parishes = collect();
        
        if ($person->state_id) {
            $municipalities = Municipality::where('state_id', $person->state_id)->get();
        }
        
        if ($person->municipality_id) {
            $parishes = Parish::where('municipality_id', $person->municipality_id)->get();
        }
        
        return view('profile.edit', compact('person', 'states', 'municipalities', 'parishes'));
    }

    public function getMunicipalities(Request $request)
    {
        $municipalities = Municipality::where('state_id', $request->state_id)->get();
        return response()->json($municipalities);
    }

    public function getParishes(Request $request)
    {
        $parishes = Parish::where('municipality_id', $request->municipality_id)->get();
        return response()->json($parishes);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Person $person */
        $person = Auth::guard('person')->user();
        
        if (!$person) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para actualizar el perfil.');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('people')->ignore($person->id)],
            'phone' => ['required', 'string', 'max:20'],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20', Rule::unique('people')->ignore($person->id)],
            'address' => ['required', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'municipality_id' => ['required', 'exists:municipalities,id'],
            'parish_id' => ['required', 'exists:parishes,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];



        $messages = [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'identification_type.required' => 'El tipo de identificación es obligatorio.',
            'identification_type.in' => 'El tipo de identificación debe ser V, E, J o G.',
            'identification_number.required' => 'El número de identificación es obligatorio.',
            'identification_number.max' => 'El número de identificación no puede tener más de 20 caracteres.',
            'identification_number.unique' => 'Este número de identificación ya está registrado.',
            'address.required' => 'La dirección es obligatoria.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            'sector.max' => 'El sector no puede tener más de 255 caracteres.',
            'state_id.required' => 'El estado es obligatorio.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
            'municipality_id.required' => 'El municipio es obligatorio.',
            'municipality_id.exists' => 'El municipio seleccionado no es válido.',
            'parish_id.required' => 'La parroquia es obligatoria.',
            'parish_id.exists' => 'La parroquia seleccionada no es válida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];

        $validated = $request->validate($rules, $messages);

        // Si se proporcionó una contraseña, hashearla
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Si no se proporcionó contraseña, remover del array para no actualizar
            unset($validated['password']);
        }

        $person->update($validated);

        $successMessage = 'Perfil actualizado correctamente.';
        if (!empty($request->password)) {
            $successMessage .= ' Contraseña configurada exitosamente.';
        }

        return redirect()->route('profile.edit')->with('success', $successMessage);
    }
}
