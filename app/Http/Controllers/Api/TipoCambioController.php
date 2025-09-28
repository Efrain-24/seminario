<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoCambio;
use Illuminate\Support\Facades\Artisan;

class TipoCambioController extends Controller
{
    /**
     * Obtener el tipo de cambio actual
     */
    public function actual()
    {
        $tipoCambio = TipoCambio::ultimoDisponible();
        
        return response()->json([
            'success' => true,
            'data' => [
                'fecha' => $tipoCambio->fecha->format('Y-m-d'),
                'fecha_formateada' => $tipoCambio->fecha->format('d/m/Y'),
                'valor' => $tipoCambio->valor,
                'valor_formateado' => $tipoCambio->valor_formateado,
                'dias_diferencia' => now()->diffInDays($tipoCambio->fecha)
            ]
        ]);
    }

    /**
     * Actualizar el tipo de cambio ejecutando el comando
     */
    public function actualizar(Request $request)
    {
        try {
            // Ejecutar el comando para obtener el tipo de cambio
            $exitCode = Artisan::call('banguat:tipo-cambio');
            
            // Capturar la salida del comando
            $output = Artisan::output();
            
            // Obtener el último tipo de cambio disponible
            $tipoCambio = TipoCambio::ultimoDisponible();
            
            if ($tipoCambio) {
                $esHoy = $tipoCambio->fecha === now()->toDateString();
                
                // Determinar si fue un éxito o se usó fallback
                $usoFallback = str_contains($output, 'fallback') || str_contains($output, 'Error al consultar Banguat');
                
                $mensaje = $usoFallback 
                    ? "⚠️ El servicio del Banguat no está disponible. Se está usando un valor aproximado de referencia."
                    : "✅ Tipo de cambio actualizado correctamente.";
                
                $status = $usoFallback ? 'warning' : 'success';
                
                return response()->json([
                    'success' => true,
                    'status' => $status,
                    'message' => $mensaje,
                    'data' => [
                        'valor' => $tipoCambio->valor_formateado,
                        'fecha' => $tipoCambio->fecha_formateada,
                        'es_hoy' => $esHoy,
                        'uso_fallback' => $usoFallback,
                        'detalle' => $usoFallback 
                            ? 'Los valores de referencia se basan en promedios históricos del tipo de cambio GTQ/USD.'
                            : 'Información obtenida directamente del Banco de Guatemala.'
                    ],
                    'output' => $output
                ]);
            } else {
                Log::error('No se pudo obtener ningún tipo de cambio, ni siquiera el fallback');
                return response()->json([
                    'success' => false,
                    'message' => '❌ Error grave: No se pudo obtener ningún tipo de cambio.',
                    'output' => $output
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar tipo de cambio: ' . $e->getMessage());
            
            // Intentar obtener el último disponible como respaldo
            $tipoCambio = TipoCambio::ultimoDisponible();
            
            if ($tipoCambio) {
                return response()->json([
                    'success' => true,
                    'status' => 'warning',
                    'message' => '⚠️ No se pudo actualizar, pero hay un tipo de cambio disponible.',
                    'data' => [
                        'valor' => $tipoCambio->valor_formateado,
                        'fecha' => $tipoCambio->fecha_formateada,
                        'es_hoy' => $tipoCambio->fecha === now()->toDateString(),
                        'uso_fallback' => true
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al actualizar el tipo de cambio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener historial de tipos de cambio
     */
    public function historial(Request $request)
    {
        $dias = $request->get('dias', 30);
        $historial = TipoCambio::historial($dias);
        
        return response()->json([
            'success' => true,
            'data' => $historial->map(function($item) {
                return [
                    'fecha' => $item->fecha->format('Y-m-d'),
                    'fecha_formateada' => $item->fecha->format('d/m/Y'),
                    'valor' => $item->valor,
                    'valor_formateado' => $item->valor_formateado
                ];
            })
        ]);
    }
}
