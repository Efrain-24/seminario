<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Models\MantenimientoUnidad;
use App\Models\Traslado;
use App\Models\Seguimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // logging
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
            // Peso en kg con tres decimales: 0.011 a 0.99
            'peso_promedio_inicial' => 'nullable|numeric|min:0.011|max:0.99',
            'talla_promedio_inicial' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'nullable|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ], [
            'peso_promedio_inicial.numeric' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.',
            'peso_promedio_inicial.min' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.',
            'peso_promedio_inicial.max' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.'
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

    /**
     * Mostrar formulario de edición de un lote
     */
    public function editLote(Lote $lote)
    {
        $unidades = UnidadProduccion::activas()->get();
        return view('produccion.lotes.edit', compact('lote', 'unidades'));
    }

    /**
     * Actualizar la información del lote
     */
    public function updateLote(Request $request, Lote $lote)
    {
        try {
            $validated = $request->validate([
                'especie' => 'required|string',
                'cantidad_inicial' => 'sometimes|integer|min:1',
                // kg entre 0.011 y 0.99
                'peso_promedio_inicial' => 'nullable|numeric|min:0.011|max:0.99',
                'talla_promedio_inicial' => 'nullable|numeric|min:0',
                'fecha_inicio' => 'required|date',
                'unidad_produccion_id' => 'nullable|exists:unidad_produccions,id',
                'observaciones' => 'nullable|string'
            ], [
                'peso_promedio_inicial.numeric' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.',
                'peso_promedio_inicial.min' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.',
                'peso_promedio_inicial.max' => 'Permite ingreso promedio de rango 0.011 a 0.99 kg.'
            ]);

            // Asegurar que no intenten modificar el código
            unset($validated['codigo_lote']);

            // Mantener cantidad_inicial si no la envían
            if (!array_key_exists('cantidad_inicial', $validated)) {
                $validated['cantidad_inicial'] = $lote->cantidad_inicial;
            }

            // Ya viene en kg, no se convierte
            $lote->update($validated);

            return redirect()->route('produccion.lotes.show', $lote)->with('success', 'Lote actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar lote', [
                'lote_id' => $lote->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar el lote: ' . $e->getMessage());
        }
    }

    // === GESTIÓN DE UNIDADES MOVIDA A UnidadProduccionController ===

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
            'este_mes' => MantenimientoUnidad::whereMonth('fecha_mantenimiento', now()->month)->count(),
            // Suma de costo total de protocolos completados en quetzales
            'costo_total' => MantenimientoUnidad::where('estado_mantenimiento', 'completado')->sum('costo_mantenimiento'),
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
            'fecha_mantenimiento' => 'required|date',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'user_id' => 'required|exists:users,id',
            'observaciones_antes' => 'nullable|string|max:1000',
            'requiere_vaciado' => 'boolean',
            'requiere_traslado_peces' => 'boolean',
            'repeat_type' => 'nullable|string',
            'repeat_every' => 'nullable|integer|min:1',
            'repeat_unit' => 'nullable|string',
            'repeat_n_months' => 'nullable|integer|min:2|max:12',
            'advanced_week' => 'nullable|integer|min:1|max:4',
            'advanced_weekday' => 'nullable|integer|min:1|max:7',
            'repeat_count' => 'nullable|integer|min:1|max:24',
        ]);

        $validated['estado_mantenimiento'] = 'programado';
        $validated['requiere_vaciado'] = $request->has('requiere_vaciado');
        $validated['requiere_traslado_peces'] = $request->has('requiere_traslado_peces');

        // Lógica de repetición
        $repeatType = $request->input('repeat_type', 'none');
        $repeatCount = (int)($request->input('repeat_count', 1));
        $fechas = [];
        // Usar el rango del ciclo para calcular las fechas
        $inicioCiclo = $request->input('fecha_inicio_ciclico') ? \Carbon\Carbon::parse($request->input('fecha_inicio_ciclico')) : \Carbon\Carbon::parse($validated['fecha_mantenimiento']);
        $finCiclo = $request->input('fecha_fin_ciclico') ? \Carbon\Carbon::parse($request->input('fecha_fin_ciclico')) : $inicioCiclo->copy();
        if ($repeatType === 'interval') {
            $every = max(1, (int)$request->input('repeat_every', 1));
            $unit = $request->input('repeat_unit', 'days');
            $fechaActual = $inicioCiclo->copy();
            while ($fechaActual->lte($finCiclo)) {
                $fechas[] = $fechaActual->copy();
                $fechaActual->add($unit, $every);
            }
        } elseif ($repeatType === 'n_months') {
            $n = max(2, (int)$request->input('repeat_n_months', 2));
            $fechaActual = $inicioCiclo->copy();
            while ($fechaActual->lte($finCiclo)) {
                $fechas[] = $fechaActual->copy();
                $fechaActual->addMonths($n);
            }
        } elseif ($repeatType === 'advanced') {
            $week = (int)$request->input('advanced_week', 1);
            $weekday = (int)$request->input('advanced_weekday', 1); // 1=Lunes ... 7=Domingo
            $fechaActual = $inicioCiclo->copy();
            while ($fechaActual->lte($finCiclo)) {
                $mes = $fechaActual->month;
                $anio = $fechaActual->year;
                $firstDay = \Carbon\Carbon::create($anio, $mes, 1);
                $target = $firstDay->copy()->next($weekday);
                if ($week > 1) {
                    $target->addWeeks($week - 1);
                }
                if ($target->gte($inicioCiclo) && $target->lte($finCiclo)) {
                    $fechas[] = $target->copy();
                }
                $fechaActual->addMonth();
            }
        } else {
            if ($inicioCiclo->lte($finCiclo)) {
                $fechas[] = $inicioCiclo->copy();
            }
        }

        $mantenimientos = [];
        foreach ($fechas as $fecha) {
            $data = $validated;
            $data['fecha_mantenimiento'] = $fecha->format('Y-m-d');
            $mantenimiento = MantenimientoUnidad::create($data);
            
            // Guardar actividades si se proporcionaron
            if ($request->has('actividades_json')) {
                $actividadesJson = $request->input('actividades_json', '[]');
                $actividades = json_decode($actividadesJson, true) ?? [];
                // Filtrar actividades vacías
                $actividades = array_filter($actividades, function($a) { return trim($a) !== ''; });
                if (!empty($actividades)) {
                    $mantenimiento->update([
                        'actividades' => array_values($actividades),
                        'actividades_ejecutadas' => [] // Inicializar como array vacío
                    ]);
                }
            }
            
            // Guardar insumos si se proporcionaron (como JSON)
            \Log::info('Verificando insumos en request', [
                'has_insumos_json' => $request->has('insumos_json'),
                'insumos_json_raw' => $request->input('insumos_json'),
                'all_request_keys' => array_keys($request->all())
            ]);
            
            // Procesar insumos desde JSON
            if ($request->has('insumos_json')) {
                $insumosJson = $request->input('insumos_json', '[]');
                $insumosArray = json_decode($insumosJson, true) ?? [];
                
                \Log::info('Guardando insumos desde JSON', ['insumos_array' => $insumosArray, 'count' => count($insumosArray)]);
                
                foreach ($insumosArray as $insumoData) {
                    $insumo_id = $insumoData['id'] ?? null;
                    $cantidad = intval($insumoData['cantidad'] ?? 1);
                    
                    if ($insumo_id) {
                        $insumo = \App\Models\InventarioItem::find($insumo_id);
                        if ($insumo) {
                            $costo_unitario = $insumo->costo_unitario ?? 0;
                            $costo_total = $costo_unitario * $cantidad;
                            
                            $mantenimiento->insumos()->attach($insumo_id, [
                                'cantidad' => $cantidad,
                                'costo_unitario' => $costo_unitario,
                                'costo_total' => $costo_total
                            ]);
                            
                            \Log::info('Insumo guardado', ['insumo_id' => $insumo_id, 'cantidad' => $cantidad]);
                        }
                    }
                }
            }
            // Mantener compatibilidad con formato antiguo (insumos[] y cantidades[]) por si acaso
            elseif ($request->has('insumos') && is_array($request->input('insumos'))) {
                $insumos = $request->input('insumos', []);
                $cantidades = $request->input('cantidades', []);
                
                \Log::info('Guardando insumos (formato antiguo)', ['insumos' => $insumos, 'cantidades' => $cantidades]);
                
                foreach ($insumos as $idx => $insumo_id) {
                    $insumo = \App\Models\InventarioItem::find($insumo_id);
                    if ($insumo) {
                        $cantidad = intval($cantidades[$idx] ?? 1);
                        $costo_unitario = $insumo->costo_unitario ?? 0;
                        $costo_total = $costo_unitario * $cantidad;
                        
                        $mantenimiento->insumos()->attach($insumo_id, [
                            'cantidad' => $cantidad,
                            'costo_unitario' => $costo_unitario,
                            'costo_total' => $costo_total
                        ]);
                    }
                }
            }
            
            $mantenimientos[] = $mantenimiento;
        }

        // Redirigir a la unidad del primer mantenimiento creado
        return redirect()->route('produccion.mantenimientos', $mantenimientos[0]->unidadProduccion)
                        ->with('success', count($mantenimientos) > 1 ? 'Mantenimientos programados exitosamente.' : 'Mantenimiento programado exitosamente.');
    }

    public function showMantenimiento(MantenimientoUnidad $mantenimiento)
    {
        $mantenimiento->load(['unidadProduccion', 'usuario', 'insumos']);
        return view('produccion.show-mantenimiento', compact('mantenimiento'));
    }

    public function editMantenimiento(MantenimientoUnidad $mantenimiento)
    {
        // Solo se pueden editar mantenimientos programados
        if ($mantenimiento->estado_mantenimiento !== 'programado') {
            return redirect()->back()->with('error', 'Solo se pueden editar mantenimientos programados.');
        }

        $mantenimiento->load(['unidadProduccion', 'usuario', 'insumos']);
        $unidades = UnidadProduccion::where('estado', '!=', 'inactivo')->get();
        $usuarios = User::active()->get();
        
        \Log::info('EditMantenimiento Debug', [
            'mantenimiento_id' => $mantenimiento->id,
            'unidad_produccion_id' => $mantenimiento->unidad_produccion_id,
            'unidad_produccion' => $mantenimiento->unidadProduccion?->nombre,
        ]);
        
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
            'fecha_mantenimiento' => 'required|date',
            'descripcion_trabajo' => 'required|string|max:1000',
            'observaciones_antes' => 'nullable|string|max:1000',
            'user_id' => 'required|exists:users,id'
        ]);

        $validated['requiere_vaciado'] = $request->has('requiere_vaciado');
        $validated['requiere_traslado_peces'] = $request->has('requiere_traslado_peces');

        $mantenimiento->update($validated);
        
        // Actualizar actividades
        if ($request->has('actividades') && is_array($request->input('actividades'))) {
            $actividades = $request->input('actividades', []);
            // Filtrar actividades vacías
            $actividades = array_filter($actividades, function($a) { return trim($a) !== ''; });
            $mantenimiento->update([
                'actividades' => $actividades
            ]);
        }

        // Actualizar insumos
        $mantenimiento->insumos()->detach(); // Eliminar insumos anteriores
        
        // Revertir movimientos de inventario anteriores
        \App\Models\InventarioMovimiento::where('referencia_type', MantenimientoUnidad::class)
            ->where('referencia_id', $mantenimiento->id)
            ->get()
            ->each(function ($movimiento) {
                // Revertir el descuento del stock
                if ($movimiento->bodega_id) {
                    $existencias = \App\Models\InventarioExistencia::where('item_id', $movimiento->item_id)
                        ->where('bodega_id', $movimiento->bodega_id)
                        ->first();
                    if ($existencias && $movimiento->tipo === 'salida') {
                        $existencias->stock_actual += $movimiento->cantidad_base;
                        $existencias->save();
                    }
                } else {
                    $existencias = \App\Models\InventarioExistencia::where('item_id', $movimiento->item_id)->first();
                    if ($existencias && $movimiento->tipo === 'salida') {
                        $existencias->stock_actual += $movimiento->cantidad_base;
                        $existencias->save();
                    }
                }
                $movimiento->delete();
            });
        
        if ($request->has('insumos') && is_array($request->input('insumos'))) {
            $insumos = $request->input('insumos', []);
            $cantidades = $request->input('cantidades', []);
            
            foreach ($insumos as $idx => $insumo_id) {
                $insumo = \App\Models\InventarioItem::find($insumo_id);
                if ($insumo) {
                    $cantidad = intval($cantidades[$idx] ?? 1);
                    $costo_unitario = $insumo->costo_unitario ?? 0;
                    $costo_total = $costo_unitario * $cantidad;
                    
                    $mantenimiento->insumos()->attach($insumo_id, [
                        'cantidad' => $cantidad,
                        'costo_unitario' => $costo_unitario,
                        'costo_total' => $costo_total
                    ]);
                    
                    // Obtener la bodega con existencias del insumo o usar la primera bodega disponible
                    $bodega = \App\Models\InventarioExistencia::where('item_id', $insumo_id)
                        ->with('bodega')
                        ->first()
                        ?->bodega;
                    
                    // Si no hay existencias, usar la primera bodega del sistema
                    if (!$bodega) {
                        $bodega = \App\Models\Bodega::first();
                    }
                    
                    // Si aún no hay bodega, no crear el movimiento (error de configuración)
                    if (!$bodega) {
                        Log::error('No se encontró bodega para crear movimiento de mantenimiento', [
                            'insumo_id' => $insumo_id,
                            'mantenimiento_id' => $mantenimiento->id
                        ]);
                        continue;
                    }
                    
                    // Crear movimiento de inventario para descontar el stock
                    \App\Models\InventarioMovimiento::create([
                        'item_id' => $insumo_id,
                        'bodega_id' => $bodega->id,
                        'tipo' => 'salida',
                        'cantidad_base' => $cantidad,
                        'unidad_origen' => $insumo->unidad_base,
                        'cantidad_origen' => $cantidad,
                        'referencia_type' => MantenimientoUnidad::class,
                        'referencia_id' => $mantenimiento->id,
                        'fecha' => \Carbon\Carbon::parse($validated['fecha_mantenimiento']),
                        'descripcion' => "Insumo utilizado en mantenimiento: {$mantenimiento->tipo_mantenimiento}",
                        'user_id' => $validated['user_id']
                    ]);
                    
                    // Actualizar stock en existencias de la bodega específica
                    $existencias = \App\Models\InventarioExistencia::where('item_id', $insumo_id)
                        ->where('bodega_id', $bodega->id)
                        ->first();
                    if ($existencias) {
                        $existencias->stock_actual = max(0, $existencias->stock_actual - $cantidad);
                        $existencias->save();
                    }
                }
            }
        }

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
            'proxima_revision' => 'nullable|date|after:today',
            'insumos_utilizados' => 'nullable|array',
            'actividades_completadas' => 'nullable|array'
        ]);

        try {
            // Guardar el estado de actividades completadas
            if ($request->has('actividades_completadas') && $mantenimiento->actividades) {
                $actividadesCompletadas = $request->input('actividades_completadas', []);
                $actividades = $mantenimiento->actividades;
                $actividadesEjecutadas = [];
                
                foreach ($actividades as $idx => $actividad) {
                    $actividadesEjecutadas[$idx] = [
                        'nombre' => $actividad,
                        'completada' => in_array($idx, $actividadesCompletadas)
                    ];
                }
                
                $mantenimiento->update([
                    'actividades_ejecutadas' => $actividadesEjecutadas
                ]);
            }
            
            // Realizar descuento de inventario al completar
            $insumosUtilizados = $request->input('insumos_utilizados', []);
            
            if ($insumosUtilizados && is_array($insumosUtilizados)) {
                foreach ($insumosUtilizados as $insumo_id) {
                    $insumo = $mantenimiento->insumos()->find($insumo_id);
                    if ($insumo) {
                        $cantidad = $insumo->pivot->cantidad;
                        
                        // Crear movimiento de inventario si no existe
                        $movimientoExistente = \App\Models\InventarioMovimiento::where('referencia_type', MantenimientoUnidad::class)
                            ->where('referencia_id', $mantenimiento->id)
                            ->where('item_id', $insumo_id)
                            ->where('tipo', 'salida')
                            ->exists();
                        
                        if (!$movimientoExistente) {
                            // Obtener la bodega con existencias del insumo o usar la primera bodega disponible
                            $bodega = \App\Models\InventarioExistencia::where('item_id', $insumo_id)
                                ->with('bodega')
                                ->first()
                                ?->bodega;
                            
                            // Si no hay existencias, usar la primera bodega del sistema
                            if (!$bodega) {
                                $bodega = \App\Models\Bodega::first();
                            }
                            
                            // Si aún no hay bodega, no crear el movimiento (error de configuración)
                            if (!$bodega) {
                                Log::error('No se encontró bodega para crear movimiento de mantenimiento', [
                                    'insumo_id' => $insumo_id,
                                    'mantenimiento_id' => $mantenimiento->id
                                ]);
                                continue;
                            }
                            
                            // Crear movimiento de inventario
                            \App\Models\InventarioMovimiento::create([
                                'item_id' => $insumo_id,
                                'bodega_id' => $bodega->id,
                                'tipo' => 'salida',
                                'cantidad_base' => $cantidad,
                                'unidad_origen' => $insumo->unidad_base,
                                'cantidad_origen' => $cantidad,
                                'referencia_type' => MantenimientoUnidad::class,
                                'referencia_id' => $mantenimiento->id,
                                'fecha' => $mantenimiento->fecha_mantenimiento,
                                'descripcion' => "Insumo utilizado en mantenimiento: {$mantenimiento->tipo_mantenimiento}",
                                'user_id' => $mantenimiento->user_id
                            ]);
                            
                            // Actualizar stock en existencias de la bodega específica
                            $existencias = \App\Models\InventarioExistencia::where('item_id', $insumo_id)
                                ->where('bodega_id', $bodega->id)
                                ->first();
                            if ($existencias) {
                                $existencias->stock_actual = max(0, $existencias->stock_actual - $cantidad);
                                $existencias->save();
                            }
                        }
                    }
                }
            }
            
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
                                                     ->whereNotNull('hora_inicio')
                                                     ->whereNotNull('hora_fin')
                                                     ->get();

        if ($mantenimientosCompletos->isEmpty()) {
            return 0;
        }

        $totalHoras = $mantenimientosCompletos->sum(function($mantenimiento) {
            if ($mantenimiento->hora_inicio && $mantenimiento->hora_fin) {
                $inicio = \Carbon\Carbon::parse($mantenimiento->hora_inicio);
                $fin = \Carbon\Carbon::parse($mantenimiento->hora_fin);
                return $inicio->diffInHours($fin);
            }
            return 0;
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

        $mortalidad = $request->mortalidad ?? 0;
        $cantidad_actual = $request->cantidad_actual !== null ? $request->cantidad_actual : $lote->cantidad_actual;
        // Descontar mortalidad del lote
        $nueva_cantidad = max(0, $cantidad_actual - $mortalidad);
        $seguimiento = Seguimiento::create([
            'lote_id' => $lote->id,
            'user_id' => Auth::id(),
            'fecha_seguimiento' => $request->fecha_seguimiento,
            'tipo_seguimiento' => $request->tipo_seguimiento,
            'cantidad_actual' => $nueva_cantidad,
            'mortalidad' => $mortalidad,
            'peso_promedio' => $request->peso_promedio,
            'talla_promedio' => $request->talla_promedio,
            'temperatura_agua' => $request->temperatura_agua,
            'ph_agua' => $request->ph_agua,
            'oxigeno_disuelto' => $request->oxigeno_disuelto,
            'observaciones' => $request->observaciones
        ]);

        // Actualizar cantidad_actual y total_peso en el lote
        $peso_promedio = $request->peso_promedio ?? $lote->peso_promedio_inicial;
        $total_peso = round($nueva_cantidad * $peso_promedio, 2);
        $lote->update([
            'cantidad_actual' => $nueva_cantidad,
            'total_peso' => $total_peso
        ]);

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

    public function inhabilitarUnidad(UnidadProduccion $unidad)
    {
        try {
            // Verificar que no tenga lotes activos
            $lotesActivos = $unidad->lotes()->activos()->count();
            if ($lotesActivos > 0) {
                return back()->withErrors(['error' => 'No se puede inhabilitar la unidad porque tiene lotes activos asignados.']);
            }

            // Verificar que no tenga mantenimientos pendientes
            $mantenimientosPendientes = $unidad->mantenimientos()
                ->whereIn('estado_mantenimiento', ['programado', 'en_proceso'])
                ->count();
            if ($mantenimientosPendientes > 0) {
                return back()->withErrors(['error' => 'No se puede inhabilitar la unidad porque tiene mantenimientos pendientes.']);
            }

            // Cambiar estado a inactivo
            $unidad->estado = 'inactivo';
            $unidad->save();

            return redirect()->route('produccion.unidades')
                           ->with('success', 'Unidad de producción inhabilitada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al inhabilitar la unidad: ' . $e->getMessage()]);
        }
    }
    /**
     * Muestra el historial de eventos de una unidad de producción (mantenimientos, traslados, seguimientos, etc.)
     */
    public function historialUnidad(Request $request, UnidadProduccion $unidad)
    {
        // Filtros
        $tipoEvento = $request->input('tipo_evento');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // 1. Obtener mantenimientos
        $mantenimientos = $unidad->mantenimientos()
            ->when($fechaDesde, function($query) use ($fechaDesde) {
                return $query->where('fecha_mantenimiento', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function($query) use ($fechaHasta) {
                return $query->where('fecha_mantenimiento', '<=', $fechaHasta);
            })
            ->get()
            ->map(function($m) {
                return (object) [
                    'fecha' => $m->fecha_mantenimiento ? $m->fecha_mantenimiento->setTime(8,0) : $m->created_at,
                    'tipo' => 'Mantenimiento',
                    'descripcion' => "Mantenimiento {$m->tipo_mantenimiento}: {$m->descripcion_trabajo}",
                    'enlace' => route('produccion.mantenimientos.show', $m->id)
                ];
            });

        // 2. Obtener traslados como origen
        $trasladosOrigen = $unidad->trasladosOrigen()
            ->when($fechaDesde, function($query) use ($fechaDesde) {
                return $query->where('fecha_traslado', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function($query) use ($fechaHasta) {
                return $query->where('fecha_traslado', '<=', $fechaHasta);
            })
            ->get()
            ->map(function($t) {
                return (object) [
                    'fecha' => $t->fecha_traslado ? $t->fecha_traslado->setTime(8,0) : $t->created_at,
                    'tipo' => 'Traslado (Salida)',
                    'descripcion' => "Salida del lote {$t->lote->codigo_lote} hacia {$t->unidadDestino->nombre}",
                    'enlace' => route('produccion.traslados.show', $t->id)
                ];
            });

        // 3. Obtener traslados como destino
        $trasladosDestino = $unidad->trasladosDestino()
            ->when($fechaDesde, function($query) use ($fechaDesde) {
                return $query->where('fecha_traslado', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function($query) use ($fechaHasta) {
                return $query->where('fecha_traslado', '<=', $fechaHasta);
            })
            ->get()
            ->map(function($t) {
                return (object) [
                    'fecha' => $t->fecha_traslado ? $t->fecha_traslado->setTime(8,0) : $t->created_at,
                    'tipo' => 'Traslado (Entrada)',
                    'descripcion' => "Entrada del lote {$t->lote->codigo_lote} desde {$t->unidadOrigen->nombre}",
                    'enlace' => route('produccion.traslados.show', $t->id)
                ];
            });

        // Combinar todos los eventos
        $eventos = collect()
            ->when(!$tipoEvento || $tipoEvento === 'Mantenimiento', function($collection) use ($mantenimientos) {
                return $collection->concat($mantenimientos);
            })
            ->when(!$tipoEvento || $tipoEvento === 'Traslado (Entrada)', function($collection) use ($trasladosDestino) {
                return $collection->concat($trasladosDestino);
            })
            ->when(!$tipoEvento || $tipoEvento === 'Traslado (Salida)', function($collection) use ($trasladosOrigen) {
                return $collection->concat($trasladosOrigen);
            })
            ->sortByDesc('fecha')
            ->values();

        // Paginar los resultados
        $perPage = 10;
        $page = $request->input('page', 1);
        $eventos = new \Illuminate\Pagination\LengthAwarePaginator(
            $eventos->forPage($page, $perPage),
            $eventos->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('produccion.historial-unidad', compact('unidad', 'eventos'));
        $tipoEvento = $request->input('tipo_evento');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // Mantenimientos
        $mantenimientos = $unidad->mantenimientos()
            ->when($fechaDesde, function($query) use ($fechaDesde) {
                return $query->where('fecha_mantenimiento', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function($query) use ($fechaHasta) {
                return $query->where('fecha_mantenimiento', '<=', $fechaHasta);
            })
            ->get()
            ->map(function($m) {
                return (object) [
                    'fecha' => $m->fecha_mantenimiento ? $m->fecha_mantenimiento->setTime(8,0) : $m->created_at,
                    'tipo' => 'Mantenimiento',
                    'descripcion' => $m->descripcion_trabajo,
                    'enlace' => route('mantenimientos.show', $m->id)
                ];
            });

        // Traslados como origen o destino
        $trasladosOrigen = $unidad->trasladosOrigen()->get()->map(function($t) {
            return (object) [
                'fecha' => $t->fecha_traslado ? $t->fecha_traslado->setTime(8,0) : $t->created_at,
                'tipo' => 'Traslado (Origen)',
                'descripcion' => 'Salida de lote ' . ($t->lote->codigo_lote ?? '-') . ' hacia ' . ($t->unidadDestino->nombre ?? '-'),
                'enlace' => route('produccion.traslados.show', $t->id)
            ];
        });
        $trasladosDestino = $unidad->trasladosDestino()->get()->map(function($t) {
            return (object) [
                'fecha' => $t->fecha_traslado ? $t->fecha_traslado->setTime(8,0) : $t->created_at,
                'tipo' => 'Traslado (Destino)',
                'descripcion' => 'Ingreso de lote ' . ($t->lote->codigo_lote ?? '-') . ' desde ' . ($t->unidadOrigen->nombre ?? '-'),
                'enlace' => route('produccion.traslados.show', $t->id)
            ];
        });

        // Seguimientos de lotes en la unidad
        $seguimientos = $unidad->lotes->flatMap(function($lote) {
            return $lote->seguimientos->map(function($s) use ($lote) {
                return (object) [
                    'fecha' => $s->fecha_seguimiento ? $s->fecha_seguimiento->setTime(8,0) : $s->created_at,
                    'tipo' => 'Seguimiento (' . $s->tipo_seguimiento . ')',
                    'descripcion' => 'Lote: ' . ($lote->codigo_lote ?? '-') . ' - ' . ($s->observaciones ?? '-') ,
                    'enlace' => null // Puedes agregar enlace si tienes una ruta de detalle
                ];
            });
        });

        // Unir y ordenar todos los eventos por fecha descendente
        $eventos = collect()
            ->merge($mantenimientos)
            ->merge($trasladosOrigen)
            ->merge($trasladosDestino)
            ->merge($seguimientos)
            ->sortByDesc('fecha')
            ->values();

        // Paginación manual (opcional, si la colección es grande)
        $perPage = 20;
        $page = request('page', 1);
        $eventosPaginados = new \Illuminate\Pagination\LengthAwarePaginator(
            $eventos->forPage($page, $perPage),
            $eventos->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('produccion.historial-unidad', [
            'unidad' => $unidad,
            'eventos' => $eventosPaginados
        ]);
    }

    // Eliminar mantenimiento
    public function eliminarMantenimiento($mantenimiento)
    {
        $mantenimiento = \App\Models\MantenimientoUnidad::findOrFail($mantenimiento);
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'gerente') {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar mantenimientos.');
        }
        
        // Revertir movimientos de inventario antes de eliminar
        $this->revertirMovimientosInventario($mantenimiento);
        
        // Si el mantenimiento es parte de un ciclo, eliminar todos los del ciclo
        $ciclicos = \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $mantenimiento->unidad_produccion_id)
            ->where('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento)
            ->where('descripcion_trabajo', $mantenimiento->descripcion_trabajo)
            ->where('user_id', $mantenimiento->user_id)
            ->where('prioridad', $mantenimiento->prioridad)
            ->where('estado_mantenimiento', $mantenimiento->estado_mantenimiento)
            ->whereDate('fecha_mantenimiento', '>=', $mantenimiento->fecha_mantenimiento)
            ->get();
        
        if ($ciclicos->count() > 1) {
            foreach ($ciclicos as $c) {
                $this->revertirMovimientosInventario($c);
                $c->delete();
            }
            // Clear query cache
            \Illuminate\Support\Facades\Cache::flush();
            return redirect()->route('produccion.mantenimientos', $mantenimiento->unidad_produccion_id)
                ->with('success', 'Todos los mantenimientos cíclicos relacionados han sido eliminados y el inventario ha sido revertido.');
        } else {
            $mantenimiento->delete();
            // Clear query cache
            \Illuminate\Support\Facades\Cache::flush();
            return redirect()->route('produccion.mantenimientos', $mantenimiento->unidad_produccion_id)
                ->with('success', 'Mantenimiento eliminado correctamente y el inventario ha sido revertido.');
        }
    }
    
    /**
     * Revertir movimientos de inventario de un mantenimiento
     */
    private function revertirMovimientosInventario(MantenimientoUnidad $mantenimiento)
    {
        // Obtener todos los movimientos de salida relacionados
        $movimientos = \App\Models\InventarioMovimiento::where('referencia_type', MantenimientoUnidad::class)
            ->where('referencia_id', $mantenimiento->id)
            ->where('tipo', 'salida')
            ->get();
        
        foreach ($movimientos as $movimiento) {
            // Revertir el stock en existencias
            if ($movimiento->bodega_id) {
                $existencias = \App\Models\InventarioExistencia::where('item_id', $movimiento->item_id)
                    ->where('bodega_id', $movimiento->bodega_id)
                    ->first();
            } else {
                $existencias = \App\Models\InventarioExistencia::where('item_id', $movimiento->item_id)->first();
            }
            
            if ($existencias) {
                $existencias->stock_actual += $movimiento->cantidad_base;
                $existencias->save();
            }
            
            // Eliminar el movimiento
            $movimiento->delete();
        }
    }
    
    /**
     * Eliminar todos los mantenimientos relacionados de un ciclo
     */
    public function eliminarCiclo(Request $request, MantenimientoUnidad $mantenimiento)
    {
        // Solo admin/gerente pueden eliminar
        $user = Auth::user();
        if (!($user->role === 'admin' || $user->role === 'gerente')) {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar mantenimientos cíclicos.');
        }
        // Buscar todos los mantenimientos del ciclo
        $ciclo = MantenimientoUnidad::where('unidad_produccion_id', $mantenimiento->unidad_produccion_id)
            ->where('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento)
            ->where('repeat_type', $mantenimiento->repeat_type)
            ->where('repeat_every', $mantenimiento->repeat_every)
            ->where('repeat_unit', $mantenimiento->repeat_unit)
            ->where('descripcion_trabajo', $mantenimiento->descripcion_trabajo)
            ->get();
        $count = $ciclo->count();
        foreach ($ciclo as $m) {
            $this->revertirMovimientosInventario($m);
            $m->delete();
        }
        return redirect()->route('produccion.mantenimientos', $mantenimiento->unidadProduccion)
            ->with('success', "Se eliminaron $count mantenimientos relacionados del ciclo y el inventario ha sido revertido.");
    }
}
