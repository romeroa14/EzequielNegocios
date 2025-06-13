<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotPerson
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web')->check() || !Auth::guard('web')->user() instanceof \App\Models\Person) {
            return redirect('/login');
        }

        return $next($request);
    }
} 