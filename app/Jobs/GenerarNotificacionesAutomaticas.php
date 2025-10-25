<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GenerarNotificacionesAutomaticas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🔄 Iniciando generación automática de notificaciones');
            
            // 1. Verificar stock bajo
            $this->verificarStockBajo();
            
            // 2. Verificar elementos próximos a vencer
            $this->verificarElementosProximosVencer();
            
            // 3. Verificar limpiezas pendientes
            $this->verificarLimpiezasPendientes();
            
            // 4. Ejecutar el comando de generación de notificaciones reales
            Artisan::call('notificaciones:generar-reales');
            
            // 5. Limpiar notificaciones antiguas
            $this->limpiarNotificacionesAntiguas();
            
            Log::info('✅ Generación automática de notificaciones completada');
            
        } catch (\Exception $e) {
            Log::error('❌ Error en generación automática de notificaciones: ' . $e->getMessage());
        }
    }

    private function verificarStockBajo()
    {
        $itemsBajos = \App\Models\InventarioItem::whereRaw('stock_minimo > 0 AND (
            SELECT COALESCE(SUM(stock_actual), 0) 
            FROM inventario_existencias 
            WHERE inventario_item_id = inventario_items.id
        ) <= stock_minimo')->get();

        foreach ($itemsBajos as $item) {
            $stockActual = $item->stockTotal();
            event(new \App\Events\StockBajoDetectado($item, $stockActual));
        }

        Log::info("📦 Verificados {$itemsBajos->count()} items con stock bajo");
    }

    private function verificarElementosProximosVencer()
    {
        $lotesProximosVencer = \App\Models\InventarioLote::where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>', now())
            ->where('cantidad_disponible', '>', 0)
            ->with('inventarioItem')
            ->get();

        foreach ($lotesProximosVencer as $lote) {
            $diasRestantes = now()->diffInDays($lote->fecha_vencimiento);
            
            // Verificar si ya existe notificación reciente
            $existeNotificacion = \App\Models\Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'proximo_vencer')
                ->where('created_at', '>', now()->subDays(7))
                ->exists();

            if (!$existeNotificacion && in_array($diasRestantes, [30, 15, 7, 3, 1])) {
                \App\Models\Notificacion::create([
                    'tipo' => $diasRestantes <= 3 ? 'error' : 'warning',
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

        Log::info("⏰ Verificados {$lotesProximosVencer->count()} lotes próximos a vencer");
    }

    private function verificarLimpiezasPendientes()
    {
        $limpiezasPendientes = \App\Models\Limpieza::where('estado', 'pendiente')
            ->where('fecha_programada', '<=', now()->addDays(2))
            ->get();

        foreach ($limpiezasPendientes as $limpieza) {
            $diasRestantes = now()->diffInDays($limpieza->fecha_programada);
            
            // Verificar si ya existe notificación reciente
            $existeNotificacion = \App\Models\Notificacion::where('tipo', 'info')
                ->whereJsonContains('datos->limpieza_id', $limpieza->id)
                ->whereJsonContains('datos->tipo_alerta', 'limpieza_pendiente')
                ->where('created_at', '>', now()->subDays(1))
                ->exists();

            if (!$existeNotificacion) {
                $tipo = $diasRestantes <= 0 ? 'warning' : 'info';
                $mensaje = $diasRestantes <= 0 ? 
                    "La limpieza en {$limpieza->area} está atrasada" : 
                    "La limpieza en {$limpieza->area} está programada para mañana";

                \App\Models\Notificacion::create([
                    'tipo' => $tipo,
                    'titulo' => 'Limpieza Pendiente',
                    'mensaje' => $mensaje,
                    'datos' => [
                        'limpieza_id' => $limpieza->id,
                        'area' => $limpieza->area,
                        'fecha_programada' => $limpieza->fecha_programada,
                        'dias_restantes' => $diasRestantes,
                        'protocolo' => $limpieza->protocoloSanidad ? $limpieza->protocoloSanidad->nombre : 'Sin protocolo',
                        'tipo_alerta' => 'limpieza_pendiente',
                        'automatica' => true
                    ],
                    'icono' => 'calendar',
                    'url' => route('limpieza.edit', $limpieza->id),
                ]);
            }
        }

        Log::info("🧹 Verificadas {$limpiezasPendientes->count()} limpiezas pendientes");
    }

    private function limpiarNotificacionesAntiguas()
    {
        // Eliminar notificaciones leídas que tienen más de 30 días
        $eliminadas = \App\Models\Notificacion::where('leida', true)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        // Eliminar notificaciones automáticas no leídas que tienen más de 7 días
        $eliminadasAutomaticas = \App\Models\Notificacion::whereJsonContains('datos->automatica', true)
            ->where('leida', false)
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        Log::info("🗑️ Eliminadas {$eliminadas} notificaciones leídas y {$eliminadasAutomaticas} automáticas antiguas");
    }
}
