<?php

namespace App\Listeners;

use App\Events\ConsumoCriticoDetectado;
use App\Models\Notificacion;

class CrearNotificacionConsumo
{
    /**
     * Handle the event.
     */
    public function handle(ConsumoCriticoDetectado $event): void
    {
        $item = $event->item;
        $cantidadPlanificada = $event->cantidadPlanificada;
        $cantidadReal = $event->cantidadReal;
        $razon = $event->razon;
        
        $diferencia = $cantidadReal - $cantidadPlanificada;
        $porcentajeDiferencia = $cantidadPlanificada > 0 ? abs($diferencia / $cantidadPlanificada) * 100 : 0;
        
        $tipo = $diferencia > 0 ? 'warning' : 'info';
        $accion = $diferencia > 0 ? 'sobreconsumido' : 'subconsumido';
        
        Notificacion::create([
            'tipo' => $tipo,
            'titulo' => 'Consumo Crítico Detectado',
            'mensaje' => "El insumo '{$item->nombre}' ha sido {$accion}. {$razon}",
            'datos' => [
                'item_id' => $item->id,
                'item_nombre' => $item->nombre,
                'cantidad_planificada' => $cantidadPlanificada,
                'cantidad_real' => $cantidadReal,
                'diferencia' => $diferencia,
                'porcentaje_diferencia' => round($porcentajeDiferencia, 1),
                'razon' => $razon,
                'accion' => $accion,
                'tipo_alerta' => 'consumo_critico',
                'automatica' => true
            ],
            'icono' => $diferencia > 0 ? 'alert-triangle' : 'info',
            'url' => route('produccion.inventario.index'),
        ]);
    }
}