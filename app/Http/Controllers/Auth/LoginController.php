<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    

    /**
     * @var string
     */
    
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        Log::info('Intento de login', [
            'email' => $credentials['email'],
            'remember' => $request->boolean('remember')
        ]);

        // Intentar autenticar como persona (comprador/vendedor)
        if (Auth::guard('person')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $person = Auth::guard('person')->user();
            $welcomeMessage = '¡Bienvenido ' . $person->first_name . '!';

            Log::info('Login exitoso como persona', [
                'person_id' => $person->id,
                'role' => $person->role,
                'email' => $person->email
            ]);

            // Redirigir según el rol
            if ($person->role === 'seller') {
                return redirect()->intended(route('seller.dashboard'))
                    ->with('success', $welcomeMessage);
            } else {
                return redirect()->intended(route('catalog'))
                    ->with('success', $welcomeMessage);
            }
        }

        // Si falla, intentar autenticar como usuario admin
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();
            $welcomeMessage = '¡Bienvenido ' . $user->name . '!';

            Log::info('Login exitoso como admin', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->intended('/admin')
                ->with('success', $welcomeMessage);
        }

        Log::warning('Login fallido', [
            'email' => $credentials['email'],
            'reason' => 'Credenciales no válidas'
        ]);

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
