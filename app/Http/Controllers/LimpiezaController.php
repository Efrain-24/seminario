<?php

namespace App\Http\Controllers;

use App\Models\Limpieza;
use App\Models\ProtocoloSanidad;
use App\Models\User;
use App\Models\UnidadProduccion;
use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimpiezaController extends Controller
{
    /**
     * Eliminar mantenimiento creado en gestión de mantenimientos (solo gerente)
     */
    public function eliminarMantenimiento($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'gerente') {
            return redirect()->route('limpieza.index')->with('error', 'No tienes permisos para eliminar mantenimientos.');
        }
        $mantenimiento = \App\Models\MantenimientoUnidad::find($id);
        if (!$mantenimiento) {
            return redirect()->route('limpieza.index')->with('error', 'Mantenimiento no encontrado.');
        }
        $mantenimiento->delete();
        return redirect()->route('limpieza.index')->with('success', 'Mantenimiento eliminado correctamente.');
    }
    /**
     * Completar limpieza y descontar inventario según protocolo planificado
     */
    public function completar(Request $request)
    {
        $id = $request->input('limpieza_id');
        $insumosReales = $request->input('insumos_reales', []);

        // Intentar encontrar el registro en Limpieza
        $limpieza = Limpieza::find($id);
        if ($limpieza) {
            if ($limpieza->estado === 'completado') {
                return redirect()->route('limpieza.index')->with('error', 'Registro ya completado.');
            }

            try {
                // Si hay insumos reales, usar el nuevo método
                if (!empty($insumosReales)) {
                    $limpieza->ejecutarConConsumoReal($insumosReales, $request->input('observaciones'));
                    
                    // Registrar en bitácora
                    \App\Models\Bitacora::create([
                        'user_id' => Auth::id(),
                        'accion' => 'completado limpieza con insumos',
                        'detalles' => "modulo: Limpieza | documento: Limpieza #{$limpieza->id} | estado: completado | insumos: " . count($insumosReales) . " | fecha: " . now()->format('Y-m-d H:i:s'),
                    ]);
                    
                    return redirect()->route('limpieza.index')->with('success', 'Limpieza completada con consumo real de insumos registrado.');
                } else {
                    // Método anterior para compatibilidad
                    $limpieza->estado = 'completado';
                    $limpieza->save();
                    
                    // Registrar en bitácora
                    \App\Models\Bitacora::create([
                        'user_id' => Auth::id(),
                        'accion' => 'completado limpieza',
                        'detalles' => "modulo: Limpieza | documento: Limpieza #{$limpieza->id} | estado: completado | fecha: " . now()->format('Y-m-d H:i:s'),
                    ]);
                    
                    return redirect()->route('limpieza.index')->with('success', 'Limpieza completada.');
                }
            } catch (\Exception $e) {
                return redirect()->route('limpieza.index')->with('error', 'Error al completar limpieza: ' . $e->getMessage());
            }
        }

        // Si no existe en Limpieza, intentar en MantenimientoUnidad
        $mantenimiento = \App\Models\MantenimientoUnidad::find($id);
        if ($mantenimiento) {
            if ($mantenimiento->estado_mantenimiento === 'completado') {
                return redirect()->route('limpieza.index')->with('error', 'Registro ya completado.');
            }
            $mantenimiento->estado_mantenimiento = 'completado';
            $mantenimiento->save();
            // Registrar en bitácora
            \App\Models\Bitacora::create([
                'user_id' => Auth::id(),
                'accion' => 'completado mantenimiento',
                'detalles' => "modulo: Mantenimiento | documento: Mantenimiento #{$mantenimiento->id} | estado: completado | fecha: " . now()->format('Y-m-d H:i:s'),
            ]);
            // Aquí podrías descontar inventario si aplica
            return redirect()->route('limpieza.index')->with('success', 'Mantenimiento completado.');
        }

        return redirect()->route('limpieza.index')->with('error', 'Registro no encontrado.');
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function historialUnidad($codigo)
    {
        $unidad = \App\Models\UnidadProduccion::where('codigo', $codigo)->firstOrFail();
        $limpiezas = \App\Models\Limpieza::where('area', 'Unidad: ' . $unidad->codigo)
            ->orderByDesc('fecha')
            ->get();
        return view('limpieza.historial_unidad', compact('unidad', 'limpiezas'));
    }
    public function index(Request $request)
    {
        $query = Limpieza::with('protocoloSanidad');
        
        // Filtro por tipo de área
        if ($request->filled('filtro_area')) {
            $filtroArea = $request->filtro_area;
            if ($filtroArea === 'unidades') {
                $query->where('area', 'LIKE', 'Unidad:%');
            } elseif ($filtroArea === 'bodegas') {
                $query->where('area', 'LIKE', 'Bodega:%');
            } elseif ($filtroArea === 'otras') {
                $query->where('area', 'NOT LIKE', 'Unidad:%')
                     ->where('area', 'NOT LIKE', 'Bodega:%');
            }
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por responsable
        if ($request->filled('responsable')) {
            $query->where('responsable', 'LIKE', '%' . $request->responsable . '%');
        }

        // Filtro por protocolo
        if ($request->filled('protocolo')) {
            $query->where('protocolo_sanidad_id', $request->protocolo);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por búsqueda general
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('area', 'like', "%{$buscar}%")
                  ->orWhere('responsable', 'like', "%{$buscar}%")
                  ->orWhere('observaciones', 'like', "%{$buscar}%");
            });
        }
        
        $limpiezas = $query->orderBy('fecha', 'desc')->get();

        // Obtener mantenimientos de tipo limpieza
        $mantenimientosLimpieza = \App\Models\MantenimientoUnidad::with('unidadProduccion')
            ->where('tipo_mantenimiento', 'Limpieza')
            ->orderByDesc('fecha_mantenimiento')
            ->get();

        // Convertir mantenimientos en formato similar a limpieza para la vista
        $mantenimientosLimpieza = $mantenimientosLimpieza->map(function($m) {
            return (object) [
                'fecha' => $m->fecha_mantenimiento,
                'area' => 'Unidad: ' . ($m->unidadProduccion->codigo ?? ''),
                'responsable' => $m->usuario->name ?? '',
                'protocoloSanidad' => null,
                'estado' => $m->estado_mantenimiento,
                'actividades_normalizadas' => [],
                'descripcion_trabajo' => $m->descripcion_trabajo,
                'origen' => 'mantenimiento',
                'mantenimiento_id' => $m->id,
                'id' => null,
            ];
        });

        // Unir ambos y agrupar por unidad
        $todos = $limpiezas->concat($mantenimientosLimpieza);

        // Agregar todos los mantenimientos creados en el módulo de mantenimiento (no solo los de tipo limpieza)
        $mantenimientosTodos = \App\Models\MantenimientoUnidad::with('unidadProduccion', 'usuario')
            ->orderByDesc('fecha_mantenimiento')
            ->get();

        $mantenimientosTodos = $mantenimientosTodos->map(function($m) {
            return (object) [
                'fecha' => $m->fecha_mantenimiento,
                'area' => 'Unidad: ' . ($m->unidadProduccion->codigo ?? ''),
                'responsable' => $m->usuario->name ?? '',
                'protocoloSanidad' => null,
                'estado' => $m->estado_mantenimiento,
                'actividades_normalizadas' => [],
                'descripcion_trabajo' => $m->descripcion_trabajo,
                'origen' => 'mantenimiento',
                'tipo_mantenimiento' => $m->tipo_mantenimiento,
                'mantenimiento_id' => $m->id,
                'id' => null,
            ];
        });

        // Unir todos los mantenimientos (de cualquier tipo) con los registros de limpieza
        $todos = $todos->concat($mantenimientosTodos);

        // Obtener datos para los selectores de filtros
        $protocolos = ProtocoloSanidad::vigentes()->get();
        $responsables = Limpieza::distinct()->pluck('responsable')->filter()->sort();

        return view('limpieza.index', compact('limpiezas', 'protocolos', 'responsables', 'todos'));
    }

    public function create()
    {
        $protocolos = ProtocoloSanidad::vigentes()->get();
        $usuarios = User::active()->get();
        
        // Obtener unidades de producción y bodegas para el dropdown de área
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        $bodegas = Bodega::all();
        
        return view('limpieza.create', compact('protocolos', 'usuarios', 'unidades', 'bodegas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'area' => 'required',
            'responsable' => 'required',
            'protocolo_sanidad_id' => 'required|exists:protocolo_sanidads,id',
            'actividades_ejecutadas' => 'nullable|array',
            'estado' => 'required|in:no_ejecutado,en_progreso,completado',
        ]);

        $data = $request->only(['fecha', 'area', 'responsable', 'protocolo_sanidad_id', 'observaciones', 'estado']);
        $data['actividades_ejecutadas'] = $request->actividades_ejecutadas ?? [];

        // Normalización server-side: si solo llegó una actividad con múltiples sub-actividades embebidas, separarlas.
        if (is_array($data['actividades_ejecutadas']) && count($data['actividades_ejecutadas']) === 1) {
            $firstKey = array_key_first($data['actividades_ejecutadas']);
            $first = $data['actividades_ejecutadas'][$firstKey];
            if (is_array($first) && isset($first['descripcion']) && is_string($first['descripcion'])) {
                $raw = $first['descripcion'];
                // Detectar separadores comunes o numeraciones
                $hasDelimiters = preg_match('/[\r\n;|,]/', $raw) || preg_match('/\d+\s*[).:-]\s+/', $raw);
                if ($hasDelimiters) {
                    // Primero dividir por saltos / ; | luego por comas si aún quedan bloques grandes
                    $parts = preg_split('/[\r\n;|]+/', $raw);
                    $expanded = [];
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p === '') continue;
                        // Intentar subdividir por comas cuando parezca lista corta
                        if (strpos($p, ',') !== false && strlen($p) > 20) {
                            $commaSegments = array_map('trim', explode(',', $p));
                            // Si produce más de 1 segmento corto, aceptarlo
                            if (count($commaSegments) > 1) {
                                foreach ($commaSegments as $seg) {
                                    if ($seg !== '') {
                                        $expanded[] = $seg;
                                    }
                                }
                                continue;
                            }
                        }
                        // Numeraciones dentro de un bloque
                        if (preg_match('/\d+\s*[).:-]\s+/', $p)) {
                            $numParts = preg_split('/\s*\d+\s*[).:-]\s*/', $p);
                            $numParts = array_filter(array_map('trim', $numParts));
                            if (count($numParts) > 1) {
                                foreach ($numParts as $seg) {
                                    if ($seg !== '') {
                                        $expanded[] = $seg;
                                    }
                                }
                                continue;
                            }
                        }
                        $expanded[] = $p;
                    }
                    if (count($expanded) > 1) {
                        $data['actividades_ejecutadas'] = [];
                        foreach ($expanded as $idx => $desc) {
                            $data['actividades_ejecutadas'][$idx] = [
                                'descripcion' => $desc,
                                'completada' => false,
                                'observaciones' => null,
                            ];
                        }
                    }
                }
            }
        }

        Limpieza::create($data);
        return redirect()->route('limpieza.index')->with('success', 'Registro de limpieza creado exitosamente.');
    }

    public function show(Limpieza $limpieza)
    {
        // Detectar si el área hace referencia a una unidad (formato "Unidad: CODIGO")
        $protocolosUnidad = collect();
        $limpiezasPorProtocolo = [];
        if ($limpieza->area && str_starts_with($limpieza->area, 'Unidad:')) {
            $codigo = trim(str_replace('Unidad:', '', $limpieza->area));
            $unidad = \App\Models\UnidadProduccion::where('codigo', $codigo)->first();
            if ($unidad) {
                $protocolosUnidad = \App\Models\ProtocoloSanidad::where('unidad_produccion_id', $unidad->id)
                    ->orderBy('nombre')->orderByDesc('version')->get();
                foreach ($protocolosUnidad as $protocolo) {
                    $limpiezasPorProtocolo[$protocolo->id] = \App\Models\Limpieza::where('protocolo_sanidad_id', $protocolo->id)
                        ->where('area', 'LIKE', 'Unidad: ' . $unidad->codigo)
                        ->orderByDesc('fecha')->get();
                }
            }
        }
        return view('limpieza.show', compact('limpieza','protocolosUnidad','limpiezasPorProtocolo'));
        return view('limpieza.show', compact('limpieza','protocolosUnidad'));
    }

    public function edit(Limpieza $limpieza)
    {
        // Verificar que solo se puedan editar registros no completados
        if ($limpieza->estado === 'completado') {
            return redirect()->route('limpieza.index')
                ->with('error', 'No se puede editar un registro de limpieza completado.');
        }

        // Cargar relación con protocolo e insumos
        $limpieza->load(['protocoloSanidad.insumos.inventarioItem']);

        $protocolos = ProtocoloSanidad::vigentes()->get();
        $usuarios = User::active()->get();
        
        // Obtener unidades de producción y bodegas para el dropdown de área
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        $bodegas = Bodega::all();
        
        // Obtener insumos del protocolo si existe y tiene insumos
        $insumosProtocolo = collect([]);
        if ($limpieza->protocoloSanidad && $limpieza->protocoloSanidad->insumos->isNotEmpty()) {
            $insumosProtocolo = $limpieza->protocoloSanidad->insumos->map(function($insumo) {
                return [
                    'id' => $insumo->inventarioItem->id,
                    'nombre' => $insumo->inventarioItem->nombre,
                    'unidad' => $insumo->unidad,
                    'cantidad_planificada' => $insumo->cantidad_necesaria,
                    'stock_disponible' => $insumo->inventarioItem->stockTotal(),
                    'costo_unitario' => $insumo->inventarioItem->costo_unitario ?? 0,
                    'es_obligatorio' => $insumo->es_obligatorio,
                ];
            });
        }
        
        return view('limpieza.edit', compact('limpieza', 'protocolos', 'usuarios', 'unidades', 'bodegas', 'insumosProtocolo'));
    }

    public function update(Request $request, Limpieza $limpieza)
    {
        // Verificar que solo se puedan actualizar registros no completados
        if ($limpieza->estado === 'completado') {
            return redirect()->route('limpieza.index')
                ->with('error', 'No se puede modificar un registro de limpieza completado.');
        }

        try {
            $request->validate([
                'fecha' => 'required|date',
                'area' => 'required',
                'responsable' => 'required',
                'protocolo_sanidad_id' => 'required|exists:protocolo_sanidads,id',
                'actividades_ejecutadas' => 'nullable|array',
                'estado' => 'required|in:no_ejecutado,en_progreso,completado',
            ]);

            $data = $request->only(['fecha', 'area', 'responsable', 'protocolo_sanidad_id', 'observaciones', 'estado']);
            $data['actividades_ejecutadas'] = $request->actividades_ejecutadas ?? [];

            // Si se está completando la limpieza y hay insumos reales válidos, procesarlos
            if ($request->estado === 'completado' && $request->has('insumos_reales') && is_array($request->insumos_reales) && !empty($request->insumos_reales)) {
                try {
                    // Filtrar solo insumos con datos válidos
                    $insumosValidos = array_filter($request->insumos_reales, function($insumo) {
                        return isset($insumo['inventario_item_id']) && isset($insumo['cantidad_real']) && is_numeric($insumo['cantidad_real']);
                    });

                    if (!empty($insumosValidos)) {
                        $costoTotal = $limpieza->ejecutarConConsumoReal($insumosValidos);
                        $data['costo_total'] = $costoTotal;
                        $data['insumos_consumidos'] = json_encode($insumosValidos);
                        
                        $limpieza->update($data);
                        
                        return redirect()->route('limpieza.index')
                            ->with('success', 'Limpieza completada exitosamente. Costo total: Q' . number_format($costoTotal, 2));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error al procesar insumos: ' . $e->getMessage());
                }
            }

            $limpieza->update($data);
            return redirect()->route('limpieza.index')
                ->with('success', 'Limpieza actualizada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar limpieza: ' . $e->getMessage());
        }
    }

    public function destroy(Limpieza $limpieza)
    {
        $limpieza->delete();
        return redirect()->route('limpieza.index');
    }

    public function getProtocoloActividades($protocoloId)
    {
        $protocolo = ProtocoloSanidad::findOrFail($protocoloId);
        $actividadesRaw = $protocolo->actividades ?? [];

        // Normalización: si viene como string (multilínea) o un solo elemento con saltos de línea / delimitadores, separarlo
        if (is_string($actividadesRaw)) {
            $actividades = preg_split('/[\r\n;|]+/', $actividadesRaw);
        } else {
            $actividades = $actividadesRaw;
        }

        // Caso: array con un único string que contiene saltos de línea o delimitadores
        if (is_array($actividades) && count($actividades) === 1 && is_string($actividades[0]) && preg_match('/\r|\n|;|\|/', $actividades[0])) {
            $actividades = preg_split('/[\r\n;|]+/', $actividades[0]);
        }

        // Fallback: si aún solo hay un elemento y contiene múltiples actividades separadas por comas
        if (is_array($actividades) && count($actividades) === 1 && is_string($actividades[0]) && str_contains($actividades[0], ',') ) {
            // Evitar dividir si parece una lista corta con comas dentro de una sola descripción (heurística: más de 1 coma)
            $comaCount = substr_count($actividades[0], ',');
            if ($comaCount >= 1) {
                $actividades = array_map('trim', explode(',', $actividades[0]));
            }
        }

        // Detección de numeraciones ("1) Limpia... 2) Desinfecta...") solo si seguimos con 1 elemento largo
        if (is_array($actividades) && count($actividades) === 1 && is_string($actividades[0]) && preg_match('/\d+\s*[\).:-]\s+/', $actividades[0])) {
            $tmp = preg_split('/\s*\d+\s*[\).:-]\s*/', $actividades[0]);
            $tmp = array_filter(array_map('trim', $tmp));
            if (count($tmp) > 1) {
                $actividades = array_values($tmp);
            }
        }

        // Limpiar, trim y descartar vacíos
        $actividades = array_values(array_filter(array_map(function ($a) {
            return is_string($a) ? trim($a) : $a;
        }, $actividades), function ($a) {
            return !empty($a);
        }));

        return response()->json([
            'actividades' => $actividades
        ]);
    }

    /**
     * Obtener insumos de un protocolo específico
     */
    public function obtenerInsumosProtocolo($protocoloId)
    {
        try {
            $protocolo = ProtocoloSanidad::with(['insumos.inventarioItem'])->find($protocoloId);
            
            if (!$protocolo) {
                return response()->json(['error' => 'Protocolo no encontrado'], 404);
            }

            $insumos = [];
            if ($protocolo->insumos && $protocolo->insumos->isNotEmpty()) {
                $insumos = $protocolo->insumos->map(function($insumo) {
                    return [
                        'id' => $insumo->inventarioItem->id,
                        'nombre' => $insumo->inventarioItem->nombre,
                        'unidad' => $insumo->unidad,
                        'cantidad_planificada' => $insumo->cantidad_necesaria,
                        'stock_disponible' => $insumo->inventarioItem->stockTotal(),
                        'costo_unitario' => $insumo->inventarioItem->costo_unitario ?? 0,
                        'es_obligatorio' => $insumo->es_obligatorio,
                    ];
                });
            }

            return response()->json([
                'insumos' => $insumos
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener insumos'], 500);
        }
    }
}
