<?php

namespace App\Http\Controllers;

use App\Models\{InventarioItem, InventarioLote, Bodega, Notificacion};
use Illuminate\Http\Request;

class InventarioAlertaController extends Controller
{
    public function index(Request $request)
    {
        $dias = (int)($request->input('dias', 30));           // horizonte de vencimiento
        $tipo = $request->input('tipo');                      // alimento|insumo|null
        $bodegaId = $request->input('bodega_id');

        // Items con stock bajo
        $items = InventarioItem::with(['existencias' => function ($q) use ($bodegaId) {
            if ($bodegaId) $q->where('bodega_id', $bodegaId);
        }])
            ->when($tipo, fn($q) => $q->where('tipo', $tipo))
            ->orderBy('nombre')
            ->get();

        $bajos = $items->filter(function ($i) {
            $total = $i->stockTotal();   // método que ya tienes
            return $i->stock_minimo > 0 && $total < $i->stock_minimo;
        });

        // Generar notificaciones para stock bajo
        $this->generarNotificacionesStockBajo($bajos);

        // Lotes por vencer / vencidos
        $lotesQuery = InventarioLote::with(['item', 'bodega'])
            ->conStock()
            ->when($tipo, fn($q) => $q->whereHas('item', fn($qq) => $qq->where('tipo', $tipo)))
            ->when($bodegaId, fn($q) => $q->where('bodega_id', $bodegaId));

        $vencidos   = (clone $lotesQuery)->vencidos()->orderBy('fecha_vencimiento')->get();
        $porVencer  = (clone $lotesQuery)->porVencer($dias)->orderBy('fecha_vencimiento')->get();

        // Generar notificaciones para productos por vencer
        $this->generarNotificacionesVencimientos($vencidos, $porVencer);

        $bodegas = Bodega::orderBy('nombre')->get();

        return view('inventario.alertas.index', compact('bajos', 'vencidos', 'porVencer', 'bodegas', 'dias', 'tipo', 'bodegaId'));
    }

    /**
     * Generar notificaciones para items con stock bajo
     */
    private function generarNotificacionesStockBajo($items): void
    {
        foreach ($items as $item) {
            // Verificar si ya existe una notificación reciente para este item
            $existeReciente = Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->item_id', $item->id)
                ->whereJsonContains('datos->tipo_alerta', 'stock_bajo')
                ->where('created_at', '>', now()->subHours(24)) // Solo crear si no hay una en las últimas 24 horas
                ->exists();

            if (!$existeReciente) {
                $stockActual = $item->stockTotal();
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
                        'tipo_alerta' => 'stock_bajo'
                    ],
                    'icono' => 'package-x',
                    'url' => route('produccion.inventario.alertas.index'),
                    'fecha_vencimiento' => now()->addDays(3) // Las alertas de stock expiran en 3 días
                ]);
            }
        }
    }

    /**
     * Generar notificaciones para productos vencidos o por vencer
     */
    private function generarNotificacionesVencimientos($vencidos, $porVencer): void
    {
        // Productos vencidos
        foreach ($vencidos as $lote) {
            $existeReciente = Notificacion::where('tipo', 'error')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'vencido')
                ->where('created_at', '>', now()->subHours(24))
                ->exists();

            if (!$existeReciente) {
                $diasVencido = now()->diffInDays($lote->fecha_vencimiento);
                
                Notificacion::create([
                    'tipo' => 'error',
                    'titulo' => 'Producto Vencido',
                    'mensaje' => "El lote '{$lote->lote}' de '{$lote->item->nombre}' venció hace {$diasVencido} días",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'item_nombre' => $lote->item->nombre,
                        'lote' => $lote->lote,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->toDateString(),
                        'dias_vencido' => $diasVencido,
                        'tipo_alerta' => 'vencido'
                    ],
                    'icono' => 'calendar-x',
                    'url' => route('produccion.inventario.alertas.index'),
                    'fecha_vencimiento' => now()->addDays(1) // Las alertas de vencimiento expiran en 1 día
                ]);
            }
        }

        // Productos por vencer
        foreach ($porVencer->take(5) as $lote) { // Solo los primeros 5 para no saturar
            $existeReciente = Notificacion::where('tipo', 'warning')
                ->whereJsonContains('datos->lote_id', $lote->id)
                ->whereJsonContains('datos->tipo_alerta', 'por_vencer')
                ->where('created_at', '>', now()->subHours(12))
                ->exists();

            if (!$existeReciente) {
                $diasRestantes = now()->diffInDays($lote->fecha_vencimiento);
                
                Notificacion::create([
                    'tipo' => 'warning',
                    'titulo' => 'Producto por Vencer',
                    'mensaje' => "El lote '{$lote->lote}' de '{$lote->item->nombre}' vence en {$diasRestantes} días",
                    'datos' => [
                        'lote_id' => $lote->id,
                        'item_nombre' => $lote->item->nombre,
                        'lote' => $lote->lote,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->toDateString(),
                        'dias_restantes' => $diasRestantes,
                        'tipo_alerta' => 'por_vencer'
                    ],
                    'icono' => 'calendar-clock',
                    'url' => route('produccion.inventario.alertas.index'),
                    'fecha_vencimiento' => now()->addDays(2) // Las alertas de próximo vencimiento expiran en 2 días
                ]);
            }
        }
    }
}
