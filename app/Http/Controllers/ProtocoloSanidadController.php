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
        return view('protocolo_sanidad.create', compact('usuarios'));
    }

    public function store(Request $request)
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

        ProtocoloSanidad::create($data);
        return redirect()->route('protocolo-sanidad.index');
    }

    public function show(ProtocoloSanidad $protocoloSanidad)
    {
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
