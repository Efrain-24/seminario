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
}
