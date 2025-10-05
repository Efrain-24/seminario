<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use App\Models\Mortalidad;
use App\Models\Lote;
use App\Models\UnidadProduccion;

class MortalidadController extends Controller
{
    public function logPorUnidad($unidadId)
    {
        $unidad = \App\Models\UnidadProduccion::findOrFail($unidadId);
        $mortalidades = \App\Models\Mortalidad::with('lote')
            ->where('unidad_produccion_id', $unidadId)
            ->orderByDesc('fecha')
            ->paginate(20);
        return view('unidad_produccions.mortalidad_log', compact('unidad', 'mortalidades'));
    }
    public function index()
    {
        $q = Mortalidad::with(['lote', 'unidadProduccion'])->latest('fecha');

        if ($unidadId = request('unidad_produccion_id')) $q->where('unidad_produccion_id', $unidadId);
        if ($loteId = request('lote_id')) $q->where('lote_id', $loteId);
        if ($desde  = request('desde'))   $q->whereDate('fecha', '>=', $desde);
        if ($hasta  = request('hasta'))   $q->whereDate('fecha', '<=', $hasta);

        $mortalidades = $q->paginate(12)->withQueryString();
        $lotes = Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);
        $unidades = \App\Models\UnidadProduccion::orderBy('nombre')->get(['id', 'nombre', 'codigo', 'tipo']);

        return view('mortalidades.index', compact('mortalidades', 'lotes', 'unidades'));
    }

    public function create()
    {
        $lotes = Lote::where('estado', 'activo')->orderBy('codigo_lote')->get(['id', 'codigo_lote as nombre', 'cantidad_actual', 'unidad_produccion_id']);
        $unidades = UnidadProduccion::orderBy('nombre')->get(['id', 'nombre', 'codigo', 'tipo']);
        return view('mortalidades.create', compact('lotes', 'unidades'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        // Determinar la causa final
        if (($input['causa_select'] ?? '') === 'Otro') {
            $input['causa'] = $input['causa'] ?? null;
        } else {
            $input['causa'] = $input['causa_select'] ?? null;
        }
        $data = $request->merge(['causa' => $input['causa']])->validate([
            'lote_id'       => ['required', 'exists:lotes,id'],
            'unidad_produccion_id' => ['required', 'exists:unidad_produccions,id'],
            'fecha'         => ['required', 'date'],
            'cantidad'      => ['required', 'integer', 'min:1'],
            'causa'         => ['nullable', 'string', 'max:160'],
            'observaciones' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $lote = Lote::lockForUpdate()->findOrFail($data['lote_id']);

            if ((int)$data['cantidad'] > (int)$lote->cantidad_actual) {
                abort(422, 'La cantidad reportada excede el stock actual del lote.');
            }

            Mortalidad::create($data + ['user_id' => Auth::id()]);
            $lote->decrement('cantidad_actual', (int)$data['cantidad']);
        });

        return redirect()->route('produccion.mortalidades.index')
            ->with('success', 'Mortalidad registrada.');
    }

    public function edit(Mortalidad $mortalidad)
    {
        $mortalidad->load('lote');
        $lotes = Lote::select('id', 'codigo_lote as nombre', 'cantidad_actual', 'unidad_produccion_id')->get();
        $unidades = UnidadProduccion::orderBy('nombre')->get(['id', 'nombre', 'codigo', 'tipo']);
        return view('mortalidades.edit', compact('mortalidad', 'lotes', 'unidades'));
    }

    // ✅ MÉTODO QUE FALTABA
    public function update(Request $request, Mortalidad $mortalidad)
    {
        $input = $request->all();
        if (($input['causa_select'] ?? '') === 'Otro') {
            $input['causa'] = $input['causa'] ?? null;
        } else {
            $input['causa'] = $input['causa_select'] ?? null;
        }
        $data = $request->merge(['causa' => $input['causa']])->validate([
            'lote_id'       => ['required', 'exists:lotes,id'],
            'unidad_produccion_id' => ['required', 'exists:unidad_produccions,id'],
            'fecha'         => ['required', 'date'],
            'cantidad'      => ['required', 'integer', 'min:1'],
            'causa'         => ['nullable', 'string', 'max:160'],
            'observaciones' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $mortalidad) {
            $mortalidad->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($mortalidad->lote_id);

            $anterior = (int)$mortalidad->cantidad;
            $nueva    = (int)$data['cantidad'];
            $delta    = $nueva - $anterior;

            if ($delta > 0 && $delta > $lote->cantidad_actual) {
                abort(422, 'El ajuste excede el stock disponible del lote.');
            }

            $mortalidad->update($data);

            if ($delta > 0) $lote->decrement('cantidad_actual',  $delta);
            elseif ($delta < 0) $lote->increment('cantidad_actual', -$delta);
        });

        return redirect()->route('produccion.mortalidades.index')
            ->with('success', 'Mortalidad actualizada.');
    }

    public function charts(Request $request)
    {

        $unidadId = $request->input('unidad_produccion_id');
        $lotesQuery = Lote::orderBy('codigo_lote');
        if ($unidadId) {
            $lotesQuery->where('unidad_produccion_id', $unidadId);
        }
        $lotes = $lotesQuery->get(['id', 'codigo_lote', 'cantidad_actual', 'cantidad_inicial', 'unidad_produccion_id']);

        // Para el filtro de unidades
        $unidades = \App\Models\UnidadProduccion::orderBy('nombre')->get(['id', 'nombre', 'codigo', 'tipo']);


        if ($lotes->isEmpty()) {
            return back()->with('success', 'Primero crea al menos un lote.');
        }

    $loteId = (int) $request->input('lote_id', $lotes->first()->id);
        $desde  = $request->input('desde', now()->subDays(30)->toDateString());
        $hasta  = $request->input('hasta', now()->toDateString());
        $group  = $request->input('group', 'day'); // day|week|month

    // Población base para calcular la tasa: SIEMPRE la cantidad_inicial del lote seleccionado
    $loteSel   = $lotes->firstWhere('id', $loteId);
    $stockBase = (int) ($loteSel->cantidad_inicial ?? 0);

        // 1) Traer muertes por día dentro del rango
        $base = Mortalidad::where('lote_id', $loteId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('DATE(fecha) as d, SUM(cantidad) as muertes')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d'); // 'YYYY-MM-DD' => { d, muertes }

        // 2) Rellenar días faltantes en el rango con 0
        $period = CarbonPeriod::create($desde, $hasta);
        $porDia = collect();
        foreach ($period as $day) {
            $key = $day->toDateString();
            $porDia[$key] = (int) ($base[$key]->muertes ?? 0);
        }

        // 3) Agrupar según $group
        $labels = [];
        $muertes = [];

        if ($group === 'day') {
            $labels  = array_keys($porDia->toArray());
            $muertes = array_values($porDia->toArray());
        } else {
            $bucketed = [];
            foreach ($porDia as $fecha => $cant) {
                $c = Carbon::parse($fecha);
                if ($group === 'week') {
                    // Semana ISO: Año-Semana (ej. 2025-W34)
                    $label = $c->isoFormat('GGGG-[W]WW');
                } else { // month
                    $label = $c->format('Y-m'); // 2025-08
                }
                $bucketed[$label] = ($bucketed[$label] ?? 0) + $cant;
            }
            $labels  = array_keys($bucketed);
            $muertes = array_values($bucketed);
        }

        // 4) Tasa de mortalidad (%) por bucket: muertes / stock_base * 100
        $tasas = [];
        foreach ($muertes as $n) {
            $tasas[] = ($stockBase > 0) ? round(($n / $stockBase) * 100, 4) : 0;
        }

        return view('mortalidades.charts', [
            'lotes'      => $lotes,
            'loteId'     => $loteId,
            'desde'      => $desde,
            'hasta'      => $hasta,
            'group'      => $group,
            'stockBase'  => $stockBase,
            'labels'     => $labels,
            'muertes'    => $muertes,
            'tasas'      => $tasas,
            'unidades'   => $unidades,
            'unidadId'   => $unidadId,
        ]);
    }

    public function destroy(Mortalidad $mortalidad)
    {
        DB::transaction(function () use ($mortalidad) {
            $mortalidad->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($mortalidad->lote_id);

            $lote->increment('cantidad_actual', (int)$mortalidad->cantidad);
            $mortalidad->delete();
        });

        return back()->with('success', 'Registro eliminado y stock revertido.');
    }
}
