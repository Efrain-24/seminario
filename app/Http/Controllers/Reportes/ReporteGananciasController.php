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

    public function reporte($loteId, Request $request)
    {
        $unidades = \App\Models\UnidadProduccion::all();
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();
        $lote = \App\Models\Lote::with('unidadProduccion')->find($loteId);


        // --- Compra Lote ---
        // El costo de compra del lote es la cantidad inicial * precio unitario del pez
        $precioUnitarioPez = $lote->precio_unitario_pez ?? 0; // Cambia el nombre del campo si es diferente
        $precioCompraLote = 0;
        if (isset($lote->cantidad_inicial) && $precioUnitarioPez > 0) {
            $precioCompraLote = $lote->cantidad_inicial * $precioUnitarioPez;
        }

        // --- Alimentación ---
        $alimentaciones = $lote->alimentaciones()->with('inventarioItem')->get();
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
        $mantenimientos = \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)->get();
        $totalMantenimientos = $mantenimientos->sum('costo_mantenimiento');
        $mantenimientoDetalle = $mantenimientos->map(function($m) {
            return [
                'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                'tipo' => $m->tipo_mantenimiento,
                'descripcion' => $m->descripcion_trabajo,
                'costo' => $m->costo_mantenimiento,
            ];
        });

        // --- Limpiezas ---
        $limpiezas = \App\Models\Limpieza::where('area', $lote->unidadProduccion->nombre)->get();
        // Si tienes un campo de costo en Limpieza, cámbialo aquí:
        $totalLimpiezas = $limpiezas->sum('costo') ?? 0;
        $limpiezaDetalle = $limpiezas->map(function($l) {
            return [
                'fecha' => optional($l->fecha)->format('d/m/Y'),
                'tipo' => $l->protocoloSanidad->nombre ?? 'N/A',
                'productos' => is_array($l->actividades_ejecutadas) ? implode(', ', array_map(fn($a) => is_array($a) ? ($a['descripcion'] ?? '') : $a, $l->actividades_ejecutadas)) : '',
                'costo' => $l->costo ?? 0,
            ];
        });

        // --- Ventas (Cosechas Parciales con destino venta) ---
        $ventas = $lote->ventas()->get();
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
        $biomasaFinalKg = $lote->biomasa ?? 0;
        $biomasaFinalLb = $biomasaFinalKg * 2.20462;

        // --- Costos totales y ganancia ---
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        // --- Costo por libra producida ---
        $costoPorLibra = $biomasaFinalLb > 0 ? $totalCostos / $biomasaFinalLb : 0;

        $desglose = [
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia,
            'precio_compra_lote' => $precioCompraLote, // Este valor ya es la multiplicación
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
        ];

        return view('reportes.ganancias.reporte', compact(
            'unidades',
            'lotes',
            'lote',
            'desglose',
            'alimentacionDetalle',
            'mantenimientoDetalle',
            'limpiezaDetalle',
            'ventasDetalle',
            'biomasaFinalKg',
            'biomasaFinalLb',
            'costoPorLibra'
        ));
    }
    public function index(Request $request)
    {
        $unidades = \App\Models\UnidadProduccion::all();
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();

        // Siempre definir $resumen, $grafica y $loteResumen
        $resumen = [
            'costoAlimentacion' => 0,
            'costoProtocolos' => 0,
            'totalVenta' => 0,
            'totalCostos' => 0,
            'margen' => 0,
            'ganancia' => 0,
            'vendido' => false,
            'estimado' => 0,
        ];
        $grafica = [
            'labels' => [],
            'data' => [],
        ];
        $loteResumen = null;

        if ($request->filled('lote_id')) {
            $loteResumen = \App\Models\Lote::with('unidadProduccion')->find($request->lote_id);
            // Aquí iría la lógica real para calcular los datos
        }

        return view('reportes.ganancias.index', compact(
            'unidades',
            'lotes',
            'resumen',
            'grafica',
            'loteResumen'
        ));
    }
}
