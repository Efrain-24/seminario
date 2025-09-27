<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocoloInsumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo_sanidad_id',
        'inventario_item_id',
        'cantidad_necesaria',
        'unidad',
        'es_obligatorio',
        'notas',
    ];

    protected $casts = [
        'cantidad_necesaria' => 'decimal:3',
        'es_obligatorio' => 'boolean',
    ];

    /**
     * Relación con el protocolo de sanidad
     */
    public function protocoloSanidad()
    {
        return $this->belongsTo(ProtocoloSanidad::class);
    }

    /**
     * Relación con el item del inventario
     */
    public function inventarioItem()
    {
        return $this->belongsTo(InventarioItem::class);
    }

    /**
     * Verificar si hay suficiente stock para este insumo
     */
    public function tieneStockSuficiente()
    {
        $stockTotal = $this->inventarioItem->stockTotal();
        return $stockTotal >= $this->cantidad_necesaria;
    }

    /**
     * Obtener el costo total estimado del insumo
     */
    public function getCostoEstimadoAttribute()
    {
        if ($this->inventarioItem && $this->inventarioItem->costo_unitario) {
            return $this->cantidad_necesaria * $this->inventarioItem->costo_unitario;
        }
        return 0;
    }
}
