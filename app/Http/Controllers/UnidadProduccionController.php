<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UnidadProduccion;
use App\Models\Lote;
use App\Models\MantenimientoUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnidadProduccionController extends Controller
{
    /**
     * Mostrar lista de unidades de producción con filtros
     */
    public function index(Request $request)
    {
        $query = UnidadProduccion::withCount('lotes');

        // Filtro de búsqueda por nombre o código
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        // Ordenar por fecha de creación (más recientes primero)
        $unidades = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('unidades.index', compact('unidades'));
    }

    /**
     * Mostrar formulario de creación de unidad
     */
    public function create()
    {
        return view('unidades.create');
    }

    /**
     * Guardar nueva unidad de producción
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:tanque,estanque,jaula,sistema_especializado',
            'capacidad_maxima' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'profundidad' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'fecha_construccion' => 'nullable|date'
        ]);

        // El código SIEMPRE se genera automáticamente
        unset($validated['codigo']);

        $unidad = UnidadProduccion::create($validated);

        return redirect()->route('unidades.index')
                        ->with('success', "Unidad de producción creada exitosamente con código: {$unidad->codigo}");
    }

    /**
     * Mostrar detalles de una unidad específica
     */
    public function show(UnidadProduccion $unidad)
    {
        $unidad->load(['lotes', 'mantenimientos' => function($query) {
            $query->orderBy('fecha_mantenimiento', 'desc')->take(10);
        }]);
        
        $estadisticas_mantenimiento = [
            'total' => $unidad->mantenimientos->count(),
            'completados' => $unidad->mantenimientos->where('estado_mantenimiento', 'completado')->count(),
            'pendientes' => $unidad->mantenimientos->where('estado_mantenimiento', 'programado')->count(),
            'en_proceso' => $unidad->mantenimientos->where('estado_mantenimiento', 'en_proceso')->count(),
            'proximo' => $unidad->mantenimientos->where('estado_mantenimiento', 'programado')->first()
        ];
        
        return view('unidades.show', compact('unidad', 'estadisticas_mantenimiento'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(UnidadProduccion $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    /**
     * Actualizar unidad de producción
     */
    public function update(Request $request, UnidadProduccion $unidad)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:tanque,estanque,jaula,sistema_especializado',
            'capacidad_maxima' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'profundidad' => 'nullable|numeric|min:0',
            'fecha_construccion' => 'nullable|date',
            'descripcion' => 'nullable|string'
        ]);

        $unidad->update($validated);

        return redirect()->route('unidades.show', $unidad)
                        ->with('success', 'Unidad de producción actualizada exitosamente.');
    }

    /**
     * Eliminar unidad de producción
     */
    public function destroy(UnidadProduccion $unidad)
    {
        // Verificar que no tenga lotes activos
        if ($unidad->lotes()->count() > 0) {
            return redirect()->route('unidades.index')
                            ->with('error', 'No se puede eliminar la unidad porque tiene lotes asociados.');
        }

        $unidad->delete();

        return redirect()->route('unidades.index')
                        ->with('success', 'Unidad de producción eliminada exitosamente.');
    }

    /**
     * Generar código automático para unidad
     */
    public function generateCodigo($tipo)
    {
        if (!in_array($tipo, ['tanque', 'estanque', 'jaula', 'sistema_especializado'])) {
            return response()->json(['error' => 'Tipo de unidad inválido'], 400);
        }

        $codigo = UnidadProduccion::generateCodigo($tipo);
        
        return response()->json(['codigo' => $codigo]);
    }

    /**
     * Inhabilitar/habilitar unidad
     */
    public function toggleEstado(UnidadProduccion $unidad)
    {
        $nuevoEstado = $unidad->estado == 'activa' ? 'inactiva' : 'activa';
        $unidad->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 'activa' ? 'habilitada' : 'inhabilitada';
        
        return redirect()->back()
                        ->with('success', "Unidad {$mensaje} exitosamente.");
    }

    /**
     * Historial de una unidad específica
     */
    public function historial(UnidadProduccion $unidad)
    {
        $unidad->load([
            'lotes' => function($query) {
                $query->orderBy('fecha_siembra', 'desc');
            },
            'mantenimientos' => function($query) {
                $query->orderBy('fecha_mantenimiento', 'desc');
            }
        ]);

        return view('unidades.historial', compact('unidad'));
    }
}
