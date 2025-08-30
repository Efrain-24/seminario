<?php

namespace App\Http\Controllers;

use App\Models\CosechaParcial;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CosechaParcialController extends Controller
{
    public function index()
    {
    $q = CosechaParcial::with('lote')->latest('fecha');
    if ($loteId = request('lote_id')) $q->where('lote_id', $loteId);
    if ($desde  = request('desde'))   $q->whereDate('fecha', '>=', $desde);
    if ($hasta  = request('hasta'))   $q->whereDate('fecha', '<=', $hasta);

    $cosechas = $q->paginate(12)->withQueryString();
    $lotes = \App\Models\Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);
    return view('cosechas.index', compact('cosechas', 'lotes'));
    }

    public function create()
    {
        // Usamos codigo_lote como "nombre" para el <select>
        $lotes = Lote::orderBy('codigo_lote')
            ->get(['id', 'codigo_lote as nombre', 'cantidad_actual']);

        return view('cosechas.create', compact('lotes'));
    }

    // ✅ ESTE ES EL QUE FALTABA
    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id'            => ['required', 'exists:lotes,id'],
            'fecha'              => ['required', 'date'],
            'cantidad_cosechada' => ['required', 'integer', 'min:1'],
            'peso_cosechado_kg'  => ['nullable', 'numeric', 'min:0'],
            'destino'            => ['required', 'in:venta,consumo,muestra,otro'],
            'responsable'        => ['nullable', 'string', 'max:120'],
            'observaciones'      => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $lote = Lote::lockForUpdate()->findOrFail($data['lote_id']);

            if ($data['cantidad_cosechada'] > $lote->cantidad_actual) {
                $v = Validator::make([], []);
                $v->errors()->add(
                    'cantidad_cosechada',
                    'La cantidad cosechada no puede superar el stock actual del lote (' . $lote->cantidad_actual . ').'
                );
                throw new \Illuminate\Validation\ValidationException($v);
            }

            CosechaParcial::create([
                'lote_id'            => $data['lote_id'],
                'fecha'              => $data['fecha'],
                'cantidad_cosechada' => $data['cantidad_cosechada'],
                'peso_cosechado_kg'  => $data['peso_cosechado_kg'] ?? null,
                'destino'            => $data['destino'],
                'responsable'        => $data['responsable'] ?? null,
                'observaciones'      => $data['observaciones'] ?? null,
            ]);

            // Descontar del stock
            $lote->decrement('cantidad_actual', (int) $data['cantidad_cosechada']);
        });

        return redirect()
            ->route('produccion.cosechas.index')
            ->with('success', 'Cosecha parcial registrada correctamente.');
    }

    public function edit(CosechaParcial $cosecha)
    {
        $cosecha->load('lote');

        // Trae TODOS los lotes como MODELOS con alias 'nombre'
        $lotes = Lote::select('id', 'codigo_lote as nombre', 'cantidad_actual')
            ->orderBy('codigo_lote')
            ->get();

        return view('cosechas.edit', compact('cosecha', 'lotes'));
    }


    public function update(Request $request, CosechaParcial $cosecha)
    {
        $data = $request->validate([
            'fecha'              => ['required', 'date'],
            'cantidad_cosechada' => ['required', 'integer', 'min:1'],
            'peso_cosechado_kg'  => ['nullable', 'numeric', 'min:0'],
            'destino'            => ['required', 'in:venta,consumo,muestra,otro'],
            'responsable'        => ['nullable', 'string', 'max:120'],
            'observaciones'      => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $cosecha) {
            $cosecha->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($cosecha->lote_id);

            $anterior = (int) $cosecha->cantidad_cosechada;
            $nueva    = (int) $data['cantidad_cosechada'];
            $delta    = $nueva - $anterior; // + => se descuenta más; - => se devuelve stock

            if ($delta > 0 && $delta > $lote->cantidad_actual) {
                $v = Validator::make([], []);
                $v->errors()->add(
                    'cantidad_cosechada',
                    'El ajuste excede el stock disponible del lote (' . $lote->cantidad_actual . ').'
                );
                throw new \Illuminate\Validation\ValidationException($v);
            }

            $cosecha->update($data);

            if ($delta > 0) {
                $lote->decrement('cantidad_actual', $delta);
            } elseif ($delta < 0) {
                $lote->increment('cantidad_actual', -$delta);
            }
        });

        return redirect()
            ->route('produccion.cosechas.index')
            ->with('success', 'Cosecha parcial actualizada.');
    }

    public function destroy(CosechaParcial $cosecha)
    {
        DB::transaction(function () use ($cosecha) {
            $cosecha->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($cosecha->lote_id);

            // Revertir stock
            $lote->increment('cantidad_actual', (int) $cosecha->cantidad_cosechada);
            $cosecha->delete();
        });

        return back()->with('success', 'Cosecha parcial eliminada y stock revertido.');
    }
}
