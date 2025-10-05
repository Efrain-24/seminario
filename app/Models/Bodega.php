<?php
// app/Models/Bodega.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $fillable = ['nombre', 'ubicacion'];
    public function existencias()
    {
        return $this->hasMany(InventarioExistencia::class);
    }
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class);
    }
}
