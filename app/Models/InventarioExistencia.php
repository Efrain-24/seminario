<?php

// app/Models/InventarioExistencia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioExistencia extends Model
{
    protected $fillable = ['item_id', 'bodega_id', 'stock_actual'];
    public function item()
    {
        return $this->belongsTo(InventarioItem::class, 'item_id');
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
}
