<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\InventarioItem;
use App\Models\Alimentacion;
use Illuminate\Support\Collection;

class ConsistenciaEstimacionService
{
    /**
     * RF37: Verificar consistencia entre Producción/Inventario/Insumos y generar estimaciones
     */
    public function verificarConsistencia(array $filtros = []): array
    {
        $lotes = $this->obtenerLotesFiltrados($filtros);
        
        $resultados = [];
        
        foreach ($lotes as $lote) {
            $consistencia = $this->analizarConsistenciaLote($lote);
            $estimaciones = $this->generarEstimaciones($lote, $consistencia);
            
            $resultados[] = [
                'lote' => [
                    'id' => $lote->id,
                    'codigo' => $lote->codigo_lote,
                    'especie' => $lote->especie,
                    'estado' => $lote->estado,
                    'fecha_inicio' => $lote->fecha_inicio,
                    'cantidad_inicial' => $lote->cantidad_inicial,
                    'cantidad_actual' => $lote->cantidad_actual
                ],
                'consistencia' => $consistencia,
                'estimaciones' => $estimaciones,
                'recomendaciones' => $this->generarRecomendacionesConsistencia($consistencia),
                'nivel_confianza' => $this->calcularNivelConfianza($consistencia)
            ];
        }
        
        return [
            'lotes' => $resultados,
            'resumen_consistencia' => $this->generarResumenConsistencia($resultados),
            'indicadores_globales' => $this->calcularIndicadoresGlobales($resultados)
        ];
    }

    /**
     * Analizar consistencia de un lote específico
     */
    private function analizarConsistenciaLote(Lote $lote): array
    {
        // 1. Consistencia de Producción
        $produccion = $this->analizarProduccion($lote);
        
        // 2. Consistencia de Inventario/Insumos
        $inventario = $this->analizarInventario($lote);
        
        // 3. Consistencia de Alimentación
        $alimentacion = $this->analizarAlimentacion($lote);
        
        // 4. Análisis de Pérdidas
        $perdidas = $this->analizarPerdidas($lote);
        
        // 5. Ganancia realizada vs esperada
        $ganancia = $this->analizarGanancias($lote);
        
        return [
            'produccion' => $produccion,
            'inventario' => $inventario,
            'alimentacion' => $alimentacion,
            'perdidas' => $perdidas,
            'ganancia' => $ganancia,
            'alertas' => $this->generarAlertasConsistencia($produccion, $inventario, $alimentacion, $perdidas)
        ];
    }

    /**
     * Analizar consistencia de producción
     */
    private function analizarProduccion(Lote $lote): array
    {
        $cantidadInicial = $lote->cantidad_inicial;
        $cantidadActual = $lote->cantidad_actual;
        
        // Obtener datos de seguimientos
        $ultimoSeguimiento = $lote->seguimientos()->latest('fecha_seguimiento')->first();
        $cantidadSeguimiento = $ultimoSeguimiento->cantidad_actual ?? $cantidadActual;
        
        // Calcular diferencias
        $cosechasParciales = $lote->cosechasParciales()->sum('cantidad_cosechada') ?? 0;
        $mortalidades = $lote->mortalidades()->sum('cantidad') ?? 0;
        
        $cantidadCalculada = $cantidadInicial - $cosechasParciales - $mortalidades;
        $diferencia = abs($cantidadActual - $cantidadCalculada);
        $porcentajeDiferencia = $cantidadCalculada > 0 ? ($diferencia / $cantidadCalculada) * 100 : 0;
        
        // Análisis de biomasa
        $biomasaRegistrada = $lote->biomasa;
        $pesoPromedio = $ultimoSeguimiento->peso_promedio ?? $lote->peso_promedio_inicial ?? 0;
        $biomasaCalculada = $cantidadActual * $pesoPromedio;
        $diferenciaBiomasa = abs($biomasaRegistrada - $biomasaCalculada);
        
        return [
            'cantidad_inicial' => $cantidadInicial,
            'cantidad_actual_registrada' => $cantidadActual,
            'cantidad_calculada' => $cantidadCalculada,
            'diferencia_cantidad' => $diferencia,
            'porcentaje_diferencia' => $porcentajeDiferencia,
            'biomasa_registrada' => $biomasaRegistrada,
            'biomasa_calculada' => $biomasaCalculada,
            'diferencia_biomasa' => $diferenciaBiomasa,
            'cosechas_parciales' => $cosechasParciales,
            'mortalidades' => $mortalidades,
            'es_consistente' => $porcentajeDiferencia <= 5, // Tolerancia del 5%
            'nivel_consistencia' => $this->calcularNivelConsistencia($porcentajeDiferencia)
        ];
    }

    /**
     * Analizar consistencia de inventario
     */
    private function analizarInventario(Lote $lote): array
    {
        $insumosUtilizados = [];
        $insumosDisponibles = [];
        $inconsistencias = [];
        
        // Obtener insumos utilizados en mantenimientos
        $mantenimientos = $lote->mantenimientos()->with(['insumos.inventarioItem'])->get();
        
        foreach ($mantenimientos as $mantenimiento) {
            foreach ($mantenimiento->insumos as $insumo) {
                $item = $insumo->inventarioItem;
                if ($item) {
                    $key = $item->id;
                    
                    if (!isset($insumosUtilizados[$key])) {
                        $insumosUtilizados[$key] = [
                            'item' => $item,
                            'cantidad_utilizada' => 0,
                            'stock_actual' => $item->stockTotal(),
                            'costo_total' => 0
                        ];
                    }
                    
                    $insumosUtilizados[$key]['cantidad_utilizada'] += $insumo->cantidad;
                    $insumosUtilizados[$key]['costo_total'] += $insumo->cantidad * ($item->costo_unitario ?? 0);
                    
                    // Verificar disponibilidad
                    if ($item->stockTotal() < 0) {
                        $inconsistencias[] = [
                            'tipo' => 'stock_negativo',
                            'item' => $item->nombre,
                            'stock_actual' => $item->stockTotal(),
                            'gravedad' => 'alta'
                        ];
                    }
                }
            }
        }
        
        // Verificar stocks mínimos
        $itemsBajoMinimo = InventarioItem::whereHas('existencias', function($query) {
                $query->havingRaw('SUM(stock_actual) < inventario_items.stock_minimo');
            })
            ->get();
            
        foreach ($itemsBajoMinimo as $item) {
            $inconsistencias[] = [
                'tipo' => 'bajo_minimo',
                'item' => $item->nombre,
                'stock_actual' => $item->stockTotal(),
                'stock_minimo' => $item->stock_minimo,
                'gravedad' => 'media'
            ];
        }
        
        return [
            'insumos_utilizados' => array_values($insumosUtilizados),
            'total_insumos_diferentes' => count($insumosUtilizados),
            'inconsistencias' => $inconsistencias,
            'items_bajo_minimo' => $itemsBajoMinimo->count(),
            'nivel_consistencia' => empty($inconsistencias) ? 'alto' : (count($inconsistencias) <= 2 ? 'medio' : 'bajo')
        ];
    }

    /**
     * Analizar consistencia de alimentación
     */
    private function analizarAlimentacion(Lote $lote): array
    {
        $alimentaciones = $lote->alimentaciones()->with(['inventarioItem'])->get();
        
        $totalAlimentoKg = $alimentaciones->sum('cantidad_kg');
        $totalCostoAlimento = 0;
        $alimentosPorTipo = [];
        $inconsistencias = [];
        
        foreach ($alimentaciones as $alimentacion) {
            $item = $alimentacion->inventarioItem;
            if ($item && $item->costo_unitario > 0) {
                $totalCostoAlimento += $alimentacion->cantidad_kg * $item->costo_unitario;
            }
            
            $tipo = $item->nombre ?? 'Sin especificar';
            if (!isset($alimentosPorTipo[$tipo])) {
                $alimentosPorTipo[$tipo] = [
                    'cantidad_total' => 0,
                    'numero_raciones' => 0,
                    'costo_total' => 0
                ];
            }
            
            $alimentosPorTipo[$tipo]['cantidad_total'] += $alimentacion->cantidad_kg;
            $alimentosPorTipo[$tipo]['numero_raciones']++;
            $alimentosPorTipo[$tipo]['costo_total'] += $alimentacion->cantidad_kg * ($item->costo_unitario ?? 0);
        }
        
        // Calcular factor de conversión alimenticia estimado
        $biomasaActual = $lote->biomasa;
        $factorConversion = $biomasaActual > 0 ? $totalAlimentoKg / $biomasaActual : 0;
        
        // Evaluar eficiencia alimenticia (FCR típico 1.2-2.0 para piscicultura)
        $eficienciaAlimenticia = 'desconocida';
        if ($factorConversion > 0) {
            if ($factorConversion <= 1.5) {
                $eficienciaAlimenticia = 'excelente';
            } elseif ($factorConversion <= 2.0) {
                $eficienciaAlimenticia = 'buena';
            } elseif ($factorConversion <= 2.5) {
                $eficienciaAlimenticia = 'regular';
            } else {
                $eficienciaAlimenticia = 'deficiente';
                $inconsistencias[] = [
                    'tipo' => 'fcr_alto',
                    'valor' => $factorConversion,
                    'descripcion' => 'Factor de conversión alimenticia muy alto',
                    'gravedad' => 'media'
                ];
            }
        }
        
        return [
            'total_alimento_kg' => $totalAlimentoKg,
            'total_alimento_lb' => $totalAlimentoKg * 2.20462,
            'total_costo_alimento' => $totalCostoAlimento,
            'numero_raciones' => $alimentaciones->count(),
            'alimentos_por_tipo' => $alimentosPorTipo,
            'factor_conversion' => $factorConversion,
            'eficiencia_alimenticia' => $eficienciaAlimenticia,
            'inconsistencias' => $inconsistencias,
            'costo_promedio_por_kg' => $totalAlimentoKg > 0 ? $totalCostoAlimento / $totalAlimentoKg : 0
        ];
    }

    /**
     * Analizar pérdidas del lote
     */
    private function analizarPerdidas(Lote $lote): array
    {
        $mortalidades = $lote->mortalidades()->get();
        $totalMortalidad = $mortalidades->sum('cantidad');
        $porcentajeMortalidad = $lote->cantidad_inicial > 0 ? ($totalMortalidad / $lote->cantidad_inicial) * 100 : 0;
        
        // Análisis por causas
        $mortalidadPorCausa = $mortalidades->groupBy('causa')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->sum('cantidad'),
                'eventos' => $grupo->count()
            ];
        });
        
        // Evaluar nivel de pérdidas
        $nivelPerdidas = 'bajo';
        if ($porcentajeMortalidad > 20) {
            $nivelPerdidas = 'muy_alto';
        } elseif ($porcentajeMortalidad > 15) {
            $nivelPerdidas = 'alto';
        } elseif ($porcentajeMortalidad > 10) {
            $nivelPerdidas = 'medio';
        }
        
        return [
            'total_mortalidad' => $totalMortalidad,
            'porcentaje_mortalidad' => $porcentajeMortalidad,
            'mortalidad_por_causa' => $mortalidadPorCausa,
            'numero_eventos' => $mortalidades->count(),
            'nivel_perdidas' => $nivelPerdidas,
            'es_aceptable' => $porcentajeMortalidad <= 10 // 10% considerado aceptable
        ];
    }

    /**
     * Analizar ganancias realizadas vs esperadas
     */
    private function analizarGanancias(Lote $lote): array
    {
        $costoProduccionService = app(CostoProduccionService::class);
        $ventasService = app(VentasResultadosService::class);
        
        $costos = $costoProduccionService->calcularCostoPorLibra($lote);
        $ventas = $ventasService->obtenerResultadosVentas(['lote_id' => $lote->id]);
        
        $gananciaSeguimientos = $costos['indicadores']['ganancia_realizada'];
        $ventaPotencial = $costos['indicadores']['venta_potencial'];
        $margenRealizado = $costos['indicadores']['margen_estimado'];
        
        return [
            'ganancia_realizada' => $gananciaSeguimientos,
            'venta_potencial' => $ventaPotencial,
            'margen_realizado' => $margenRealizado,
            'costo_total' => $costos['costos']['total'],
            'esta_siendo_rentable' => $gananciaSeguimientos > 0,
            'potencial_mejora' => $ventaPotencial - $gananciaSeguimientos
        ];
    }

    /**
     * Generar estimaciones basadas en datos existentes
     */
    private function generarEstimaciones(Lote $lote, array $consistencia): array
    {
        $estimaciones = [];
        
        // Estimación de cosecha final
        $biomasaActual = $lote->biomasa;
        $diasEnProduccion = $lote->fecha_inicio->diffInDays(now());
        $tasaCrecimientoDiaria = $diasEnProduccion > 0 ? $biomasaActual / $diasEnProduccion : 0;
        
        // Estimar biomasa en 30, 60 y 90 días
        foreach ([30, 60, 90] as $dias) {
            $biomasaEstimada = $biomasaActual + ($tasaCrecimientoDiaria * $dias);
            $estimaciones["biomasa_{$dias}_dias"] = [
                'biomasa_kg' => $biomasaEstimada,
                'biomasa_lb' => $biomasaEstimada * 2.20462,
                'fecha_estimada' => now()->addDays($dias)->format('Y-m-d')
            ];
        }
        
        // Estimación de costos futuros
        $costoActualPorKg = $consistencia['alimentacion']['costo_promedio_por_kg'];
        $consumoEstimadoDiario = $consistencia['alimentacion']['total_alimento_kg'] / max($diasEnProduccion, 1);
        
        foreach ([30, 60, 90] as $dias) {
            $costoAlimentoAdicional = $consumoEstimadoDiario * $dias * $costoActualPorKg;
            $estimaciones["costo_{$dias}_dias"] = [
                'costo_alimento_adicional' => $costoAlimentoAdicional,
                'costo_total_estimado' => $consistencia['ganancia']['costo_total'] + $costoAlimentoAdicional
            ];
        }
        
        return $estimaciones;
    }

    /**
     * Calcular nivel de consistencia
     */
    private function calcularNivelConsistencia(float $porcentajeDiferencia): string
    {
        if ($porcentajeDiferencia <= 2) return 'excelente';
        if ($porcentajeDiferencia <= 5) return 'bueno';
        if ($porcentajeDiferencia <= 10) return 'regular';
        return 'deficiente';
    }

    /**
     * Generar alertas de consistencia
     */
    private function generarAlertasConsistencia($produccion, $inventario, $alimentacion, $perdidas): array
    {
        $alertas = [];
        
        if (!$produccion['es_consistente']) {
            $alertas[] = [
                'tipo' => 'produccion_inconsistente',
                'mensaje' => "Diferencia del {$produccion['porcentaje_diferencia']}% entre cantidad registrada y calculada",
                'gravedad' => $produccion['porcentaje_diferencia'] > 10 ? 'alta' : 'media'
            ];
        }
        
        if ($perdidas['porcentaje_mortalidad'] > 15) {
            $alertas[] = [
                'tipo' => 'mortalidad_alta',
                'mensaje' => "Mortalidad del {$perdidas['porcentaje_mortalidad']}% supera niveles aceptables",
                'gravedad' => 'alta'
            ];
        }
        
        if ($alimentacion['factor_conversion'] > 2.5) {
            $alertas[] = [
                'tipo' => 'fcr_alto',
                'mensaje' => "Factor de conversión alimenticia alto: {$alimentacion['factor_conversion']}",
                'gravedad' => 'media'
            ];
        }
        
        foreach ($inventario['inconsistencias'] as $inconsistencia) {
            $alertas[] = [
                'tipo' => $inconsistencia['tipo'],
                'mensaje' => "Inventario: {$inconsistencia['item']} - " . ($inconsistencia['tipo'] == 'stock_negativo' ? 'Stock negativo' : 'Bajo mínimo'),
                'gravedad' => $inconsistencia['gravedad']
            ];
        }
        
        return $alertas;
    }

    /**
     * Generar recomendaciones de consistencia
     */
    private function generarRecomendacionesConsistencia(array $consistencia): array
    {
        $recomendaciones = [];
        
        if (!$consistencia['produccion']['es_consistente']) {
            $recomendaciones[] = 'Revisar y actualizar registros de cantidad actual del lote';
            $recomendaciones[] = 'Realizar conteo físico para verificar población real';
        }
        
        if ($consistencia['perdidas']['porcentaje_mortalidad'] > 10) {
            $recomendaciones[] = 'Implementar medidas de bioseguridad adicionales';
            $recomendaciones[] = 'Revisar protocolos de sanidad y calidad del agua';
        }
        
        if ($consistencia['alimentacion']['factor_conversion'] > 2.0) {
            $recomendaciones[] = 'Optimizar programa de alimentación';
            $recomendaciones[] = 'Evaluar calidad y tipo de alimento utilizado';
        }
        
        if (!empty($consistencia['inventario']['inconsistencias'])) {
            $recomendaciones[] = 'Actualizar registros de inventario';
            $recomendaciones[] = 'Implementar control de stock más estricto';
        }
        
        return $recomendaciones;
    }

    /**
     * Calcular nivel de confianza general
     */
    private function calcularNivelConfianza(array $consistencia): array
    {
        $puntuacion = 100;
        
        // Descontar por inconsistencias
        if (!$consistencia['produccion']['es_consistente']) {
            $puntuacion -= $consistencia['produccion']['porcentaje_diferencia'];
        }
        
        if ($consistencia['perdidas']['porcentaje_mortalidad'] > 10) {
            $puntuacion -= 10;
        }
        
        if ($consistencia['alimentacion']['factor_conversion'] > 2.0) {
            $puntuacion -= 15;
        }
        
        $puntuacion -= count($consistencia['inventario']['inconsistencias']) * 5;
        
        $puntuacion = max(0, min(100, $puntuacion));
        
        $nivel = 'bajo';
        if ($puntuacion >= 90) $nivel = 'muy_alto';
        elseif ($puntuacion >= 75) $nivel = 'alto';
        elseif ($puntuacion >= 60) $nivel = 'medio';
        
        return [
            'puntuacion' => $puntuacion,
            'nivel' => $nivel,
            'descripcion' => $this->obtenerDescripcionConfianza($nivel)
        ];
    }

    /**
     * Obtener descripción del nivel de confianza
     */
    private function obtenerDescripcionConfianza(string $nivel): string
    {
        $descripciones = [
            'muy_alto' => 'Datos muy confiables, registros consistentes',
            'alto' => 'Datos confiables con inconsistencias menores',
            'medio' => 'Datos aceptables, revisar algunas inconsistencias',
            'bajo' => 'Datos poco confiables, requiere revisión y corrección'
        ];
        
        return $descripciones[$nivel] ?? 'Nivel de confianza desconocido';
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
        
        if (isset($filtros['lote_id'])) {
            $query->where('id', $filtros['lote_id']);
        }
        
        return $query->get();
    }

    /**
     * Generar resumen de consistencia global
     */
    private function generarResumenConsistencia(array $resultados): array
    {
        if (empty($resultados)) return [];
        
        $totalLotes = count($resultados);
        $lotesConsistentes = 0;
        $alertasTotales = 0;
        
        foreach ($resultados as $resultado) {
            if ($resultado['consistencia']['produccion']['es_consistente']) {
                $lotesConsistentes++;
            }
            $alertasTotales += count($resultado['consistencia']['alertas']);
        }
        
        return [
            'total_lotes' => $totalLotes,
            'lotes_consistentes' => $lotesConsistentes,
            'porcentaje_consistencia' => $totalLotes > 0 ? ($lotesConsistentes / $totalLotes) * 100 : 0,
            'alertas_totales' => $alertasTotales,
            'promedio_alertas_por_lote' => $totalLotes > 0 ? $alertasTotales / $totalLotes : 0
        ];
    }

    /**
     * Calcular indicadores globales
     */
    private function calcularIndicadoresGlobales(array $resultados): array
    {
        if (empty($resultados)) return [];
        
        $totalMortalidad = 0;
        $totalAlimento = 0;
        $totalBiomasa = 0;
        $nivelesConfianza = [];
        
        foreach ($resultados as $resultado) {
            $totalMortalidad += $resultado['consistencia']['perdidas']['porcentaje_mortalidad'];
            $totalAlimento += $resultado['consistencia']['alimentacion']['total_alimento_kg'];
            $totalBiomasa += $resultado['lote']['biomasa_actual_kg'] ?? 0;
            $nivelesConfianza[] = $resultado['nivel_confianza']['puntuacion'];
        }
        
        $totalLotes = count($resultados);
        
        return [
            'mortalidad_promedio' => $totalLotes > 0 ? $totalMortalidad / $totalLotes : 0,
            'alimento_total_kg' => $totalAlimento,
            'biomasa_total_kg' => $totalBiomasa,
            'fcr_promedio' => $totalBiomasa > 0 ? $totalAlimento / $totalBiomasa : 0,
            'confianza_promedio' => $totalLotes > 0 ? array_sum($nivelesConfianza) / $totalLotes : 0
        ];
    }
}