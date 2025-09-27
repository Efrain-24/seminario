<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class CosechaParcial extends Model
{
    use HasFactory;


    protected $table = 'cosechas_parciales';


    protected $fillable = [
        'lote_id',
        'fecha',
        'cantidad_cosechada',
        'peso_cosechado_kg',
        'destino',
        'responsable',
        'observaciones',
        'user_id',
        // Campos de venta
        'codigo_venta',
        'cliente',
        'telefono_cliente',
        'email_cliente',
        'fecha_venta',
        'precio_kg',
        'total_venta',
        'tipo_cambio',
        'total_usd',
        'metodo_pago',
        'estado_venta',
        'observaciones_venta',
    ];


    protected $casts = [
        'fecha' => 'date',
        'fecha_venta' => 'date',
        'peso_cosechado_kg' => 'decimal:2',
        'precio_kg' => 'decimal:2',
        'total_venta' => 'decimal:2',
        'tipo_cambio' => 'decimal:4',
        'total_usd' => 'decimal:2',
    ];


    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Scopes para ventas
    public function scopeVentas($query)
    {
        return $query->where('destino', 'venta');
    }

    public function scopeVentasPendientes($query)
    {
        return $query->where('destino', 'venta')->where('estado_venta', 'pendiente');
    }

    public function scopeVentasCompletadas($query)
    {
        return $query->where('destino', 'venta')->where('estado_venta', 'completada');
    }

    // Accessors para badges de estado
    public function getEstadoVentaBadgeAttribute()
    {
        if ($this->destino !== 'venta') return null;
        
        $estados = [
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'completada' => 'bg-green-100 text-green-800',
            'cancelada' => 'bg-red-100 text-red-800'
        ];

        return $estados[$this->estado_venta] ?? 'bg-gray-100 text-gray-800';
    }

    public function getMetodoPagoBadgeAttribute()
    {
        if ($this->destino !== 'venta' || !$this->metodo_pago) return null;
        
        $metodos = [
            'efectivo' => 'bg-green-100 text-green-800',
            'transferencia' => 'bg-blue-100 text-blue-800',
            'cheque' => 'bg-purple-100 text-purple-800',
            'credito' => 'bg-orange-100 text-orange-800'
        ];

        return $metodos[$this->metodo_pago] ?? 'bg-gray-100 text-gray-800';
    }

    // Mutator para generar código de venta automáticamente
    public function setCodigoVentaAttribute($value)
    {
        if ($this->destino === 'venta' && !$value) {
            $this->attributes['codigo_venta'] = 'V-' . date('Y') . '-' . str_pad(
                (static::where('destino', 'venta')->whereYear('created_at', date('Y'))->count() + 1), 
                4, 
                '0', 
                STR_PAD_LEFT
            );
        } else {
            $this->attributes['codigo_venta'] = $value;
        }
    }

    // Método para verificar si es una venta
    public function esVenta()
    {
        return $this->destino === 'venta';
    }

    // Método para calcular totales automáticamente
    public function calcularTotales()
    {
        if ($this->esVenta() && $this->precio_kg && $this->peso_cosechado_kg) {
            $this->total_venta = $this->precio_kg * $this->peso_cosechado_kg;
            
            // Buscar tipo de cambio actual (asumiendo que tienes un modelo TipoCambio)
            $tipoCambio = \App\Models\TipoCambio::latest()->first();
            $this->tipo_cambio = $tipoCambio ? $tipoCambio->venta : 8.0;
            $this->total_usd = $this->total_venta / $this->tipo_cambio;
            
            return $this;
        }
        return $this;
    }
}