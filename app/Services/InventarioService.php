<?php

namespace App\Services;

use App\Models\{InventarioItem, InventarioExistencia, InventarioMovimiento, Bodega, InventarioLote};
use App\Events\StockBajoDetectado;
use Illuminate\Support\Facades\{DB, Auth};
use Carbon\Carbon;

class InventarioService
{
    // conversión a unidad_base del item
    protected function toBase(string $unidadBase, float $cantidad, ?string $unidadOrigen): float
    {
        $u = strtolower($unidadOrigen ?? $unidadBase);
        if ($unidadBase === 'kg' && $u === 'lb') return $cantidad * 0.45359237;
        if ($unidadBase === 'lb' && $u === 'kg') return $cantidad / 0.45359237;
        return $cantidad; // misma unidad o unidades no convertibles (unidad/litro)
    }

    protected function filaExistencia(int $itemId, int $bodegaId): InventarioExistencia
    {
        return InventarioExistencia::lockForUpdate()->firstOrCreate(
            ['item_id' => $itemId, 'bodega_id' => $bodegaId],
            ['stock_actual' => 0]
        );
    }

    /**
     * ENTRADA: aumenta existencias y, si se especifica, registra/actualiza el LOTE
     * $lote y $fechaVenc son opcionales; se agregan al final para NO romper llamadas existentes.
     */
    public function entrada(
        InventarioItem $item,
        Bodega $bodega,
        float $cantidad,
        string $unidadOrigen = 'kg',
        ?string $desc = null,
        $ref = null,
        ?string $lote = null,
        ?string $fechaVenc = null
    ): void {
        DB::transaction(function () use ($item, $bodega, $cantidad, $unidadOrigen, $desc, $ref, $lote, $fechaVenc) {
            $base = $this->toBase($item->unidad_base, $cantidad, $unidadOrigen);

            // 1) existencias
            $ex = $this->filaExistencia($item->id, $bodega->id);
            $ex->increment('stock_actual', $base);

            // 2) movimiento
            InventarioMovimiento::create([
                'item_id'          => $item->id,
                'bodega_id'        => $bodega->id,
                'tipo'             => 'entrada',
                'cantidad_base'    => $base,
                'unidad_origen'    => $unidadOrigen,
                'cantidad_origen'  => $cantidad,
                'fecha'            => Carbon::today(),
                'descripcion'      => $desc,
                'user_id'          => Auth::id(),
                'referencia_type'  => $ref?->getMorphClass(),
                'referencia_id'    => $ref?->id,
            ]);

            // 3) lote (opcional)
            if ($lote || $fechaVenc) {
                // Busca por item/bodega/lote/fecha_venc; si no existe lo crea
                $registro = InventarioLote::lockForUpdate()->firstOrCreate(
                    [
                        'item_id'          => $item->id,
                        'bodega_id'        => $bodega->id,
                        'lote'             => $lote,
                        'fecha_vencimiento' => $fechaVenc,
                    ],
                    [
                        'fecha_ingreso' => Carbon::today(),
                        'stock_lote'    => 0,
                    ]
                );
                $registro->increment('stock_lote', $base);
            }
        });
    }

    /**
     * SALIDA: descuenta existencias y, si hay lotes, descuenta por FEFO (primero que vence)
     */
    public function salida(
        InventarioItem $item,
        Bodega $bodega,
        float $cantidad,
        string $unidadOrigen = 'kg',
        ?string $desc = null,
        $ref = null
    ): void {
        DB::transaction(function () use ($item, $bodega, $cantidad, $unidadOrigen, $desc, $ref) {
            $base = $this->toBase($item->unidad_base, $cantidad, $unidadOrigen);

            // 1) existencias
            $ex = $this->filaExistencia($item->id, $bodega->id);
            if ($ex->stock_actual < $base) {
                abort(422, 'Stock insuficiente en bodega.');
            }
            $ex->decrement('stock_actual', $base);

            // Verificar si el stock quedó por debajo del mínimo
            if ($item->stock_minimo > 0 && $ex->stock_actual < $item->stock_minimo) {
                // Disparar evento para notificación automática
                StockBajoDetectado::dispatch($item, $ex->stock_actual);
            }

            // 2) movimiento
            InventarioMovimiento::create([
                'item_id'          => $item->id,
                'bodega_id'        => $bodega->id,
                'tipo'             => 'salida',
                'cantidad_base'    => $base,
                'unidad_origen'    => $unidadOrigen,
                'cantidad_origen'  => $cantidad,
                'fecha'            => Carbon::today(),
                'descripcion'      => $desc,
                'user_id'          => Auth::id(),
                'referencia_type'  => $ref?->getMorphClass(),
                'referencia_id'    => $ref?->id,
            ]);

            // 3) consumir lotes por FEFO (vencen primero)
            $restante = $base;

            $lotes = InventarioLote::where('item_id', $item->id)
                ->where('bodega_id', $bodega->id)
                ->where('stock_lote', '>', 0)
                ->lockForUpdate()
                // primero con fecha, luego más próximo a vencer, luego por id
                ->orderByRaw('fecha_vencimiento is null')  // false(0) antes que true(1) -> con fecha primero
                ->orderBy('fecha_vencimiento', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($lotes as $lot) {
                if ($restante <= 0) break;
                $toma = min($lot->stock_lote, $restante);
                $lot->decrement('stock_lote', $toma);
                $restante -= $toma;
            }
            // si quedó restante > 0 significa que no había lotes suficientes; las existencias ya se ajustaron arriba
        });
    }

    public function ajuste(InventarioItem $item, Bodega $bodega, float $nuevoStockBase, ?string $motivo = null): void
    {
        DB::transaction(function () use ($item, $bodega, $nuevoStockBase, $motivo) {
            $ex = $this->filaExistencia($item->id, $bodega->id);
            $delta = $nuevoStockBase - $ex->stock_actual;
            $ex->update(['stock_actual' => $nuevoStockBase]);

            InventarioMovimiento::create([
                'item_id'          => $item->id,
                'bodega_id'        => $bodega->id,
                'tipo'             => 'ajuste',
                'cantidad_base'    => $delta,
                'unidad_origen'    => $item->unidad_base,
                'cantidad_origen'  => $delta,
                'fecha'            => Carbon::today(),
                'descripcion'      => $motivo ?? 'Ajuste de inventario',
                'user_id'          => Auth::id(),
            ]);

            // Nota: no redistribuimos lotes en ajuste; si necesitas, lo hacemos luego.
        });
    }
}
