<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\CosechaParcial;
use App\Models\DetalleVenta;
use Illuminate\Support\Collection;

class VentasResultadosService
{
    /**
     * RF36: Obtener análisis de ventas ejecutadas y potenciales
     */
    public function obtenerResultadosVentas(array $filtros = []): array
    {
        // Obtener lotes según filtros
        $lotes = $this->obtenerLotesFiltrados($filtros);
        
        $resultados = [];
        
        foreach ($lotes as $lote) {
            $ventasEjecutadas = $this->calcularVentasEjecutadas($lote);
            $ventasPotenciales = $this->calcularVentasPotenciales($lote);
            
            $resultados[] = [
                'lote' => [
                    'id' => $lote->id,
                    'codigo' => $lote->codigo_lote,
                    'especie' => $lote->especie,
                    'estado' => $lote->estado,
                    'fecha_inicio' => $lote->fecha_inicio,
                    'biomasa_actual_kg' => $lote->biomasa,
                    'biomasa_actual_lb' => $lote->biomasa * 2.20462
                ],
                'ventas_ejecutadas' => $ventasEjecutadas,
                'ventas_potenciales' => $ventasPotenciales,
                'comparacion' => $this->compararVentasEjecutadasVsPotenciales($ventasEjecutadas, $ventasPotenciales),
                'margenes' => $this->calcularMargenes($lote, $ventasEjecutadas, $ventasPotenciales)
            ];
        }
        
        return [
            'lotes' => $resultados,
            'resumen_global' => $this->generarResumenGlobal($resultados),
            'filtros_aplicados' => $filtros
        ];
    }

    /**
     * Calcular ventas ejecutadas (realizadas)
     */
    private function calcularVentasEjecutadas(Lote $lote): array
    {
        // Ventas directas desde cosechas parciales
        $cosechasVenta = $lote->cosechasParciales()
            ->where('destino', 'venta')
            ->get();
            
        $totalPecesVendidos = $cosechasVenta->sum('cantidad_cosechada');
        $totalPesoVendidoKg = $cosechasVenta->sum('peso_cosechado_kg') ?: 0;
        $totalPesoVendidoLb = $totalPesoVendidoKg * 2.20462;
        
        // Calcular precio promedio de ventas realizadas
        $ventasConPrecio = $cosechasVenta->where('precio_venta', '>', 0);
        $precioPromedioVenta = $ventasConPrecio->count() > 0 
            ? $ventasConPrecio->avg('precio_venta') 
            : $this->obtenerPrecioMercadoEstimado($lote->especie);
        
        // Ingresos por ventas
        $ingresoTotal = $totalPesoVendidoLb * $precioPromedioVenta;
        
        return [
            'cantidad_peces' => $totalPecesVendidos,
            'peso_total_kg' => $totalPesoVendidoKg,
            'peso_total_lb' => $totalPesoVendidoLb,
            'precio_promedio_lb' => $precioPromedioVenta,
            'ingreso_total' => $ingresoTotal,
            'numero_ventas' => $cosechasVenta->count(),
            'detalle_ventas' => $this->obtenerDetalleVentas($cosechasVenta),
            'ultima_venta' => $cosechasVenta->sortByDesc('fecha')->first()
        ];
    }

    /**
     * Calcular ventas potenciales (inventario disponible)
     */
    private function calcularVentasPotenciales(Lote $lote): array
    {
        $biomasaDisponibleKg = $lote->biomasa;
        $biomasaDisponibleLb = $biomasaDisponibleKg * 2.20462;
        $cantidadDisponible = $lote->cantidad_actual ?: $this->calcularCantidadActualEstimada($lote);
        
        // Precios estimados por especie y calidad
        $escenarios = $this->generarEscenariosPrecios($lote->especie);
        
        $ventasPotenciales = [];
        
        foreach ($escenarios as $escenario => $precio) {
            $ingresoEstimado = $biomasaDisponibleLb * $precio;
            
            $ventasPotenciales[$escenario] = [
                'precio_por_lb' => $precio,
                'peso_disponible_lb' => $biomasaDisponibleLb,
                'peso_disponible_kg' => $biomasaDisponibleKg,
                'cantidad_peces_estimada' => $cantidadDisponible,
                'ingreso_estimado' => $ingresoEstimado,
                'descripcion' => $this->obtenerDescripcionEscenario($escenario)
            ];
        }
        
        return [
            'biomasa_disponible_kg' => $biomasaDisponibleKg,
            'biomasa_disponible_lb' => $biomasaDisponibleLb,
            'cantidad_estimada' => $cantidadDisponible,
            'escenarios' => $ventasPotenciales,
            'recomendacion' => $this->generarRecomendacionVenta($lote, $ventasPotenciales)
        ];
    }

    /**
     * Comparar ventas ejecutadas vs potenciales
     */
    private function compararVentasEjecutadasVsPotenciales(array $ejecutadas, array $potenciales): array
    {
        $pesoTotalDisponible = $ejecutadas['peso_total_lb'] + $potenciales['biomasa_disponible_lb'];
        
        $porcentajeVendido = $pesoTotalDisponible > 0 
            ? ($ejecutadas['peso_total_lb'] / $pesoTotalDisponible) * 100 
            : 0;
            
        $porcentajePendiente = 100 - $porcentajeVendido;
        
        // Mejor escenario de venta potencial
        $mejorEscenario = isset($potenciales['escenarios']) && !empty($potenciales['escenarios'])
            ? collect($potenciales['escenarios'])->sortByDesc('precio_por_lb')->first()
            : ['ingreso_estimado' => 0];
        
        return [
            'peso_total_produccion' => $pesoTotalDisponible,
            'porcentaje_vendido' => $porcentajeVendido,
            'porcentaje_pendiente' => $porcentajePendiente,
            'valor_realizado' => $ejecutadas['ingreso_total'],
            'valor_potencial_maximo' => $mejorEscenario['ingreso_estimado'] ?? 0,
            'oportunidad_mejora' => $this->calcularOportunidadMejora($ejecutadas, $potenciales)
        ];
    }

    /**
     * Calcular márgenes de rentabilidad
     */
    private function calcularMargenes(Lote $lote, array $ejecutadas, array $potenciales): array
    {
        $costoProduccionService = app(CostoProduccionService::class);
        $costos = $costoProduccionService->calcularCostoPorLibra($lote);
        
        $costoTotal = $costos['costos']['total'];
        $costoPorLibra = $costos['indicadores']['costo_por_libra'];
        
        // Margen en ventas ejecutadas
        $margenEjecutado = $ejecutadas['ingreso_total'] > 0 
            ? (($ejecutadas['ingreso_total'] - ($ejecutadas['peso_total_lb'] * $costoPorLibra)) / $ejecutadas['ingreso_total']) * 100
            : 0;
        
        // Margen en ventas potenciales (mejor escenario)
        $mejorEscenario = isset($potenciales['escenarios']) && !empty($potenciales['escenarios'])
            ? collect($potenciales['escenarios'])->sortByDesc('precio_por_lb')->first()
            : null;
        $margenPotencial = 0;
        
        if ($mejorEscenario) {
            $costoPotencial = $potenciales['biomasa_disponible_lb'] * $costoPorLibra;
            $margenPotencial = $mejorEscenario['ingreso_estimado'] > 0 
                ? (($mejorEscenario['ingreso_estimado'] - $costoPotencial) / $mejorEscenario['ingreso_estimado']) * 100
                : 0;
        }
        
        return [
            'costo_por_libra' => $costoPorLibra,
            'costo_total_produccion' => $costoTotal,
            'margen_ventas_ejecutadas' => $margenEjecutado,
            'margen_ventas_potenciales' => $margenPotencial,
            'ganancia_ejecutada' => $ejecutadas['ingreso_total'] - ($ejecutadas['peso_total_lb'] * $costoPorLibra),
            'ganancia_potencial' => ($mejorEscenario['ingreso_estimado'] ?? 0) - ($potenciales['biomasa_disponible_lb'] * $costoPorLibra)
        ];
    }

    /**
     * Generar escenarios de precios por especie usando datos reales del sistema
     */
    private function generarEscenariosPrecios(string $especie): array
    {
        // Obtener precios reales de ventas de la misma especie
        $preciosReales = \App\Models\CosechaParcial::whereHas('lote', function($query) use ($especie) {
            $query->where('especie', $especie);
        })
        ->whereNotNull('precio_kg')
        ->where('precio_kg', '>', 0)
        ->pluck('precio_kg')
        ->toArray();
        
        if (empty($preciosReales)) {
            // Si no hay datos reales para la especie, obtener de todas las especies
            $preciosReales = \App\Models\CosechaParcial::whereNotNull('precio_kg')
                ->where('precio_kg', '>', 0)
                ->pluck('precio_kg')
                ->toArray();
        }
        
        if (empty($preciosReales)) {
            // Si no hay datos en el sistema, retornar array vacío
            return [];
        }
        
        // Calcular estadísticas de precios reales
        $precioPromedio = array_sum($preciosReales) / count($preciosReales);
        $precioMinimo = min($preciosReales);
        $precioMaximo = max($preciosReales);
        
        return [
            'conservador' => $precioMinimo,
            'realista' => $precioPromedio,
            'optimista' => $precioMaximo
        ];
    }

    /**
     * Obtener precio de mercado real del sistema
     */
    private function obtenerPrecioMercadoEstimado(string $especie): float
    {
        // Buscar precio promedio real de la especie
        $precioEspecie = \App\Models\CosechaParcial::whereHas('lote', function($query) use ($especie) {
            $query->where('especie', $especie);
        })
        ->whereNotNull('precio_kg')
        ->where('precio_kg', '>', 0)
        ->avg('precio_kg');
        
        if ($precioEspecie > 0) {
            return $precioEspecie;
        }
        
        // Si no hay datos de la especie, buscar precio promedio general
        $precioGeneral = \App\Models\CosechaParcial::whereNotNull('precio_kg')
            ->where('precio_kg', '>', 0)
            ->avg('precio_kg');
            
        return $precioGeneral ?? 0;
    }

    /**
     * Obtener detalle de ventas realizadas
     */
    private function obtenerDetalleVentas(Collection $cosechas): array
    {
        return $cosechas->map(function ($cosecha) {
            return [
                'fecha' => $cosecha->fecha,
                'cantidad_peces' => $cosecha->cantidad_cosechada,
                'peso_kg' => $cosecha->peso_cosechado_kg,
                'peso_lb' => ($cosecha->peso_cosechado_kg ?: 0) * 2.20462,
                'precio_lb' => $cosecha->precio_venta ?: 0,
                'ingreso' => (($cosecha->peso_cosechado_kg ?: 0) * 2.20462) * ($cosecha->precio_venta ?: 0),
                'responsable' => $cosecha->responsable
            ];
        })->toArray();
    }

    /**
     * Calcular cantidad actual estimada
     */
    private function calcularCantidadActualEstimada(Lote $lote): int
    {
        $cantidadInicial = $lote->cantidad_inicial;
        $cosechasParciales = $lote->cosechasParciales->sum('cantidad_cosechada');
        $mortalidades = $lote->mortalidades->sum('cantidad') ?? 0;
        
        return max(0, $cantidadInicial - $cosechasParciales - $mortalidades);
    }

    /**
     * Generar recomendación de venta
     */
    private function generarRecomendacionVenta(Lote $lote, array $ventasPotenciales): array
    {
        $biomasaKg = $lote->biomasa;
        $pesoPromedioKg = $biomasaKg > 0 && $lote->cantidad_actual > 0 
            ? $biomasaKg / $lote->cantidad_actual 
            : ($lote->peso_promedio_inicial ?: 0.5);
        
        $recomendaciones = [];
        
        // Evaluación por peso promedio
        if ($pesoPromedioKg >= 1.5) {
            $recomendaciones[] = 'Peso comercial óptimo alcanzado - Recomendable vender ahora';
        } elseif ($pesoPromedioKg >= 1.0) {
            $recomendaciones[] = 'Peso comercial aceptable - Evaluar precios de mercado';
        } else {
            $recomendaciones[] = 'Peso aún bajo - Considerar esperar más crecimiento';
        }
        
        // Evaluación por tiempo
        $diasEnProduccion = $lote->fecha_inicio->diffInDays(now());
        if ($diasEnProduccion >= 180) {
            $recomendaciones[] = 'Tiempo de producción prolongado - Evaluar costos vs beneficios';
        }
        
        return [
            'peso_promedio_kg' => $pesoPromedioKg,
            'peso_promedio_lb' => $pesoPromedioKg * 2.20462,
            'dias_en_produccion' => $diasEnProduccion,
            'recomendaciones' => $recomendaciones,
            'mejor_escenario' => isset($ventasPotenciales['escenarios']) && !empty($ventasPotenciales['escenarios'])
                ? collect($ventasPotenciales['escenarios'])->sortByDesc('precio_por_lb')->keys()->first()
                : 'conservador'
        ];
    }

    /**
     * Calcular oportunidad de mejora
     */
    private function calcularOportunidadMejora(array $ejecutadas, array $potenciales): array
    {
        $mejorEscenario = isset($potenciales['escenarios']) && !empty($potenciales['escenarios'])
            ? collect($potenciales['escenarios'])->sortByDesc('precio_por_lb')->first()
            : ['precio_por_lb' => 0];
        $precioMejor = $mejorEscenario['precio_por_lb'] ?? 0;
        $precioEjecutado = $ejecutadas['precio_promedio_lb'] ?? 0;
        
        $diferenciaPrecio = $precioMejor - $precioEjecutado;
        $porcentajeMejora = $precioEjecutado > 0 ? ($diferenciaPrecio / $precioEjecutado) * 100 : 0;
        
        return [
            'diferencia_precio_lb' => $diferenciaPrecio,
            'porcentaje_mejora_precio' => $porcentajeMejora,
            'valor_oportunidad' => $diferenciaPrecio * $potenciales['biomasa_disponible_lb'],
            'recomendacion' => $diferenciaPrecio > 2 ? 'Alta oportunidad de mejora' : 'Precios competitivos'
        ];
    }

    /**
     * Obtener descripción del escenario
     */
    private function obtenerDescripcionEscenario(string $escenario): string
    {
        $descripciones = [
            'conservador' => 'Precio mínimo de mercado, venta rápida garantizada',
            'realista' => 'Precio promedio de mercado actual',
            'optimista' => 'Precio premium, requiere calidad superior y mercado especializado'
        ];
        
        return $descripciones[$escenario] ?? 'Escenario de precio estándar';
    }

    /**
     * Obtener lotes filtrados
     */
    private function obtenerLotesFiltrados(array $filtros): Collection
    {
        $query = Lote::query();
        
        if (isset($filtros['especie'])) {
            $query->where('especie', 'like', '%' . $filtros['especie'] . '%');
        }
        
        if (isset($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }
        
        if (isset($filtros['fecha_desde'])) {
            $query->where('fecha_inicio', '>=', $filtros['fecha_desde']);
        }
        
        if (isset($filtros['fecha_hasta'])) {
            $query->where('fecha_inicio', '<=', $filtros['fecha_hasta']);
        }
        
        return $query->get();
    }

    /**
     * Generar resumen global
     */
    private function generarResumenGlobal(array $resultados): array
    {
        if (empty($resultados)) {
            return [];
        }
        
        $totalVentasEjecutadas = array_sum(array_column(array_column($resultados, 'ventas_ejecutadas'), 'ingreso_total'));
        $totalVentasPotenciales = array_sum(array_map(function ($resultado) {
            if (isset($resultado['ventas_potenciales']['escenarios']) && !empty($resultado['ventas_potenciales']['escenarios'])) {
                $mejorEscenario = collect($resultado['ventas_potenciales']['escenarios'])->sortByDesc('precio_por_lb')->first();
                return $mejorEscenario['ingreso_estimado'] ?? 0;
            }
            return 0;
        }, $resultados));
        
        $totalPesoEjecutado = array_sum(array_column(array_column($resultados, 'ventas_ejecutadas'), 'peso_total_lb'));
        $totalPesoPotencial = array_sum(array_column(array_column($resultados, 'ventas_potenciales'), 'biomasa_disponible_lb'));
        
        return [
            'total_lotes' => count($resultados),
            'ventas_ejecutadas_total' => $totalVentasEjecutadas,
            'ventas_potenciales_total' => $totalVentasPotenciales,
            'peso_vendido_total_lb' => $totalPesoEjecutado,
            'peso_disponible_total_lb' => $totalPesoPotencial,
            'oportunidad_total' => $totalVentasPotenciales - $totalVentasEjecutadas,
            'eficiencia_ventas' => $totalVentasPotenciales > 0 ? ($totalVentasEjecutadas / $totalVentasPotenciales) * 100 : 0
        ];
    }
}