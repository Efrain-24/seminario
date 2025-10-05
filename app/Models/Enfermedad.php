<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enfermedad extends Model
{
    use HasFactory;

    protected $table = 'enfermedades';

    protected $fillable = [
        'lote_id',
        'nombre',
        'descripcion',
        'fecha',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
