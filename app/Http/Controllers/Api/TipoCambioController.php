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
    public function actualizar()
    {
        try {
            // Ejecutar el comando para obtener el tipo de cambio
            Artisan::call('banguat:tipo-cambio');
            
            // Obtener el resultado actualizado
            $tipoCambio = TipoCambio::actual();
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de cambio actualizado correctamente',
                'data' => [
                    'fecha' => $tipoCambio->fecha->format('Y-m-d'),
                    'fecha_formateada' => $tipoCambio->fecha->format('d/m/Y'),
                    'valor' => $tipoCambio->valor,
                    'valor_formateado' => $tipoCambio->valor_formateado
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de cambio: ' . $e->getMessage()
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
