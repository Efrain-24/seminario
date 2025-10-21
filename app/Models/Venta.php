<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_venta',
        'año',
        'correlativo',
        'fecha_venta',
        'hora_venta',
        'tipo_cliente',
        'cliente_nombre',
        'cliente_nit',
        'subtotal',
        'impuestos',
        'total',
        'metodo_pago',
        'estado',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'hora_venta' => 'datetime:H:i:s',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'año' => 'integer',
        'correlativo' => 'integer'
    ];

    // Relaciones
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function scopeDelAño($query, $año = null)
    {
        $año = $año ?? date('Y');
        return $query->where('año', $año);
    }

    // Métodos estáticos
    public static function generarNumeroVenta()
    {
        $año = date('Y');
        
        // Obtener el último correlativo del año
        $ultimaVenta = self::where('año', $año)->orderBy('correlativo', 'desc')->first();
        $nuevoCorrelativo = $ultimaVenta ? $ultimaVenta->correlativo + 1 : 1;
        
        // Formato: 2025000001 (año + correlativo de 6 dígitos)
        $numeroVenta = $año . str_pad($nuevoCorrelativo, 6, '0', STR_PAD_LEFT);
        
        return [
            'numero_venta' => $numeroVenta,
            'año' => $año,
            'correlativo' => $nuevoCorrelativo
        ];
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

    public function getTipoClienteBadgeAttribute()
    {
        return $this->tipo_cliente === 'CF' 
            ? 'bg-blue-100 text-blue-800' 
            : 'bg-green-100 text-green-800';
    }
}