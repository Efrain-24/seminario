<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class CosechaParcial extends Model
{
    use HasFactory;


    protected $table = 'cosechas_parciales';


    protected $fillable = [
        'lote_id',
        'fecha',
        'cantidad_cosechada',
        'peso_cosechado_kg',
        'destino',
        'responsable',
        'observaciones',
        'user_id',
    ];


    protected $casts = [
        'fecha' => 'date',
        'peso_cosechado_kg' => 'decimal:2',
    ];


    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}