<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PrecioLibra extends Model
{
    use HasFactory;

    protected $table = 'precios_libra';

    protected $fillable = [
        'precio',
        'user_id',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('activo', true);
    }

    // Métodos estáticos
    public static function precioActual()
    {
        return self::where('activo', true)
                  ->orderBy('created_at', 'desc')
                  ->first();
    }

    public static function establecerPrecio($precio, $userId, $observaciones = null)
    {
        // Desactivar todos los precios anteriores
        self::where('activo', true)->update(['activo' => false]);

        // Crear el nuevo precio
        return self::create([
            'precio' => $precio,
            'user_id' => $userId,
            'activo' => true,
            'observaciones' => $observaciones
        ]);
    }
}