<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_venta',
        'cliente',
        'telefono_cliente',
        'email_cliente',
        'fecha_venta',
        'cantidad_kg',
        'precio_kg',
        'total',
        'tipo_cambio',
        'total_usd',
        'metodo_pago',
        'estado',
        'observaciones',
        'cosecha_parcial_id'
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'cantidad_kg' => 'decimal:2',
        'precio_kg' => 'decimal:2',
        'total' => 'decimal:2',
        'tipo_cambio' => 'decimal:4',
        'total_usd' => 'decimal:2'
    ];

    // Relaciones
    public function cosechaParcial()
    {
        return $this->belongsTo(CosechaParcial::class);
    }

    public function lote()
    {
        return $this->hasOneThrough(Lote::class, CosechaParcial::class, 'id', 'id', 'cosecha_parcial_id', 'lote_id');
    }


    // Relación con detalles de venta
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    // Mutators
    public function setCodigoVentaAttribute($value)
    {
        if (!$value) {
            $this->attributes['codigo_venta'] = 'V-' . date('Y') . '-' . str_pad(
                (static::whereYear('created_at', date('Y'))->count() + 1), 
                4, 
                '0', 
                STR_PAD_LEFT
            );
        } else {
            $this->attributes['codigo_venta'] = $value;
        }
    }

    // Accessors
    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'completada' => 'bg-green-100 text-green-800',
            'cancelada' => 'bg-red-100 text-red-800'
        ];

        return $estados[$this->estado] ?? 'bg-gray-100 text-gray-800';
    }

    public function getMetodoPagoBadgeAttribute()
    {
        $metodos = [
            'efectivo' => 'bg-green-100 text-green-800',
            'transferencia' => 'bg-blue-100 text-blue-800',
            'cheque' => 'bg-purple-100 text-purple-800',
            'credito' => 'bg-orange-100 text-orange-800'
        ];

        return $metodos[$this->metodo_pago] ?? 'bg-gray-100 text-gray-800';
    }
}