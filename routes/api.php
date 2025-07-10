<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\State;
use App\Models\Municipality;

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
    // Rutas para los selects en cascada de ubicación
    Route::get('/states/{state}/municipalities', function (State $state) {
        return $state->municipalities()->select('id', 'name')->get();
    });

    Route::get('/municipalities/{municipality}/parishes', function (Municipality $municipality) {
        return $municipality->parishes()->select('id', 'name')->get();
    });
}); 