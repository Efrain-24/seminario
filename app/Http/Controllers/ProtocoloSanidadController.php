<?php

namespace App\Http\Controllers;

use App\Models\ProtocoloSanidad;
use App\Models\User;
use Illuminate\Http\Request;

class ProtocoloSanidadController extends Controller
{
    public function index(Request $request)
    {
        $query = ProtocoloSanidad::with('protocoloBase');

        // Filtro por búsqueda general
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%")
                  ->orWhere('responsable', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por responsable
        if ($request->filled('responsable')) {
            $query->where('responsable', $request->responsable);
        }

        // Filtro por rango de fechas de implementación
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_implementacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_implementacion', '<=', $request->fecha_hasta);
        }

        // Filtro por versión
        if ($request->filled('version')) {
            $query->where('version', $request->version);
        }

        $protocolos = $query->orderBy('nombre')->orderByDesc('version')->get();
        
        // Obtener datos para los selectores de filtros
        $responsables = ProtocoloSanidad::distinct()->pluck('responsable')->filter()->sort();
        $versiones = ProtocoloSanidad::distinct()->pluck('version')->filter()->sort();
        
        return view('protocolo_sanidad.index', compact('protocolos', 'responsables', 'versiones'));
    }

    public function create()
    {
        $usuarios = User::active()->get();
        // Obtener solo insumos (no alimentos) del inventario
        $insumos = \App\Models\InventarioItem::where('tipo', 'insumo')
            ->with('existencias.bodega')
            ->get();
            
        return view('protocolo_sanidad.create', compact('usuarios', 'insumos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'fecha_implementacion' => 'required|date',
            'responsable' => 'required',
            'actividades' => 'nullable|array',
            'actividades.*' => 'required|string|max:255',
            // Validación para insumos
            'insumos' => 'nullable|array',
            'insumos.*.inventario_item_id' => 'required_with:insumos|exists:inventario_items,id',
            'insumos.*.cantidad_necesaria' => 'required_with:insumos|numeric|min:0.001',
            'insumos.*.es_obligatorio' => 'boolean',
            'insumos.*.notas' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['nombre', 'fecha_implementacion', 'responsable']);
        $data['actividades'] = array_filter($request->actividades ?? []);

        // Crear el protocolo
        $protocolo = ProtocoloSanidad::create($data);

        // Guardar los insumos si existen
        if ($request->has('insumos') && is_array($request->insumos)) {
            foreach ($request->insumos as $insumoData) {
                if (!empty($insumoData['inventario_item_id']) && !empty($insumoData['cantidad_necesaria'])) {
                    // Obtener la unidad del item del inventario
                    $inventarioItem = \App\Models\InventarioItem::find($insumoData['inventario_item_id']);
                    
                    $protocolo->insumos()->create([
                        'inventario_item_id' => $insumoData['inventario_item_id'],
                        'cantidad_necesaria' => $insumoData['cantidad_necesaria'],
                        'unidad' => $inventarioItem->unidad_base,
                        'es_obligatorio' => $insumoData['es_obligatorio'] ?? true,
                        'notas' => $insumoData['notas'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('protocolo-sanidad.index')
                       ->with('success', 'Protocolo de sanidad creado correctamente.');
    }

    /**
     * Ejecutar protocolo y descontar insumos
     */
    public function ejecutar(ProtocoloSanidad $protocoloSanidad)
    {
        try {
            $protocoloSanidad->ejecutarYDescontarInsumos(request('observaciones_ejecucion'));
            
            return redirect()->route('protocolo-sanidad.show', $protocoloSanidad)
                           ->with('success', 'Protocolo ejecutado correctamente. Los insumos han sido descontados del inventario.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al ejecutar el protocolo: ' . $e->getMessage());
        }
    }

    public function show(ProtocoloSanidad $protocoloSanidad)
    {
        $protocoloSanidad->load('insumos.inventarioItem');
        return view('protocolo_sanidad.show', compact('protocoloSanidad'));
    }

    public function edit(ProtocoloSanidad $protocoloSanidad)
    {
        $usuarios = User::active()->get();
        return view('protocolo_sanidad.edit', compact('protocoloSanidad', 'usuarios'));
    }

    public function update(Request $request, ProtocoloSanidad $protocoloSanidad)
    {
        $request->validate([
            'nombre' => 'required',
            'fecha_implementacion' => 'required|date',
            'responsable' => 'required',
            'actividades' => 'nullable|array',
            'actividades.*' => 'required|string|max:255',
        ]);

        $data = $request->only(['nombre', 'fecha_implementacion', 'responsable']);
        $data['actividades'] = array_filter($request->actividades ?? []);

        $protocoloSanidad->update($data);
        return redirect()->route('protocolo-sanidad.index');
    }

    public function destroy(ProtocoloSanidad $protocoloSanidad)
    {
        $protocoloSanidad->delete();
        return redirect()->route('protocolo-sanidad.index');
    }

    // Método para crear nueva versión de un protocolo
    public function crearNuevaVersion(ProtocoloSanidad $protocoloSanidad)
    {
        $usuarios = User::active()->get();
        return view('protocolo_sanidad.nueva_version', compact('protocoloSanidad', 'usuarios'));
    }

    // Método para guardar la nueva versión
    public function guardarNuevaVersion(Request $request, ProtocoloSanidad $protocoloSanidad)
    {
        $request->validate([
            'nombre' => 'required',
            'fecha_implementacion' => 'required|date',
            'responsable' => 'required',
            'actividades' => 'nullable|array',
            'actividades.*' => 'required|string|max:255',
        ]);

        $data = $request->only(['nombre', 'fecha_implementacion', 'responsable']);
        $data['actividades'] = array_filter($request->actividades ?? []);

        // Crear nueva versión usando el método del modelo
        $nuevaVersion = $protocoloSanidad->crearNuevaVersion($data);

        return redirect()->route('protocolo-sanidad.index')
                        ->with('success', 'Nueva versión del protocolo creada exitosamente. Versión anterior marcada como obsoleta.');
    }

    // Método para marcar un protocolo como obsoleto
    public function marcarObsoleto(ProtocoloSanidad $protocoloSanidad)
    {
        if ($protocoloSanidad->estado === 'vigente') {
            $protocoloSanidad->update(['estado' => 'obsoleta']);
            return redirect()->back()
                            ->with('success', 'Protocolo marcado como obsoleto exitosamente.');
        }

        return redirect()->back()
                        ->with('error', 'El protocolo ya está obsoleto.');
    }
}
