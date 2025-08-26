<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Models\Seguimiento;
use App\Models\Traslado;
use App\Models\MantenimientoUnidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduccionController extends Controller
{
    public function index()
    {
        return view('produccion.index');
    }

    // === GESTIÓN DE LOTES ===
    public function gestionLotes()
    {
        $lotes = Lote::with('unidadProduccion')->orderBy('created_at', 'desc')->paginate(10);
        return view('produccion.lotes', compact('lotes'));
    }

    public function createLote()
    {
        $unidades = UnidadProduccion::activas()->get();
        return view('produccion.create-lote', compact('unidades'));
    }

    public function storeLote(Request $request)
    {
        $validated = $request->validate([
            'especie' => 'required|string',
            'cantidad_inicial' => 'required|integer|min:1',
            'peso_promedio_inicial' => 'nullable|numeric|min:0',
            'talla_promedio_inicial' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'nullable|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ]);

        // Generar código automáticamente
        $validated['codigo_lote'] = Lote::generarCodigoLote($validated['especie']);
        $validated['cantidad_actual'] = $validated['cantidad_inicial'];

        Lote::create($validated);

        return redirect()->route('produccion.lotes')->with('success', 'Lote creado exitosamente con código: ' . $validated['codigo_lote']);
    }

    public function showLote(Lote $lote)
    {
        $lote->load('unidadProduccion');
        return view('produccion.show-lote', compact('lote'));
    }

    // === GESTIÓN DE UNIDADES ===
    public function gestionUnidades(Request $request)
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
        
        return view('produccion.unidades', compact('unidades'));
    }

    public function createUnidad()
    {
        return view('produccion.create-unidad');
    }

    public function storeUnidad(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'tipo' => 'required|in:tanque,estanque,jaula,sistema_especializado',
            'capacidad_maxima' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'profundidad' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'fecha_construccion' => 'nullable|date'
        ]);

        // El código SIEMPRE se genera automáticamente, no se permite manual
        // Se elimina cualquier código que haya podido venir del formulario
        unset($validated['codigo']);

        $unidad = UnidadProduccion::create($validated);

        return redirect()->route('produccion.unidades')
                        ->with('success', "Unidad de producción creada exitosamente con código: {$unidad->codigo}");
    }

    public function showUnidad(UnidadProduccion $unidad)
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
        
        return view('produccion.show-unidad', compact('unidad', 'estadisticas_mantenimiento'));
    }

    public function editUnidad(UnidadProduccion $unidad)
    {
        return view('produccion.edit-unidad', compact('unidad'));
    }

    public function updateUnidad(Request $request, UnidadProduccion $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:tanque,estanque,jaula,sistema_especializado',
            'capacidad_maxima' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'profundidad' => 'nullable|numeric|min:0',
            'fecha_construccion' => 'nullable|date',
            'descripcion' => 'nullable|string'
        ]);

        $unidad->update($request->all());

        return redirect()->route('produccion.unidades.show', $unidad)
                        ->with('success', 'Unidad de producción actualizada exitosamente.');
    }

    public function generateUnidadCode($tipo)
    {
        if (!in_array($tipo, ['tanque', 'estanque', 'jaula', 'sistema_especializado'])) {
            return response()->json(['error' => 'Tipo de unidad inválido'], 400);
        }

        $codigo = UnidadProduccion::generateCodigo($tipo);
        
        return response()->json(['codigo' => $codigo]);
    }

    // === GESTIÓN DE MANTENIMIENTOS ===
    public function gestionMantenimientos(Request $request, ?UnidadProduccion $unidad = null)
    {
        $query = MantenimientoUnidad::with(['unidadProduccion', 'usuario']);
        
        if ($unidad) {
            $query->where('unidad_produccion_id', $unidad->id);
        }

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado_mantenimiento', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_mantenimiento', $request->tipo);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('unidad_id')) {
            $query->where('unidad_produccion_id', $request->unidad_id);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_mantenimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_mantenimiento', '<=', $request->fecha_hasta);
        }
        
        $mantenimientos = $query->orderBy('fecha_mantenimiento', 'desc')->paginate(15);
        
        $estadisticas = [
            'total' => MantenimientoUnidad::count(),
            'pendientes' => MantenimientoUnidad::where('estado_mantenimiento', 'programado')->count(),
            'en_proceso' => MantenimientoUnidad::where('estado_mantenimiento', 'en_proceso')->count(),
            'completados' => MantenimientoUnidad::where('estado_mantenimiento', 'completado')->count(),
            'este_mes' => MantenimientoUnidad::whereMonth('fecha_mantenimiento', now()->month)->count()
        ];
        
        $unidades = UnidadProduccion::all();
        
        return view('produccion.mantenimientos', compact('mantenimientos', 'estadisticas', 'unidades', 'unidad'));
    }

    public function crearMantenimiento(?UnidadProduccion $unidad = null)
    {
        $unidades = UnidadProduccion::where('estado', '!=', 'inactivo')->get();
        $usuarios = User::orderBy('name')->get();
        return view('produccion.crear-mantenimiento', compact('unidades', 'unidad', 'usuarios'));
    }

    public function storeMantenimiento(Request $request)
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
            'requiere_traslado_peces' => 'boolean'
        ]);

        $validated['estado_mantenimiento'] = 'programado';

        // Convertir checkboxes
        $validated['requiere_vaciado'] = $request->has('requiere_vaciado');
        $validated['requiere_traslado_peces'] = $request->has('requiere_traslado_peces');

        $mantenimiento = MantenimientoUnidad::create($validated);

        return redirect()->route('produccion.mantenimientos', $mantenimiento->unidadProduccion)
                        ->with('success', 'Mantenimiento programado exitosamente.');
    }

    public function showMantenimiento(MantenimientoUnidad $mantenimiento)
    {
        $mantenimiento->load(['unidadProduccion', 'usuario']);
        return view('produccion.show-mantenimiento', compact('mantenimiento'));
    }

    public function editMantenimiento(MantenimientoUnidad $mantenimiento)
    {
        // Solo se pueden editar mantenimientos programados
        if ($mantenimiento->estado_mantenimiento !== 'programado') {
            return redirect()->back()->with('error', 'Solo se pueden editar mantenimientos programados.');
        }

        $unidades = UnidadProduccion::activas()->get();
        $usuarios = User::active()->get();
        
        return view('produccion.edit-mantenimiento', compact('mantenimiento', 'unidades', 'usuarios'));
    }

    public function updateMantenimiento(Request $request, MantenimientoUnidad $mantenimiento)
    {
        // Solo se pueden editar mantenimientos programados
        if ($mantenimiento->estado_mantenimiento !== 'programado') {
            return redirect()->back()->with('error', 'Solo se pueden editar mantenimientos programados.');
        }

        $validated = $request->validate([
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'tipo_mantenimiento' => 'required|in:preventivo,correctivo,limpieza,reparacion,inspeccion,desinfeccion',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'fecha_mantenimiento' => 'required|date|after_or_equal:today',
            'descripcion_trabajo' => 'required|string|max:1000',
            'observaciones_antes' => 'nullable|string|max:1000',
            'usuario_id' => 'required|exists:users,id'
        ]);

        $validated['requiere_vaciado'] = $request->has('requiere_vaciado');
        $validated['requiere_traslado_peces'] = $request->has('requiere_traslado_peces');

        $mantenimiento->update($validated);

        return redirect()->route('produccion.mantenimientos.show', $mantenimiento)
                        ->with('success', 'Mantenimiento actualizado exitosamente.');
    }

    public function iniciarMantenimiento(MantenimientoUnidad $mantenimiento)
    {
        try {
            $mantenimiento->iniciar();
            return redirect()->back()->with('success', 'Mantenimiento iniciado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al iniciar mantenimiento: ' . $e->getMessage());
        }
    }

    public function completarMantenimiento(Request $request, MantenimientoUnidad $mantenimiento)
    {
        $validated = $request->validate([
            'observaciones_despues' => 'nullable|string|max:1000',
            'costo_mantenimiento' => 'nullable|numeric|min:0',
            'materiales_utilizados' => 'nullable|string|max:1000',
            'proxima_revision' => 'nullable|date|after:today'
        ]);

        try {
            $mantenimiento->completar($validated);
            return redirect()->route('produccion.mantenimientos', $mantenimiento->unidadProduccion)
                            ->with('success', 'Mantenimiento completado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al completar mantenimiento: ' . $e->getMessage());
        }
    }

    public function cancelarMantenimiento(Request $request, MantenimientoUnidad $mantenimiento)
    {
        $validated = $request->validate([
            'motivo_cancelacion' => 'required|string|max:500'
        ]);

        try {
            $mantenimiento->cancelar($validated['motivo_cancelacion']);
            return redirect()->back()->with('success', 'Mantenimiento cancelado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cancelar mantenimiento: ' . $e->getMessage());
        }
    }

    public function historialMantenimientos(Request $request, ?UnidadProduccion $unidad = null)
    {
        $query = MantenimientoUnidad::with(['unidadProduccion', 'usuario'])
                                  ->whereIn('estado_mantenimiento', ['completado', 'cancelado']);
        
        if ($unidad) {
            $query->where('unidad_produccion_id', $unidad->id);
        }

        // Filtros
        if ($request->filled('tipo')) {
            $query->where('tipo_mantenimiento', $request->tipo);
        }

        if ($request->filled('anio')) {
            $query->whereYear('fecha_mantenimiento', $request->anio);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_mantenimiento', $request->mes);
        }

        if ($request->filled('unidad_id')) {
            $query->where('unidad_produccion_id', $request->unidad_id);
        }

        $mantenimientos = $query->orderBy('fecha_fin', 'desc')
                               ->orderBy('fecha_mantenimiento', 'desc')
                               ->paginate(10);
        
        // Estadísticas para el historial
        $estadisticas = [
            'completados' => MantenimientoUnidad::where('estado_mantenimiento', 'completado')->count(),
            'costo_total' => MantenimientoUnidad::where('estado_mantenimiento', 'completado')
                                               ->sum('costo_mantenimiento'),
            'este_anio' => MantenimientoUnidad::whereYear('fecha_mantenimiento', now()->year)
                                             ->whereIn('estado_mantenimiento', ['completado', 'cancelado'])
                                             ->count(),
            'tiempo_promedio' => $this->calcularTiempoPromedioMantenimiento()
        ];

        $unidades = UnidadProduccion::all();
        
        return view('produccion.historial-mantenimiento', compact('mantenimientos', 'estadisticas', 'unidades', 'unidad'));
    }

    private function calcularTiempoPromedioMantenimiento()
    {
        $mantenimientosCompletos = MantenimientoUnidad::where('estado_mantenimiento', 'completado')
                                                     ->whereNotNull('fecha_inicio')
                                                     ->whereNotNull('fecha_fin')
                                                     ->get();

        if ($mantenimientosCompletos->isEmpty()) {
            return 0;
        }

        $totalHoras = $mantenimientosCompletos->sum(function($mantenimiento) {
            return $mantenimiento->fecha_inicio->diffInHours($mantenimiento->fecha_fin);
        });

        return round($totalHoras / $mantenimientosCompletos->count(), 1);
    }

    // === OTROS MÓDULOS ===
    public function gestionTraslados()
    {
        $traslados = Traslado::with(['lote', 'unidadOrigen', 'unidadDestino', 'usuario'])
                            ->orderBy('fecha_traslado', 'desc')
                            ->paginate(15);
        
        $estadisticas = [
            'total' => Traslado::count(),
            'completados' => Traslado::completados()->count(),
            'pendientes' => Traslado::pendientes()->count(),
            'este_mes' => Traslado::whereMonth('fecha_traslado', now()->month)->count()
        ];

        return view('produccion.traslados', compact('traslados', 'estadisticas'));
    }

    public function seguimientoLotes()
    {
        $lotes = Lote::with(['unidadProduccion', 'seguimientos' => function($query) {
            $query->orderBy('fecha_seguimiento', 'desc')->limit(3);
        }])->get();
        
        // Obtener estadísticas generales
        $totalSeguimientos = Seguimiento::count();
        $seguimientosRecientes = Seguimiento::recientes(7)->count();
        
        return view('produccion.seguimiento_lotes', compact('lotes', 'totalSeguimientos', 'seguimientosRecientes'));
    }

    public function crearSeguimiento($loteId)
    {
        $lote = Lote::with('unidadProduccion')->findOrFail($loteId);
        return view('produccion.crear_seguimiento', compact('lote'));
    }

    public function storeSeguimiento(Request $request, $loteId)
    {
        $lote = Lote::findOrFail($loteId);
        
        $request->validate([
            'fecha_seguimiento' => 'required|date',
            'tipo_seguimiento' => 'required|in:rutinario,muestreo,mortalidad,traslado',
            'cantidad_actual' => 'nullable|integer|min:0',
            'mortalidad' => 'nullable|integer|min:0',
            'peso_promedio' => 'nullable|numeric|min:0',
            'talla_promedio' => 'nullable|numeric|min:0',
            'temperatura_agua' => 'nullable|numeric',
            'ph_agua' => 'nullable|numeric|min:0|max:14',
            'oxigeno_disuelto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $seguimiento = Seguimiento::create([
            'lote_id' => $lote->id,
            'user_id' => Auth::id(),
            'fecha_seguimiento' => $request->fecha_seguimiento,
            'tipo_seguimiento' => $request->tipo_seguimiento,
            'cantidad_actual' => $request->cantidad_actual,
            'mortalidad' => $request->mortalidad ?? 0,
            'peso_promedio' => $request->peso_promedio,
            'talla_promedio' => $request->talla_promedio,
            'temperatura_agua' => $request->temperatura_agua,
            'ph_agua' => $request->ph_agua,
            'oxigeno_disuelto' => $request->oxigeno_disuelto,
            'observaciones' => $request->observaciones
        ]);

        // Actualizar cantidad actual del lote si se proporciona
        if ($request->cantidad_actual !== null) {
            $lote->update(['cantidad_actual' => $request->cantidad_actual]);
        }

        return redirect()->route('produccion.seguimiento.lotes')
                        ->with('success', 'Seguimiento registrado exitosamente.');
    }

    public function verSeguimientos($loteId)
    {
        $lote = Lote::with(['unidadProduccion', 'seguimientos.usuario'])
                   ->findOrFail($loteId);
        
        $seguimientos = $lote->seguimientos()
                           ->orderBy('fecha_seguimiento', 'desc')
                           ->paginate(15);

        return view('produccion.ver_seguimientos', compact('lote', 'seguimientos'));
    }

    public function seguimientoUnidades()
    {
        $unidades = UnidadProduccion::activas()->withCount('lotes')->get();
        return view('produccion.seguimiento_unidades', compact('unidades'));
    }

    // ============ MÉTODOS DE TRASLADOS ============

    public function crearTraslado(?int $loteId = null)
    {
        $lote = $loteId ? Lote::with('unidadProduccion')->findOrFail($loteId) : null;
        $lotes = Lote::activos()->with('unidadProduccion')->get();
        $unidades = UnidadProduccion::activas()->get();

        return view('produccion.crear_traslado', compact('lote', 'lotes', 'unidades'));
    }

    public function storeTraslado(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'unidad_destino_id' => 'required|exists:unidad_produccions,id',
            'fecha_traslado' => 'required|date',
            'cantidad_trasladada' => 'required|integer|min:1',
            'cantidad_perdida' => 'nullable|integer|min:0',
            'peso_promedio_traslado' => 'nullable|numeric|min:0',
            'motivo_traslado' => 'required|in:crecimiento,sobrepoblacion,mejores_condiciones,mantenimiento,clasificacion,otro',
            'observaciones_origen' => 'nullable|string|max:1000',
            'observaciones_destino' => 'nullable|string|max:1000',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio'
        ]);

        $lote = Lote::findOrFail($request->lote_id);
        
        // Validar que la cantidad no exceda la disponible
        if ($request->cantidad_trasladada > $lote->cantidad_actual) {
            return back()->withErrors([
                'cantidad_trasladada' => 'La cantidad a trasladar no puede ser mayor a la cantidad actual del lote (' . number_format($lote->cantidad_actual) . ').'
            ])->withInput();
        }

        // Obtener unidad origen actual del lote
        $request->merge(['unidad_origen_id' => $lote->unidad_produccion_id]);

        try {
            $traslado = Traslado::crearConSeguimiento($request->all());
            
            return redirect()->route('produccion.traslados')
                           ->with('success', 'Traslado planificado exitosamente y seguimiento registrado.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el traslado: ' . $e->getMessage()])->withInput();
        }
    }

    public function showTraslado(Traslado $traslado)
    {
        $traslado->load(['lote', 'unidadOrigen', 'unidadDestino', 'usuario', 'seguimiento']);
        return view('produccion.show_traslado', compact('traslado'));
    }

    public function completarTraslado(Request $request, Traslado $traslado)
    {
        $request->validate([
            'cantidad_perdida' => 'nullable|integer|min:0|max:' . $traslado->cantidad_trasladada,
            'observaciones_destino' => 'nullable|string|max:1000',
            'hora_fin' => 'nullable|date_format:H:i'
        ]);

        try {
            $traslado->completar($request->all());
            
            return redirect()->route('produccion.traslados.show', $traslado)
                           ->with('success', 'Traslado completado exitosamente. El lote ha sido actualizado.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar el traslado: ' . $e->getMessage()]);
        }
    }

    public function cancelarTraslado(Traslado $traslado)
    {
        if ($traslado->estado_traslado !== 'planificado') {
            return back()->withErrors(['error' => 'Solo se pueden cancelar traslados planificados.']);
        }

        $traslado->update(['estado_traslado' => 'cancelado']);
        
        // Actualizar seguimiento asociado si existe
        if ($traslado->seguimiento) {
            $traslado->seguimiento->update([
                'observaciones' => $traslado->seguimiento->observaciones . "\n\nTRASLADO CANCELADO: " . now()->format('d/m/Y H:i')
            ]);
        }

        return redirect()->route('produccion.traslados')
                       ->with('success', 'Traslado cancelado exitosamente.');
    }

    public function destroyUnidad(UnidadProduccion $unidad)
    {
        try {
            // Verificar que no tenga lotes activos
            $lotesActivos = $unidad->lotes()->activos()->count();
            if ($lotesActivos > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar la unidad porque tiene lotes activos asignados.']);
            }

            // Verificar que no tenga mantenimientos pendientes
            $mantenimientosPendientes = $unidad->mantenimientos()
                ->whereIn('estado_mantenimiento', ['programado', 'en_proceso'])
                ->count();
            if ($mantenimientosPendientes > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar la unidad porque tiene mantenimientos pendientes.']);
            }

            // Soft delete de la unidad
            $unidad->delete();

            return redirect()->route('produccion.unidades')
                           ->with('success', 'Unidad de producción eliminada exitosamente.');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la unidad: ' . $e->getMessage()]);
        }
    }
}
