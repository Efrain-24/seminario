<?php

namespace App\Observers;

use App\Events\ProblemaResuelto;
use App\Models\Seguimiento;

class SeguimientoObserver
{
    /**
     * Handle the Seguimiento "created" event.
     */
    public function created(Seguimiento $seguimiento): void
    {
        // Cuando se crea un seguimiento, eliminar notificaciones de seguimiento pendiente
        event(new ProblemaResuelto('seguimiento_realizado', $seguimiento->lote_id, [
            'fecha_seguimiento' => $seguimiento->fecha_seguimiento,
            'tipo' => $seguimiento->tipo ?? 'general'
        ]));
    }

    /**
     * Handle the Seguimiento "updated" event.
     */
    public function updated(Seguimiento $seguimiento): void
    {
        // Similar para actualizaciones
        event(new ProblemaResuelto('seguimiento_realizado', $seguimiento->lote_id, [
            'fecha_seguimiento' => $seguimiento->fecha_seguimiento,
            'tipo' => $seguimiento->tipo ?? 'general'
        ]));
    }
}
