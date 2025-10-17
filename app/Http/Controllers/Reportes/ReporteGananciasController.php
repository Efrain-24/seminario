<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use App\Models\Lote;
use App\Models\Limpieza;
use App\Models\MantenimientoUnidad;

class ReporteGananciasController extends Controller
{

    public function reporte(Request $request, $lote = null)
    {
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();
        $loteSeleccionado = $lote ? \App\Models\Lote::with('unidadProduccion')->find($lote) : null;

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $ventasQuery = $loteSeleccionado ? $loteSeleccionado->ventas() : null;
        if ($ventasQuery && $fechaInicio && $fechaFin) {
            $ventasQuery->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }

        $ventas = $ventasQuery ? $ventasQuery->get() : collect();

        $desglose = $loteSeleccionado ? [
            'total_ventas' => $ventas->sum('total_venta'),
            'total_costos' => $loteSeleccionado->alimentaciones()->sum('costo_total') +
                              \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->sum('costo_mantenimiento') +
                              \App\Models\Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->sum('costo'),
            'ganancia_real' => $ventas->sum('total_venta') - (
                              $loteSeleccionado->alimentaciones()->sum('costo_total') +
                              \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->sum('costo_mantenimiento') +
                              \App\Models\Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->sum('costo')
                              ),
            'margen_ganancia' => $ventas->sum('total_venta') > 0 ? (
                              ($ventas->sum('total_venta') - (
                              $loteSeleccionado->alimentaciones()->sum('costo_total') +
                              \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->sum('costo_mantenimiento') +
                              \App\Models\Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->sum('costo')
                              )) / $ventas->sum('total_venta')) * 100 : 0,
        ] : null;

        $grafica = $ventas->isNotEmpty() ? [
            'labels' => $ventas->pluck('fecha_venta')->map(fn($fecha) => optional($fecha)->format('d/m/Y'))->toArray(),
            'data' => $ventas->pluck('total_venta')->toArray(),
        ] : null;

        return view('reportes.ganancias.reporte', compact('lotes', 'loteSeleccionado', 'desglose', 'grafica', 'fechaInicio', 'fechaFin'));
    }
    public function index(Request $request)
    {
        $lotes = Lote::with('unidadProduccion')->get();
        return view('reportes.ganancias.reporte', compact('lotes'));
    }
}
