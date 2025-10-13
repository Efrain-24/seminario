<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Services\CostoProduccionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostoProduccionController extends Controller
{
    protected $costoService;
    
    public function __construct(CostoProduccionService $costoService)
    {
        $this->costoService = $costoService;
    }

    /**
     * RF22: Mostrar cálculo detallado del costo por libra producida
     */
    public function index(Request $request)
    {
        // Obtener lotes del usuario actual
        $query = Lote::query();
        
        // Filtros opcionales
        if ($request->filled('especie')) {
            $query->where('especie', 'like', '%' . $request->especie . '%');
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_inicio', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inicio', '<=', $request->fecha_hasta);
        }
        
        $lotes = $query->get();
        
        // Generar reporte de costos
        $reporteCostos = $this->costoService->generarReporteCostos($lotes);
        
        return view('costos.produccion.index', compact('reporteCostos', 'lotes'));
    }

    /**
     * Mostrar detalle de costos para un lote específico
     */
    public function show(Lote $lote)
    {
        $detalleCostos = $this->costoService->calcularCostoPorLibra($lote);
        
        // Obtener datos adicionales para el detalle
        $alimentaciones = $lote->alimentaciones()
            ->with(['inventarioItem'])
            ->orderBy('fecha_alimentacion', 'desc')
            ->get();
            
        $mantenimientos = $lote->mantenimientos()
            ->with(['insumos.inventarioItem'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('costos.produccion.show', compact('lote', 'detalleCostos', 'alimentaciones', 'mantenimientos'));
    }

    /**
     * Exportar reporte de costos en formato Excel/CSV
     */
    public function exportar(Request $request)
    {
        $lotes = Lote::all();
        $reporteCostos = $this->costoService->generarReporteCostos($lotes);
        
        // Preparar datos para exportación
        $datosExport = [];
        foreach ($reporteCostos['lotes'] as $loteData) {
            $datosExport[] = [
                'Código Lote' => $loteData['codigo_lote'],
                'Especie' => $loteData['especie'],
                'Costo Insumos (Q)' => number_format($loteData['costos']['insumos'], 2),
                'Costo Alimentación (Q)' => number_format($loteData['costos']['alimentacion'], 2),
                'Costo Total (Q)' => number_format($loteData['costos']['total'], 2),
                'Producción Total (lb)' => number_format($loteData['produccion']['total_libras'], 2),
                'Costo por Libra (Q)' => number_format($loteData['indicadores']['costo_por_libra'], 2),
                'Ganancia Realizada (Q)' => number_format($loteData['indicadores']['ganancia_realizada'], 2),
                'Venta Potencial (Q)' => number_format($loteData['indicadores']['venta_potencial'], 2),
                'Margen Estimado (%)' => number_format($loteData['indicadores']['margen_estimado'], 1)
            ];
        }
        
        $filename = 'reporte_costos_produccion_' . date('Y-m-d') . '.csv';
        
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
     * API para obtener costos de un lote específico
     */
    public function api(Lote $lote)
    {
        $detalleCostos = $this->costoService->calcularCostoPorLibra($lote);
        
        return response()->json([
            'success' => true,
            'data' => $detalleCostos
        ]);
    }

    /**
     * Comparar costos entre múltiples lotes
     */
    public function comparar(Request $request)
    {
        $request->validate([
            'lotes' => 'required|array|min:2',
            'lotes.*' => 'exists:lotes,id'
        ]);
        
        $lotes = Lote::whereIn('id', $request->lotes)->get();
        $comparacion = [];
        
        foreach ($lotes as $lote) {
            $comparacion[] = $this->costoService->calcularCostoPorLibra($lote);
        }
        
        return view('costos.produccion.comparar', compact('comparacion'));
    }
}