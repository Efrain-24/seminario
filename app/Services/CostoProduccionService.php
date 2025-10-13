<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Alimentacion;
use App\Models\InventarioItem;
use App\Models\CosechaParcial;
use Illuminate\Support\Collection;

class CostoProduccionService
{
    /**
     * Calcular el costo detallado por libra producida
     */
    public function calcularCostoPorLibra(Lote $lote): array
    {
        // 1. Obtener datos del lote
        $costoInsumos = $this->calcularCostoInsumos($lote);
        $costoAlimentacion = $this->calcularCostoAlimentacion($lote);
        $produccionTotal = $this->calcularProduccionTotal($lote);
        
        // 2. Calcular costo total
        $costoTotal = $costoInsumos + $costoAlimentacion;
        
        // 3. Calcular costo por libra
        $costoPorLibra = $produccionTotal > 0 ? $costoTotal / $produccionTotal : 0;
        
        return [
            'lote_id' => $lote->id,
            'codigo_lote' => $lote->codigo_lote,
            'especie' => $lote->especie,
            'costos' => [
                'insumos' => $costoInsumos,
                'alimentacion' => $costoAlimentacion,
                'total' => $costoTotal
            ],
            'produccion' => [
                'total_libras' => $produccionTotal,
                'biomasa_actual' => $this->calcularBiomasaActual($lote),
                'rentabilidad' => $this->calcularRentabilidad($lote)
            ],
            'indicadores' => [
                'costo_por_libra' => $costoPorLibra,
                'ganancia_realizada' => $this->calcularGananciaRealizada($lote),
                'venta_potencial' => $this->calcularVentaPotencial($lote),
                'margen_estimado' => $this->calcularMargenEstimado($lote)
            ]
        ];
    }

    /**
     * Calcular costo de insumos utilizados
     */
    private function calcularCostoInsumos(Lote $lote): float
    {
        $costoTotal = 0;
        
        // Insumos de mantenimientos
        $mantenimientos = $lote->mantenimientos()
            ->with(['insumos.inventarioItem'])
            ->get();
            
        foreach ($mantenimientos as $mantenimiento) {
            foreach ($mantenimiento->insumos as $insumo) {
                if ($insumo->inventarioItem && $insumo->inventarioItem->costo_unitario > 0) {
                    $costoTotal += $insumo->cantidad * $insumo->inventarioItem->costo_unitario;
                }
            }
        }
        
        // Insumos de protocolos de sanidad
        $protocolos = $lote->seguimientos()
            ->with(['protocoloSanidad.insumos.inventarioItem'])
            ->get();
            
        foreach ($protocolos as $seguimiento) {
            if ($seguimiento->protocoloSanidad) {
                foreach ($seguimiento->protocoloSanidad->insumos as $insumo) {
                    if ($insumo->inventarioItem && $insumo->inventarioItem->costo_unitario > 0) {
                        $costoTotal += $insumo->cantidad_necesaria * $insumo->inventarioItem->costo_unitario;
                    }
                }
            }
        }
        
        return $costoTotal;
    }

    /**
     * Calcular costo de alimentación
     */
    private function calcularCostoAlimentacion(Lote $lote): float
    {
        $costoTotal = 0;
        
        $alimentaciones = $lote->alimentaciones()
            ->with(['inventarioItem'])
            ->get();
            
        foreach ($alimentaciones as $alimentacion) {
            if ($alimentacion->inventarioItem && $alimentacion->inventarioItem->costo_unitario > 0) {
                // cantidad_kg está en libras
                $costoTotal += $alimentacion->cantidad_kg * $alimentacion->inventarioItem->costo_unitario;
            }
        }
        
        return $costoTotal;
    }

    /**
     * Calcular producción total en libras
     */
    private function calcularProduccionTotal(Lote $lote): float
    {
        // Biomasa actual + cosechas parciales
        $biomasaActual = $this->calcularBiomasaActual($lote);
        $cosechasParciales = $this->calcularCosechasParciales($lote);
        
        return $biomasaActual + $cosechasParciales;
    }

    /**
     * Calcular biomasa actual en libras
     */
    private function calcularBiomasaActual(Lote $lote): float
    {
        $biomasaKg = $lote->biomasa; // en kg
        return $biomasaKg * 2.20462; // convertir a libras
    }

    /**
     * Calcular cosechas parciales en libras
     */
    private function calcularCosechasParciales(Lote $lote): float
    {
        $totalKg = $lote->cosechasParciales()
            ->sum('peso_cosechado_kg') ?? 0;
            
        return $totalKg * 2.20462; // convertir a libras
    }

    /**
     * Calcular rentabilidad actual
     */
    private function calcularRentabilidad(Lote $lote): array
    {
        // Obtener ventas reales con precios registrados
        $ventasConPrecio = $lote->ventas()
            ->whereNotNull('precio_kg')
            ->where('precio_kg', '>', 0)
            ->get();
            
        // Calcular ingresos reales solo de ventas con precio registrado
        $ingresoTotal = 0;
        $pesoTotalVendido = 0;
        $cantidadVentas = 0;
        
        foreach ($ventasConPrecio as $venta) {
            $ingresoTotal += $venta->total_venta ?? ($venta->peso_cosechado_kg * $venta->precio_kg);
            $pesoTotalVendido += $venta->peso_cosechado_kg;
            $cantidadVentas++;
        }
        
        // Calcular precio promedio real (no estimado)
        $precioPromedioReal = $pesoTotalVendido > 0 ? ($ingresoTotal / $pesoTotalVendido) : 0;
        
        // Convertir peso a libras
        $ventasLibras = $pesoTotalVendido * 2.20462;
        
        $costoTotal = $this->calcularCostoInsumos($lote) + $this->calcularCostoAlimentacion($lote);
        
        // Solo calcular ganancia si hay ventas reales
        if ($cantidadVentas > 0) {
            $ganancia = $ingresoTotal - $costoTotal;
            $margen = $ingresoTotal > 0 ? ($ganancia / $ingresoTotal) * 100 : 0;
        } else {
            // Sin ventas reales, no hay ganancia (ni positiva ni negativa)
            $ganancia = 0;
            $margen = 0;
        }
        
        return [
            'ventas_libras' => $ventasLibras,
            'ventas_kg' => $pesoTotalVendido,
            'cantidad_ventas' => $cantidadVentas,
            'precio_promedio_kg' => $precioPromedioReal,
            'precio_promedio_lb' => $precioPromedioReal * 2.20462,
            'ingreso_total' => $ingresoTotal,
            'costo_total' => $costoTotal,
            'ganancia' => $ganancia,
            'margen_porcentaje' => $margen,
            'tiene_ventas_reales' => $cantidadVentas > 0
        ];
    }

    /**
     * Calcular ganancia realizada hasta la fecha
     */
    private function calcularGananciaRealizada(Lote $lote): float
    {
        $rentabilidad = $this->calcularRentabilidad($lote);
        return $rentabilidad['ganancia'];
    }

    /**
     * Calcular venta potencial si se cosecha todo (usando datos reales cuando estén disponibles)
     */
    private function calcularVentaPotencial(Lote $lote): float
    {
        $produccionTotal = $this->calcularProduccionTotal($lote);
        
        // Intentar obtener precio promedio real de ventas del mismo lote
        $rentabilidad = $this->calcularRentabilidad($lote);
        $precioReal = $rentabilidad['precio_promedio_kg'];
        
        // Si no hay precio real, intentar obtener precio promedio de otros lotes de la misma especie
        if ($precioReal <= 0) {
            $precioReal = \App\Models\CosechaParcial::whereHas('lote', function($query) use ($lote) {
                $query->where('especie', $lote->especie);
            })
            ->whereNotNull('precio_kg')
            ->where('precio_kg', '>', 0)
            ->avg('precio_kg') ?? 0;
        }
        
        // Solo calcular si tenemos precio real, si no, retornar 0
        return $precioReal > 0 ? ($produccionTotal * $precioReal) : 0;
    }

    /**
     * Calcular margen estimado usando datos reales
     */
    private function calcularMargenEstimado(Lote $lote): float
    {
        $ventaPotencial = $this->calcularVentaPotencial($lote);
        
        // Solo calcular si tenemos datos reales de venta potencial
        if ($ventaPotencial <= 0) {
            return 0;
        }
        
        $costoTotal = $this->calcularCostoInsumos($lote) + $this->calcularCostoAlimentacion($lote);
        
        return (($ventaPotencial - $costoTotal) / $ventaPotencial) * 100;
    }

    /**
     * Obtener reporte de costos para múltiples lotes
     */
    public function generarReporteCostos(Collection $lotes): array
    {
        $reporte = [];
        
        foreach ($lotes as $lote) {
            $reporte[] = $this->calcularCostoPorLibra($lote);
        }
        
        return [
            'lotes' => $reporte,
            'resumen' => $this->generarResumenGlobal($reporte)
        ];
    }

    /**
     * Generar resumen global de costos
     */
    private function generarResumenGlobal(array $reporteLotes): array
    {
        if (empty($reporteLotes)) {
            return [];
        }
        
        $totalCostos = array_sum(array_column(array_column($reporteLotes, 'costos'), 'total'));
        $totalProduccion = array_sum(array_column(array_column($reporteLotes, 'produccion'), 'total_libras'));
        $promedioCostoPorLibra = $totalProduccion > 0 ? $totalCostos / $totalProduccion : 0;
        
        return [
            'total_lotes' => count($reporteLotes),
            'costo_total_operacion' => $totalCostos,
            'produccion_total_libras' => $totalProduccion,
            'costo_promedio_por_libra' => $promedioCostoPorLibra,
            'ganancia_total' => array_sum(array_column(array_column($reporteLotes, 'indicadores'), 'ganancia_realizada')),
            'venta_potencial_total' => array_sum(array_column(array_column($reporteLotes, 'indicadores'), 'venta_potencial'))
        ];
    }
}