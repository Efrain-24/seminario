<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MantenimientoUnidad extends Model
{
    protected $table = 'mantenimiento_unidades';

    protected $fillable = [
        'unidad_produccion_id',
        'user_id',
        'fecha_mantenimiento',
        'tipo_mantenimiento',
        'estado_mantenimiento',
        'descripcion_trabajo',
        'materiales_utilizados',
        'costo_mantenimiento',
        'hora_inicio',
        'hora_fin',
        'prioridad',
        'observaciones_antes',
        'observaciones_despues',
        'proxima_revision',
        'requiere_vaciado',
        'requiere_traslado_peces',
        'actividades',
        'actividades_ejecutadas'
    ];

    protected $casts = [
        'fecha_mantenimiento' => 'date',
        'proxima_revision' => 'date',
        'costo_mantenimiento' => 'decimal:2',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'requiere_vaciado' => 'boolean',
        'requiere_traslado_peces' => 'boolean',
        'actividades' => 'array',
        'actividades_ejecutadas' => 'array'
    ];

    /**
     * Relaciones
     */
    public function unidadProduccion(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function insumos()
    {
        return $this->belongsToMany(
            InventarioItem::class,
            'mantenimiento_insumo',
            'mantenimiento_unidad_id',
            'inventario_item_id'
        )
        ->withPivot('cantidad', 'costo_unitario', 'costo_total')
        ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado_mantenimiento', 'completado');
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado_mantenimiento', ['programado', 'en_proceso']);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_mantenimiento', $tipo);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_mantenimiento', '<', now())
                    ->whereIn('estado_mantenimiento', ['programado', 'en_proceso']);
    }

    public function scopeProximos($query, $dias = 7)
    {
        return $query->whereBetween('fecha_mantenimiento', [now(), now()->addDays($dias)])
                    ->whereIn('estado_mantenimiento', ['programado', 'en_proceso']);
    }

    /**
     * Accesorios
     */
    public function getDuracionAttribute()
    {
        if ($this->hora_inicio && $this->hora_fin) {
            $inicio = Carbon::parse($this->hora_inicio);
            $fin = Carbon::parse($this->hora_fin);
            return $inicio->diffInMinutes($fin);
        }
        return null;
    }

    public function getEsVencidoAttribute()
    {
        return $this->fecha_mantenimiento->isPast() && 
               in_array($this->estado_mantenimiento, ['programado', 'en_proceso']);
    }

    public function getDiasRestantesAttribute()
    {
        if ($this->estado_mantenimiento === 'completado') return 0;
        return (int) $this->fecha_mantenimiento->diffInDays(now());
    }

    public function getPorcentajeCompletadoAttribute()
    {
        if (!$this->actividades || count($this->actividades) === 0) {
            return 0;
        }
        
        if (!$this->actividades_ejecutadas || count($this->actividades_ejecutadas) === 0) {
            return 0;
        }
        
        $total = count($this->actividades);
        $completadas = count(array_filter($this->actividades_ejecutadas, function($a) {
            return isset($a['completada']) && $a['completada'] === true;
        }));
        
        return (int) (($completadas / $total) * 100);
    }

    public function getPrioridadColorAttribute()
    {
        return match($this->prioridad) {
            'baja' => 'green',
            'media' => 'yellow',
            'alta' => 'orange',
            'critica' => 'red',
            default => 'gray'
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado_mantenimiento) {
            'programado' => 'blue',
            'en_proceso' => 'yellow',
            'completado' => 'green',
            'cancelado' => 'red',
            default => 'gray'
        };
    }

    /**
     * Métodos de acción
     */
    public function iniciar()
    {
        $this->update([
            'estado_mantenimiento' => 'en_proceso',
            'hora_inicio' => now()->format('H:i')
        ]);

        // Cambiar estado de la unidad a mantenimiento si es necesario
        if ($this->requiere_vaciado || $this->tipo_mantenimiento === 'reparacion') {
            $this->unidadProduccion->update(['estado' => 'mantenimiento']);
        }

        return $this;
    }

    public function completar($datos = [])
    {
        $this->update([
            'estado_mantenimiento' => 'completado',
            'hora_fin' => $datos['hora_fin'] ?? now()->format('H:i'),
            'observaciones_despues' => $datos['observaciones_despues'] ?? $this->observaciones_despues,
            'materiales_utilizados' => $datos['materiales_utilizados'] ?? $this->materiales_utilizados,
            'costo_mantenimiento' => $datos['costo_mantenimiento'] ?? $this->costo_mantenimiento,
            'proxima_revision' => $datos['proxima_revision'] ?? $this->proxima_revision
        ]);

        // Realizar descuento de inventario al completar el mantenimiento
        // Solo si no se ha hecho descuento anterior (cuando se crea el mantenimiento)
        // Se asume que ya se hizo descuento al crear, así que solo confirmamos el movimiento

        // Reactivar la unidad si estaba en mantenimiento
        if ($this->unidadProduccion->estado === 'mantenimiento') {
            $this->unidadProduccion->update([
                'estado' => 'activo',
                'ultimo_mantenimiento' => $this->fecha_mantenimiento
            ]);
        }

        return $this;
    }

    public function cancelar($motivo = null)
    {
        $observaciones = $this->observaciones_despues ?? '';
        if ($motivo) {
            $observaciones .= "\n\nMANTENIMIENTO CANCELADO: " . $motivo;
        }

        $this->update([
            'estado_mantenimiento' => 'cancelado',
            'observaciones_despues' => $observaciones
        ]);

        // Reactivar la unidad si estaba en mantenimiento
        if ($this->unidadProduccion->estado === 'mantenimiento') {
            $this->unidadProduccion->update(['estado' => 'activo']);
        }

        return $this;
    }
}
