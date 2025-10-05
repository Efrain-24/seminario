<?php

namespace App\Services;

use App\Models\Trazabilidad\TrazabilidadCosecha;
use App\Models\Lote;
use Illuminate\Support\Facades\DB;

class TrazabilidadService
{
    /**
     * Registra una nueva cosecha en el sistema de trazabilidad
     * 
     * @param array $datos Datos de la cosecha a registrar
     * @return TrazabilidadCosecha
     * @throws \Exception Si hay error en el registro
     */
    public function registrarCosecha(array $datos)
    {
        DB::beginTransaction();
        try {
            // Convertir valores string a números
            $costoManoObra = floatval($datos['costo_mano_obra']);
            $costoInsumos = floatval($datos['costo_insumos']);
            $costoOperativo = floatval($datos['costo_operativo']);
            $pesoBruto = floatval($datos['peso_bruto']);
            $pesoNeto = floatval($datos['peso_neto']);
            $unidades = isset($datos['unidades']) ? intval($datos['unidades']) : null;
            
            // Calculamos el costo total sumando todos los componentes
            $costoTotal = $costoManoObra + $costoInsumos + $costoOperativo;
            
            // Creamos el registro de trazabilidad
            $trazabilidad = TrazabilidadCosecha::create([
                'lote_id' => $datos['lote_id'],
                'fecha_cosecha' => $datos['fecha_cosecha'],
                'tipo_cosecha' => $datos['tipo_cosecha'],
                'peso_bruto' => $pesoBruto,
                'peso_neto' => $pesoNeto,
                'unidades' => $unidades,
                'costo_mano_obra' => $costoManoObra,
                'costo_insumos' => $costoInsumos,
                'costo_operativo' => $costoOperativo,
                'costo_total' => $costoTotal,
                'destino_tipo' => $datos['destino_tipo'],
                'destino_detalle' => $datos['destino_detalle'],
                'notas' => $datos['notas'] ?? null
            ]);

            // Si es una cosecha total, marcamos el lote como cosechado
            if ($datos['tipo_cosecha'] === 'total') {
                $lote = Lote::find($datos['lote_id']);
                $lote->update(['estado' => 'cosechado']);
            }

            DB::commit();
            return $trazabilidad;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Obtiene un resumen detallado de las cosechas de un lote específico
     * 
     * @param int $loteId ID del lote a consultar
     * @return array Resumen con totales y detalles de cosechas
     */
    public function obtenerResumenLote($loteId)
    {
        $cosechas = TrazabilidadCosecha::where('lote_id', $loteId)
            ->orderBy('fecha_cosecha', 'asc')
            ->get();

        $resumen = [
            'total_cosechado' => $cosechas->sum('peso_neto'),
            'total_costos' => $cosechas->sum('costo_total'),
            'numero_cosechas' => $cosechas->count(),
            'cosechas_parciales' => $cosechas->where('tipo_cosecha', 'parcial')->count(),
            'detalle_cosechas' => $cosechas->map(function ($cosecha) {
                return [
                    'fecha' => $cosecha->fecha_cosecha->format('d/m/Y'),
                    'tipo' => $cosecha->tipo_cosecha,
                    'peso_neto' => $cosecha->peso_neto,
                    'costo_total' => $cosecha->costo_total,
                    'destino' => $cosecha->destino_detalle
                ];
            })
        ];

        return $resumen;
    }

    /**
     * Genera un reporte estadístico de cosechas por período
     * 
     * @param string $fechaInicio Fecha inicial del período
     * @param string $fechaFin Fecha final del período
     * @return array Estadísticas agrupadas por tipo de destino
     */
    public function reportePeriodo($fechaInicio, $fechaFin)
    {
        return TrazabilidadCosecha::whereBetween('fecha_cosecha', [$fechaInicio, $fechaFin])
            ->with('lote')
            ->get()
            ->groupBy('destino_tipo')
            ->map(function ($grupo) {
                return [
                    'total_peso' => $grupo->sum('peso_neto'),
                    'total_costo' => $grupo->sum('costo_total'),
                    'numero_cosechas' => $grupo->count(),
                    'costo_promedio' => $grupo->avg('costo_total')
                ];
            });
    }
}
