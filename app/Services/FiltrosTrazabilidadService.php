<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\InventarioItem;
use App\Models\Alimentacion;
use App\Models\CosechaParcial;
use App\Models\Seguimiento;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class FiltrosTrazabilidadService
{
    /**
     * RF38: Implementar filtros y trazabilidad avanzada para lotes, productos e insumos
     */
    public function aplicarFiltrosAvanzados(array $filtros): array
    {
        // Construir query base con relaciones
        $query = $this->construirQueryBase();
        
        // Aplicar filtros específicos
        $query = $this->aplicarFiltrosLote($query, $filtros);
        $query = $this->aplicarFiltrosProduccion($query, $filtros);
        $query = $this->aplicarFiltrosInsumos($query, $filtros);
        $query = $this->aplicarFiltrosEconomicos($query, $filtros);
        $query = $this->aplicarFiltrosParametrizables($query, $filtros);
        
        // Ejecutar query y obtener resultados
        $lotes = $query->get();
        
        // Generar trazabilidad detallada
        $trazabilidad = $this->generarTrazabilidadDetallada($lotes, $filtros);
        
        return [
            'lotes' => $lotes->map(function ($lote) {
                return $this->formatearLoteParaResultado($lote);
            }),
            'trazabilidad' => $trazabilidad,
            'estadisticas' => $this->generarEstadisticasFiltradas($lotes),
            'filtros_aplicados' => $this->documentarFiltrosAplicados($filtros),
            'acciones_sugeridas' => $this->generarAccionesSugeridas($lotes, $filtros)
        ];
    }

    /**
     * Construir query base con todas las relaciones necesarias
     */
    private function construirQueryBase(): Builder
    {
        return Lote::with([
            'alimentaciones.inventarioItem',
            'seguimientos.protocoloSanidad.insumos.inventarioItem',
            'mantenimientos.insumos.inventarioItem',
            'cosechasParciales',
            'mortalidades',
            'unidadProduccion',
            'enfermedades'
        ]);
    }

    /**
     * Aplicar filtros relacionados con el lote
     */
    private function aplicarFiltrosLote(Builder $query, array $filtros): Builder
    {
        // Filtro por especie
        if (!empty($filtros['especie'])) {
            $query->where('especie', 'like', '%' . $filtros['especie'] . '%');
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            if (is_array($filtros['estado'])) {
                $query->whereIn('estado', $filtros['estado']);
            } else {
                $query->where('estado', $filtros['estado']);
            }
        }
        
        // Filtro por rango de fechas
        if (!empty($filtros['fecha_inicio_desde'])) {
            $query->where('fecha_inicio', '>=', $filtros['fecha_inicio_desde']);
        }
        
        if (!empty($filtros['fecha_inicio_hasta'])) {
            $query->where('fecha_inicio', '<=', $filtros['fecha_inicio_hasta']);
        }
        
        // Filtro por código de lote
        if (!empty($filtros['codigo_lote'])) {
            $query->where('codigo_lote', 'like', '%' . $filtros['codigo_lote'] . '%');
        }
        
        // Filtro por unidad de producción
        if (!empty($filtros['unidad_produccion_id'])) {
            $query->where('unidad_produccion_id', $filtros['unidad_produccion_id']);
        }
        
        return $query;
    }

    /**
     * Aplicar filtros de producción
     */
    private function aplicarFiltrosProduccion(Builder $query, array $filtros): Builder
    {
        // Filtro por cantidad inicial
        if (!empty($filtros['cantidad_inicial_min'])) {
            $query->where('cantidad_inicial', '>=', $filtros['cantidad_inicial_min']);
        }
        
        if (!empty($filtros['cantidad_inicial_max'])) {
            $query->where('cantidad_inicial', '<=', $filtros['cantidad_inicial_max']);
        }
        
        // Filtro por peso promedio inicial
        if (!empty($filtros['peso_inicial_min'])) {
            $query->where('peso_promedio_inicial', '>=', $filtros['peso_inicial_min']);
        }
        
        if (!empty($filtros['peso_inicial_max'])) {
            $query->where('peso_promedio_inicial', '<=', $filtros['peso_inicial_max']);
        }
        
        // Filtro por biomasa actual (calculada)
        if (!empty($filtros['biomasa_min']) || !empty($filtros['biomasa_max'])) {
            $query->whereHas('seguimientos', function ($q) use ($filtros) {
                if (!empty($filtros['biomasa_min'])) {
                    $q->havingRaw('(peso_promedio * cantidad_actual) >= ?', [$filtros['biomasa_min']]);
                }
                if (!empty($filtros['biomasa_max'])) {
                    $q->havingRaw('(peso_promedio * cantidad_actual) <= ?', [$filtros['biomasa_max']]);
                }
            });
        }
        
        // Filtro por nivel de mortalidad
        if (!empty($filtros['mortalidad_max'])) {
            $query->whereHas('mortalidades', function ($q) use ($filtros) {
                $q->havingRaw('(SUM(cantidad) / lotes.cantidad_inicial * 100) <= ?', [$filtros['mortalidad_max']]);
            });
        }
        
        return $query;
    }

    /**
     * Aplicar filtros de insumos
     */
    private function aplicarFiltrosInsumos(Builder $query, array $filtros): Builder
    {
        // Filtro por tipo de alimento usado
        if (!empty($filtros['tipo_alimento'])) {
            $query->whereHas('alimentaciones.inventarioItem', function ($q) use ($filtros) {
                $q->where('nombre', 'like', '%' . $filtros['tipo_alimento'] . '%');
            });
        }
        
        // Filtro por insumo específico utilizado
        if (!empty($filtros['insumo_utilizado'])) {
            $query->where(function ($q) use ($filtros) {
                $q->whereHas('mantenimientos.insumos.inventarioItem', function ($subQ) use ($filtros) {
                    $subQ->where('nombre', 'like', '%' . $filtros['insumo_utilizado'] . '%');
                })->orWhereHas('seguimientos.protocoloSanidad.insumos.inventarioItem', function ($subQ) use ($filtros) {
                    $subQ->where('nombre', 'like', '%' . $filtros['insumo_utilizado'] . '%');
                });
            });
        }
        
        // Filtro por rango de consumo de alimento
        if (!empty($filtros['consumo_alimento_min']) || !empty($filtros['consumo_alimento_max'])) {
            $query->whereHas('alimentaciones', function ($q) use ($filtros) {
                if (!empty($filtros['consumo_alimento_min'])) {
                    $q->havingRaw('SUM(cantidad_kg) >= ?', [$filtros['consumo_alimento_min']]);
                }
                if (!empty($filtros['consumo_alimento_max'])) {
                    $q->havingRaw('SUM(cantidad_kg) <= ?', [$filtros['consumo_alimento_max']]);
                }
            });
        }
        
        return $query;
    }

    /**
     * Aplicar filtros económicos
     */
    private function aplicarFiltrosEconomicos(Builder $query, array $filtros): Builder
    {
        // Filtro por rango de costo de producción
        if (!empty($filtros['costo_min']) || !empty($filtros['costo_max'])) {
            $costoService = app(CostoProduccionService::class);
            
            $query->get()->filter(function ($lote) use ($filtros, $costoService) {
                $costos = $costoService->calcularCostoPorLibra($lote);
                $costoTotal = $costos['costos']['total'];
                
                $cumpleCostoMin = empty($filtros['costo_min']) || $costoTotal >= $filtros['costo_min'];
                $cumpleCostoMax = empty($filtros['costo_max']) || $costoTotal <= $filtros['costo_max'];
                
                return $cumpleCostoMin && $cumpleCostoMax;
            });
        }
        
        // Filtro por rentabilidad
        if (!empty($filtros['rentabilidad_min'])) {
            // Se aplicará en post-procesamiento debido a la complejidad del cálculo
        }
        
        return $query;
    }

    /**
     * Aplicar filtros parametrizables personalizados
     */
    private function aplicarFiltrosParametrizables(Builder $query, array $filtros): Builder
    {
        // Filtro por días en producción
        if (!empty($filtros['dias_produccion_min']) || !empty($filtros['dias_produccion_max'])) {
            $fechaActual = now();
            
            if (!empty($filtros['dias_produccion_min'])) {
                $fechaLimite = $fechaActual->copy()->subDays($filtros['dias_produccion_min']);
                $query->where('fecha_inicio', '<=', $fechaLimite);
            }
            
            if (!empty($filtros['dias_produccion_max'])) {
                $fechaLimite = $fechaActual->copy()->subDays($filtros['dias_produccion_max']);
                $query->where('fecha_inicio', '>=', $fechaLimite);
            }
        }
        
        // Filtro por factor de conversión alimenticia
        if (!empty($filtros['fcr_max'])) {
            // Se aplicará en post-procesamiento
        }
        
        // Filtro por presencia de enfermedades
        if (isset($filtros['tiene_enfermedades'])) {
            if ($filtros['tiene_enfermedades']) {
                $query->whereHas('enfermedades');
            } else {
                $query->whereDoesntHave('enfermedades');
            }
        }
        
        // Filtro por número mínimo de seguimientos
        if (!empty($filtros['seguimientos_min'])) {
            $query->has('seguimientos', '>=', $filtros['seguimientos_min']);
        }
        
        return $query;
    }

    /**
     * Generar trazabilidad detallada
     */
    private function generarTrazabilidadDetallada(Collection $lotes, array $filtros): array
    {
        $trazabilidad = [];
        
        foreach ($lotes as $lote) {
            $trazabilidad[$lote->id] = [
                'lote_info' => $this->obtenerInfoLote($lote),
                'cronologia' => $this->generarCronologia($lote),
                'insumos_detalle' => $this->obtenerDetalleInsumos($lote),
                'produccion_detalle' => $this->obtenerDetalleProduccion($lote),
                'costos_detalle' => $this->obtenerDetalleCostos($lote),
                'eventos_criticos' => $this->identificarEventosCriticos($lote),
                'cadena_suministro' => $this->trazarCadenaSupministro($lote)
            ];
        }
        
        return $trazabilidad;
    }

    /**
     * Obtener información básica del lote
     */
    private function obtenerInfoLote(Lote $lote): array
    {
        return [
            'codigo' => $lote->codigo_lote,
            'especie' => $lote->especie,
            'estado' => $lote->estado,
            'fecha_inicio' => $lote->fecha_inicio,
            'dias_en_produccion' => $lote->fecha_inicio->diffInDays(now()),
            'unidad_produccion' => $lote->unidadProduccion->nombre ?? 'No asignada',
            'cantidad_inicial' => $lote->cantidad_inicial,
            'cantidad_actual' => $lote->cantidad_actual,
            'biomasa_actual' => $lote->biomasa
        ];
    }

    /**
     * Generar cronología de eventos
     */
    private function generarCronologia(Lote $lote): array
    {
        $eventos = collect();
        
        // Eventos de alimentación
        foreach ($lote->alimentaciones as $alimentacion) {
            $eventos->push([
                'fecha' => $alimentacion->fecha_alimentacion,
                'tipo' => 'alimentacion',
                'descripcion' => "Alimentación: {$alimentacion->cantidad_kg} kg de " . ($alimentacion->inventarioItem->nombre ?? 'alimento'),
                'costo' => ($alimentacion->inventarioItem->costo_unitario ?? 0) * $alimentacion->cantidad_kg
            ]);
        }
        
        // Eventos de seguimiento
        foreach ($lote->seguimientos as $seguimiento) {
            $eventos->push([
                'fecha' => $seguimiento->fecha_seguimiento,
                'tipo' => 'seguimiento',
                'descripcion' => "Seguimiento: {$seguimiento->cantidad_actual} peces, peso promedio {$seguimiento->peso_promedio} kg",
                'observaciones' => $seguimiento->observaciones
            ]);
        }
        
        // Eventos de mortalidad
        foreach ($lote->mortalidades as $mortalidad) {
            $eventos->push([
                'fecha' => $mortalidad->fecha,
                'tipo' => 'mortalidad',
                'descripcion' => "Mortalidad: {$mortalidad->cantidad} peces por {$mortalidad->causa}",
                'gravedad' => 'alta'
            ]);
        }
        
        // Eventos de cosecha
        foreach ($lote->cosechasParciales as $cosecha) {
            $eventos->push([
                'fecha' => $cosecha->fecha,
                'tipo' => 'cosecha',
                'descripcion' => "Cosecha {$cosecha->destino}: {$cosecha->cantidad_cosechada} peces",
                'peso' => $cosecha->peso_cosechado_kg
            ]);
        }
        
        return $eventos->sortBy('fecha')->values()->toArray();
    }

    /**
     * Obtener detalle de insumos utilizados
     */
    private function obtenerDetalleInsumos(Lote $lote): array
    {
        $insumos = [];
        
        // Insumos de mantenimientos
        foreach ($lote->mantenimientos as $mantenimiento) {
            foreach ($mantenimiento->insumos as $insumo) {
                $item = $insumo->inventarioItem;
                if ($item) {
                    $key = $item->id;
                    if (!isset($insumos[$key])) {
                        $insumos[$key] = [
                            'nombre' => $item->nombre,
                            'sku' => $item->sku,
                            'tipo' => $item->tipo,
                            'unidad' => $item->unidad_base,
                            'cantidad_total' => 0,
                            'costo_total' => 0,
                            'usos' => []
                        ];
                    }
                    
                    $insumos[$key]['cantidad_total'] += $insumo->cantidad;
                    $insumos[$key]['costo_total'] += $insumo->cantidad * ($item->costo_unitario ?? 0);
                    $insumos[$key]['usos'][] = [
                        'fecha' => $mantenimiento->created_at,
                        'tipo_uso' => 'mantenimiento',
                        'cantidad' => $insumo->cantidad,
                        'descripcion' => $mantenimiento->tipo_mantenimiento ?? 'Mantenimiento general'
                    ];
                }
            }
        }
        
        // Insumos de protocolos de sanidad
        foreach ($lote->seguimientos as $seguimiento) {
            if ($seguimiento->protocoloSanidad) {
                foreach ($seguimiento->protocoloSanidad->insumos as $protocoloInsumo) {
                    $item = $protocoloInsumo->inventarioItem;
                    if ($item) {
                        $key = $item->id;
                        if (!isset($insumos[$key])) {
                            $insumos[$key] = [
                                'nombre' => $item->nombre,
                                'sku' => $item->sku,
                                'tipo' => $item->tipo,
                                'unidad' => $item->unidad_base,
                                'cantidad_total' => 0,
                                'costo_total' => 0,
                                'usos' => []
                            ];
                        }
                        
                        $insumos[$key]['cantidad_total'] += $protocoloInsumo->cantidad_necesaria;
                        $insumos[$key]['costo_total'] += $protocoloInsumo->cantidad_necesaria * ($item->costo_unitario ?? 0);
                        $insumos[$key]['usos'][] = [
                            'fecha' => $seguimiento->fecha_seguimiento,
                            'tipo_uso' => 'protocolo_sanidad',
                            'cantidad' => $protocoloInsumo->cantidad_necesaria,
                            'descripcion' => $seguimiento->protocoloSanidad->nombre ?? 'Protocolo de sanidad'
                        ];
                    }
                }
            }
        }
        
        return array_values($insumos);
    }

    /**
     * Obtener detalle de producción
     */
    private function obtenerDetalleProduccion(Lote $lote): array
    {
        $alimentaciones = $lote->alimentaciones;
        $totalAlimento = $alimentaciones->sum('cantidad_kg');
        $costoAlimento = 0;
        
        foreach ($alimentaciones as $alimentacion) {
            if ($alimentacion->inventarioItem && $alimentacion->inventarioItem->costo_unitario) {
                $costoAlimento += $alimentacion->cantidad_kg * $alimentacion->inventarioItem->costo_unitario;
            }
        }
        
        $mortalidadTotal = $lote->mortalidades->sum('cantidad');
        $cosechasTotal = $lote->cosechasParciales->sum('cantidad_cosechada');
        
        return [
            'alimentacion' => [
                'total_kg' => $totalAlimento,
                'total_lb' => $totalAlimento * 2.20462,
                'costo_total' => $costoAlimento,
                'numero_raciones' => $alimentaciones->count(),
                'fcr' => $lote->biomasa > 0 ? $totalAlimento / $lote->biomasa : 0
            ],
            'mortalidad' => [
                'total_peces' => $mortalidadTotal,
                'porcentaje' => $lote->cantidad_inicial > 0 ? ($mortalidadTotal / $lote->cantidad_inicial) * 100 : 0,
                'eventos' => $lote->mortalidades->count()
            ],
            'cosechas' => [
                'total_peces' => $cosechasTotal,
                'porcentaje' => $lote->cantidad_inicial > 0 ? ($cosechasTotal / $lote->cantidad_inicial) * 100 : 0,
                'eventos' => $lote->cosechasParciales->count()
            ],
            'supervivencia' => [
                'actual' => $lote->cantidad_actual,
                'porcentaje' => $lote->cantidad_inicial > 0 ? ($lote->cantidad_actual / $lote->cantidad_inicial) * 100 : 0
            ]
        ];
    }

    /**
     * Obtener detalle de costos
     */
    private function obtenerDetalleCostos(Lote $lote): array
    {
        $costoService = app(CostoProduccionService::class);
        return $costoService->calcularCostoPorLibra($lote);
    }

    /**
     * Identificar eventos críticos
     */
    private function identificarEventosCriticos(Lote $lote): array
    {
        $eventos = [];
        
        // Mortalidad alta en un solo evento
        foreach ($lote->mortalidades as $mortalidad) {
            $porcentaje = $lote->cantidad_inicial > 0 ? ($mortalidad->cantidad / $lote->cantidad_inicial) * 100 : 0;
            if ($porcentaje > 5) {
                $eventos[] = [
                    'fecha' => $mortalidad->fecha,
                    'tipo' => 'mortalidad_alta',
                    'descripcion' => "Mortalidad del {$porcentaje}% en un solo evento",
                    'gravedad' => $porcentaje > 10 ? 'critica' : 'alta'
                ];
            }
        }
        
        // Enfermedades registradas
        foreach ($lote->enfermedades as $enfermedad) {
            $eventos[] = [
                'fecha' => $enfermedad->fecha_deteccion,
                'tipo' => 'enfermedad',
                'descripcion' => "Enfermedad detectada: {$enfermedad->tipo_enfermedad}",
                'gravedad' => 'alta'
            ];
        }
        
        // Periodos sin alimentación prolongados
        $alimentaciones = $lote->alimentaciones->sortBy('fecha_alimentacion');
        $fechaAnterior = null;
        
        foreach ($alimentaciones as $alimentacion) {
            if ($fechaAnterior) {
                $diasSinAlimento = $fechaAnterior->diffInDays($alimentacion->fecha_alimentacion);
                if ($diasSinAlimento > 3) {
                    $eventos[] = [
                        'fecha' => $alimentacion->fecha_alimentacion,
                        'tipo' => 'ayuno_prolongado',
                        'descripcion' => "{$diasSinAlimento} días sin alimentación registrada",
                        'gravedad' => 'media'
                    ];
                }
            }
            $fechaAnterior = $alimentacion->fecha_alimentacion;
        }
        
        return $eventos;
    }

    /**
     * Trazar cadena de suministro
     */
    private function trazarCadenaSupministro(Lote $lote): array
    {
        $cadena = [
            'insumos_origen' => [],
            'alimentos_origen' => [],
            'proveedores' => [],
            'destinos_cosecha' => []
        ];
        
        // Rastrear origen de insumos (limitado por datos disponibles)
        $insumosUtilizados = $this->obtenerDetalleInsumos($lote);
        foreach ($insumosUtilizados as $insumo) {
            $cadena['insumos_origen'][] = [
                'nombre' => $insumo['nombre'],
                'sku' => $insumo['sku'],
                'cantidad_utilizada' => $insumo['cantidad_total'],
                'costo_total' => $insumo['costo_total']
            ];
        }
        
        // Rastrear alimentos
        foreach ($lote->alimentaciones as $alimentacion) {
            if ($alimentacion->inventarioItem) {
                $cadena['alimentos_origen'][] = [
                    'nombre' => $alimentacion->inventarioItem->nombre,
                    'cantidad' => $alimentacion->cantidad_kg,
                    'fecha' => $alimentacion->fecha_alimentacion
                ];
            }
        }
        
        // Rastrear destinos de cosecha
        foreach ($lote->cosechasParciales as $cosecha) {
            $cadena['destinos_cosecha'][] = [
                'fecha' => $cosecha->fecha,
                'destino' => $cosecha->destino,
                'cantidad' => $cosecha->cantidad_cosechada,
                'peso' => $cosecha->peso_cosechado_kg,
                'responsable' => $cosecha->responsable
            ];
        }
        
        return $cadena;
    }

    /**
     * Formatear lote para resultado
     */
    private function formatearLoteParaResultado(Lote $lote): array
    {
        return [
            'id' => $lote->id,
            'codigo' => $lote->codigo_lote,
            'especie' => $lote->especie,
            'estado' => $lote->estado,
            'fecha_inicio' => $lote->fecha_inicio,
            'cantidad_inicial' => $lote->cantidad_inicial,
            'cantidad_actual' => $lote->cantidad_actual,
            'biomasa' => $lote->biomasa,
            'dias_en_produccion' => $lote->fecha_inicio->diffInDays(now()),
            'unidad_produccion' => $lote->unidadProduccion->nombre ?? null,
            'resumen_actividad' => [
                'alimentaciones' => $lote->alimentaciones->count(),
                'seguimientos' => $lote->seguimientos->count(),
                'mortalidades' => $lote->mortalidades->count(),
                'cosechas' => $lote->cosechasParciales->count()
            ]
        ];
    }

    /**
     * Generar estadísticas de los lotes filtrados
     */
    private function generarEstadisticasFiltradas(Collection $lotes): array
    {
        if ($lotes->isEmpty()) {
            return [];
        }
        
        return [
            'total_lotes' => $lotes->count(),
            'especies' => $lotes->pluck('especie')->unique()->values(),
            'estados' => $lotes->groupBy('estado')->map->count(),
            'biomasa_total' => $lotes->sum('biomasa'),
            'peces_totales' => $lotes->sum('cantidad_actual'),
            'promedio_dias_produccion' => $lotes->avg(function ($lote) {
                return $lote->fecha_inicio->diffInDays(now());
            }),
            'rango_fechas' => [
                'inicio_mas_antiguo' => $lotes->min('fecha_inicio'),
                'inicio_mas_reciente' => $lotes->max('fecha_inicio')
            ]
        ];
    }

    /**
     * Documentar filtros aplicados
     */
    private function documentarFiltrosAplicados(array $filtros): array
    {
        $documentacion = [];
        
        foreach ($filtros as $filtro => $valor) {
            if (!empty($valor)) {
                $documentacion[] = [
                    'filtro' => $filtro,
                    'valor' => $valor,
                    'descripcion' => $this->obtenerDescripcionFiltro($filtro, $valor)
                ];
            }
        }
        
        return $documentacion;
    }

    /**
     * Obtener descripción de filtro
     */
    private function obtenerDescripcionFiltro(string $filtro, $valor): string
    {
        $descripciones = [
            'especie' => "Especie contiene: {$valor}",
            'estado' => "Estado: {$valor}",
            'fecha_inicio_desde' => "Fecha inicio desde: {$valor}",
            'fecha_inicio_hasta' => "Fecha inicio hasta: {$valor}",
            'cantidad_inicial_min' => "Cantidad inicial mínima: {$valor}",
            'cantidad_inicial_max' => "Cantidad inicial máxima: {$valor}",
            'biomasa_min' => "Biomasa mínima: {$valor} kg",
            'biomasa_max' => "Biomasa máxima: {$valor} kg",
            'mortalidad_max' => "Mortalidad máxima: {$valor}%",
            'costo_min' => "Costo mínimo: Q{$valor}",
            'costo_max' => "Costo máximo: Q{$valor}",
            'dias_produccion_min' => "Días mínimos en producción: {$valor}",
            'dias_produccion_max' => "Días máximos en producción: {$valor}"
        ];
        
        return $descripciones[$filtro] ?? "Filtro {$filtro}: {$valor}";
    }

    /**
     * Generar acciones sugeridas basadas en resultados
     */
    private function generarAccionesSugeridas(Collection $lotes, array $filtros): array
    {
        $acciones = [];
        
        if ($lotes->isEmpty()) {
            $acciones[] = [
                'tipo' => 'info',
                'accion' => 'Ajustar filtros',
                'descripcion' => 'No se encontraron lotes con los criterios especificados. Considere ampliar los rangos de filtros.'
            ];
            return $acciones;
        }
        
        // Analizar mortalidad alta
        $lotesAltaMortalidad = $lotes->filter(function ($lote) {
            $mortalidadTotal = $lote->mortalidades->sum('cantidad');
            return $lote->cantidad_inicial > 0 && ($mortalidadTotal / $lote->cantidad_inicial) > 0.15;
        });
        
        if ($lotesAltaMortalidad->count() > 0) {
            $acciones[] = [
                'tipo' => 'alerta',
                'accion' => 'Revisar protocolos de sanidad',
                'descripcion' => "{$lotesAltaMortalidad->count()} lote(s) con mortalidad superior al 15%"
            ];
        }
        
        // Lotes sin seguimiento reciente
        $lotesSinSeguimiento = $lotes->filter(function ($lote) {
            $ultimoSeguimiento = $lote->seguimientos->max('fecha_seguimiento');
            return !$ultimoSeguimiento || now()->diffInDays($ultimoSeguimiento) > 30;
        });
        
        if ($lotesSinSeguimiento->count() > 0) {
            $acciones[] = [
                'tipo' => 'recomendacion',
                'accion' => 'Actualizar seguimientos',
                'descripcion' => "{$lotesSinSeguimiento->count()} lote(s) sin seguimiento reciente (>30 días)"
            ];
        }
        
        // Lotes listos para cosecha
        $lotesListosCosecha = $lotes->filter(function ($lote) {
            return $lote->fecha_inicio->diffInDays(now()) > 120 && $lote->estado == 'activo';
        });
        
        if ($lotesListosCosecha->count() > 0) {
            $acciones[] = [
                'tipo' => 'oportunidad',
                'accion' => 'Evaluar cosecha',
                'descripcion' => "{$lotesListosCosecha->count()} lote(s) con más de 120 días, evaluar para cosecha"
            ];
        }
        
        return $acciones;
    }
}