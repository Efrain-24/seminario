<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $fillable = [
        'user_id',
        'accion',
        'detalles',
        'modulo',
        'documento',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
