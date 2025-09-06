<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Trazabilidad\TrazabilidadCosecha;
use App\Services\TrazabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrazabilidadCosechaController extends Controller
{
    protected $trazabilidadService;

    public function __construct(TrazabilidadService $trazabilidadService)
    {
        $this->trazabilidadService = $trazabilidadService;
    }

    /**
     * Muestra el listado de registros de trazabilidad
     */
    public function index(Request $request)
    {
        $query = TrazabilidadCosecha::with('lote');

        // Filtro por lote
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        // Filtro por fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_cosecha', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        } elseif ($request->filled('fecha_inicio')) {
            $query->where('fecha_cosecha', '>=', $request->fecha_inicio . ' 00:00:00');
        } elseif ($request->filled('fecha_fin')) {
            $query->where('fecha_cosecha', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Filtro por destino
        if ($request->filled('destino')) {
            $query->where('destino_tipo', $request->destino);
        }

        $trazabilidades = $query->orderBy('fecha_cosecha', 'desc')
                               ->paginate(10);

        // Obtener lotes activos ordenados por código
        $lotes = Lote::orderBy('codigo_lote', 'asc')->get();

        // Obtener estadísticas
        $estadisticas = [
            'total_peso' => $query->sum('peso_neto'),
            'total_cosechas' => $query->count(),
            'costo_promedio' => $query->avg('costo_total'),
            'cosechas_parciales' => $query->where('tipo_cosecha', 'parcial')->count()
        ];

        $lotes = Lote::orderBy('codigo_lote')->get();

        return view('cosechas.trazabilidad.index', compact('trazabilidades', 'estadisticas', 'lotes'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro
     */
    public function create()
    {
        $lotes = Lote::where('estado', '!=', 'cosechado')
                     ->orderBy('codigo_lote')
                     ->get();

        return view('cosechas.trazabilidad.create', compact('lotes'));
    }

    /**
     * Almacena un nuevo registro de trazabilidad
     */
    public function store(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_cosecha' => 'required|date',
            'tipo_cosecha' => 'required|in:parcial,total',
            'peso_bruto' => 'required|numeric|min:0',
            'peso_neto' => 'required|numeric|min:0|lte:peso_bruto',
            'unidades' => 'nullable|integer|min:0',
            'costo_mano_obra' => 'required|numeric|min:0',
            'costo_insumos' => 'required|numeric|min:0',
            'costo_operativo' => 'required|numeric|min:0',
            'destino_tipo' => 'required|in:cliente_final,bodega,mercado_local,exportacion',
            'destino_detalle' => 'required|string|max:255',
            'notas' => 'nullable|string'
        ]);

        try {
            $trazabilidad = $this->trazabilidadService->registrarCosecha($request->all());

            return redirect()
                ->route('cosechas.trazabilidad.index')
                ->with('success', 'Registro de cosecha creado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar la cosecha: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un registro de trazabilidad
     */
    public function show(TrazabilidadCosecha $trazabilidad)
    {
        $trazabilidad->load('lote');

        // Si es cosecha parcial, obtener otras cosechas del mismo lote
        $otrasCosechas = collect();
        if ($trazabilidad->tipo_cosecha === 'parcial') {
            $otrasCosechas = TrazabilidadCosecha::where('lote_id', $trazabilidad->lote_id)
                ->where('id', '!=', $trazabilidad->id)
                ->orderBy('fecha_cosecha', 'desc')
                ->get();
        }

        return view('cosechas.trazabilidad.show', compact('trazabilidad', 'otrasCosechas'));
    }

    /**
     * Muestra el formulario para editar un registro
     */
    public function edit(TrazabilidadCosecha $trazabilidad)
    {
        $lotes = Lote::orderBy('codigo_lote')->get();
        return view('cosechas.trazabilidad.create', compact('trazabilidad', 'lotes'));
    }

    /**
     * Actualiza un registro de trazabilidad
     */
    public function update(Request $request, TrazabilidadCosecha $trazabilidad)
    {
        $this->validarFormulario($request);

        try {
            DB::beginTransaction();

            // Si cambia de cosecha parcial a total, verificar que no existan otras cosechas parciales
            if ($trazabilidad->tipo_cosecha === 'parcial' && $request->tipo_cosecha === 'total') {
                $otrasCosechas = TrazabilidadCosecha::where('lote_id', $trazabilidad->lote_id)
                    ->where('id', '!=', $trazabilidad->id)
                    ->exists();

                if ($otrasCosechas) {
                    throw new \Exception('No se puede cambiar a cosecha total porque existen otras cosechas parciales registradas.');
                }
            }

            $trazabilidad->update($request->all());

            // Si es cosecha total, actualizar el estado del lote
            if ($request->tipo_cosecha === 'total') {
                $trazabilidad->lote->update(['estado' => 'cosechado']);
            }

            DB::commit();

            return redirect()
                ->route('cosechas.trazabilidad.index')
                ->with('success', 'Registro actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un registro de trazabilidad
     */
    public function destroy(TrazabilidadCosecha $trazabilidad)
    {
        try {
            DB::beginTransaction();

            // Si era una cosecha total, revertir el estado del lote
            if ($trazabilidad->tipo_cosecha === 'total') {
                $trazabilidad->lote->update(['estado' => 'activo']);
            }

            $trazabilidad->delete();

            DB::commit();

            return redirect()
                ->route('cosechas.trazabilidad.index')
                ->with('success', 'Registro eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Valida los datos del formulario
     */
    protected function validarFormulario(Request $request)
    {
        $messages = [
            'peso_neto.lte' => 'El peso neto (:input kg) no puede ser mayor que el peso bruto (:value kg).',
        ];

        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_cosecha' => 'required|date',
            'tipo_cosecha' => 'required|in:parcial,total',
            'peso_bruto' => 'required|numeric|min:0',
            'peso_neto' => 'required|numeric|min:0|lte:peso_bruto',
            'unidades' => 'nullable|integer|min:0',
            'costo_mano_obra' => 'required|numeric|min:0',
            'costo_insumos' => 'required|numeric|min:0',
            'costo_operativo' => 'required|numeric|min:0',
            'destino_tipo' => 'required|in:cliente_final,bodega,mercado_local,exportacion',
            'destino_detalle' => 'required|string|max:255',
            'notas' => 'nullable|string'
        ]);
    }
}
