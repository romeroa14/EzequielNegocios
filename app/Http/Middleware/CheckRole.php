<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'No tienes permiso para acceder a esta sección.',
                    'required_role' => $role,
                    'your_role' => $request->user()->role
                ], 403);
            }

            return redirect()->route($request->user()->getDashboardRoute())
                ->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
} 