<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->person || $user->person->role !== $role) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección. Debes registrarte como vendedor.');
        }

        return $next($request);
    }
} 