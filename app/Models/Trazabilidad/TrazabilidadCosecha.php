<?php

namespace App\Models\Trazabilidad;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lote;
use App\Models\CosechaParcial;

class TrazabilidadCosecha extends Model
{
    protected $table = 'trazabilidad_cosechas';

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'lote_id',              // ID del lote relacionado
        'fecha_cosecha',        // Fecha en que se realizó la cosecha
        'tipo_cosecha',         // Tipo de cosecha: parcial o total
        'peso_bruto',           // Peso total incluyendo empaque y hielo
        'peso_neto',            // Peso real del producto
        'unidades',             // Número de unidades cosechadas (opcional)
        'costo_mano_obra',      // Costo del personal involucrado
        'costo_insumos',        // Costo de materiales (hielo, empaque, etc.)
        'costo_operativo',      // Otros costos operativos
        'costo_total',          // Suma total de todos los costos
        'destino_tipo',         // Tipo de destino: cliente_final, bodega, mercado_local, exportacion
        'destino_detalle',      // Detalles específicos del destino
        'notas'                 // Observaciones adicionales
    ];

    protected $casts = [
        'fecha_cosecha' => 'datetime',
        'peso_bruto' => 'decimal:2',
        'peso_neto' => 'decimal:2',
        'costo_mano_obra' => 'decimal:2',
        'costo_insumos' => 'decimal:2',
        'costo_operativo' => 'decimal:2',
        'costo_total' => 'decimal:2'
    ];

    /**
     * Obtiene el lote al que pertenece esta trazabilidad
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Obtiene las cosechas parciales relacionadas
     */
    public function cosechasParciales()
    {
        return $this->hasMany(CosechaParcial::class, 'trazabilidad_id');
    }

    /**
     * Calcula el costo total sumando todos los componentes
     * @return float Suma de costos de mano de obra, insumos y operativos
     */
    public function calcularCostoTotal()
    {
        return $this->costo_mano_obra + $this->costo_insumos + $this->costo_operativo;
    }

    /**
     * Obtiene todo el historial de cosechas de un lote específico
     * @param int $lote_id ID del lote a consultar
     * @return Collection Colección de registros de cosecha ordenados por fecha
     */
    public static function historialLote($lote_id)
    {
        return self::where('lote_id', $lote_id)
                   ->orderBy('fecha_cosecha', 'desc')
                   ->get();
    }

    /**
     * Genera un resumen de costos para un período específico
     * @param string $fecha_inicio Fecha inicial del período
     * @param string $fecha_fin Fecha final del período
     * @return object Objeto con totales de costos, peso y número de cosechas
     */
    public static function resumenCostos($fecha_inicio, $fecha_fin)
    {
        return self::whereBetween('fecha_cosecha', [$fecha_inicio, $fecha_fin])
                   ->selectRaw('SUM(costo_total) as total_costos,
                              SUM(peso_neto) as total_peso,
                              COUNT(*) as total_cosechas')
                   ->first();
    }
}
