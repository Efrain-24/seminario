<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TipoCambio extends Model
{
    protected $fillable = ['fecha', 'valor'];
    
    protected $casts = [
        'fecha' => 'date',
        'valor' => 'decimal:4'
    ];

    /**
     * Obtener el tipo de cambio actual (más reciente)
     */
    public static function actual()
    {
        return self::orderBy('fecha', 'desc')->first();
    }

    /**
     * Obtener el tipo de cambio de una fecha específica
     */
    public static function deFecha($fecha)
    {
        return self::where('fecha', $fecha)->first();
    }

    /**
     * Obtener el tipo de cambio más reciente o crear uno de prueba
     */
    public static function ultimoDisponible()
    {
        $ultimo = self::actual();
        
        if (!$ultimo) {
            // Si no hay registros, crear uno de prueba
            $ultimo = self::create([
                'fecha' => now()->toDateString(),
                'valor' => 7.75 // Valor aproximado
            ]);
        }
        
        return $ultimo;
    }

    /**
     * Formatear el valor para mostrar
     */
    public function getValorFormateadoAttribute()
    {
        return 'Q' . number_format($this->valor, 4);
    }

    /**
     * Obtener historial de los últimos N días
     */
    public static function historial($dias = 30)
    {
        return self::orderBy('fecha', 'desc')
                   ->limit($dias)
                   ->get();
    }
}
