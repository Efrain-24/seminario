<?php

// app/Models/InventarioItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioItem extends Model
{
    protected $fillable = ['nombre', 'sku', 'tipo', 'unidad_base', 'stock_minimo', 'descripcion'];
    public function existencias()
    {
        return $this->hasMany(InventarioExistencia::class, 'item_id');
    }
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'item_id');
    }
    public function stockTotal(): float
    {
        return (float) $this->existencias()->sum('stock_actual');
    }
}
