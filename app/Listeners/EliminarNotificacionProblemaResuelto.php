<?php

namespace App\Listeners;

use App\Events\ProblemaResuelto;
use App\Models\Notificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EliminarNotificacionProblemaResuelto
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProblemaResuelto $event): void
    {
        $eliminadas = 0;

        switch ($event->tipoProblema) {
            case 'stock_resuelto':
                $eliminadas = $this->eliminarNotificacionesStock($event->identificador);
                break;
                
            case 'vencimiento_gestionado':
                $eliminadas = $this->eliminarNotificacionesVencimiento($event->identificador);
                break;
                
            case 'seguimiento_realizado':
                $eliminadas = $this->eliminarNotificacionesSeguimiento($event->identificador);
                break;
                
            case 'produccion_mejorada':
                $eliminadas = $this->eliminarNotificacionesProduccion($event->identificador);
                break;
        }

        if ($eliminadas > 0) {
            Log::info("✅ Problema resuelto: se eliminaron {$eliminadas} notificaciones de {$event->tipoProblema}");
        }
    }

    /**
     * Eliminar notificaciones de stock bajo resuelto
     */
    private function eliminarNotificacionesStock($itemId): int
    {
        return Notificacion::where('tipo', 'warning')
            ->whereJsonContains('datos->tipo_alerta', 'stock_bajo')
            ->whereJsonContains('datos->item_id', $itemId)
            ->where('leida', false)
            ->delete();
    }

    /**
     * Eliminar notificaciones de vencimiento gestionado
     */
    private function eliminarNotificacionesVencimiento($loteId): int
    {
        return Notificacion::whereIn('tipo', ['warning', 'error'])
            ->whereIn('datos->tipo_alerta', ['vencido', 'por_vencer'])
            ->whereJsonContains('datos->lote_id', $loteId)
            ->where('leida', false)
            ->delete();
    }

    /**
     * Eliminar notificaciones de seguimiento realizado
     */
    private function eliminarNotificacionesSeguimiento($loteId): int
    {
        return Notificacion::where('tipo', 'info')
            ->whereJsonContains('datos->tipo_alerta', 'sin_seguimiento')
            ->whereJsonContains('datos->lote_id', $loteId)
            ->where('leida', false)
            ->delete();
    }

    /**
     * Eliminar notificaciones de producción mejorada
     */
    private function eliminarNotificacionesProduccion($loteId): int
    {
        return Notificacion::where('tipo', 'error')
            ->whereJsonContains('datos->tipo_alerta', 'bajo_rendimiento')
            ->whereJsonContains('datos->lote_id', $loteId)
            ->where('leida', false)
            ->delete();
    }
}
