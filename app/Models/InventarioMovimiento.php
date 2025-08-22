<?php

// app/Models/InventarioMovimiento.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventarioMovimiento extends Model
{
    protected $fillable = [
        'item_id',
        'bodega_id',
        'tipo',
        'cantidad_base',
        'unidad_origen',
        'cantidad_origen',
        'referencia_type',
        'referencia_id',
        'fecha',
        'descripcion',
        'user_id'
    ];
    public function item()
    {
        return $this->belongsTo(InventarioItem::class);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }
}
