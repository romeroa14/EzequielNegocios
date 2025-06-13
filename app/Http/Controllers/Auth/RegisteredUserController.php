<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:people,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20', 'unique:people,identification_number'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:buyer,seller'],
            'company_name' => ['required_if:role,seller', 'nullable', 'string', 'max:255'],
            'company_rif' => ['required_if:role,seller', 'nullable', 'string', 'max:20'],
        ]);

        // Crear usuario base
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'role' => 'producer', // Rol por defecto para el sistema admin
        ]);

        // Crear persona asociada
        $person = Person::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'identification_type' => $request->identification_type,
            'identification_number' => $request->identification_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'sector' => $request->sector,
            'role' => $request->role,
            'company_name' => $request->company_name,
            'company_rif' => $request->company_rif,
            'is_active' => true,
            'is_verified' => false,
        ]);

        event(new Registered($person));

        // Autenticar al usuario como persona
        Auth::guard('web')->login($person);

        return redirect(RouteServiceProvider::HOME)
            ->with('success', '¡Cuenta creada exitosamente!');
    }
} 