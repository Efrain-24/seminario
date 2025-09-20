<?php

// app/Models/InventarioItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioItem extends Model
{
    protected $fillable = [
        'nombre', 
        'sku', 
        'tipo', 
        'unidad_base', 
        'stock_minimo', 
        'descripcion',
        'costo_unitario',
        'precio_promedio',
        'moneda',
        'fecha_ultimo_costo',
        'costo_minimo',
        'costo_maximo'
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'precio_promedio' => 'decimal:2',
        'costo_minimo' => 'decimal:2',
        'costo_maximo' => 'decimal:2',
        'stock_minimo' => 'decimal:3',
        'fecha_ultimo_costo' => 'date',
    ];
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
    
    public function tipoAlimento()
    {
        return $this->hasOne(TipoAlimento::class, 'inventario_item_id');
    }

    // Métodos para manejo de costos
    public function actualizarCosto($nuevoCosto, $fecha = null)
    {
        $fecha = $fecha ?: now()->toDateString();
        
        // Actualizar rangos de costo
        if (!$this->costo_minimo || $nuevoCosto < $this->costo_minimo) {
            $this->costo_minimo = $nuevoCosto;
        }
        
        if (!$this->costo_maximo || $nuevoCosto > $this->costo_maximo) {
            $this->costo_maximo = $nuevoCosto;
        }
        
        // Actualizar costo actual y fecha
        $this->costo_unitario = $nuevoCosto;
        $this->fecha_ultimo_costo = $fecha;
        
        $this->save();
    }

    public function getCostoTotalAttribute()
    {
        return $this->costo_unitario * $this->stockTotal();
    }

    public function getCostoFormatadoAttribute()
    {
        if (!$this->costo_unitario) return 'Sin costo';
        
        return $this->moneda . ' ' . number_format($this->costo_unitario, 2) . '/' . $this->unidad_base;
    }

    public function tieneCosto()
    {
        return !is_null($this->costo_unitario) && $this->costo_unitario > 0;
    }
}
