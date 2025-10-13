<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Services\CostoProduccionService;
use App\Services\VentasResultadosService;
use App\Services\ConsistenciaEstimacionService;
use App\Services\FiltrosTrazabilidadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReporteIntegradoController extends Controller
{
    protected $costoService;
    protected $ventasService;
    protected $consistenciaService;
    protected $trazabilidadService;

    public function __construct(
        CostoProduccionService $costoService,
        VentasResultadosService $ventasService,
        ConsistenciaEstimacionService $consistenciaService,
        FiltrosTrazabilidadService $trazabilidadService
    ) {
        $this->costoService = $costoService;
        $this->ventasService = $ventasService;
        $this->consistenciaService = $consistenciaService;
        $this->trazabilidadService = $trazabilidadService;
    }

    /**
     * Panel principal de reportes con integración Sprint 11
     */
    public function panel()
    {
        try {
            // Obtener estadísticas generales
            $estadisticas = $this->obtenerEstadisticasGenerales();
            
            return view('reportes.panel', compact('estadisticas'));
        } catch (\Exception $e) {
            Log::error('Error en panel de reportes: ' . $e->getMessage());
            return view('reportes.panel', ['estadisticas' => []]);
        }
    }

    /**
     * Reporte consolidado que combina todos los análisis
     */
    public function consolidado(Request $request)
    {
        try {
            $filtros = $request->only(['lote_id', 'unidad_id', 'fecha_inicio', 'fecha_fin']);
            
            // Obtener datos consolidados
            $datos = [
                'resumen_ejecutivo' => $this->obtenerResumenEjecutivo($filtros),
                'costos_detallados' => $this->obtenerCostosConsolidados($filtros),
                'ventas_analisis' => $this->obtenerVentasConsolidadas($filtros),
                'consistencia_datos' => $this->obtenerConsistenciaConsolidada($filtros),
                'trazabilidad' => $this->obtenerTrazabilidadConsolidada($filtros),
                'filtros_aplicados' => $filtros
            ];

            return view('reportes.consolidado', compact('datos'));
        } catch (\Exception $e) {
            Log::error('Error en reporte consolidado: ' . $e->getMessage());
            return back()->with('error', 'Error al generar reporte consolidado');
        }
    }

    /**
     * Comparativa entre reportes tradicionales y Sprint 11
     */
    public function comparativa(Request $request)
    {
        try {
            $loteId = $request->get('lote_id');
            
            if (!$loteId) {
                return back()->with('error', 'Debe seleccionar un lote para la comparativa');
            }

            $lote = Lote::findOrFail($loteId);
            
            // Análisis tradicional
            $analisisTradicional = $this->obtenerAnalisisTradicional($lote);
            
            // Análisis Sprint 11
            $analisisSprint11 = $this->obtenerAnalisisSprint11($lote);
            
            $comparativa = [
                'lote' => $lote,
                'tradicional' => $analisisTradicional,
                'sprint11' => $analisisSprint11,
                'diferencias' => $this->calcularDiferencias($analisisTradicional, $analisisSprint11)
            ];

            return view('reportes.comparativa', compact('comparativa'));
        } catch (\Exception $e) {
            Log::error('Error en comparativa de reportes: ' . $e->getMessage());
            return back()->with('error', 'Error al generar comparativa');
        }
    }

    /**
     * Exportar reporte integrado
     */
    public function exportarIntegrado(Request $request)
    {
        try {
            $formato = $request->get('formato', 'pdf');
            $filtros = $request->only(['lote_id', 'unidad_id', 'fecha_inicio', 'fecha_fin']);
            
            // Generar datos para exportación
            $datos = $this->obtenerDatosCompletos($filtros);
            
            switch ($formato) {
                case 'pdf':
                    return $this->exportarPDF($datos);
                case 'excel':
                    return $this->exportarExcel($datos);
                case 'csv':
                    return $this->exportarCSV($datos);
                default:
                    return back()->with('error', 'Formato de exportación no válido');
            }
        } catch (\Exception $e) {
            Log::error('Error en exportación integrada: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar reporte');
        }
    }

    // Métodos privados de apoyo

    private function obtenerEstadisticasGenerales()
    {
        return [
            'total_lotes' => Lote::count(),
            'total_unidades' => UnidadProduccion::count(),
            'lotes_activos' => Lote::where('estado', 'activo')->count(),
            'reportes_disponibles' => [
                'ganancias_tradicional' => true,
                'costos_sprint11' => true,
                'ventas_sprint11' => true,
                'consistencia_sprint11' => true,
                'trazabilidad_sprint11' => true
            ]
        ];
    }

    private function obtenerResumenEjecutivo($filtros)
    {
        $lotes = $this->aplicarFiltrosBasicos($filtros);
        
        return [
            'total_produccion' => $lotes->sum('biomasa_total'),
            'total_ventas' => $lotes->sum('ventas_total'),
            'costo_promedio_libra' => $this->calcularCostoPromedioLibra($lotes),
            'margen_promedio' => $this->calcularMargenPromedio($lotes),
            'nivel_consistencia' => $this->calcularNivelConsistencia($lotes),
            'alertas_activas' => $this->contarAlertasActivas($lotes)
        ];
    }

    private function obtenerCostosConsolidados($filtros)
    {
        $lotes = $this->aplicarFiltrosBasicos($filtros);
        $resultados = [];

        foreach ($lotes->take(10) as $lote) {
            try {
                $costos = $this->costoService->calcularCostoPorLibra($lote);
                $resultados[] = [
                    'lote' => $lote,
                    'costos' => $costos
                ];
            } catch (\Exception $e) {
                Log::warning("Error calculando costos para lote {$lote->id}: " . $e->getMessage());
            }
        }

        return $resultados;
    }

    private function obtenerVentasConsolidadas($filtros)
    {
        try {
            return $this->ventasService->obtenerResultadosVentas($filtros);
        } catch (\Exception $e) {
            Log::warning("Error obteniendo ventas consolidadas: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerConsistenciaConsolidada($filtros)
    {
        try {
            // Usar los filtros para obtener consistencia, no lotes individuales
            return $this->consistenciaService->verificarConsistencia($filtros);
        } catch (\Exception $e) {
            Log::warning("Error obteniendo consistencia consolidada: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerTrazabilidadConsolidada($filtros)
    {
        try {
            return $this->trazabilidadService->aplicarFiltrosAvanzados($filtros);
        } catch (\Exception $e) {
            Log::warning("Error obteniendo trazabilidad: " . $e->getMessage());
            return [];
        }
    }

    private function aplicarFiltrosBasicos($filtros)
    {
        $query = Lote::with(['unidadProduccion', 'alimentaciones', 'ventas']);

        if (!empty($filtros['lote_id'])) {
            $query->where('id', $filtros['lote_id']);
        }

        if (!empty($filtros['unidad_id'])) {
            $query->where('unidad_produccion_id', $filtros['unidad_id']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $query->where('fecha_siembra', '>=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $query->where('fecha_siembra', '<=', $filtros['fecha_fin']);
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function obtenerAnalisisTradicional($lote)
    {
        $lote->load(['alimentaciones.tipoAlimento', 'ventas', 'unidadProduccion']);

        $totalAlimentacion = $lote->alimentaciones->sum('costo_total');
        $totalVentas = $lote->ventas->sum('total_venta');
        $precioCompra = $lote->precio_compra ?? 0;
        $totalCostos = $precioCompra + $totalAlimentacion;

        return [
            'precio_compra' => $precioCompra,
            'costo_alimentacion' => $totalAlimentacion,
            'total_costos' => $totalCostos,
            'total_ventas' => $totalVentas,
            'ganancia' => $totalVentas - $totalCostos,
            'margen' => $totalVentas > 0 ? (($totalVentas - $totalCostos) / $totalVentas) * 100 : 0
        ];
    }

    private function obtenerAnalisisSprint11($lote)
    {
        try {
            $costos = $this->costoService->calcularCostoPorLibra($lote);
            $ventas = $this->ventasService->obtenerResultadosVentas(['lote_id' => $lote->id]);
            $consistencia = $this->consistenciaService->verificarConsistencia(['lote_ids' => [$lote->id]]);

            return [
                'costos_detallados' => $costos,
                'analisis_ventas' => $ventas,
                'nivel_consistencia' => isset($consistencia[0]['nivel_confianza']) ? $consistencia[0]['nivel_confianza'] : 0,
                'alertas' => isset($consistencia[0]['alertas']) ? $consistencia[0]['alertas'] : []
            ];
        } catch (\Exception $e) {
            Log::warning("Error en análisis Sprint 11 para lote {$lote->id}: " . $e->getMessage());
            return [];
        }
    }

    private function calcularDiferencias($tradicional, $sprint11)
    {
        $diferencias = [];

        if (isset($tradicional['total_costos']) && isset($sprint11['costos_detallados']['costo_total'])) {
            $diferencias['costos'] = $sprint11['costos_detallados']['costo_total'] - $tradicional['total_costos'];
        }

        if (isset($tradicional['margen']) && isset($sprint11['costos_detallados']['margen_porcentaje'])) {
            $diferencias['margen'] = $sprint11['costos_detallados']['margen_porcentaje'] - $tradicional['margen'];
        }

        return $diferencias;
    }

    private function calcularCostoPromedioLibra($lotes)
    {
        $totalCosto = 0;
        $totalLibras = 0;

        foreach ($lotes as $lote) {
            try {
                $costos = $this->costoService->calcularCostoPorLibra($lote);
                if (isset($costos['costo_por_libra']) && isset($costos['produccion_libras'])) {
                    $totalCosto += $costos['costo_total'];
                    $totalLibras += $costos['produccion_libras'];
                }
            } catch (\Exception $e) {
                // Continuar con el siguiente lote
            }
        }

        return $totalLibras > 0 ? $totalCosto / $totalLibras : 0;
    }

    private function calcularMargenPromedio($lotes)
    {
        $margenes = [];

        foreach ($lotes as $lote) {
            try {
                $costos = $this->costoService->calcularCostoPorLibra($lote);
                if (isset($costos['margen_porcentaje'])) {
                    $margenes[] = $costos['margen_porcentaje'];
                }
            } catch (\Exception $e) {
                // Continuar con el siguiente lote
            }
        }

        return count($margenes) > 0 ? array_sum($margenes) / count($margenes) : 0;
    }

    private function calcularNivelConsistencia($lotes)
    {
        try {
            // Obtener IDs de lotes para usar como filtro
            $lotesIds = $lotes->pluck('id')->toArray();
            $consistencia = $this->consistenciaService->verificarConsistencia(['lote_ids' => $lotesIds]);
            
            if (!empty($consistencia)) {
                $niveles = collect($consistencia)->pluck('nivel_confianza')->filter();
                return $niveles->count() > 0 ? $niveles->avg() : 0;
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::warning("Error calculando nivel de consistencia: " . $e->getMessage());
            return 0;
        }
    }

    private function contarAlertasActivas($lotes)
    {
        try {
            // Obtener IDs de lotes para usar como filtro
            $lotesIds = $lotes->pluck('id')->toArray();
            $consistencia = $this->consistenciaService->verificarConsistencia(['lote_ids' => $lotesIds]);
            
            $totalAlertas = 0;
            foreach ($consistencia as $resultado) {
                if (isset($resultado['alertas'])) {
                    $totalAlertas += count($resultado['alertas']);
                }
            }
            
            return $totalAlertas;
        } catch (\Exception $e) {
            Log::warning("Error contando alertas activas: " . $e->getMessage());
            return 0;
        }
    }

    private function obtenerDatosCompletos($filtros)
    {
        return [
            'resumen' => $this->obtenerResumenEjecutivo($filtros),
            'costos' => $this->obtenerCostosConsolidados($filtros),
            'ventas' => $this->obtenerVentasConsolidadas($filtros),
            'consistencia' => $this->obtenerConsistenciaConsolidada($filtros),
            'filtros' => $filtros,
            'fecha_generacion' => now()
        ];
    }

    private function exportarPDF($datos)
    {
        // Implementar exportación PDF
        return response()->json(['message' => 'Exportación PDF en desarrollo']);
    }

    private function exportarExcel($datos)
    {
        // Implementar exportación Excel
        return response()->json(['message' => 'Exportación Excel en desarrollo']);
    }

    private function exportarCSV($datos)
    {
        // Implementar exportación CSV
        return response()->json(['message' => 'Exportación CSV en desarrollo']);
    }
}