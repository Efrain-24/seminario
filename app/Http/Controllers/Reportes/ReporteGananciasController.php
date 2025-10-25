<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use App\Models\Lote;
use App\Models\Limpieza;
use App\Models\MantenimientoUnidad;
use App\Models\ProtocoloSanidad;

class ReporteGananciasController extends Controller
{

    public function reporte(Request $request, $lote = null)
    {
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();
        $loteSeleccionado = $lote ? \App\Models\Lote::with('unidadProduccion')->find($lote) : null;

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if (!$loteSeleccionado) {
            return view('reportes.ganancias.reporte', compact('lotes'));
        }

        // --- Compra Lote ---
        $precioUnitarioPez = $loteSeleccionado->precio_unitario_pez ?? 0;
        $precioCompraLote = $precioUnitarioPez > 0 ? $loteSeleccionado->cantidad_inicial * $precioUnitarioPez : 0;

        // --- Alimentación ---
        $alimentaciones = $loteSeleccionado->alimentaciones()->with('inventarioItem')->get();
        $totalAlimentacion = $alimentaciones->sum('costo_total');
        $alimentacionDetalle = $alimentaciones->map(function($a) {
            return [
                'fecha' => optional($a->fecha_alimentacion)->format('d/m/Y'),
                'producto' => $a->inventarioItem->nombre ?? 'N/A',
                'cantidad' => $a->cantidad_kg,
                'costo' => $a->costo_total,
            ];
        });

        // --- Mantenimientos ---
        $mantenimientos = MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->get();
        $totalMantenimientos = $mantenimientos->sum('costo_mantenimiento');
        $mantenimientoDetalle = $mantenimientos->map(function($m) {
            return [
                'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                'tipo' => $m->tipo_mantenimiento,
                'descripcion' => $m->descripcion_trabajo,
                'costo' => $m->costo_mantenimiento,
            ];
        });

        // --- Costos de Insumos en Mantenimientos ---
        $mantenimientosConInsumos = MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)
            ->with('insumos')
            ->get();
        
        $costoInsumosMantenimientos = $mantenimientosConInsumos->sum(function($m) {
            return $m->insumos->sum('costo_total');
        });

        // --- Limpiezas ---
        $limpiezas = Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->get();
        $totalLimpiezas = $limpiezas->sum('costo') ?? 0;
        $limpiezaDetalle = $limpiezas->map(function($l) {
            return [
                'fecha' => optional($l->fecha)->format('d/m/Y'),
                'tipo' => $l->protocoloSanidad->nombre ?? 'N/A',
                'productos' => is_array($l->actividades_ejecutadas) ? implode(', ', array_map(fn($a) => is_array($a) ? ($a['descripcion'] ?? '') : $a, $l->actividades_ejecutadas)) : '',
                'costo' => $l->costo ?? 0,
            ];
        });

        // --- Protocolos de Sanidad Ejecutados ---
        $protocolosEjecutados = ProtocoloSanidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)
            ->where('estado', 'ejecutado')
            ->with('insumos.inventarioItem')
            ->get();
        
        $totalProtocolos = $protocolosEjecutados->sum(function($p) {
            return $p->insumos->sum(function($insumo) {
                return $insumo->cantidad_necesaria * ($insumo->inventarioItem->costo_unitario ?? 0);
            });
        });
        
        $protocoloDetalle = $protocolosEjecutados->map(function($p) {
            $costoInsumos = $p->insumos->sum(function($insumo) {
                return $insumo->cantidad_necesaria * ($insumo->inventarioItem->costo_unitario ?? 0);
            });
            
            return [
                'fecha' => optional($p->fecha_ejecucion)->format('d/m/Y'),
                'protocolo' => $p->nombre . ' (v' . $p->version . ')',
                'responsable' => $p->responsable,
                'insumos' => $p->insumos->map(function($i) {
                    return $i->inventarioItem->nombre . ' (' . $i->cantidad_necesaria . ' ' . $i->unidad . ')';
                })->implode(', '),
                'costo' => $costoInsumos,
                'observaciones' => $p->observaciones_ejecucion ?? 'Sin observaciones',
            ];
        });

        // --- Ventas ---
        $ventasQuery = $loteSeleccionado->ventas();
        if ($fechaInicio && $fechaFin) {
            $ventasQuery->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        $ventas = $ventasQuery->get();
        $totalVentas = $ventas->sum('total_venta');
        $ventasDetalle = $ventas->map(function($v) {
            return [
                'fecha' => optional($v->fecha_venta)->format('d/m/Y'),
                'codigo' => $v->codigo_venta,
                'cliente' => $v->cliente,
                'peso_kg' => $v->peso_cosechado_kg,
                'precio_kg' => $v->precio_kg,
                'total' => $v->total_venta,
                'estado' => $v->estado_venta,
            ];
        });

        // --- Biomasa final ---
        $biomasaFinalKg = $loteSeleccionado->biomasa ?? 0;
        $biomasaFinalLb = $biomasaFinalKg * 2.20462;

        // --- Costos totales y ganancia ---
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas + $totalProtocolos + $costoInsumosMantenimientos;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        // --- Gráfica ---
        $grafica = $ventas->isNotEmpty() ? [
            'labels' => $ventas->pluck('fecha_venta')->map(fn($fecha) => optional($fecha)->format('d/m/Y'))->toArray(),
            'data' => $ventas->pluck('total_venta')->toArray(),
        ] : null;

        $desglose = [
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia,
            'precio_compra_lote' => $precioCompraLote,
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
            'total_protocolos' => $totalProtocolos,
            'costo_insumos_mantenimientos' => $costoInsumosMantenimientos,
        ];

        return view('reportes.ganancias.reporte', compact(
            'lotes',
            'loteSeleccionado',
            'desglose',
            'alimentacionDetalle',
            'mantenimientoDetalle',
            'limpiezaDetalle',
            'protocoloDetalle',
            'ventasDetalle',
            'biomasaFinalKg',
            'biomasaFinalLb',
            'grafica',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function index(Request $request)
    {
        $lotes = Lote::with('unidadProduccion')->get();
        return view('reportes.ganancias.reporte', compact('lotes'));
    }
}
