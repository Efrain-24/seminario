<?php

namespace App\Http\Controllers;

use App\Models\Limpieza;
use App\Models\ProtocoloSanidad;
use App\Models\User;
use App\Models\UnidadProduccion;
use App\Models\Bodega;
use Illuminate\Http\Request;

class LimpiezaController extends Controller
{
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

        // Obtener datos para los selectores de filtros
        $protocolos = ProtocoloSanidad::vigentes()->get();
        $responsables = Limpieza::distinct()->pluck('responsable')->filter()->sort();
        
        return view('limpieza.index', compact('limpiezas', 'protocolos', 'responsables'));
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

        Limpieza::create($data);
        return redirect()->route('limpieza.index');
    }

    public function show(Limpieza $limpieza)
    {
        return view('limpieza.show', compact('limpieza'));
    }

    public function edit(Limpieza $limpieza)
    {
        // Verificar que solo se puedan editar registros no completados
        if ($limpieza->estado === 'completado') {
            return redirect()->route('limpieza.index')
                ->with('error', 'No se puede editar un registro de limpieza completado.');
        }

        $protocolos = ProtocoloSanidad::vigentes()->get();
        $usuarios = User::active()->get();
        
        // Obtener unidades de producción y bodegas para el dropdown de área
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        $bodegas = Bodega::all();
        
        return view('limpieza.edit', compact('limpieza', 'protocolos', 'usuarios', 'unidades', 'bodegas'));
    }

    public function update(Request $request, Limpieza $limpieza)
    {
        // Verificar que solo se puedan actualizar registros no completados
        if ($limpieza->estado === 'completado') {
            return redirect()->route('limpieza.index')
                ->with('error', 'No se puede modificar un registro de limpieza completado.');
        }

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

        $limpieza->update($data);
        return redirect()->route('limpieza.index');
    }

    public function destroy(Limpieza $limpieza)
    {
        $limpieza->delete();
        return redirect()->route('limpieza.index');
    }

    public function getProtocoloActividades($protocoloId)
    {
        $protocolo = ProtocoloSanidad::findOrFail($protocoloId);
        return response()->json([
            'actividades' => $protocolo->actividades ?? []
        ]);
    }
}
