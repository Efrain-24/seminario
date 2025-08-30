<?php

namespace App\Console\Commands;

use App\Models\{Notificacion, InventarioItem, Lote};
use App\Services\AlertaAnomaliasService;
use Illuminate\Console\Command;

class LimpiarNotificacionesResueltas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:limpiar-resueltas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar notificaciones cuyos problemas ya fueron solucionados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eliminadas = 0;

        $this->info('🔍 Revisando notificaciones que podrían estar resueltas...');

        // 1. Revisar notificaciones de stock bajo
        $eliminadas += $this->limpiarNotificacionesStockBajo();

        // 2. Revisar notificaciones de productos vencidos
        $eliminadas += $this->limpiarNotificacionesVencimiento();

        // 3. Revisar notificaciones de anomalías de producción
        $eliminadas += $this->limpiarNotificacionesProduccion();

        // 4. Revisar notificaciones de seguimientos pendientes
        $eliminadas += $this->limpiarNotificacionesSeguimiento();

        if ($eliminadas > 0) {
            $this->info("✅ Se eliminaron {$eliminadas} notificaciones resueltas");
        } else {
            $this->info("✨ No se encontraron notificaciones resueltas para eliminar");
        }

        return Command::SUCCESS;
    }

    /**
     * Limpiar notificaciones de stock bajo que ya fueron solucionadas
     */
    private function limpiarNotificacionesStockBajo(): int
    {
        $count = 0;
        
        $notificaciones = Notificacion::where('tipo', 'warning')
            ->whereJsonContains('datos->tipo_alerta', 'stock_bajo')
            ->where('leida', false)
            ->get();

        foreach ($notificaciones as $notificacion) {
            $itemId = data_get($notificacion->datos, 'item_id');
            
            if ($itemId) {
                $item = InventarioItem::find($itemId);
                
                if ($item) {
                    $stockActual = $item->stockTotal();
                    
                    // Si el stock ya no está bajo, eliminar la notificación
                    if ($item->stock_minimo <= 0 || $stockActual >= $item->stock_minimo) {
                        $notificacion->delete();
                        $count++;
                        $this->line("📦 Stock resuelto: {$item->nombre} ({$stockActual} disponible)");
                    }
                } else {
                    // Si el item ya no existe, eliminar la notificación
                    $notificacion->delete();
                    $count++;
                    $this->line("📦 Item eliminado, notificación limpiada");
                }
            }
        }

        return $count;
    }

    /**
     * Limpiar notificaciones de productos vencidos que ya fueron gestionados
     */
    private function limpiarNotificacionesVencimiento(): int
    {
        $count = 0;
        
        $notificaciones = Notificacion::whereIn('tipo', ['warning', 'error'])
            ->whereIn('datos->tipo_alerta', ['vencido', 'por_vencer'])
            ->where('leida', false)
            ->get();

        foreach ($notificaciones as $notificacion) {
            $loteId = data_get($notificacion->datos, 'lote_id');
            
            if ($loteId) {
                $lote = \App\Models\InventarioLote::find($loteId);
                
                if (!$lote || $lote->stock_lote <= 0) {
                    // Si el lote ya no existe o no tiene stock, la notificación no es relevante
                    $notificacion->delete();
                    $count++;
                    $this->line("📅 Producto vencido resuelto: lote gestionado");
                }
            }
        }

        return $count;
    }

    /**
     * Limpiar notificaciones de anomalías de producción que ya no aplican
     */
    private function limpiarNotificacionesProduccion(): int
    {
        $count = 0;
        
        $notificaciones = Notificacion::where('tipo', 'error')
            ->whereJsonContains('datos->tipo_alerta', 'bajo_rendimiento')
            ->where('leida', false)
            ->get();

        $service = new AlertaAnomaliasService();

        foreach ($notificaciones as $notificacion) {
            $loteId = data_get($notificacion->datos, 'lote_id');
            
            if ($loteId) {
                $lote = Lote::find($loteId);
                
                if ($lote) {
                    // Revisar si la anomalía persiste
                    $alerta = $service->detectarBajoPeso($lote);
                    
                    if (!$alerta) {
                        // La anomalía ya no se detecta, eliminar notificación
                        $notificacion->delete();
                        $count++;
                        $this->line("🐟 Anomalía de producción resuelta: {$lote->codigo_lote}");
                    }
                } else {
                    // Si el lote ya no existe, eliminar la notificación
                    $notificacion->delete();
                    $count++;
                    $this->line("🐟 Lote eliminado, notificación limpiada");
                }
            }
        }

        return $count;
    }

    /**
     * Limpiar notificaciones de seguimiento pendiente que ya fueron atendidas
     */
    private function limpiarNotificacionesSeguimiento(): int
    {
        $count = 0;
        
        $notificaciones = Notificacion::where('tipo', 'info')
            ->whereJsonContains('datos->tipo_alerta', 'sin_seguimiento')
            ->where('leida', false)
            ->get();

        foreach ($notificaciones as $notificacion) {
            $loteId = data_get($notificacion->datos, 'lote_id');
            
            if ($loteId) {
                $lote = Lote::find($loteId);
                
                if ($lote) {
                    // Verificar si ya se hizo seguimiento reciente
                    $ultimoSeguimiento = $lote->seguimientos()->latest('fecha_seguimiento')->first();
                    
                    if ($ultimoSeguimiento) {
                        $diasSinSeguimiento = now()->diffInDays($ultimoSeguimiento->fecha_seguimiento);
                        
                        // Si ya se hizo seguimiento en los últimos 5 días, eliminar notificación
                        if ($diasSinSeguimiento < 5) {
                            $notificacion->delete();
                            $count++;
                            $this->line("📅 Seguimiento realizado: {$lote->codigo_lote}");
                        }
                    }
                } else {
                    // Si el lote ya no existe, eliminar la notificación
                    $notificacion->delete();
                    $count++;
                    $this->line("📅 Lote eliminado, notificación limpiada");
                }
            }
        }

        return $count;
    }
}
