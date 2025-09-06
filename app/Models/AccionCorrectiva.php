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
        'fecha_detectada',
        'fecha_limite',
        'estado',
        'observaciones',
    ];

    // Relación con el usuario responsable
    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
