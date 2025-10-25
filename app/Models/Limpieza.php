<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Limpieza extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'area',
        'responsable',
        'protocolo_sanidad_id',
        'actividades_ejecutadas',
        'observaciones',
        'estado',
        'costo_total',
        'insumos_consumidos',
    ];

    protected $casts = [
        'actividades_ejecutadas' => 'array',
        'insumos_consumidos' => 'array',
        'costo_total' => 'decimal:2',
    ];

    public function protocoloSanidad()
    {
        return $this->belongsTo(ProtocoloSanidad::class);
    }

    /**
     * Relación con insumos consumidos en la limpieza
     */
    public function insumosUtilizados()
    {
        return $this->belongsToMany(
            \App\Models\InventarioItem::class,
            'limpieza_insumo',
            'limpieza_id',
            'inventario_item_id'
        )
        ->withPivot('cantidad_planificada', 'cantidad_real', 'costo_unitario', 'costo_total')
        ->withTimestamps();
    }

    /**
     * Verificar si el registro puede ser editado
     */
    public function puedeSerEditado()
    {
        return $this->estado !== 'completado';
    }

    /**
     * Scope para obtener solo registros editables
     */
    public function scopeEditables($query)
    {
        return $query->where('estado', '!=', 'completado');
    }

    /**
     * Accesor: actividades normalizadas para soportar registros antiguos que guardaron todo en una sola descripción.
     * Reutiliza la lógica de separación (saltos de línea, ; | comas, numeraciones 1) 2. 3- ) sólo si existe exactamente un elemento.
     */
    public function getActividadesNormalizadasAttribute()
    {
        $actividades = $this->actividades_ejecutadas ?? [];

        if (is_array($actividades) && count($actividades) === 1) {
            $first = $actividades[array_key_first($actividades)];
            if (is_array($first) && isset($first['descripcion']) && is_string($first['descripcion'])) {
                $raw = trim($first['descripcion']);
                // Heurística: si contiene separadores claros o numeraciones, intentar dividir
                $hasDelimiters = preg_match('/[\r\n;|]/', $raw) || preg_match('/\d+\s*[).:-]\s+/', $raw) || strpos($raw, ',') !== false;
                if ($hasDelimiters) {
                    $segments = preg_split('/[\r\n;|]+/', $raw); // primera pasada
                    if (count($segments) === 1) {
                        // numeraciones dentro de un mismo string
                        if (preg_match('/\d+\s*[).:-]\s+/', $raw)) {
                            $tmp = preg_split('/\s*\d+\s*[).:-]\s*/', $raw);
                            $tmp = array_filter(array_map('trim', $tmp));
                            if (count($tmp) > 1) {
                                $segments = $tmp;
                            }
                        }
                    }
                    // Intentar coma sólo si seguimos con un único bloque largo
                    if (count($segments) === 1 && strpos($segments[0], ',') !== false) {
                        $commaParts = array_map('trim', explode(',', $segments[0]));
                        if (count($commaParts) > 1) {
                            $segments = $commaParts;
                        }
                    }
                    // Limpieza final: descartar vacíos
                    $segments = array_values(array_filter(array_map('trim', $segments), fn($s) => $s !== ''));
                    if (count($segments) > 1) {
                        $originalCompleted = $first['completada'] ?? ($this->estado === 'completado');
                        $originalObs = $first['observaciones'] ?? null;
                        $actividades = [];
                        foreach ($segments as $idx => $seg) {
                            $actividades[$idx] = [
                                'descripcion' => $seg,
                                // Si el registro ya está completado o la actividad original estaba marcada, propagamos el estado
                                'completada' => $originalCompleted ? true : false,
                                'observaciones' => $originalObs,
                            ];
                        }
                    }
                }
            }
        }

        return $actividades;
    }

    /**
     * Ejecutar limpieza y registrar consumo real de insumos
     */
    public function ejecutarConConsumoReal($insumosReales, $observaciones = null)
    {
        try {
            DB::beginTransaction();

            // Validar que la limpieza no esté ya completada
            if ($this->estado === 'completado') {
                throw new \Exception('La limpieza ya está completada');
            }

            $costoTotal = 0;

            // Procesar cada insumo real consumido
            foreach ($insumosReales as $insumoData) {
                $inventarioItem = \App\Models\InventarioItem::find($insumoData['inventario_item_id']);
                if (!$inventarioItem) {
                    throw new \Exception("Insumo no encontrado: ID {$insumoData['inventario_item_id']}");
                }

                $cantidadReal = $insumoData['cantidad_real'];
                $cantidadPlanificada = $insumoData['cantidad_planificada'] ?? 0;

                // Verificar stock disponible
                if ($inventarioItem->stockTotal() < $cantidadReal) {
                    throw new \Exception("Stock insuficiente para {$inventarioItem->nombre}. Stock disponible: {$inventarioItem->stockTotal()}, requerido: {$cantidadReal}");
                }

                // Calcular costo
                $costoUnitario = $inventarioItem->costo_unitario ?? 0;
                $costoTotalInsumo = $cantidadReal * $costoUnitario;
                $costoTotal += $costoTotalInsumo;

                // Registrar la relación con insumo
                $this->insumosUtilizados()->attach($inventarioItem->id, [
                    'cantidad_planificada' => $cantidadPlanificada,
                    'cantidad_real' => $cantidadReal,
                    'costo_unitario' => $costoUnitario,
                    'costo_total' => $costoTotalInsumo,
                ]);

                // Descontar del inventario usando FIFO
                $this->descontarInsumoDelInventario($inventarioItem, $cantidadReal);
            }

            // Actualizar el estado y costo de la limpieza
            $this->update([
                'estado' => 'completado',
                'costo_total' => $costoTotal,
                'observaciones' => $observaciones ?? $this->observaciones,
            ]);

            // Disparar evento de limpieza completada
            event(new \App\Events\LimpiezaCompletada($this, $insumosReales));

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Descontar un insumo específico del inventario usando FIFO
     */
    private function descontarInsumoDelInventario($inventarioItem, $cantidadNecesaria)
    {
        // Obtener existencias del item ordenadas por fecha (FIFO) - usar created_at si no hay fecha_vencimiento
        $existencias = $inventarioItem->existencias()
            ->where('stock_actual', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $cantidadRestante = $cantidadNecesaria;

        foreach ($existencias as $existencia) {
            if ($cantidadRestante <= 0) break;

            $cantidadADescontar = min($cantidadRestante, $existencia->stock_actual);

            // Crear movimiento de salida
            \App\Models\InventarioMovimiento::create([
                'item_id' => $inventarioItem->id,
                'bodega_id' => $existencia->bodega_id,
                'tipo' => 'salida',
                'cantidad_base' => $cantidadADescontar,
                'fecha' => now()->toDateString(),
                'descripcion' => "Limpieza ID: {$this->id} - Área: {$this->area}",
                'referencia_type' => 'limpieza',
                'referencia_id' => $this->id,
                'user_id' => 1,
            ]);

            // Actualizar existencia
            $existencia->update([
                'stock_actual' => $existencia->stock_actual - $cantidadADescontar
            ]);

            $cantidadRestante -= $cantidadADescontar;
        }

        if ($cantidadRestante > 0) {
            throw new \Exception("No se pudo descontar completamente el insumo: {$inventarioItem->nombre}");
        }

        // Verificar si el stock quedó bajo después del descuento
        $stockTotalActual = $inventarioItem->stockTotal();
        if ($inventarioItem->stock_minimo > 0 && $stockTotalActual <= $inventarioItem->stock_minimo) {
            event(new \App\Events\StockBajoDetectado($inventarioItem, $stockTotalActual));
        }
    }
}
