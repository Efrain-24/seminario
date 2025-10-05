<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntradaCompraDetalle extends Model
{
    protected $fillable = [
        'entrada_id',
        'item_id',
        'descripcion',
        'cantidad',
        'unidad',
        'costo_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:4',
        'subtotal' => 'decimal:2'
    ];

    public function entrada()
    {
        return $this->belongsTo(EntradaCompra::class, 'entrada_id');
    }

    public function item()
    {
        return $this->belongsTo(InventarioItem::class, 'item_id');
    }
}
