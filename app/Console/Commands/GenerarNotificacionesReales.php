<?php

namespace App\Console\Commands;

use App\Models\{Notificacion, InventarioItem, InventarioLote, Lote, Seguimiento};
use App\Http\Controllers\InventarioAlertaController;
use App\Services\AlertaAnomaliasService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarNotificacionesReales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:generar-reales {--limpiar : Limpiar notificaciones existentes primero}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar notificaciones reales basadas en las alertas del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('limpiar')) {
            $this->info('🧹 Limpiando notificaciones existentes...');
            Notificacion::where('datos->demo', '!=', true)->delete();
        }

        // Primero limpiar notificaciones que ya fueron resueltas
        $this->call('notificaciones:limpiar-resueltas');
        $this->newLine();

        $this->info('🔍 Analizando sistema para generar alertas reales...');

        $count = 0;

        // 1. Alertas de inventario (stock bajo)
        $count += $this->generarAlertasInventario();

        // 2. Alertas de productos por vencer
        $count += $this->generarAlertasVencimiento();

        // 3. Alertas de anomalías de producción
        $count += $this->generarAlertasProduccion();

        // 4. Alertas de lotes sin seguimiento reciente
        $count += $this->generarAlertasSeguimiento();

        if ($count > 0) {
            $this->info("✅ Se generaron {$count} notificaciones reales");
            $this->info('📱 Revisa el icono de campana en el navbar para verlas');
        } else {
            $this->info('✨ No se encontraron alertas que generar - tu sistema está funcionando bien!');
        }
        
        return Command::SUCCESS;
    }

    /**
     * Generar alertas de stock bajo en inventario
     */
    private function generarAlertasInventario(): int
    {
        $count = 0;
        
        $items = InventarioItem::with(['existencias'])->get();
        
        foreach ($items as $item) {
            $stockTotal = $item->stockTotal();
            
            if ($item->stock_minimo > 0 && $stockTotal < $item->stock_minimo) {
                // Verificar si ya existe una notificación reciente
                $existeReciente = Notificacion::where('tipo', 'warning')
                    ->whereJsonContains('datos->item_id', $item->id)
                    ->whereJsonContains('datos->tipo_alerta', 'stock_bajo')
                    ->where('created_at', '>', now()->subHours(24))
                    ->exists();

                if (!$existeReciente) {
                    $porcentaje = round(($stockTotal / $item->stock_minimo) * 100, 1);
                    
                    Notificacion::create([
                        'tipo' => 'warning',
                        'titulo' => 'Stock Bajo en Inventario',
                        'mensaje' => "El ítem '{$item->nombre}' tiene stock bajo ({$stockTotal} disponible, mínimo {$item->stock_minimo})",
                        'datos' => [
                            'item_id' => $item->id,
                            'item_nombre' => $item->nombre,
                            'stock_actual' => $stockTotal,
                            'stock_minimo' => $item->stock_minimo,
                            'porcentaje' => $porcentaje,
                            'tipo_alerta' => 'stock_bajo'
                        ],
                        'icono' => 'package-x',
                        'url' => route('produccion.inventario.items.show', ['item' => $item->id]) . '?accion=entrada',
                    ]);
                    
                    $count++;
                    $this->info("📦 Stock bajo: {$item->nombre} ({$stockTotal}/{$item->stock_minimo})");
                }
            }
        }
        
        return $count;
    }

    /**
     * Generar alertas de productos por vencer
     */
    private function generarAlertasVencimiento(): int
    {
        $count = 0;
        $diasLimite = 30;
        
        $lotesVencidos = InventarioLote::with(['item', 'bodega'])
            ->conStock()
            ->vencidos()
            ->get();
            
        foreach ($lotesVencidos as $lote) {
            $existeReciente = Notificacion::where('tipo', 'error')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'vencido')
                ->where('created_at', '>', now()->subHours(24))
                ->exists();

            if (!$existeReciente) {
                Notificacion::create([
                    'tipo' => 'error',
                    'titulo' => 'Lote Vencido',
                    'mensaje' => "El lote {$lote->lote} de {$lote->item->nombre} ha vencido.",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'item_nombre' => $lote->item->nombre,
                        'lote' => $lote->lote,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->format('Y-m-d'),
                        'stock' => $lote->stock_lote,
                        'tipo_alerta' => 'vencido',
                        'automatica' => true
                    ],
                    'icono' => 'calendar-x',
                    'url' => route('produccion.inventario.alertas.index'),
                ]);
                $count++;
                $this->info("🚨 Vencido: {$lote->item->nombre} - Lote {$lote->lote}");
            }
        }

        $lotesPorVencer = InventarioLote::with(['item', 'bodega'])
            ->conStock()
            ->porVencer($diasLimite)
            ->get();
            
        foreach ($lotesPorVencer as $lote) {
            $diasRestantes = now()->diffInDays($lote->fecha_vencimiento);
            
            $existeReciente = Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'por_vencer')
                ->where('created_at', '>', now()->subHours(12))
                ->exists();

            if (!$existeReciente && $diasRestantes <= 7) { // Solo alertar si vence en 7 días o menos
                Notificacion::create([
                    'tipo' => 'warning',
                    'titulo' => 'Producto por Vencer',
                    'mensaje' => "El lote {$lote->lote} de {$lote->item->nombre} vence en {$diasRestantes} días",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'item_nombre' => $lote->item->nombre,
                        'lote' => $lote->lote,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->format('Y-m-d'),
                        'dias_restantes' => $diasRestantes,
                        'stock' => $lote->stock_lote,
                        'tipo_alerta' => 'por_vencer'
                    ],
                    'icono' => 'calendar-clock',
                    'url' => route('produccion.inventario.alertas.index'),
                ]);
                
                $count++;
                $this->info("⚠️ Por vencer: {$lote->item->nombre} - Lote {$lote->lote} ({$diasRestantes} días)");
            }
        }
        
        return $count;
    }

    /**
     * Generar alertas de anomalías de producción
     */
    private function generarAlertasProduccion(): int
    {
        $count = 0;
        
        $service = new AlertaAnomaliasService();
        $lotes = Lote::where('estado', 'activo')->get();
        
        foreach ($lotes as $lote) {
            $alerta = $service->detectarBajoPeso($lote);
            
            if ($alerta) {
                // Verificar si ya existe una notificación reciente
                $existeReciente = Notificacion::where('tipo', 'error')
                    ->whereJsonContains('datos->lote_id', $lote->id)
                    ->whereJsonContains('datos->tipo_alerta', 'bajo_rendimiento')
                    ->where('created_at', '>', now()->subHours(6))
                    ->exists();

                if (!$existeReciente) {
                    $deficitPct = $alerta['deficit_pct'];
                    $severidad = $deficitPct >= 40 ? 'crítica' : ($deficitPct >= 25 ? 'alta' : 'media');
                    
                    Notificacion::create([
                        'tipo' => 'error',
                        'titulo' => 'Anomalía de Producción Detectada',
                        'mensaje' => "El lote {$lote->codigo_lote} presenta bajo rendimiento ({$severidad}). Déficit: {$deficitPct}%",
                        'datos' => [
                            'lote_id' => $lote->id,
                            'codigo_lote' => $lote->codigo_lote,
                            'deficit_pct' => $deficitPct,
                            'deficit_kg' => $alerta['deficit_kg'],
                            'severidad' => $severidad,
                            'tipo_alerta' => 'bajo_rendimiento'
                        ],
                        'icono' => 'alert-triangle',
                        'url' => route('produccion.alertas.index', ['lote_id' => $lote->id]),
                        'fecha_vencimiento' => now()->addDays(7)
                    ]);
                    
                    $count++;
                    $this->info("🔥 Anomalía: {$lote->codigo_lote} - Déficit {$deficitPct}% ({$severidad})");
                }
            }
        }
        
        return $count;
    }

    /**
     * Generar alertas de lotes sin seguimiento reciente
     */
    private function generarAlertasSeguimiento(): int
    {
        $count = 0;
        $diasLimite = 7; // Lotes sin seguimiento en los últimos 7 días
        
        $lotes = Lote::where('estado', 'activo')->get();
        // Excluir lotes que no están activos
        $lotes = $lotes->filter(function($lote) {
            return $lote->estado === 'activo';
        });
        foreach ($lotes as $lote) {
            $ultimoSeguimiento = $lote->seguimientos()->latest('fecha_seguimiento')->first();
            $diasSinSeguimiento = $ultimoSeguimiento 
                ? now()->diffInDays($ultimoSeguimiento->fecha_seguimiento) 
                : 999;

            // Si el lote ya tiene seguimiento reciente, marcar notificaciones como resueltas
            if ($diasSinSeguimiento < $diasLimite) {
                Notificacion::where('tipo', 'warning')
                    ->whereJsonContains('datos->lote_id', $lote->id)
                    ->whereJsonContains('datos->tipo_alerta', 'sin_seguimiento')
                    ->where('resuelta', false)
                    ->update(['resuelta' => true]);
                continue;
            }

            $existeReciente = Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'sin_seguimiento')
                ->where('created_at', '>', now()->subDays(2))
                ->where('resuelta', false)
                ->exists();

            if (!$existeReciente && $diasSinSeguimiento >= $diasLimite) {
                Notificacion::create([
                    'tipo' => 'success', // verde
                    'titulo' => 'Seguimiento Pendiente',
                    'mensaje' => "El lote {$lote->codigo_lote} no tiene seguimiento hace {$diasSinSeguimiento} días",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'codigo_lote' => $lote->codigo_lote,
                        'dias_sin_seguimiento' => $diasSinSeguimiento,
                        'tipo_alerta' => 'sin_seguimiento'
                    ],
                    'icono' => 'calendar-check', // icono de calendario con check
                    'url' => route('produccion.lotes.seguimientos.ver', ['lote' => $lote->id]),
                    'fecha_vencimiento' => now()->addDays(3)
                ]);
                $count++;
                $this->info("📅 Sin seguimiento: {$lote->codigo_lote} ({$diasSinSeguimiento} días)");
            }
        }
        
        return $count;
    }
}
