<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Verificar qué tipo de usuario se autenticó
        if (Auth::guard('person')->check()) {
            $person = Auth::guard('person')->user();
            $welcomeMessage = '¡Bienvenido ' . $person->first_name . '!';

            // Redirigir según el rol
            if ($person->role === 'seller') {
                return redirect()->intended(route('seller.dashboard'))
                    ->with('success', $welcomeMessage);
            } else {
                return redirect()->intended(route('catalog'))
                    ->with('success', $welcomeMessage);
            }
        }

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $welcomeMessage = '¡Bienvenido ' . $user->name . '!';

            return redirect()->intended('/admin')
                ->with('success', $welcomeMessage);
        }

        // Fallback
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Cerrar sesión en ambos guards
        Auth::guard('person')->logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
} 