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
    /**
     * Mostrar el detalle de ganancias de un lote (usado por la ruta reportes.ganancias.reporte)
     */
    public function reporte($loteId)
    {
        return $this->generarReporte($loteId);
    }

    /**
     * Mostrar listado general con resumen de ganancias por lote
     */
    public function index(Request $request)
    {
        $unidades = UnidadProduccion::all();

        $resumen = null;
        $grafica = null;
        $loteResumen = null;

        $query = Lote::with(['unidadProduccion', 'alimentaciones', 'ventas', 'mantenimientos', 'limpiezas'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('unidad')) {
            $query->where('unidad_produccion_id', $request->input('unidad'));
        }

        $lotes = $query->get();

        if ($request->filled('lote_id')) {
            $loteResumen = $lotes->where('id', $request->input('lote_id'))->first();

            if ($loteResumen) {
                $costoAlimentacion = $loteResumen->alimentaciones->sum('costo_total');
                $costoProtocolos = ($loteResumen->mantenimientos->sum('costo_total') ?? 0)
                                 + ($loteResumen->limpiezas->sum('costo_total') ?? 0);
                $totalVenta = $loteResumen->ventas->sum('total_venta');
                $totalCostos = $costoAlimentacion + $costoProtocolos + ($loteResumen->precio_compra ?? 0);

                $ganancia = $totalVenta - $totalCostos;
                $margen = $totalVenta > 0 ? ($ganancia / $totalVenta) * 100 : 0;

                $vendido = $totalVenta > 0;
                $estimado = null;

                if (!$vendido && $loteResumen->ventas->count() > 0) {
                    $promedio = $loteResumen->ventas->avg('precio_unitario');
                    $estimado = $promedio * ($loteResumen->cantidad_total ?? 0);
                }

                $resumen = [
                    'costoAlimentacion' => $costoAlimentacion,
                    'costoProtocolos' => $costoProtocolos,
                    'totalVenta' => $totalVenta,
                    'totalCostos' => $totalCostos,
                    'ganancia' => $ganancia,
                    'margen' => $margen,
                    'vendido' => $vendido,
                    'estimado' => $estimado
                ];

                $grafica = [
                    'labels' => ['Alimentación', 'Protocolos', 'Costos Totales', 'Venta', 'Ganancia'],
                    'data' => [
                        $costoAlimentacion,
                        $costoProtocolos,
                        $totalCostos,
                        $totalVenta,
                        $ganancia
                    ]
                ];
            }
        }

        return view('reportes.ganancias.index', compact('lotes', 'unidades', 'resumen', 'grafica', 'loteResumen'));
    }

    /**
     * Generar el reporte detallado del lote
     */
    public function generarReporte($loteId)
    {
        $lote = Lote::with(['unidadProduccion', 'ventas', 'alimentaciones.inventarioItem', 'seguimientos'])
            ->findOrFail($loteId);

        $precioCompraLote = $lote->precio_compra ?? 0;
        $totalAlimentacion = $lote->alimentaciones->sum('costo_total');

        $fechaInicio = $lote->fecha_siembra ?? $lote->created_at;
        $fechaFin = $lote->fecha_cosecha ?? now();

        $totalMantenimientos = MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('costo_total');

        $totalLimpiezas = Limpieza::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('costo_total');

        $totalVentas = $lote->ventas->sum('total_venta');
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        $desglose = [
            'precio_compra_lote' => $precioCompraLote,
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia
        ];

        $alimentacionDetalle = $lote->alimentaciones->map(function ($alimentacion) {
            return [
                'fecha' => $alimentacion->fecha,
                'cantidad' => $alimentacion->cantidad,
                'costo' => $alimentacion->costo_total,
                'producto' => $alimentacion->inventarioItem->nombre ?? 'N/A'
            ];
        });

        $mantenimientoDetalle = MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->map(function ($mantenimiento) {
                return [
                    'fecha' => $mantenimiento->fecha,
                    'tipo' => $mantenimiento->tipo_mantenimiento,
                    'costo' => $mantenimiento->costo_total,
                    'descripcion' => $mantenimiento->descripcion
                ];
            });

        $limpiezaDetalle = Limpieza::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->map(function ($limpieza) {
                return [
                    'fecha' => $limpieza->fecha,
                    'tipo' => $limpieza->tipo_limpieza,
                    'costo' => $limpieza->costo_total,
                    'productos' => $limpieza->productos_utilizados
                ];
            });

        $ventasDetalle = $lote->ventas->map(function ($venta) {
            return [
                'fecha' => $venta->fecha_venta,
                'codigo' => $venta->codigo_venta,
                'cliente' => $venta->cliente,
                'peso_kg' => $venta->peso_cosechado_kg,
                'precio_kg' => $venta->precio_kg,
                'total' => $venta->total_venta,
                'estado' => $venta->estado_venta
            ];
        });

        return view('reportes.ganancias.reporte', compact(
            'lote',
            'desglose',
            'alimentacionDetalle',
            'mantenimientoDetalle',
            'limpiezaDetalle',
            'ventasDetalle'
        ));
    }
}
