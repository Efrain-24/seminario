<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seguimiento extends Model
{
    protected $fillable = [
        'lote_id',
        'user_id',
        'fecha_seguimiento',
        'cantidad_actual',
        'mortalidad',
        'peso_promedio',
        'talla_promedio',
        'temperatura_agua',
        'ph_agua',
        'oxigeno_disuelto',
        'observaciones',
        'tipo_seguimiento'
    ];

    protected $casts = [
        'fecha_seguimiento' => 'date',
        'peso_promedio' => 'decimal:2',
        'talla_promedio' => 'decimal:2',
        'temperatura_agua' => 'decimal:2',
        'ph_agua' => 'decimal:2',
        'oxigeno_disuelto' => 'decimal:2'
    ];

    /**
     * Relación con el lote
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Relación con el usuario que registró el seguimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Calcular biomasa estimada
     */
    public function getBiomasaAttribute()
    {
        if ($this->peso_promedio && $this->cantidad_actual) {
            return ($this->peso_promedio * $this->cantidad_actual) / 1000; // kg
        }
        return 0;
    }

    /**
     * Scopes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_seguimiento', '>=', now()->subDays($dias));
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_seguimiento', $tipo);
    }
}
