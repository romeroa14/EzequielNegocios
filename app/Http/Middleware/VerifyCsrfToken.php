<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/*', // Excluir todas las rutas de webhook del CSRF
        'webhook/bcv/*', // Excluir específicamente las rutas del BCV
        'webhook/bcv/update-rates', // Excluir la ruta específica del BCV
    ];
} 