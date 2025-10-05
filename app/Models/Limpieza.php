<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limpieza extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'area',
        'responsable',
        'protocolo_sanidad_id',
        'actividades_ejecutadas',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'actividades_ejecutadas' => 'array',
    ];

    public function protocoloSanidad()
    {
        return $this->belongsTo(ProtocoloSanidad::class);
    }

    /**
     * Verificar si el registro puede ser editado
     */
    public function puedeSerEditado()
    {
        return $this->estado !== 'completado';
    }

    /**
     * Scope para obtener solo registros editables
     */
    public function scopeEditables($query)
    {
        return $query->where('estado', '!=', 'completado');
    }

    /**
     * Accesor: actividades normalizadas para soportar registros antiguos que guardaron todo en una sola descripción.
     * Reutiliza la lógica de separación (saltos de línea, ; | comas, numeraciones 1) 2. 3- ) sólo si existe exactamente un elemento.
     */
    public function getActividadesNormalizadasAttribute()
    {
        $actividades = $this->actividades_ejecutadas ?? [];

        if (is_array($actividades) && count($actividades) === 1) {
            $first = $actividades[array_key_first($actividades)];
            if (is_array($first) && isset($first['descripcion']) && is_string($first['descripcion'])) {
                $raw = trim($first['descripcion']);
                // Heurística: si contiene separadores claros o numeraciones, intentar dividir
                $hasDelimiters = preg_match('/[\r\n;|]/', $raw) || preg_match('/\d+\s*[).:-]\s+/', $raw) || strpos($raw, ',') !== false;
                if ($hasDelimiters) {
                    $segments = preg_split('/[\r\n;|]+/', $raw); // primera pasada
                    if (count($segments) === 1) {
                        // numeraciones dentro de un mismo string
                        if (preg_match('/\d+\s*[).:-]\s+/', $raw)) {
                            $tmp = preg_split('/\s*\d+\s*[).:-]\s*/', $raw);
                            $tmp = array_filter(array_map('trim', $tmp));
                            if (count($tmp) > 1) {
                                $segments = $tmp;
                            }
                        }
                    }
                    // Intentar coma sólo si seguimos con un único bloque largo
                    if (count($segments) === 1 && strpos($segments[0], ',') !== false) {
                        $commaParts = array_map('trim', explode(',', $segments[0]));
                        if (count($commaParts) > 1) {
                            $segments = $commaParts;
                        }
                    }
                    // Limpieza final: descartar vacíos
                    $segments = array_values(array_filter(array_map('trim', $segments), fn($s) => $s !== ''));
                    if (count($segments) > 1) {
                        $originalCompleted = $first['completada'] ?? ($this->estado === 'completado');
                        $originalObs = $first['observaciones'] ?? null;
                        $actividades = [];
                        foreach ($segments as $idx => $seg) {
                            $actividades[$idx] = [
                                'descripcion' => $seg,
                                // Si el registro ya está completado o la actividad original estaba marcada, propagamos el estado
                                'completada' => $originalCompleted ? true : false,
                                'observaciones' => $originalObs,
                            ];
                        }
                    }
                }
            }
        }

        return $actividades;
    }
}
