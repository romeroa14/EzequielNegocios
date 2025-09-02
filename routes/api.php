<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\State;
use App\Models\Municipality;
use App\Http\Controllers\Api\ExchangeRateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->group(function () {
    // Webhooks para automatización (sin CSRF, sin sesiones)
    Route::post('/webhook/bcv/update-rates', [App\Http\Controllers\WebhookController::class, 'updateBcvRates'])->name('api.webhook.bcv.update-rates');
    Route::get('/webhook/health', [App\Http\Controllers\WebhookController::class, 'healthCheck'])->name('api.webhook.health');
    Route::post('/webhook/bcv/cleanup', [App\Http\Controllers\WebhookController::class, 'cleanupBcvRates'])->name('api.webhook.bcv.cleanup');
    
    // Rutas para los selects en cascada de ubicación
    // Rutas para los selects en cascada de ubicación
    Route::get('/states/{state}/municipalities', function (State $state) {
        return $state->municipalities()->select('id', 'name')->get();
    });

    Route::get('/municipalities/{municipality}/parishes', function (Municipality $municipality) {
        return $municipality->parishes()->select('id', 'name')->get();
    });
    
    // Rutas para tasas de cambio
    Route::get('/exchange-rates', [ExchangeRateController::class, 'getAllRates']);
    Route::get('/exchange-rates/{currency}', [ExchangeRateController::class, 'getRate']);
    
    // Ruta para actualizar tasas (protegida con throttle)
    Route::post('/exchange-rates/update', [ExchangeRateController::class, 'forceUpdate'])
        ->middleware('throttle:10,1'); // Máximo 10 requests por minuto
}); 