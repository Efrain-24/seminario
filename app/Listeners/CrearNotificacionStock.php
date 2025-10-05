<?php

namespace App\Listeners;

use App\Events\StockBajoDetectado;
use App\Models\Notificacion;

class CrearNotificacionStock
{
    /**
     * Handle the event.
     */
    public function handle(StockBajoDetectado $event): void
    {
        $item = $event->item;
        $stockActual = $event->stockActual;
        
        // Verificar si ya existe una notificación reciente
        $existeReciente = Notificacion::where('tipo', 'warning')
            ->whereJsonContains('datos->item_id', $item->id)
            ->whereJsonContains('datos->tipo_alerta', 'stock_bajo')
            ->where('created_at', '>', now()->subHours(4))
            ->exists();

        if (!$existeReciente) {
            $porcentaje = round(($stockActual / $item->stock_minimo) * 100, 1);
            
            Notificacion::create([
                'tipo' => 'warning',
                'titulo' => 'Stock Bajo en Inventario',
                'mensaje' => "El ítem '{$item->nombre}' tiene stock bajo ({$stockActual} disponible, mínimo {$item->stock_minimo})",
                'datos' => [
                    'item_id' => $item->id,
                    'item_nombre' => $item->nombre,
                    'stock_actual' => $stockActual,
                    'stock_minimo' => $item->stock_minimo,
                    'porcentaje' => $porcentaje,
                    'tipo_alerta' => 'stock_bajo',
                    'automatica' => true,
                    'tiempo_real' => true
                ],
                'icono' => 'package-x',
                'url' => route('produccion.inventario.alertas.index'),
            ]);
        }
    }
}
