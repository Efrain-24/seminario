<?php

namespace App\Http\Controllers;

use App\Services\VentasResultadosService;
use Illuminate\Http\Request;

class VentasResultadosController extends Controller
{
    protected $ventasService;
    
    public function __construct(VentasResultadosService $ventasService)
    {
        $this->ventasService = $ventasService;
    }

    /**
     * RF36: Mostrar análisis de ventas ejecutadas y potenciales
     */
    public function index(Request $request)
    {
        // Obtener filtros del request
        $filtros = $request->only(['especie', 'estado', 'fecha_desde', 'fecha_hasta']);
        
        // Obtener resultados de ventas
        $resultados = $this->ventasService->obtenerResultadosVentas($filtros);
        
        return view('ventas.resultados.index', compact('resultados'));
    }

    /**
     * Mostrar comparativa consolidada de ventas vs inventario disponible
     */
    public function consolidado(Request $request)
    {
        $filtros = $request->only(['especie', 'estado', 'fecha_desde', 'fecha_hasta']);
        $resultados = $this->ventasService->obtenerResultadosVentas($filtros);
        
        // Calcular datos consolidados para gráficos
        $datosGraficos = $this->prepararDatosGraficos($resultados);
        
        return view('ventas.resultados.consolidado', compact('resultados', 'datosGraficos'));
    }

    /**
     * Exportar reporte de ventas en formato CSV
     */
    public function exportar(Request $request)
    {
        $filtros = $request->only(['especie', 'estado', 'fecha_desde', 'fecha_hasta']);
        $resultados = $this->ventasService->obtenerResultadosVentas($filtros);
        
        $datosExport = [];
        
        foreach ($resultados['lotes'] as $loteData) {
            $mejorEscenario = [];
            if (isset($loteData['ventas_potenciales']['escenarios']) && !empty($loteData['ventas_potenciales']['escenarios'])) {
                $mejorEscenario = collect($loteData['ventas_potenciales']['escenarios'])
                    ->sortByDesc('precio_por_lb')
                    ->first();
            }
                
            $datosExport[] = [
                'Código Lote' => $loteData['lote']['codigo'],
                'Especie' => $loteData['lote']['especie'],
                'Estado' => ucfirst($loteData['lote']['estado']),
                'Biomasa Actual (kg)' => number_format($loteData['lote']['biomasa_actual_kg'], 2),
                'Biomasa Actual (lb)' => number_format($loteData['lote']['biomasa_actual_lb'], 2),
                
                // Ventas Ejecutadas
                'Peso Vendido (lb)' => number_format($loteData['ventas_ejecutadas']['peso_total_lb'], 2),
                'Precio Promedio Venta (Q/lb)' => number_format($loteData['ventas_ejecutadas']['precio_promedio_lb'], 2),
                'Ingreso Ejecutado (Q)' => number_format($loteData['ventas_ejecutadas']['ingreso_total'], 2),
                'Número de Ventas' => $loteData['ventas_ejecutadas']['numero_ventas'],
                
                // Ventas Potenciales
                'Peso Disponible (lb)' => number_format($loteData['ventas_potenciales']['biomasa_disponible_lb'], 2),
                'Precio Óptimo (Q/lb)' => number_format($mejorEscenario['precio_por_lb'] ?? 0, 2),
                'Ingreso Potencial (Q)' => number_format($mejorEscenario['ingreso_estimado'] ?? 0, 2),
                
                // Comparación
                'Porcentaje Vendido (%)' => number_format($loteData['comparacion']['porcentaje_vendido'], 1),
                'Oportunidad Mejora (Q)' => number_format($loteData['comparacion']['oportunidad_mejora']['valor_oportunidad'], 2),
                
                // Márgenes
                'Margen Ejecutado (%)' => number_format($loteData['margenes']['margen_ventas_ejecutadas'], 1),
                'Margen Potencial (%)' => number_format($loteData['margenes']['margen_ventas_potenciales'], 1),
            ];
        }
        
        $filename = 'reporte_ventas_resultados_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($datosExport) {
            $file = fopen('php://output', 'w');
            
            // Escribir BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Escribir encabezados
            if (!empty($datosExport)) {
                fputcsv($file, array_keys($datosExport[0]));
            }
            
            // Escribir datos
            foreach ($datosExport as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * API para obtener datos de ventas
     */
    public function api(Request $request)
    {
        $filtros = $request->only(['especie', 'estado', 'fecha_desde', 'fecha_hasta']);
        $resultados = $this->ventasService->obtenerResultadosVentas($filtros);
        
        return response()->json([
            'success' => true,
            'data' => $resultados
        ]);
    }

    /**
     * Preparar datos para gráficos
     */
    private function prepararDatosGraficos(array $resultados): array
    {
        $etiquetas = [];
        $ventasEjecutadas = [];
        $ventasPotenciales = [];
        $margenes = [];
        
        foreach ($resultados['lotes'] as $loteData) {
            $etiquetas[] = $loteData['lote']['codigo'];
            $ventasEjecutadas[] = $loteData['ventas_ejecutadas']['ingreso_total'];
            
            $mejorEscenario = [];
            if (isset($loteData['ventas_potenciales']['escenarios']) && !empty($loteData['ventas_potenciales']['escenarios'])) {
                $mejorEscenario = collect($loteData['ventas_potenciales']['escenarios'])
                    ->sortByDesc('precio_por_lb')
                    ->first();
            }
            $ventasPotenciales[] = $mejorEscenario['ingreso_estimado'] ?? 0;
            
            $margenes[] = $loteData['margenes']['margen_ventas_ejecutadas'];
        }
        
        return [
            'etiquetas' => $etiquetas,
            'ventas_ejecutadas' => $ventasEjecutadas,
            'ventas_potenciales' => $ventasPotenciales,
            'margenes' => $margenes,
            'colores' => [
                'ejecutadas' => 'rgba(34, 197, 94, 0.8)',
                'potenciales' => 'rgba(59, 130, 246, 0.8)',
                'margenes' => 'rgba(168, 85, 247, 0.8)'
            ]
        ];
    }
}