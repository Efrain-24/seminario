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
        'descripcion',
        'responsable',
        'protocolo_sanidad_id',
    ];

    public function protocoloSanidad()
    {
        return $this->belongsTo(ProtocoloSanidad::class);
    }
}
