<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'lote_id',
        'tipo_alerta',
        'detalles',
        'fecha_resolucion',
        // Campos para enfermedades
        'nombre_enfermedad',
        'cantidad_afectados',
        'porcentaje_afectados',
        'nivel_riesgo',
        'estado_tratamiento',
        'descripcion_tratamiento',
        'fecha_deteccion',
        'fecha_inicio_tratamiento',
        // Campos para peso bajo
        'peso_actual',
        'peso_esperado',
        'porcentaje_desviacion',
        'tasa_crecimiento',
        'consumo_alimento_reciente',
        'factor_conversion_alimento',
        'dias_desviacion',
        'observaciones_alimentacion',
        'historico_pesos'
    ];

    protected $casts = [
        'fecha_deteccion' => 'date',
        'fecha_inicio_tratamiento' => 'date',
        'fecha_resolucion' => 'datetime',
        'historico_pesos' => 'array',
        'peso_actual' => 'float',
        'peso_esperado' => 'float',
        'porcentaje_desviacion' => 'float',
        'tasa_crecimiento' => 'float',
        'consumo_alimento_reciente' => 'float',
        'factor_conversion_alimento' => 'float',
        'dias_desviacion' => 'integer',
        'cantidad_afectados' => 'integer',
        'porcentaje_afectados' => 'float'
    ];

    // Relaciones
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    // Métodos para clasificación de alertas
    public function getColorClase(): string
    {
        return match($this->tipo_alerta) {
            'bajo peso' => match(true) {
                abs($this->porcentaje_desviacion) >= 25 => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                abs($this->porcentaje_desviacion) >= 20 => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
            },
            'enfermedad' => match($this->nivel_riesgo) {
                'alto' => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                'medio' => 'text-amber-600 bg-amber-100 dark:bg-amber-900/20',
                default => 'text-orange-600 bg-orange-100 dark:bg-orange-900/20'
            },
            default => 'text-gray-600 bg-gray-100 dark:bg-gray-900/20'
        };
    }

    public function getNivelAlerta(): string
    {
        return match($this->tipo_alerta) {
            'bajo peso' => match(true) {
                abs($this->porcentaje_desviacion) >= 25 => '¡Alerta Crítica de Peso!',
                abs($this->porcentaje_desviacion) >= 20 => '¡Alerta de Peso!',
                default => '¡Peso Bajo!'
            },
            'enfermedad' => match($this->nivel_riesgo) {
                'alto' => '¡Alerta Sanitaria Crítica!',
                'medio' => '¡Alerta Sanitaria!',
                default => '¡Precaución Sanitaria!'
            },
            default => '¡Alerta!'
        };
    }

    // Scopes
    public function scopePesoBajo($query)
    {
        return $query->where('tipo_alerta', 'bajo peso')
                    ->where(function($q) {
                        $q->where('porcentaje_desviacion', '<=', -15)
                          ->orWhere('dias_desviacion', '>=', 7);
                    });
    }

    public function scopeEnfermedades($query)
    {
        return $query->where('tipo_alerta', 'enfermedad')
                    ->where(function($q) {
                        $q->where('nivel_riesgo', 'alto')
                          ->orWhere('porcentaje_afectados', '>=', 50);
                    });
    }
}
