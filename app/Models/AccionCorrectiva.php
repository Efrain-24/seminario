<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionCorrectiva extends Model
{
    //
        use HasFactory;

        protected $fillable = [
            'why1',
            'why2',
            'why3',
            'why4',
            'why5',
            'accion_inmediata',
            'accion_correctiva',
            'responsable',
            'fecha_compromiso',
            'evidencia',
            'verificacion_eficacia',
            'estado',
        ];
}
