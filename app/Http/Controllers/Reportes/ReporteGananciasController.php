<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use App\Models\Lote;

class ReporteGananciasController extends Controller
{
    public function index(Request $request)
    {
        $unidades = UnidadProduccion::all();
        $query = Lote::query();
        if ($request->filled('unidad')) {
            $query->where('unidad_produccion_id', $request->unidad);
        }
        $lotes = $query->get()->map(function ($lote) {
            $costoTotal = $lote->alimentaciones->sum('costo_total');
            $ventasTotal = $lote->ventas->sum('total_venta');
            $lote->costo_total = $costoTotal;
            $lote->ventas_total = $ventasTotal;
            $lote->ganancia_real = $ventasTotal - $costoTotal;
            return $lote;
        });
        return view('reportes.ganancias.index', compact('unidades', 'lotes'));
    }

    public function reporte(Lote $lote)
    {
        $lote->costo_total = $lote->alimentaciones->sum('costo_total');
        $lote->ventas_total = $lote->ventas->sum('total_venta');
        $lote->ganancia_real = $lote->ventas_total - $lote->costo_total;
        return view('reportes.ganancias.reporte', compact('lote'));
    }
}<?php
namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\Limpieza;
use App\Models\MantenimientoUnidad;
use App\Models\Alimentacion;
use App\Models\UnidadProduccion;

class ReporteGananciasController extends Controller
{
    public function index(Request $request)
    {
        $tanques = UnidadProduccion::all();
        
        if ($request->has('lote_id')) {
            return $this->generarReporte($request->lote_id);
        }

        $lotes = Lote::with(['unidadProduccion'])->orderBy('created_at', 'desc')->get();
        
        return view('reportes.ganancias.index', compact('lotes', 'tanques'));
    }

    public function generarReporte($loteId)
    {
        $lote = Lote::with(['unidadProduccion', 'ventas', 'alimentaciones.inventarioItem', 'seguimientos'])
            ->findOrFail($loteId);

        // 1. Precio de compra inicial del lote (alevines)
        $precioCompraLote = $lote->precio_compra ?? 0;

        // 2. Costos de alimentación
        $totalAlimentacion = $lote->alimentaciones->sum('costo_total');

        // 3. Costos de mantenimiento y limpieza del tanque
        $fechaInicio = $lote->fecha_siembra ?? $lote->created_at;
        $fechaFin = $lote->fecha_cosecha ?? now();
        
        $totalMantenimientos = MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('costo_total');

        $totalLimpiezas = Limpieza::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('costo_total');

        // 4. Total de ventas del lote
        $totalVentas = $lote->ventas->sum('total_venta');

        // 5. Cálculos finales
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        // Desglose detallado
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

        // Obtener detalles de alimentación por fecha
        $alimentacionDetalle = $lote->alimentaciones->map(function($alimentacion) {
            return [
                'fecha' => $alimentacion->fecha,
                'cantidad' => $alimentacion->cantidad,
                'costo' => $alimentacion->costo_total,
                'producto' => $alimentacion->inventarioItem->nombre ?? 'N/A'
            ];
        });

        // Obtener detalles de mantenimientos
        $mantenimientoDetalle = MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->map(function($mantenimiento) {
                return [
                    'fecha' => $mantenimiento->fecha,
                    'tipo' => $mantenimiento->tipo_mantenimiento,
                    'costo' => $mantenimiento->costo_total,
                    'descripcion' => $mantenimiento->descripcion
                ];
            });

        // Obtener detalles de limpiezas
        $limpiezaDetalle = Limpieza::where('unidad_produccion_id', $lote->unidad_produccion_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->map(function($limpieza) {
                return [
                    'fecha' => $limpieza->fecha,
                    'tipo' => $limpieza->tipo_limpieza,
                    'costo' => $limpieza->costo_total,
                    'productos' => $limpieza->productos_utilizados
                ];
            });

        // Obtener detalles de ventas
        $ventasDetalle = $lote->ventas->map(function($venta) {
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
            'lote', 'desglose', 'alimentacionDetalle', 'mantenimientoDetalle', 'limpiezaDetalle', 'ventasDetalle'
        ));
    }
}
