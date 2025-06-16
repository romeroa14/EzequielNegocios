<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:people'],
            'phone' => ['required', 'string', 'max:20'],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:buyer,seller'],
            'address' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
        ];

        if ($request->role === 'seller') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['company_rif'] = ['required', 'string', 'max:20'];
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

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
                'company_name' => $request->company_name,
                'company_rif' => $request->company_rif,
                'is_active' => true,
                'is_verified' => false,
            ]);

            event(new Registered($person));

            Auth::guard('web')->login($person);

            DB::commit();

            return redirect()->route($person->getDashboardRoute());

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar el usuario. Por favor, intente nuevamente.']);
        }
    }
} 