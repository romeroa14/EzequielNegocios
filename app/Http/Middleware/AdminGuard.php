<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Usar el guard admin para autenticar
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();
        
        // Verificar que el usuario esté activo
        if (!$user->is_active) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Tu cuenta está desactivada.');
        }

        // Verificar roles permitidos para admin
        if (!in_array($user->role, ['admin', 'producer', 'technician', 'support'])) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
