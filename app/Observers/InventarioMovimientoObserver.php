<?php

namespace App\Observers;

use App\Events\ProblemaResuelto;
use App\Models\InventarioMovimiento;

class InventarioMovimientoObserver
{
    /**
     * Handle the InventarioMovimiento "created" event.
     */
    public function created(InventarioMovimiento $movimiento): void
    {
        // Si es un movimiento de entrada (ingreso de stock)
        if ($movimiento->tipo_movimiento === 'entrada' || $movimiento->tipo_movimiento === 'ajuste_entrada') {
            // Verificar si esto resuelve algún problema de stock bajo
            $item = $movimiento->existencia->inventarioItem;
            $stockActual = $item->stockTotal();
            
            // Si el stock ahora supera el mínimo, disparar evento
            if ($item->stock_minimo > 0 && $stockActual >= $item->stock_minimo) {
                event(new ProblemaResuelto('stock_resuelto', $item->id, [
                    'stock_anterior' => $stockActual - $movimiento->cantidad,
                    'stock_actual' => $stockActual,
                    'stock_minimo' => $item->stock_minimo
                ]));
            }
        }
    }

    /**
     * Handle the InventarioMovimiento "updated" event.
     */
    public function updated(InventarioMovimiento $movimiento): void
    {
        // Similar lógica para actualizaciones
        if ($movimiento->tipo_movimiento === 'entrada' || $movimiento->tipo_movimiento === 'ajuste_entrada') {
            $item = $movimiento->existencia->inventarioItem;
            $stockActual = $item->stockTotal();
            
            if ($item->stock_minimo > 0 && $stockActual >= $item->stock_minimo) {
                event(new ProblemaResuelto('stock_resuelto', $item->id, [
                    'stock_actual' => $stockActual,
                    'stock_minimo' => $item->stock_minimo
                ]));
            }
        }
    }
}
