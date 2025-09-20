<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\UnidadProduccion;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['unidadProduccion', 'seguimientos'])
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produccion.lotes.index', compact('lotes'));
    }

    public function show(Lote $lote)
    {
        $lote->load(['unidadProduccion', 'seguimientos', 'traslados']);
        
        return view('produccion.lotes.show', compact('lote'));
    }

    public function create()
    {
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        
        return view('produccion.lotes.create', compact('unidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_lote' => 'required|string|unique:lotes',
            'especie' => 'required|string',
            'cantidad_inicial' => 'required|integer|min:1',
            'peso_promedio_inicial' => 'required|numeric|min:0',
            'talla_promedio_inicial' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ]);

        $validated['cantidad_actual'] = $validated['cantidad_inicial'];
        $validated['estado'] = 'activo';

        $lote = Lote::create($validated);

        return redirect()->route('lotes.show', $lote)
            ->with('success', 'Lote creado exitosamente.');
    }

    public function edit(Lote $lote)
    {
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        
        return view('produccion.lotes.edit', compact('lote', 'unidades'));
    }

    public function update(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'codigo_lote' => 'required|string|unique:lotes,codigo_lote,' . $lote->id,
            'especie' => 'required|string',
            'cantidad_inicial' => 'required|integer|min:1',
            'peso_promedio_inicial' => 'required|numeric|min:0',
            'talla_promedio_inicial' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ]);

        $lote->update($validated);

        return redirect()->route('lotes.show', $lote)
            ->with('success', 'Lote actualizado exitosamente.');
    }

    public function destroy(Lote $lote)
    {
        $lote->update(['estado' => 'inactivo']);

        return redirect()->route('lotes.index')
            ->with('success', 'Lote desactivado exitosamente.');
    }

    public function historial(Lote $lote)
    {
        $seguimientos = $lote->seguimientos()
            ->with('usuario')
            ->orderBy('fecha_seguimiento', 'desc')
            ->paginate(15);

        return view('produccion.lotes.historial', compact('lote', 'seguimientos'));
    }
}
