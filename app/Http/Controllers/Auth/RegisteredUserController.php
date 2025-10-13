<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $states = State::where('country_id', 296)->get();
        return view('auth.register', compact('states'));
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

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:people'],
            'phone' => ['required', 'string', 'max:20'],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20', 'unique:people,identification_number'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:buyer,seller'],
            'address' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'municipality_id' => ['required', 'exists:municipalities,id'],
            'parish_id' => ['required', 'exists:parishes,id'],
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
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol debe ser comprador o vendedor.',
            'state_id.required' => 'El estado es obligatorio.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
            'municipality_id.required' => 'El municipio es obligatorio.',
            'municipality_id.exists' => 'El municipio seleccionado no es válido.',
            'parish_id.required' => 'La parroquia es obligatoria.',
            'parish_id.exists' => 'La parroquia seleccionada no es válida.',
        ];

        // if ($request->role === 'seller') {
        //     $rules['company_name'] = ['required', 'string', 'max:255'];
        //     $rules['company_rif'] = ['required', 'string', 'max:20', 'unique:people,company_rif'];
            
        //     $messages['company_name.required'] = 'El nombre de la empresa es obligatorio.';
        //     $messages['company_name.max'] = 'El nombre de la empresa no puede tener más de 255 caracteres.';
        //     $messages['company_rif.required'] = 'El RIF de la empresa es obligatorio.';
        //     $messages['company_rif.max'] = 'El RIF de la empresa no puede tener más de 20 caracteres.';
        //     $messages['company_rif.unique'] = 'Este RIF ya está registrado.';
        // }

        $request->validate($rules, $messages);

        try {
            $person = Person::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'identification_type' => $request->identification_type,
                'identification_number' => $request->identification_number,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'address' => $request->address,
                'sector' => $request->sector,
                'state_id' => $request->state_id,
                'municipality_id' => $request->municipality_id,
                'parish_id' => $request->parish_id,
                // 'company_name' => $request->company_name,
                // 'company_rif' => $request->company_rif,
                'is_active' => true,
                'is_verified' => false,
            ]);

            event(new Registered($person));

            Auth::login($person);

            if ($person->role === 'seller') {
                return redirect()->route('seller.dashboard');
            }

            return redirect()->route('buyer.seller/listings');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar el usuario. Por favor, intente nuevamente.']);
        }
    }
} 