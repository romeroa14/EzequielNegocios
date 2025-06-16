<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Person;

class RedirectIfNotPerson
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user() instanceof Person) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
} 