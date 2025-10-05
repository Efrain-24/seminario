<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnidadProduccion extends Model
{
    use HasFactory;

    protected $table = 'unidad_produccions';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'capacidad_maxima',
        'area',
        'profundidad',
        'estado',
        'descripcion',
        'fecha_construccion',
        'ultimo_mantenimiento'
    ];

    protected $casts = [
        'fecha_construccion' => 'date',
        'ultimo_mantenimiento' => 'date',
        'capacidad_maxima' => 'decimal:2',
        'area' => 'decimal:2',
        'profundidad' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($unidad) {
            if (empty($unidad->codigo)) {
                $unidad->codigo = static::generateCodigo($unidad->tipo);
            }
        });
    }

    /**
     * Genera un código automático para la unidad basado en el tipo
     */
    public static function generateCodigo($tipo)
    {
        // Mapeo de tipos a prefijos de código
        $prefijos = [
            'tanque' => 'TQ',
            'estanque' => 'ES',
            'jaula' => 'JL',
            'sistema_especializado' => 'SE'
        ];
        
        $prefijo = $prefijos[$tipo] ?? 'UN';
        
        // Contar unidades existentes del mismo tipo para generar número consecutivo
        $count = static::where('tipo', $tipo)->count() + 1;
        
        // Generar código con formato: TQ001, ES001, JL001, SE001, etc.
        return $prefijo . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // Relaciones
    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    public function trasladosOrigen()
    {
        return $this->hasMany(Traslado::class, 'unidad_origen_id');
    }

    public function trasladosDestino()
    {
        return $this->hasMany(Traslado::class, 'unidad_destino_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(MantenimientoUnidad::class);
    }

    // Accesorios
    public function getLotesActivosCountAttribute()
    {
        return $this->lotes()->where('estado', 'activo')->count();
    }

    public function getCapacidadOcupadaAttribute()
    {
        // Suma de biomasa estimada de todos los lotes activos
        $biomasaTotal = 0;
        foreach ($this->lotes()->where('estado', 'activo')->get() as $lote) {
            $biomasaTotal += $lote->biomasa_estimada ?? 0;
        }
        return $biomasaTotal;
    }

    public function getOcupacionAttribute()
    {
        $totalPeces = $this->lotes()->where('estado', 'activo')->sum('cantidad_actual');
        return $totalPeces;
    }

    public function getUltimoMantenimientoRealizadoAttribute()
    {
        return $this->mantenimientos()->where('estado_mantenimiento', 'completado')->latest('fecha_mantenimiento')->first();
    }

    public function getMantenimientosPendientesAttribute()
    {
        return $this->mantenimientos()->where('estado_mantenimiento', 'programado')->count();
    }

    public function getDiasDesdeUltimoMantenimientoAttribute()
    {
        $ultimo = $this->ultimo_mantenimiento_realizado;
        if ($ultimo) {
            return $ultimo->fecha_mantenimiento->diffInDays(now());
        }
        return $this->created_at->diffInDays(now());
    }

    public function getRequiereMantenimientoAttribute()
    {
        // Considerar que requiere mantenimiento si han pasado más de 30 días
        return $this->dias_desde_ultimo_mantenimiento > 30;
    }

    public function getPorcentajeOcupacionAttribute()
    {
        if ($this->capacidad_maxima && $this->capacidad_maxima > 0) {
            return ($this->capacidad_ocupada / $this->capacidad_maxima) * 100;
        }
        return 0;
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
