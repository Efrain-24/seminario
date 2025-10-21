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

// Búsqueda de clientes por NIT
Route::get('/clientes/buscar-nit', function (Request $request) {
    $nit = $request->query('nit');
    
    if (!$nit || strlen($nit) < 3) {
        return response()->json([
            'success' => false,
            'message' => 'NIT debe tener al menos 3 caracteres'
        ]);
    }
    
    try {
        $clientes = \App\Models\Cliente::where('documento', 'LIKE', "%{$nit}%")
            ->orWhere('nombre', 'LIKE', "%{$nit}%")
            ->limit(10)
            ->get(['id', 'documento as nit', 'nombre']);
        
        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en la búsqueda: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para Tipo de Cambio
Route::middleware(['auth'])->group(function () {
    Route::get('/tipo-cambio/actual', [TipoCambioController::class, 'actual']);
    Route::post('/tipo-cambio/actualizar', [TipoCambioController::class, 'actualizar']);
    Route::get('/tipo-cambio/historial', [TipoCambioController::class, 'historial']);
});

// Ruta para obtener precio por libra actual
Route::get('/precio-libra/actual', [App\Http\Controllers\PrecioLibraController::class, 'getPrecioActual']);

// Ruta temporal sin auth para pruebas
Route::post('/tipo-cambio/test-actualizar', [TipoCambioController::class, 'actualizar']);