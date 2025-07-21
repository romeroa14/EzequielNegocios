<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CookieController extends Controller
{
    /**
     * Guardar las preferencias de cookies del usuario
     */
    public function storePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'essential' => 'required|boolean',
            'analytics' => 'required|boolean',
            'advertising' => 'required|boolean',
        ]);

        // Guardar preferencias en la sesión
        session([
            'cookies_accepted' => true,
            'cookie_preferences' => [
                'essential' => $request->essential,
                'analytics' => $request->analytics,
                'advertising' => $request->advertising,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferencias de cookies guardadas correctamente'
        ]);
    }

    /**
     * Mostrar la página de política de privacidad
     */
    public function privacyPolicy()
    {
        return view('policies.privacy-policy');
    }

    /**
     * Mostrar la página de política de cookies
     */
    public function cookiePolicy()
    {
        return view('policies.cookie-policy');
    }

    /**
     * Mostrar las preferencias actuales de cookies
     */
    public function showPreferences()
    {
        $preferences = session('cookie_preferences', [
            'essential' => true,
            'analytics' => false,
            'advertising' => false,
        ]);

        return view('policies.cookie-preferences', compact('preferences'));
    }

    /**
     * Actualizar las preferencias de cookies
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'essential' => 'required|boolean',
            'analytics' => 'required|boolean',
            'advertising' => 'required|boolean',
        ]);

        session([
            'cookie_preferences' => [
                'essential' => $request->essential,
                'analytics' => $request->analytics,
                'advertising' => $request->advertising,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferencias actualizadas correctamente'
        ]);
    }
}
