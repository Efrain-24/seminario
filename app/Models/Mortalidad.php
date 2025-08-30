<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Mortalidad extends Model
{
    use HasFactory;


    protected $table = 'mortalidades';


    protected $fillable = [
        'lote_id',
        'unidad_produccion_id',
        'fecha',
        'cantidad',
        'causa',
        'observaciones',
        'user_id'
    ];


    protected $casts = [
        'fecha' => 'date',
    ];


    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function unidadProduccion()
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_produccion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
