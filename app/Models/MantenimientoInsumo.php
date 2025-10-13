<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoInsumo extends Model
{
    protected $table = 'mantenimiento_insumos';

    protected $fillable = [
        'mantenimiento_unidad_id',
        'inventario_item_id',
        'cantidad_utilizada',
        'costo_unitario',
        'costo_total',
        'observaciones'
    ];

    protected $casts = [
        'cantidad_utilizada' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2'
    ];

    /**
     * Relaciones
     */
    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(MantenimientoUnidad::class, 'mantenimiento_unidad_id');
    }

    public function inventarioItem(): BelongsTo
    {
        return $this->belongsTo(InventarioItem::class);
    }

    /**
     * Mutadores
     */
    public function setCantidadUtilizadaAttribute($value)
    {
        $this->attributes['cantidad_utilizada'] = $value;
        $this->calcularCostoTotal();
    }

    public function setCostoUnitarioAttribute($value)
    {
        $this->attributes['costo_unitario'] = $value;
        $this->calcularCostoTotal();
    }

    /**
     * Calcular costo total automáticamente
     */
    private function calcularCostoTotal()
    {
        if (isset($this->attributes['cantidad_utilizada']) && isset($this->attributes['costo_unitario'])) {
            $this->attributes['costo_total'] = $this->attributes['cantidad_utilizada'] * $this->attributes['costo_unitario'];
        }
    }

    /**
     * Boot method para calcular costo total al crear/actualizar
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($insumo) {
            $insumo->costo_total = $insumo->cantidad_utilizada * $insumo->costo_unitario;
        });
    }
}