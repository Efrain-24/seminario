<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'lote_id',
        'producto_nombre',
        'lote_codigo',
        'cantidad_peces',
        'peso_libras',
        'precio_libra',
        'subtotal'
    ];

    protected $casts = [
        'cantidad_peces' => 'integer',
        'peso_libras' => 'decimal:2',
        'precio_libra' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    // Accessors
    public function getPesoKgAttribute()
    {
        // Convertir libras a kg (1 libra = 0.453592 kg)
        return round($this->peso_libras * 0.453592, 2);
    }

    public function getPrecioKgAttribute()
    {
        // Convertir precio por libra a precio por kg
        return round($this->precio_libra / 0.453592, 2);
    }
}
