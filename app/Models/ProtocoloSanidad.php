<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProtocoloSanidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_implementacion',
        'responsable',
        'unidad_produccion_id',
        'actividades',
        'version',
        'estado',
        'protocolo_base_id',
        'fecha_ejecucion',
        'observaciones_ejecucion',
    ];

    protected $casts = [
        'actividades' => 'array',
    ];

    // Scope para obtener solo protocolos vigentes
    public function scopeVigentes($query)
    {
        return $query->where('estado', 'vigente');
    }

    // Relación con el protocolo base (protocolo original)
    public function protocoloBase()
    {
        return $this->belongsTo(ProtocoloSanidad::class, 'protocolo_base_id');
    }

    // Relación con las versiones derivadas
    public function versiones()
    {
        return $this->hasMany(ProtocoloSanidad::class, 'protocolo_base_id');
    }

    public function unidadProduccion()
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_produccion_id');
    }

    // Relación con los insumos del protocolo
    public function insumos()
    {
        return $this->hasMany(ProtocoloInsumo::class);
    }

    // Método para crear nueva versión
    public function crearNuevaVersion($data)
    {
        // Marcar la versión actual como obsoleta
        $this->update(['estado' => 'obsoleta']);
        
        // Obtener el protocolo base (si existe) o usar el actual como base
        $protocoloBaseId = $this->protocolo_base_id ?? $this->id;
        
        // Obtener la siguiente versión
        $siguienteVersion = ProtocoloSanidad::where('protocolo_base_id', $protocoloBaseId)
                                          ->orWhere('id', $protocoloBaseId)
                                          ->max('version') + 1;
        
        // Crear nueva versión
        return ProtocoloSanidad::create([
            'nombre' => $data['nombre'] ?? $this->nombre,
            'descripcion' => $data['descripcion'] ?? $this->descripcion,
            'fecha_implementacion' => $data['fecha_implementacion'] ?? now()->toDateString(),
            'responsable' => $data['responsable'] ?? $this->responsable,
            'actividades' => $data['actividades'] ?? $this->actividades,
            'version' => $siguienteVersion,
            'estado' => 'vigente',
            'protocolo_base_id' => $protocoloBaseId,
        ]);
    }

    // Obtener nombre completo with versión
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' (v' . $this->version . ')';
    }

    /**
     * Verificar si el protocolo tiene insumos definidos
     */
    public function tieneInsumos()
    {
        return $this->insumos()->exists();
    }

    /**
     * Verificar si todos los insumos tienen stock suficiente
     */
    public function tieneStockSuficiente()
    {
        return $this->insumos->every(function($insumo) {
            return $insumo->tieneStockSuficiente();
        });
    }

    /**
     * Obtener el costo total estimado de todos los insumos
     */
    public function getCostoTotalInsumosAttribute()
    {
        return $this->insumos->sum('costo_estimado');
    }

    /**
     * Accesor: actividades normalizadas para protocolos antiguos con una sola actividad larga.
     */
    public function getActividadesNormalizadasAttribute()
    {
        $acts = $this->actividades ?? [];
        if (is_array($acts) && count($acts) === 1 && is_string($acts[0])) {
            $raw = trim($acts[0]);
            $hasDelimiters = preg_match('/[\r\n;|]/', $raw) || preg_match('/\d+\s*[).:-]\s+/', $raw) || strpos($raw, ',') !== false;
            if ($hasDelimiters) {
                $segments = preg_split('/[\r\n;|]+/', $raw);
                if (count($segments) === 1) {
                    if (preg_match('/\d+\s*[).:-]\s+/', $raw)) {
                        $tmp = preg_split('/\s*\d+\s*[).:-]\s*/', $raw);
                        $tmp = array_filter(array_map('trim', $tmp));
                        if (count($tmp) > 1) {
                            $segments = $tmp;
                        }
                    }
                }
                if (count($segments) === 1 && strpos($segments[0], ',') !== false) {
                    $commaParts = array_map('trim', explode(',', $segments[0]));
                    if (count($commaParts) > 1) {
                        $segments = $commaParts;
                    }
                }
                $segments = array_values(array_filter(array_map('trim', $segments), fn($s) => $s !== ''));
                if (count($segments) > 1) {
                    return $segments;
                }
            }
        }
        return $acts;
    }

    /**
     * Ejecutar protocolo y descontar insumos del inventario
     */
    public function ejecutarYDescontarInsumos($observaciones = null)
    {
        try {
            DB::beginTransaction();

            // Verificar que todos los insumos obligatorios tengan stock suficiente
            foreach ($this->insumos->where('es_obligatorio', true) as $insumo) {
                if (!$insumo->tieneStockSuficiente()) {
                    throw new \Exception("Stock insuficiente para el insumo obligatorio: {$insumo->inventarioItem->nombre}");
                }
            }

            // Descontar insumos del inventario
            foreach ($this->insumos as $insumo) {
                if ($insumo->tieneStockSuficiente()) {
                    $this->descontarInsumo($insumo);
                }
            }

            // Marcar protocolo como ejecutado
            $this->update([
                'fecha_ejecucion' => now(),
                'observaciones_ejecucion' => $observaciones,
                'estado' => 'ejecutado'
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Descontar un insumo específico del inventario
     */
    private function descontarInsumo($protocoloInsumo)
    {
        $inventarioItem = $protocoloInsumo->inventarioItem;
        $cantidadNecesaria = $protocoloInsumo->cantidad_necesaria;

        // Obtener existencias del item ordenadas por fecha de vencimiento (FIFO)
        $existencias = $inventarioItem->existencias()
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        $cantidadRestante = $cantidadNecesaria;

        foreach ($existencias as $existencia) {
            if ($cantidadRestante <= 0) break;

            $cantidadADescontar = min($cantidadRestante, $existencia->cantidad_disponible);

            // Crear movimiento de salida
            \App\Models\InventarioMovimiento::create([
                'inventario_item_id' => $inventarioItem->id,
                'tipo_movimiento' => 'salida',
                'cantidad' => $cantidadADescontar,
                'fecha_movimiento' => now(),
                'concepto' => "Protocolo de sanidad ID: {$this->id}",
                'referencia_tipo' => 'protocolo_sanidad',
                'referencia_id' => $this->id,
                'costo_unitario' => $existencia->costo_unitario ?? $inventarioItem->costo_unitario,
                'bodega_id' => $existencia->bodega_id
            ]);

            // Actualizar existencia
            $existencia->update([
                'cantidad_disponible' => $existencia->cantidad_disponible - $cantidadADescontar
            ]);

            $cantidadRestante -= $cantidadADescontar;
        }

        if ($cantidadRestante > 0) {
            throw new \Exception("No se pudo descontar completamente el insumo: {$inventarioItem->nombre}");
        }
    }
}
