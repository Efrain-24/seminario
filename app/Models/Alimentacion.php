<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Alimentacion extends Model
{
    use HasFactory;

    protected $table = 'alimentacions';

    protected $fillable = [
        'lote_id',
        'tipo_alimento_id',
        'inventario_item_id', // Agregar para relación directa con inventario
        'bodega_id',
        'usuario_id',
        'fecha_alimentacion',
        'hora_alimentacion',
        'cantidad_kg',
        'costo_total',
        'metodo_alimentacion',
        'estado_peces',
        'porcentaje_consumo',
        'observaciones'
    ];

    protected $casts = [
        'fecha_alimentacion' => 'date',
        'hora_alimentacion' => 'datetime:H:i:s',
        'cantidad_kg' => 'decimal:3',
        'costo_total' => 'decimal:2',
        'porcentaje_consumo' => 'decimal:2'
    ];

    // Relaciones
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function tipoAlimento()
    {
        return $this->belongsTo(TipoAlimento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function inventarioItem()
    {
        return $this->belongsTo(InventarioItem::class);
    }

    // Scopes
    public function scopeDelPeriodo($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin]);
    }

    public function scopeDelLote($query, $loteId)
    {
        return $query->where('lote_id', $loteId);
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_alimentacion', '>=', Carbon::now()->subDays($dias));
    }

    // Accesorios
    public function getFechaHoraCompleta()
    {
        return $this->fecha_alimentacion->format('d/m/Y') . ' ' . $this->hora_alimentacion->format('H:i');
    }

    public function getCostoPromedioPorKgAttribute()
    {
        if ($this->cantidad_kg > 0 && $this->costo_total) {
            return $this->costo_total / $this->cantidad_kg;
        }
        return 0;
    }

    public function getEstadoConsumoBadgeAttribute()
    {
        if ($this->porcentaje_consumo >= 90) {
            return ['class' => 'bg-green-100 text-green-800', 'texto' => 'Excelente'];
        } elseif ($this->porcentaje_consumo >= 70) {
            return ['class' => 'bg-blue-100 text-blue-800', 'texto' => 'Bueno'];
        } elseif ($this->porcentaje_consumo >= 50) {
            return ['class' => 'bg-yellow-100 text-yellow-800', 'texto' => 'Regular'];
        } else {
            return ['class' => 'bg-red-100 text-red-800', 'texto' => 'Bajo'];
        }
    }

    public function getMetodoAlimentacionTextoAttribute()
    {
        $metodos = self::getMetodosAlimentacion();
        return $metodos[$this->metodo_alimentacion] ?? $this->metodo_alimentacion;
    }

    public function getEstadoPecesTextoAttribute()
    {
        $estados = self::getEstadosPeces();
        return $estados[$this->estado_peces] ?? $this->estado_peces;
    }

    // Alias para compatibilidad
    public function createdBy()
    {
        return $this->usuario();
    }

    // Constantes para opciones
    public static function getMetodosAlimentacion()
    {
        return [
            'manual' => 'Manual',
            'automatico' => 'Automático',
            'semi_automatico' => 'Semi-automático'
        ];
    }

    public static function getEstadosPeces()
    {
        return [
            'normal' => 'Normal',
            'poco_apetito' => 'Poco apetito',
            'muy_activos' => 'Muy activos',
            'estresados' => 'Estresados',
            'enfermedad' => 'Con signos de enfermedad'
        ];
    }
}
