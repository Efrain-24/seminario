<?php

namespace App\Services;

use App\Models\{InventarioItem, InventarioExistencia, InventarioMovimiento, Bodega};
use Illuminate\Support\Facades\DB;
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

    public function entrada(InventarioItem $item, Bodega $bodega, float $cantidad, string $unidadOrigen = 'kg', ?string $desc = null, $ref = null): void
    {
        DB::transaction(function () use ($item, $bodega, $cantidad, $unidadOrigen, $desc, $ref) {
            $base = $this->toBase($item->unidad_base, $cantidad, $unidadOrigen);
            $ex = $this->filaExistencia($item->id, $bodega->id);
            $ex->increment('stock_actual', $base);

            InventarioMovimiento::create([
                'item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'tipo' => 'entrada',
                'cantidad_base' => $base,
                'unidad_origen' => $unidadOrigen,
                'cantidad_origen' => $cantidad,
                'fecha' => Carbon::today(),
                'descripcion' => $desc,
                'user_id' => auth()->id(),
                'referencia_type' => $ref?->getMorphClass(),
                'referencia_id' => $ref?->id,
            ]);
        });
    }

    public function salida(InventarioItem $item, Bodega $bodega, float $cantidad, string $unidadOrigen = 'kg', ?string $desc = null, $ref = null): void
    {
        DB::transaction(function () use ($item, $bodega, $cantidad, $unidadOrigen, $desc, $ref) {
            $base = $this->toBase($item->unidad_base, $cantidad, $unidadOrigen);
            $ex = $this->filaExistencia($item->id, $bodega->id);
            if ($ex->stock_actual < $base) abort(422, 'Stock insuficiente en bodega.');
            $ex->decrement('stock_actual', $base);

            InventarioMovimiento::create([
                'item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'tipo' => 'salida',
                'cantidad_base' => $base,
                'unidad_origen' => $unidadOrigen,
                'cantidad_origen' => $cantidad,
                'fecha' => Carbon::today(),
                'descripcion' => $desc,
                'user_id' => auth()->id(),
                'referencia_type' => $ref?->getMorphClass(),
                'referencia_id' => $ref?->id,
            ]);
        });
    }

    public function ajuste(InventarioItem $item, Bodega $bodega, float $nuevoStockBase, ?string $motivo = null): void
    {
        DB::transaction(function () use ($item, $bodega, $nuevoStockBase, $motivo) {
            $ex = $this->filaExistencia($item->id, $bodega->id);
            $delta = $nuevoStockBase - $ex->stock_actual;
            $ex->update(['stock_actual' => $nuevoStockBase]);

            InventarioMovimiento::create([
                'item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'tipo' => 'ajuste',
                'cantidad_base' => $delta,
                'unidad_origen' => $item->unidad_base,
                'cantidad_origen' => $delta,
                'fecha' => Carbon::today(),
                'descripcion' => $motivo ?? 'Ajuste de inventario',
                'user_id' => auth()->id(),
            ]);
        });
    }
}
