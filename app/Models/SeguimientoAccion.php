<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoAccion extends Model
{
    protected $table = 'seguimiento_acciones';

    protected $fillable = [
        'accion_correctiva_id',
        'user_id',
        'descripcion',
        'estado_anterior',
        'estado_nuevo',
        'archivo_evidencia',
        'nombre_archivo_original',
        'tipo_archivo',
        'tamaño_archivo',
        'estado',
    ];

    public function accionCorrectiva(): BelongsTo
    {
        return $this->belongsTo(AccionCorrectiva::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para obtener solo registros activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
