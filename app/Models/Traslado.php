<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Traslado extends Model
{
    protected $fillable = [
        'lote_id',
        'unidad_origen_id',
        'unidad_destino_id',
        'user_id',
        'seguimiento_id',
        'fecha_traslado',
        'cantidad_trasladada',
        'cantidad_perdida',
        'peso_promedio_traslado',
        'motivo_traslado',
        'estado_traslado',
        'observaciones_origen',
        'observaciones_destino',
        'hora_inicio',
        'hora_fin'
    ];

    protected $casts = [
        'fecha_traslado' => 'date',
        'peso_promedio_traslado' => 'decimal:2',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i'
    ];

    /**
     * Relaciones
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function unidadOrigen(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_origen_id');
    }

    public function unidadDestino(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seguimiento(): BelongsTo
    {
        return $this->belongsTo(Seguimiento::class);
    }

    /**
     * Scopes
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado_traslado', 'completado');
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado_traslado', ['planificado', 'en_proceso']);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_traslado', '>=', now()->subDays($dias));
    }

    /**
     * Accesorios
     */
    public function getDuracionAttribute()
    {
        if ($this->hora_inicio && $this->hora_fin) {
            $inicio = $this->hora_inicio;
            $fin = $this->hora_fin;
            return $inicio->diffInMinutes($fin);
        }
        return null;
    }

    public function getCantidadEfectivaAttribute()
    {
        return $this->cantidad_trasladada - $this->cantidad_perdida;
    }

    public function getPorcentajePerdidaAttribute()
    {
        if ($this->cantidad_trasladada > 0) {
            return ($this->cantidad_perdida / $this->cantidad_trasladada) * 100;
        }
        return 0;
    }

    /**
     * Crear traslado y registrar seguimiento automáticamente
     */
    public static function crearConSeguimiento($datos)
    {
        return DB::transaction(function () use ($datos) {
            // Crear el traslado
            $traslado = self::create([
                'lote_id' => $datos['lote_id'],
                'unidad_origen_id' => $datos['unidad_origen_id'],
                'unidad_destino_id' => $datos['unidad_destino_id'],
                'user_id' => Auth::id(),
                'fecha_traslado' => $datos['fecha_traslado'],
                'cantidad_trasladada' => $datos['cantidad_trasladada'],
                'cantidad_perdida' => $datos['cantidad_perdida'] ?? 0,
                'peso_promedio_traslado' => $datos['peso_promedio_traslado'] ?? null,
                'motivo_traslado' => $datos['motivo_traslado'],
                'estado_traslado' => 'planificado',
                'observaciones_origen' => $datos['observaciones_origen'] ?? null,
                'observaciones_destino' => $datos['observaciones_destino'] ?? null,
                'hora_inicio' => $datos['hora_inicio'] ?? null,
                'hora_fin' => $datos['hora_fin'] ?? null
            ]);

            // Crear seguimiento asociado
            $seguimiento = Seguimiento::create([
                'lote_id' => $datos['lote_id'],
                'user_id' => Auth::id(),
                'fecha_seguimiento' => $datos['fecha_traslado'],
                'tipo_seguimiento' => 'traslado',
                'cantidad_actual' => $datos['cantidad_trasladada'] - ($datos['cantidad_perdida'] ?? 0),
                'mortalidad' => $datos['cantidad_perdida'] ?? 0,
                'peso_promedio' => $datos['peso_promedio_traslado'] ?? null,
                'observaciones' => self::generarObservacionSeguimiento($traslado, $datos)
            ]);

            // Asociar el seguimiento al traslado
            $traslado->update(['seguimiento_id' => $seguimiento->id]);

            return $traslado->load(['seguimiento', 'unidadOrigen', 'unidadDestino']);
        });
    }

    /**
     * Completar traslado y actualizar datos del lote
     */
    public function completar($datosFinales = [])
    {
        return DB::transaction(function () use ($datosFinales) {
            // Actualizar estado del traslado
            $this->update([
                'estado_traslado' => 'completado',
                'hora_fin' => $datosFinales['hora_fin'] ?? now()->format('H:i'),
                'cantidad_perdida' => $datosFinales['cantidad_perdida'] ?? $this->cantidad_perdida,
                'observaciones_destino' => $datosFinales['observaciones_destino'] ?? $this->observaciones_destino
            ]);

            // Actualizar lote con nueva unidad y cantidad
            $cantidadFinal = $this->cantidad_trasladada - $this->cantidad_perdida;
            $this->lote->update([
                'unidad_produccion_id' => $this->unidad_destino_id,
                'cantidad_actual' => $cantidadFinal
            ]);

            // Actualizar seguimiento asociado
            if ($this->seguimiento) {
                $this->seguimiento->update([
                    'cantidad_actual' => $cantidadFinal,
                    'mortalidad' => $this->cantidad_perdida,
                    'observaciones' => self::generarObservacionSeguimiento($this, [])
                ]);
            }

            return $this;
        });
    }

    /**
     * Generar observación para el seguimiento basada en el traslado
     */
    private static function generarObservacionSeguimiento($traslado, $datos)
    {
        $observacion = "Traslado de lote realizado.\n";
        
        if ($traslado->unidadOrigen) {
            $observacion .= "Origen: {$traslado->unidadOrigen->nombre} ({$traslado->unidadOrigen->codigo})\n";
        }
        
        $observacion .= "Destino: {$traslado->unidadDestino->nombre} ({$traslado->unidadDestino->codigo})\n";
        $observacion .= "Cantidad trasladada: " . number_format($traslado->cantidad_trasladada) . " peces\n";
        
        if ($traslado->cantidad_perdida > 0) {
            $observacion .= "Pérdidas durante traslado: " . number_format($traslado->cantidad_perdida) . " peces\n";
        }
        
        $observacion .= "Motivo: " . ucfirst(str_replace('_', ' ', $traslado->motivo_traslado)) . "\n";
        
        if (!empty($datos['observaciones_origen'])) {
            $observacion .= "Observaciones origen: {$datos['observaciones_origen']}\n";
        }
        
        if (!empty($datos['observaciones_destino'])) {
            $observacion .= "Observaciones destino: {$datos['observaciones_destino']}";
        }

        return trim($observacion);
    }
}
