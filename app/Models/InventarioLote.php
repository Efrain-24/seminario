<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioLote extends Model
{
    protected $fillable = [
        'item_id',
        'bodega_id',
        'lote',
        'fecha_ingreso',
        'fecha_vencimiento',
        'stock_lote'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventarioItem::class, 'item_id');
    }
    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }

    /** Ámbito: lotes con stock */
    public function scopeConStock($q)
    {
        return $q->where('stock_lote', '>', 0);
    }

    /** Ámbito: vencidos o por vencer en N días */
    public function scopeVencidos($q)
    {
        return $q->conStock()->whereNotNull('fecha_vencimiento')->whereDate('fecha_vencimiento', '<', now()->toDateString());
    }
    public function scopePorVencer($q, int $dias = 30)
    {
        $limite = now()->addDays($dias)->toDateString();
        return $q->conStock()->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->whereDate('fecha_vencimiento', '<=', $limite);
    }
}
