<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TipoCambioController;

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

// Rutas para Tipo de Cambio
Route::middleware(['auth'])->group(function () {
    Route::get('/tipo-cambio/actual', [TipoCambioController::class, 'actual']);
    Route::post('/tipo-cambio/actualizar', [TipoCambioController::class, 'actualizar']);
    Route::get('/tipo-cambio/historial', [TipoCambioController::class, 'historial']);
});