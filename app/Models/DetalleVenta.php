<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $fillable = [
        'venta_id',
        'articulo_id',
        'nombre_articulo',
        'precio_unitario',
        'cantidad',
        'total',
    ];
    public function venta() {
        return $this->belongsTo(Venta::class);
    }
}
