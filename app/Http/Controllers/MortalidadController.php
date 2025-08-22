<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Mortalidad;
use App\Models\Lote;

class MortalidadController extends Controller
{
    public function index()
    {
        $q = Mortalidad::with('lote')->latest('fecha');

        if ($loteId = request('lote_id')) $q->where('lote_id', $loteId);
        if ($desde  = request('desde'))   $q->whereDate('fecha', '>=', $desde);
        if ($hasta  = request('hasta'))   $q->whereDate('fecha', '<=', $hasta);

        $mortalidades = $q->paginate(12)->withQueryString();
        $lotes = Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);

        return view('mortalidades.index', compact('mortalidades', 'lotes'));
    }

    public function create()
    {
        $lotes = Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote as nombre', 'cantidad_actual']);
        return view('mortalidades.create', compact('lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id'       => ['required', 'exists:lotes,id'],
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

            Mortalidad::create($data + ['user_id' => Auth::id()]); // ← aquí el cambio
            $lote->decrement('cantidad_actual', (int)$data['cantidad']);
        });

        return redirect()->route('produccion.mortalidades.index')
            ->with('success', 'Mortalidad registrada.');
    }

    public function edit(Mortalidad $mortalidad)
    {
        $mortalidad->load('lote');
        $lotes = Lote::select('id', 'codigo_lote as nombre', 'cantidad_actual')
            ->where('id', $mortalidad->lote_id)->get();

        return view('mortalidades.edit', compact('mortalidad', 'lotes'));
    }

    // ✅ MÉTODO QUE FALTABA
    public function update(Request $request, Mortalidad $mortalidad)
    {
        $data = $request->validate([
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
            $delta    = $nueva - $anterior; // +: restar más stock, -: devolver stock

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
