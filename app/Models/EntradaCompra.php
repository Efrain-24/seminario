<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntradaCompra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'proveedor_id',
        'numero_documento',
        'fecha_documento',
        'fecha_ingreso',
        'moneda',
        'tipo_cambio',
        'subtotal',
        'impuesto',
        'total',
        'observaciones'
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_ingreso' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'tipo_cambio' => 'decimal:4'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles()
    {
        return $this->hasMany(EntradaCompraDetalle::class, 'entrada_id');
    }
}
