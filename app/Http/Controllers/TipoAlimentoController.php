<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoAlimento;

class TipoAlimentoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoAlimento::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === 'true');
        }

        $tiposAlimento = $query->orderBy('nombre')
                              ->orderBy('marca')
                              ->paginate(15);

        $categorias = TipoAlimento::getCategorias();
        
        return view('tipos-alimento.index', compact('tiposAlimento', 'categorias'));
    }

    public function create()
    {
        $categorias = TipoAlimento::getCategorias();
        $presentaciones = TipoAlimento::getPresentaciones();
        
        return view('tipos-alimento.create', compact('categorias', 'presentaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'categoria' => 'required|in:' . implode(',', array_keys(TipoAlimento::getCategorias())),
            'proteina' => 'nullable|numeric|min:0|max:100',
            'grasa' => 'nullable|numeric|min:0|max:100',
            'fibra' => 'nullable|numeric|min:0|max:100',
            'humedad' => 'nullable|numeric|min:0|max:100',
            'ceniza' => 'nullable|numeric|min:0|max:100',
            'presentacion' => 'nullable|in:' . implode(',', array_keys(TipoAlimento::getPresentaciones())),
            'peso_presentacion' => 'nullable|numeric|min:0.01',
            'costo_por_kg' => 'nullable|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean'
        ]);

        $tipoAlimento = TipoAlimento::create($validated);

        return redirect()->route('tipos-alimento.index')
                        ->with('success', 'Tipo de alimento creado exitosamente.');
    }

    public function show(TipoAlimento $tipoAlimento)
    {
        $tipoAlimento->load('alimentaciones.lote.unidadProduccion');
        
        // Estadísticas de uso
        $estadisticas = [
            'total_usos' => $tipoAlimento->alimentaciones()->count(),
            'cantidad_total_usada' => $tipoAlimento->alimentaciones()->sum('cantidad_kg'),
            'costo_total_usado' => $tipoAlimento->alimentaciones()->sum('costo_total'),
            'ultimo_uso' => $tipoAlimento->alimentaciones()->latest('fecha_alimentacion')->first()?->fecha_alimentacion,
        ];
        
        return view('tipos-alimento.show', compact('tipoAlimento', 'estadisticas'));
    }

    public function edit(TipoAlimento $tipoAlimento)
    {
        $categorias = TipoAlimento::getCategorias();
        $presentaciones = TipoAlimento::getPresentaciones();
        
        return view('tipos-alimento.edit', compact('tipoAlimento', 'categorias', 'presentaciones'));
    }

    public function update(Request $request, TipoAlimento $tipoAlimento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'categoria' => 'required|in:' . implode(',', array_keys(TipoAlimento::getCategorias())),
            'proteina' => 'nullable|numeric|min:0|max:100',
            'grasa' => 'nullable|numeric|min:0|max:100',
            'fibra' => 'nullable|numeric|min:0|max:100',
            'humedad' => 'nullable|numeric|min:0|max:100',
            'ceniza' => 'nullable|numeric|min:0|max:100',
            'presentacion' => 'nullable|in:' . implode(',', array_keys(TipoAlimento::getPresentaciones())),
            'peso_presentacion' => 'nullable|numeric|min:0.01',
            'costo_por_kg' => 'nullable|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean'
        ]);

        $tipoAlimento->update($validated);

        return redirect()->route('tipos-alimento.show', $tipoAlimento)
                        ->with('success', 'Tipo de alimento actualizado exitosamente.');
    }

    public function destroy(TipoAlimento $tipoAlimento)
    {
        // Verificar si el tipo de alimento está siendo usado
        if ($tipoAlimento->alimentaciones()->exists()) {
            return redirect()->route('tipos-alimento.index')
                           ->with('error', 'No se puede eliminar este tipo de alimento porque está siendo usado en registros de alimentación.');
        }

        $tipoAlimento->delete();

        return redirect()->route('tipos-alimento.index')
                        ->with('success', 'Tipo de alimento eliminado exitosamente.');
    }

    public function toggle(TipoAlimento $tipoAlimento)
    {
        $tipoAlimento->update(['activo' => !$tipoAlimento->activo]);
        
        $estado = $tipoAlimento->activo ? 'activado' : 'desactivado';
        
        return redirect()->back()
                        ->with('success', "Tipo de alimento {$estado} exitosamente.");
    }
}
