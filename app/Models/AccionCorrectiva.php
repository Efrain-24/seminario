<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionCorrectiva extends Model
{
    use HasFactory;

    protected $table = 'acciones_correctivas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'user_id',
        'fecha_prevista',
        'fecha_limite',
        'estado',
    ];

    protected $casts = [
        'fecha_prevista' => 'date',
        'fecha_limite' => 'date',
    ];

    // Relación con el usuario responsable
    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con seguimientos (solo activos)
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoAccion::class)->activos()->with('usuario')->orderBy('created_at', 'desc');
    }

    // Relación con todos los seguimientos (incluyendo eliminados)
    public function todosSeguimientos()
    {
        return $this->hasMany(SeguimientoAccion::class)->with('usuario')->orderBy('created_at', 'desc');
    }

    // Método helper para obtener evidencias con URL completa
    public function getEvidenciasConUrlAttribute()
    {
        if (!$this->evidencias) {
            return [];
        }

        return collect($this->evidencias)->map(function ($evidencia) {
            return [
                'nombre_original' => $evidencia['nombre_original'] ?? '',
                'nombre_archivo' => $evidencia['nombre_archivo'] ?? '',
                'url' => asset('storage/' . $evidencia['ruta']),
                'tipo' => $evidencia['tipo'] ?? '',
                'tamaño' => $evidencia['tamaño'] ?? 0,
            ];
        });
    }
}
