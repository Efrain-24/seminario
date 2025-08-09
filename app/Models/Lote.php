<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_lote',
        'especie',
        'cantidad_inicial',
        'cantidad_actual',
        'peso_promedio_inicial',
        'talla_promedio_inicial',
        'fecha_inicio',
        'unidad_produccion_id',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'peso_promedio_inicial' => 'decimal:2',
        'talla_promedio_inicial' => 'decimal:2',
    ];

    // Relaciones
    public function unidadProduccion()
    {
        return $this->belongsTo(UnidadProduccion::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class);
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class);
    }

    // Accesorios
    public function getBiomasaAttribute()
    {
        return $this->cantidad_actual * $this->peso_promedio_inicial;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Métodos estáticos
    public static function generarCodigoLote($especie)
    {
        // Generar acrónimo de la especie
        $acronimo = self::generarAcronimo($especie);
        
        // Año actual
        $año = date('Y');
        
        // Buscar el último correlativo para esta especie en el año actual
        $ultimoLote = self::where('codigo_lote', 'like', $acronimo . '-' . $año . '-%')
                         ->orderBy('codigo_lote', 'desc')
                         ->first();
        
        $correlativo = 1;
        if ($ultimoLote) {
            // Extraer el correlativo del último código
            $partes = explode('-', $ultimoLote->codigo_lote);
            if (count($partes) >= 3) {
                $correlativo = (int)end($partes) + 1;
            }
        }
        
        // Formatear correlativo con ceros a la izquierda (3 dígitos)
        $correlativoFormateado = str_pad($correlativo, 3, '0', STR_PAD_LEFT);
        
        return $acronimo . '-' . $año . '-' . $correlativoFormateado;
    }
    
    private static function generarAcronimo($especie)
    {
        $especie = strtolower(trim($especie));
        
        // Mapeo de especies comunes a acrónimos
        $mapeoEspecies = [
            'tilapia' => 'TIL',
            'tilapia nilótica' => 'TIL',
            'tilapia nilotica' => 'TIL',
            'trucha' => 'TRU',
            'trucha arcoíris' => 'TRU',
            'trucha arcoiris' => 'TRU',
            'carpa' => 'CAR',
            'salmón' => 'SAL',
            'salmon' => 'SAL',
            'bagre' => 'BAG',
            'cachama' => 'CAC',
            'bocachico' => 'BOC',
            'yamú' => 'YAM',
            'yamu' => 'YAM'
        ];
        
        // Buscar coincidencia exacta primero
        if (isset($mapeoEspecies[$especie])) {
            return $mapeoEspecies[$especie];
        }
        
        // Buscar coincidencia parcial
        foreach ($mapeoEspecies as $nombreEspecie => $acronimo) {
            if (str_contains($especie, $nombreEspecie)) {
                return $acronimo;
            }
        }
        
        // Si no hay coincidencia, generar acrónimo de las primeras letras
        $palabras = explode(' ', $especie);
        $acronimo = '';
        
        foreach ($palabras as $palabra) {
            if (strlen($palabra) > 0) {
                $acronimo .= strtoupper(substr($palabra, 0, 1));
            }
        }
        
        // Asegurar que tenga al menos 3 caracteres
        if (strlen($acronimo) < 3) {
            $acronimo = strtoupper(substr(str_replace(' ', '', $especie), 0, 3));
        }
        
        return substr($acronimo, 0, 3); // Máximo 3 caracteres
    }
}
