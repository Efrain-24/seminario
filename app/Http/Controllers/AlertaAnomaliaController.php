<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Services\AlertaAnomaliasService;
use Illuminate\Support\Facades\DB;

class AlertaAnomaliaController extends Controller
{
    public function index(Request $request)
    {
        $tipoAlerta = $request->input('tipo_alerta', '');
        $loteId = $request->input('lote_id', '');

        $query = DB::table('alertas')
            ->join('lotes', 'alertas.lote_id', '=', 'lotes.id')
            ->join('unidad_produccions', 'lotes.unidad_produccion_id', '=', 'unidad_produccions.id')
            ->select(
                'alertas.*',
                'lotes.codigo_lote',
                'lotes.cantidad_inicial',
                'lotes.cantidad_actual',
                'unidad_produccions.area',
                'unidad_produccions.profundidad',
                'unidad_produccions.capacidad_maxima',
                DB::raw('((lotes.cantidad_inicial - lotes.cantidad_actual) / lotes.cantidad_inicial * 100) as porcentaje_mortalidad'),
                DB::raw('(lotes.cantidad_actual / (unidad_produccions.area * unidad_produccions.profundidad)) as densidad_actual'),
                DB::raw('((lotes.cantidad_actual / (unidad_produccions.area * unidad_produccions.profundidad)) / unidad_produccions.capacidad_maxima * 100) as porcentaje_capacidad')
            )
            ->when($request->input('tipo_alerta') === 'mortalidad', function($query) {
                return $query->whereRaw('((lotes.cantidad_inicial - lotes.cantidad_actual) / lotes.cantidad_inicial * 100) >= 45');
            })
            ->when($request->input('tipo_alerta') === 'densidad', function($query) {
                return $query->whereRaw('(lotes.cantidad_actual / (unidad_produccions.area * unidad_produccions.profundidad)) > unidad_produccions.capacidad_maxima');
            })
            ->when($request->input('tipo_alerta') === 'enfermedad', function($query) {
                return $query->where('alertas.tipo_alerta', 'enfermedad')
                           ->where(function($q) {
                               $q->where('alertas.nivel_riesgo', 'alto')
                                 ->orWhere('alertas.porcentaje_afectados', '>=', 50);
                           });
            })
            ->when($request->input('tipo_alerta') === 'bajo_peso', function($query) {
                return $query->where('alertas.tipo_alerta', 'bajo peso')
                           ->where(function($q) {
                               $q->where('alertas.porcentaje_desviacion', '<=', -15) // Desviación mayor al 15% por debajo
                                 ->orWhere('alertas.dias_desviacion', '>=', 7); // 7 días o más con peso bajo
                           });
            });

        if ($tipoAlerta) {
            $query->where('alertas.tipo_alerta', $tipoAlerta);
        }

        if ($loteId) {
            $query->where('alertas.lote_id', $loteId);
        }

        $alertas = $query->get()->toArray(); // Convertir resultados a arreglo

        $lotes = Lote::orderBy('codigo_lote')->get();

        return view('produccion.alertas.index', [
            'lotes' => $lotes,
            'loteId' => $loteId,
            'tipoAlerta' => $tipoAlerta,
            'alertas' => $alertas,
        ]);
    }
}
