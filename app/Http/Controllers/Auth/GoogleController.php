<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Providers\RouteServiceProvider;

class GoogleController extends Controller
{
    /**
     * Redirigir al usuario a Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        Log::info('Google OAuth redirect iniciado', [
            'role' => $request->role,
            'all_params' => $request->all()
        ]);

        // Validar que se haya seleccionado un rol
        $request->validate([
            'role' => 'required|in:buyer,seller'
        ]);

        // Guardar el rol en la sesión para usarlo después
        session(['oauth_role' => $request->role]);

        Log::info('Redirigiendo a Google OAuth', [
            'role' => $request->role,
            'session_role' => session('oauth_role')
        ]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Manejar el callback de Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('Google OAuth callback iniciado', [
                'request_params' => $request->all(),
                'session_role' => session('oauth_role')
            ]);

            // Obtener los datos del usuario de Google
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Datos de Google recibidos', [
                'google_id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'role' => session('oauth_role', 'buyer')
            ]);

            // Obtener el rol guardado en la sesión
            $role = session('oauth_role', 'buyer');

            // Buscar si la persona ya existe (solo usamos Person, no User)
            $person = Person::where('email', $googleUser->email)->first();

            Log::info('Verificación de persona existente', [
                'person_exists' => $person ? true : false,
                'person_id' => $person ? $person->id : null,
                'person_verified' => $person ? $person->is_verified : null
            ]);

            if ($person) {
                Log::info('Persona existente encontrada');
                
                // Verificar si el google_id coincide
                if ($person->google_id !== $googleUser->id) {
                    Log::warning('Google ID no coincide', [
                        'stored_google_id' => $person->google_id,
                        'new_google_id' => $googleUser->id,
                        'email' => $googleUser->email
                    ]);
                    
                    // Actualizar el google_id si es diferente
                    $person->update(['google_id' => $googleUser->id]);
                }
                
                // Persona existe, verificar si está verificado
                if (!$person->is_verified) {
                    // Verificar si ya tiene datos completos
                    if ($person->hasCompleteData()) {
                        Log::info('Persona tiene datos completos, marcando como verificado automáticamente');
                        $person->markAsVerifiedIfComplete();
                        
                        Auth::guard('person')->login($person);
                        
                        if ($person->role === 'seller') {
                            return redirect()->route('seller.dashboard')
                                ->with('success', '¡Bienvenido de vuelta! Tu cuenta ha sido verificada automáticamente.');
                        } else {
                            return redirect()->route('catalog')
                                ->with('success', '¡Bienvenido de vuelta! Tu cuenta ha sido verificada automáticamente.');
                        }
                    } else {
                        Log::info('Persona no verificado y sin datos completos, redirigiendo a completar perfil');
                        // Persona no verificado, redirigir a completar perfil
                        Auth::guard('person')->login($person);
                        return redirect()->route('profile.complete')
                            ->with('warning', 'Por favor completa tu perfil para verificar tu cuenta.');
                    }
                }

                Log::info('Persona verificado, iniciando sesión');
                // Persona verificado, iniciar sesión
                Auth::guard('person')->login($person);
                
                // Redirigir al dashboard según el rol
                if ($person->role === 'seller') {
                    Log::info('Redirigiendo al dashboard del seller existente');
                    return redirect()->route('seller.dashboard')
                        ->with('success', '¡Bienvenido de vuelta!');
                } else {
                    Log::info('Redirigiendo al catálogo para compradores existentes');
                    return redirect()->route('catalog')
                        ->with('success', '¡Bienvenido de vuelta!');
                }
            }

            Log::info('Creando nueva persona');

            // Crear nueva persona (solo Person, no User)
            $person = Person::create([
                'first_name' => explode(' ', $googleUser->name)[0] ?? $googleUser->name,
                'last_name' => explode(' ', $googleUser->name)[1] ?? '',
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(16)),
                'role' => $role,
                'google_id' => $googleUser->id,
                'is_active' => true,
                'is_verified' => false, // Necesita completar perfil
                'email_verified_at' => now(), // Google ya verificó el email
            ]);

            Log::info('Persona creada', ['person_id' => $person->id]);

            // Iniciar sesión con el guard de Person
            Auth::guard('person')->login($person);
            
            // Verificar que la sesión se inició correctamente
            $isAuthenticated = Auth::guard('person')->check();
            $currentUser = Auth::guard('person')->user();
            
            Log::info('Sesión iniciada', [
                'person_id' => $person->id,
                'is_authenticated' => $isAuthenticated,
                'current_user_id' => $currentUser ? $currentUser->id : null,
                'session_id' => session()->getId(),
                'guard' => 'person'
            ]);
            
            if (!$isAuthenticated) {
                Log::error('Error: No se pudo iniciar sesión con el guard person');
                return redirect()->route('register')
                    ->with('error', 'Error al iniciar sesión. Por favor intenta de nuevo.');
            }

            // Redirigir al dashboard según el rol
            if ($role === 'seller') {
                Log::info('Redirigiendo al dashboard del seller');
                return redirect()->route('seller.dashboard')
                    ->with('success', '¡Bienvenido! Tu cuenta ha sido creada exitosamente.');
            } else {
                Log::info('Redirigiendo al catálogo para compradores');
                return redirect()->route('catalog')
                    ->with('success', '¡Bienvenido! Tu cuenta ha sido creada exitosamente.');
            }

        } catch (\Exception $e) {
            Log::error('Error en Google OAuth callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('register')
                ->with('error', 'Error al autenticarse con Google. Por favor intenta de nuevo.');
        }
    }

    /**
     * Mostrar formulario para completar perfil
     */
    public function showCompleteProfile()
    {
        $person = Auth::guard('person')->user();
        
        // Verificar si ya está verificado
        if ($person->is_verified) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        
        // Verificar si ya tiene datos completos y marcarlo como verificado automáticamente
        if ($person->hasCompleteData()) {
            $person->markAsVerifiedIfComplete();
            
            if ($person->role === 'seller') {
                return redirect()->route('seller.dashboard')
                    ->with('success', '¡Tu cuenta ha sido verificada automáticamente!');
            } else {
                return redirect()->route('catalog')
                    ->with('success', '¡Tu cuenta ha sido verificada automáticamente!');
            }
        }

        // Obtener los estados de Venezuela para el formulario
        $states = \App\Models\State::where('country_id', 296)->orderBy('name')->get();
        
        // Obtener municipios y parroquias si el usuario ya tiene state_id y municipality_id
        $municipalities = collect();
        $parishes = collect();
        
        if ($person->state_id) {
            $municipalities = \App\Models\Municipality::where('state_id', $person->state_id)->get();
        }
        
        if ($person->municipality_id) {
            $parishes = \App\Models\Parish::where('municipality_id', $person->municipality_id)->get();
        }

        return view('auth.complete-profile', compact('person', 'states', 'municipalities', 'parishes'));
    }

    /**
     * Completar perfil del usuario
     */
    public function completeProfile(Request $request)
    {
        $person = Auth::guard('person')->user();

        if (!$person) {
            return redirect()->route('register')
                ->with('error', 'No se encontró el perfil asociado.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'identification_type' => 'required|in:V,E,J,G',
            'identification_number' => 'required|string|max:20',
            'state_id' => 'required|exists:states,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'parish_id' => 'required|exists:parishes,id',
            'address' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
        ]);

        // Actualizar persona
        $person->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'identification_type' => $request->identification_type,
            'identification_number' => $request->identification_number,
            'state_id' => $request->state_id,
            'municipality_id' => $request->municipality_id,
            'parish_id' => $request->parish_id,
            'address' => $request->address,
            'sector' => $request->sector,
            'is_verified' => true,
        ]);

        // Redirigir según el rol después de completar el perfil
        if ($person->role === 'seller') {
            return redirect()->route('seller.dashboard')
                ->with('success', '¡Perfil completado exitosamente! Tu cuenta ha sido verificada.');
        } else {
            return redirect()->route('catalog')
                ->with('success', '¡Perfil completado exitosamente! Tu cuenta ha sido verificada.');
        }
    }
}
