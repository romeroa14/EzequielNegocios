<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:buyer,seller'],
            'company_name' => ['required_if:role,seller', 'string', 'max:255'],
            'company_rif' => ['required_if:role,seller', 'string', 'max:20'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $person = Person::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'identification_type' => $request->identification_type,
                'identification_number' => $request->identification_number,
                'role' => $request->role,
                'company_name' => $request->company_name,
                'company_rif' => $request->company_rif,
            ]);

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            if ($request->role === 'seller') {
                return redirect()->route('seller.dashboard');
            }

            return redirect(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear el usuario. Por favor, intente nuevamente.']);
        }
    }
} 