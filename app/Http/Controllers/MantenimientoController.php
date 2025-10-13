<?php

namespace App\Http\Controllers;

use App\Models\MantenimientoUnidad;
use App\Models\UnidadProduccion;
use App\Models\User;
use App\Models\InventarioItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MantenimientoController extends Controller
{
    /**
     * Panel principal de mantenimientos
     */
    public function panel()
    {
        $estadisticas = $this->obtenerEstadisticas();
        $mantenimientosRecientes = MantenimientoUnidad::with(['unidadProduccion', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $mantenimientosVencidos = MantenimientoUnidad::vencidos()
            ->with(['unidadProduccion', 'usuario'])
            ->limit(10)
            ->get();
        
        $mantenimientosProximos = MantenimientoUnidad::proximos(7)
            ->with(['unidadProduccion', 'usuario'])
            ->limit(10)
            ->get();

        return view('mantenimientos.panel', compact(
            'estadisticas', 
            'mantenimientosRecientes', 
            'mantenimientosVencidos', 
            'mantenimientosProximos'
        ));
    }

    /**
     * Lista de todos los mantenimientos
     */
    public function index(Request $request)
    {
        $query = MantenimientoUnidad::with(['unidadProduccion', 'usuario']);

        // Filtros
        if ($request->filled('unidad_id')) {
            $query->where('unidad_produccion_id', $request->unidad_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado_mantenimiento', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_mantenimiento', $request->tipo);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_mantenimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_mantenimiento', '<=', $request->fecha_hasta);
        }

        $mantenimientos = $query->orderBy('fecha_mantenimiento', 'desc')->paginate(15);
        $unidades = UnidadProduccion::orderBy('codigo')->get();

        return view('mantenimientos.index', compact('mantenimientos', 'unidades'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Request $request)
    {
        $unidades = UnidadProduccion::where('estado', '!=', 'inactivo')
            ->orderBy('codigo')
            ->get();
        
        $usuarios = User::orderBy('name')->get();
        $inventarioItems = InventarioItem::where('categoria', 'insumo')
            ->orderBy('nombre')
            ->get();
        
        $unidadSeleccionada = null;
        if ($request->filled('unidad_id')) {
            $unidadSeleccionada = UnidadProduccion::find($request->unidad_id);
        }

        return view('mantenimientos.create', compact(
            'unidades', 
            'usuarios', 
            'inventarioItems',
            'unidadSeleccionada'
        ));
    }

    /**
     * Almacenar nuevo mantenimiento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'tipo_mantenimiento' => 'required|in:preventivo,correctivo,limpieza,reparacion,inspeccion,desinfeccion',
            'descripcion_trabajo' => 'required|string|max:1000',
            'fecha_mantenimiento' => 'required|date|after_or_equal:today',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'user_id' => 'required|exists:users,id',
            'observaciones_antes' => 'nullable|string|max:1000',
            'requiere_vaciado' => 'boolean',
            'requiere_traslado_peces' => 'boolean',
            'costo_estimado' => 'nullable|numeric|min:0',
            // Campos para insumos
            'insumos' => 'nullable|array',
            'insumos.*.item_id' => 'required_with:insumos|exists:inventario_items,id',
            'insumos.*.cantidad' => 'required_with:insumos|numeric|min:0.01',
            'insumos.*.costo_unitario' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Crear el mantenimiento
            $mantenimiento = MantenimientoUnidad::create([
                'unidad_produccion_id' => $validated['unidad_produccion_id'],
                'user_id' => $validated['user_id'],
                'tipo_mantenimiento' => $validated['tipo_mantenimiento'],
                'descripcion_trabajo' => $validated['descripcion_trabajo'],
                'fecha_mantenimiento' => $validated['fecha_mantenimiento'],
                'prioridad' => $validated['prioridad'],
                'observaciones_antes' => $validated['observaciones_antes'] ?? null,
                'requiere_vaciado' => $request->boolean('requiere_vaciado'),
                'requiere_traslado_peces' => $request->boolean('requiere_traslado_peces'),
                'estado_mantenimiento' => 'programado',
                'costo_mantenimiento' => $validated['costo_estimado'] ?? 0,
            ]);

            // Asociar insumos si se proporcionaron
            if ($request->filled('insumos')) {
                foreach ($validated['insumos'] as $insumoData) {
                    $mantenimiento->insumos()->create([
                        'inventario_item_id' => $insumoData['item_id'],
                        'cantidad_utilizada' => $insumoData['cantidad'],
                        'costo_unitario' => $insumoData['costo_unitario'] ?? 0,
                        'costo_total' => ($insumoData['cantidad'] * ($insumoData['costo_unitario'] ?? 0)),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento programado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Error al programar el mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles del mantenimiento
     */
    public function show(MantenimientoUnidad $mantenimiento)
    {
        $mantenimiento->load([
            'unidadProduccion', 
            'usuario', 
            'insumos.inventarioItem'
        ]);
        
        return view('mantenimientos.show', compact('mantenimiento'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(MantenimientoUnidad $mantenimiento)
    {
        // Solo permitir edición si está programado o en proceso
        if (!in_array($mantenimiento->estado_mantenimiento, ['programado', 'en_proceso'])) {
            return back()->with('error', 'No se puede editar un mantenimiento completado o cancelado.');
        }

        $unidades = UnidadProduccion::where('estado', '!=', 'inactivo')
            ->orderBy('codigo')
            ->get();
        
        $usuarios = User::orderBy('name')->get();
        $inventarioItems = InventarioItem::where('categoria', 'insumo')
            ->orderBy('nombre')
            ->get();

        $mantenimiento->load('insumos.inventarioItem');

        return view('mantenimientos.edit', compact(
            'mantenimiento',
            'unidades', 
            'usuarios', 
            'inventarioItems'
        ));
    }

    /**
     * Actualizar mantenimiento
     */
    public function update(Request $request, MantenimientoUnidad $mantenimiento)
    {
        if (!in_array($mantenimiento->estado_mantenimiento, ['programado', 'en_proceso'])) {
            return back()->with('error', 'No se puede editar un mantenimiento completado o cancelado.');
        }

        $validated = $request->validate([
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'tipo_mantenimiento' => 'required|in:preventivo,correctivo,limpieza,reparacion,inspeccion,desinfeccion',
            'descripcion_trabajo' => 'required|string|max:1000',
            'fecha_mantenimiento' => 'required|date',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'user_id' => 'required|exists:users,id',
            'observaciones_antes' => 'nullable|string|max:1000',
            'requiere_vaciado' => 'boolean',
            'requiere_traslado_peces' => 'boolean',
            'costo_mantenimiento' => 'nullable|numeric|min:0',
        ]);

        $mantenimiento->update($validated);

        return redirect()
            ->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento actualizado exitosamente.');
    }

    /**
     * Iniciar mantenimiento
     */
    public function iniciar(MantenimientoUnidad $mantenimiento)
    {
        if ($mantenimiento->estado_mantenimiento !== 'programado') {
            return back()->with('error', 'Solo se pueden iniciar mantenimientos programados.');
        }

        $mantenimiento->iniciar();

        return back()->with('success', 'Mantenimiento iniciado exitosamente.');
    }

    /**
     * Completar mantenimiento
     */
    public function completar(Request $request, MantenimientoUnidad $mantenimiento)
    {
        if (!in_array($mantenimiento->estado_mantenimiento, ['programado', 'en_proceso'])) {
            return back()->with('error', 'No se puede completar este mantenimiento.');
        }

        $validated = $request->validate([
            'observaciones_despues' => 'required|string|max:1000',
            'materiales_utilizados' => 'nullable|string|max:1000',
            'costo_final' => 'nullable|numeric|min:0',
            'proxima_revision' => 'nullable|date|after:today',
        ]);

        $mantenimiento->completar([
            'observaciones_despues' => $validated['observaciones_despues'],
            'materiales_utilizados' => $validated['materiales_utilizados'] ?? null,
            'costo_mantenimiento' => $validated['costo_final'] ?? $mantenimiento->costo_mantenimiento,
            'proxima_revision' => $validated['proxima_revision'] ?? null,
        ]);

        return redirect()
            ->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento completado exitosamente.');
    }

    /**
     * Cancelar mantenimiento
     */
    public function cancelar(Request $request, MantenimientoUnidad $mantenimiento)
    {
        if (!in_array($mantenimiento->estado_mantenimiento, ['programado', 'en_proceso'])) {
            return back()->with('error', 'No se puede cancelar este mantenimiento.');
        }

        $validated = $request->validate([
            'motivo_cancelacion' => 'required|string|max:500',
        ]);

        $mantenimiento->cancelar($validated['motivo_cancelacion']);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento cancelado exitosamente.');
    }

    /**
     * Eliminar mantenimiento
     */
    public function destroy(MantenimientoUnidad $mantenimiento)
    {
        // Solo permitir eliminación si está programado
        if ($mantenimiento->estado_mantenimiento !== 'programado') {
            return back()->with('error', 'Solo se pueden eliminar mantenimientos programados.');
        }

        $mantenimiento->delete();

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado exitosamente.');
    }

    /**
     * Obtener estadísticas de mantenimientos
     */
    private function obtenerEstadisticas()
    {
        return [
            'total' => MantenimientoUnidad::count(),
            'programados' => MantenimientoUnidad::where('estado_mantenimiento', 'programado')->count(),
            'en_proceso' => MantenimientoUnidad::where('estado_mantenimiento', 'en_proceso')->count(),
            'completados' => MantenimientoUnidad::where('estado_mantenimiento', 'completado')->count(),
            'vencidos' => MantenimientoUnidad::vencidos()->count(),
            'proximos_7_dias' => MantenimientoUnidad::proximos(7)->count(),
            'costo_total_mes' => MantenimientoUnidad::whereMonth('fecha_mantenimiento', now()->month)
                ->whereYear('fecha_mantenimiento', now()->year)
                ->sum('costo_mantenimiento'),
            'tipos_frecuentes' => MantenimientoUnidad::select('tipo_mantenimiento', DB::raw('count(*) as total'))
                ->groupBy('tipo_mantenimiento')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Exportar mantenimientos
     */
    public function exportar(Request $request)
    {
        // Implementar exportación (CSV, Excel, PDF)
        return response()->json(['message' => 'Exportación en desarrollo']);
    }

    /**
     * API para obtener mantenimientos de una unidad
     */
    public function apiPorUnidad(UnidadProduccion $unidad)
    {
        $mantenimientos = $unidad->mantenimientos()
            ->with('usuario')
            ->orderBy('fecha_mantenimiento', 'desc')
            ->get();

        return response()->json($mantenimientos);
    }

    /**
     * Dashboard de métricas
     */
    public function metricas()
    {
        $estadisticas = $this->obtenerEstadisticas();
        
        // Métricas adicionales para dashboard
        $metricas = [
            'eficiencia_mantenimientos' => $this->calcularEficiencia(),
            'tiempo_promedio_resolucion' => $this->calcularTiempoPromedio(),
            'costos_por_mes' => $this->obtenerCostosPorMes(),
            'unidades_mas_mantenimiento' => $this->obtenerUnidadesMasMantenimiento(),
        ];

        return view('mantenimientos.metricas', compact('estadisticas', 'metricas'));
    }

    private function calcularEficiencia()
    {
        $completados = MantenimientoUnidad::where('estado_mantenimiento', 'completado')->count();
        $total = MantenimientoUnidad::count();
        
        return $total > 0 ? round(($completados / $total) * 100, 2) : 0;
    }

    private function calcularTiempoPromedio()
    {
        $mantenimientos = MantenimientoUnidad::where('estado_mantenimiento', 'completado')
            ->whereNotNull('hora_inicio')
            ->whereNotNull('hora_fin')
            ->get();

        if ($mantenimientos->isEmpty()) {
            return 0;
        }

        $tiempoTotal = $mantenimientos->sum('duracion');
        return round($tiempoTotal / $mantenimientos->count(), 2);
    }

    private function obtenerCostosPorMes()
    {
        return MantenimientoUnidad::selectRaw('MONTH(fecha_mantenimiento) as mes, SUM(costo_mantenimiento) as total')
            ->whereYear('fecha_mantenimiento', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    private function obtenerUnidadesMasMantenimiento()
    {
        return MantenimientoUnidad::with('unidadProduccion')
            ->select('unidad_produccion_id', DB::raw('count(*) as total_mantenimientos'))
            ->groupBy('unidad_produccion_id')
            ->orderByDesc('total_mantenimientos')
            ->limit(5)
            ->get();
    }
}