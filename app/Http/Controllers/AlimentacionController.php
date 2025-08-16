<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alimentacion;
use App\Models\TipoAlimento;
use App\Models\Lote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AlimentacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Alimentacion::with(['lote.unidadProduccion', 'tipoAlimento', 'usuario']);

        // Filtros
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        if ($request->filled('tipo_alimento_id')) {
            $query->where('tipo_alimento_id', $request->tipo_alimento_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_alimentacion', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Ordenar por fecha más reciente
        $alimentaciones = $query->orderBy('fecha_alimentacion', 'desc')
                               ->orderBy('hora_alimentacion', 'desc')
                               ->paginate(15);

        // Estadísticas del período actual
        $fechaInicio = $request->filled('fecha_inicio') ? $request->fecha_inicio : Carbon::now()->startOfMonth()->toDateString();
        $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : Carbon::now()->endOfMonth()->toDateString();

        $estadisticas = [
            'total_alimentaciones' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->count(),
            'total_cantidad_kg' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->sum('cantidad_kg'),
            'costo_total' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->sum('costo_total'),
            'promedio_diario' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])
                                ->selectRaw('AVG(cantidad_kg) as promedio')
                                ->value('promedio') ?? 0,
        ];

        // Datos para los filtros
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        $tiposAlimento = TipoAlimento::where('activo', true)->orderBy('nombre')->get();
        $usuarios = User::whereIn('role', ['admin', 'manager', 'empleado'])->orderBy('name')->get();

        return view('alimentacion.index', compact(
            'alimentaciones',
            'estadisticas',
            'lotes',
            'tiposAlimento',
            'usuarios',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function create()
    {
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        $tiposAlimento = TipoAlimento::where('activo', true)->orderBy('nombre')->get();

        return view('alimentacion.create', compact('lotes', 'tiposAlimento'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'tipo_alimento_id' => 'required|exists:tipo_alimentos,id',
            'fecha_alimentacion' => 'required|date|before_or_equal:today',
            'hora_alimentacion' => 'required|date_format:H:i',
            'cantidad_kg' => 'required|numeric|min:0.01|max:9999.99',
            'metodo_alimentacion' => 'required|in:' . implode(',', array_keys(Alimentacion::getMetodosAlimentacion())),
            'estado_peces' => 'nullable|in:' . implode(',', array_keys(Alimentacion::getEstadosPeces())),
            'porcentaje_consumo' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Calcular costo automáticamente
        $tipoAlimento = TipoAlimento::find($validated['tipo_alimento_id']);
        if ($tipoAlimento && $tipoAlimento->costo_por_kg) {
            $validated['costo_total'] = $validated['cantidad_kg'] * $tipoAlimento->costo_por_kg;
        }

        // Combinar fecha y hora
        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha_alimentacion'] . ' ' . $validated['hora_alimentacion']);
        $validated['fecha_alimentacion'] = $fechaHora->toDateString();
        $validated['hora_alimentacion'] = $fechaHora->toTimeString();
        $validated['usuario_id'] = Auth::id();

        $alimentacion = Alimentacion::create($validated);

        return redirect()->route('alimentacion.index')
                        ->with('success', 'Registro de alimentación creado exitosamente.');
    }

    public function show(Alimentacion $alimentacion)
    {
        $alimentacion->load(['lote.unidadProduccion', 'tipoAlimento', 'usuario']);
        return view('alimentacion.show', compact('alimentacion'));
    }

    public function edit(Alimentacion $alimentacion)
    {
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        $tiposAlimento = TipoAlimento::where('activo', true)->orderBy('nombre')->get();

        return view('alimentacion.edit', compact('alimentacion', 'lotes', 'tiposAlimento'));
    }

    public function update(Request $request, Alimentacion $alimentacion)
    {
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'tipo_alimento_id' => 'required|exists:tipo_alimentos,id',
            'fecha_alimentacion' => 'required|date|before_or_equal:today',
            'hora_alimentacion' => 'required|date_format:H:i',
            'cantidad_kg' => 'required|numeric|min:0.01|max:9999.99',
            'metodo_alimentacion' => 'required|in:' . implode(',', array_keys(Alimentacion::getMetodosAlimentacion())),
            'estado_peces' => 'nullable|in:' . implode(',', array_keys(Alimentacion::getEstadosPeces())),
            'porcentaje_consumo' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Calcular costo automáticamente
        $tipoAlimento = TipoAlimento::find($validated['tipo_alimento_id']);
        if ($tipoAlimento && $tipoAlimento->costo_por_kg) {
            $validated['costo_total'] = $validated['cantidad_kg'] * $tipoAlimento->costo_por_kg;
        }

        // Combinar fecha y hora
        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha_alimentacion'] . ' ' . $validated['hora_alimentacion']);
        $validated['fecha_alimentacion'] = $fechaHora->toDateString();
        $validated['hora_alimentacion'] = $fechaHora->toTimeString();

        $alimentacion->update($validated);

        return redirect()->route('alimentacion.show', $alimentacion)
                        ->with('success', 'Registro de alimentación actualizado exitosamente.');
    }

    public function destroy(Alimentacion $alimentacion)
    {
        $alimentacion->delete();

        return redirect()->route('alimentacion.index')
                        ->with('success', 'Registro de alimentación eliminado exitosamente.');
    }
}
