<?php

namespace App\Listeners;

use App\Events\LimpiezaCompletada;
use App\Models\Notificacion;
use App\Models\User;

class CrearNotificacionLimpieza
{
    /**
     * Handle the event.
     */
    public function handle(LimpiezaCompletada $event): void
    {
        $limpieza = $event->limpieza;
        $insumosConsumidos = $event->insumosConsumidos;
        
        // Notificación general de limpieza completada
        Notificacion::create([
            'tipo' => 'success',
            'titulo' => 'Limpieza Completada',
            'mensaje' => "Se ha completado la limpieza en {$limpieza->area}",
            'datos' => [
                'limpieza_id' => $limpieza->id,
                'area' => $limpieza->area,
                'protocolo' => $limpieza->protocoloSanidad ? $limpieza->protocoloSanidad->nombre : 'Sin protocolo',
                'costo_total' => $limpieza->costo_total,
                'insumos_utilizados' => count($insumosConsumidos),
                'tipo_alerta' => 'limpieza_completada',
                'automatica' => true
            ],
            'icono' => 'check-circle',
            'url' => route('limpieza.show', $limpieza->id),
        ]);

        // Notificaciones específicas si hay problemas con insumos
        foreach ($insumosConsumidos as $insumo) {
            $cantidadPlanificada = $insumo['cantidad_planificada'] ?? 0;
            $cantidadReal = $insumo['cantidad_real'] ?? 0;
            $diferencia = abs($cantidadReal - $cantidadPlanificada);
            $porcentajeDiferencia = $cantidadPlanificada > 0 ? ($diferencia / $cantidadPlanificada) * 100 : 0;

            // Si la diferencia es mayor al 20%, crear notificación
            if ($porcentajeDiferencia > 20) {
                $tipoProblema = $cantidadReal > $cantidadPlanificada ? 'sobreconsumido' : 'subconsumido';
                $icono = $cantidadReal > $cantidadPlanificada ? 'alert-triangle' : 'info';
                $tipo = $cantidadReal > $cantidadPlanificada ? 'warning' : 'info';

                Notificacion::create([
                    'tipo' => $tipo,
                    'titulo' => 'Desviación en Consumo de Insumos',
                    'mensaje' => "En la limpieza de {$limpieza->area}, el insumo '{$insumo['nombre']}' fue {$tipoProblema} en un {$porcentajeDiferencia}%",
                    'datos' => [
                        'limpieza_id' => $limpieza->id,
                        'area' => $limpieza->area,
                        'insumo_id' => $insumo['inventario_item_id'],
                        'insumo_nombre' => $insumo['nombre'],
                        'cantidad_planificada' => $cantidadPlanificada,
                        'cantidad_real' => $cantidadReal,
                        'diferencia' => $diferencia,
                        'porcentaje_diferencia' => round($porcentajeDiferencia, 1),
                        'tipo_problema' => $tipoProblema,
                        'tipo_alerta' => 'desviacion_consumo',
                        'automatica' => true
                    ],
                    'icono' => $icono,
                    'url' => route('limpieza.show', $limpieza->id),
                ]);
            }
        }

        // Verificar si hay elementos próximos a vencer después del consumo
        $this->verificarElementosProximosVencer();
    }

    private function verificarElementosProximosVencer()
    {
        // Obtener items que están cerca de su fecha de vencimiento
        $itemsProximosVencer = \App\Models\InventarioLote::where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>', now())
            ->where('cantidad_disponible', '>', 0)
            ->with('inventarioItem')
            ->get();

        foreach ($itemsProximosVencer as $lote) {
            $diasRestantes = now()->diffInDays($lote->fecha_vencimiento);
            
            // Solo notificar si no existe una notificación reciente
            $existeNotificacion = Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'proximo_vencer')
                ->where('created_at', '>', now()->subDays(7))
                ->exists();

            if (!$existeNotificacion && $diasRestantes <= 7) {
                Notificacion::create([
                    'tipo' => 'warning',
                    'titulo' => 'Insumo Próximo a Vencer',
                    'mensaje' => "El lote '{$lote->numero_lote}' del insumo '{$lote->inventarioItem->nombre}' vence en {$diasRestantes} días",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'item_id' => $lote->inventario_item_id,
                        'item_nombre' => $lote->inventarioItem->nombre,
                        'numero_lote' => $lote->numero_lote,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->toDateString(),
                        'dias_restantes' => $diasRestantes,
                        'cantidad_disponible' => $lote->cantidad_disponible,
                        'tipo_alerta' => 'proximo_vencer',
                        'automatica' => true
                    ],
                    'icono' => 'clock',
                    'url' => route('produccion.inventario.index'),
                ]);
            }
        }
    }
}