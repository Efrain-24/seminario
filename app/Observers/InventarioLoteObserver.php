<?php

namespace App\Observers;

use App\Events\ProblemaResuelto;
use App\Models\InventarioLote;

class InventarioLoteObserver
{
    /**
     * Handle the InventarioLote "updated" event.
     */
    public function updated(InventarioLote $lote): void
    {
        // Si el stock del lote se redujo a 0, significa que se gestionó el producto vencido/por vencer
        if ($lote->wasChanged('stock_lote') && $lote->stock_lote == 0) {
            event(new ProblemaResuelto('vencimiento_gestionado', $lote->id, [
                'motivo' => 'stock_agotado',
                'fecha_vencimiento' => $lote->fecha_vencimiento
            ]));
        }
    }

    /**
     * Handle the InventarioLote "deleted" event.
     */
    public function deleted(InventarioLote $lote): void
    {
        // Si se eliminó el lote, también eliminar notificaciones relacionadas
        event(new ProblemaResuelto('vencimiento_gestionado', $lote->id, [
            'motivo' => 'lote_eliminado',
            'fecha_vencimiento' => $lote->fecha_vencimiento
        ]));
    }
}
