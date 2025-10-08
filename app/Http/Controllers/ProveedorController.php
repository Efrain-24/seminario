<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    /**
     * Búsqueda rápida AJAX de proveedores por nombre o NIT
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q',''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $proveedores = Proveedor::where(function($query) use ($q) {
                $query->where('nombre','LIKE',"%{$q}%")
                      ->orWhere('nit','LIKE',"%{$q}%");
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get(['id','nombre','nit','categoria','estado']);
        return response()->json($proveedores);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Proveedor::query()
                ->with(['registradoPor', 'actualizadoPor'])
                ->orderBy('nombre');

            // Aplicar filtros
            if ($request->filled('busqueda')) {
                $busqueda = $request->get('busqueda');
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('nit', 'LIKE', "%{$busqueda}%")
                      ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                      ->orWhere('especialidades', 'LIKE', "%{$busqueda}%");
                });
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->get('estado'));
            }

            if ($request->filled('categoria')) {
                $query->where('categoria', $request->get('categoria'));
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->get('tipo'));
            }

            if ($request->filled('departamento')) {
                $query->where('departamento', $request->get('departamento'));
            }

            // Filtros especiales
            if ($request->get('solo_con_credito') == '1') {
                $query->where('dias_credito', '>', 0);
            }

            if ($request->get('solo_con_saldo') == '1') {
                $query->where('saldo_actual', '>', 0);
            }

            $proveedores = $query->paginate(15)->withQueryString();

            // Estadísticas para el dashboard
            $estadisticas = [
                'total' => Proveedor::count(),
                'activos' => Proveedor::where('estado', 'activo')->count(),
                'inactivos' => Proveedor::where('estado', 'inactivo')->count(),
                'suspendidos' => Proveedor::where('estado', 'suspendido')->count(),
                'con_saldo_pendiente' => Proveedor::where('saldo_actual', '>', 0)->count(),
                'total_saldo_pendiente' => Proveedor::sum('saldo_actual'),
                'compras_mes_actual' => Proveedor::sum('total_compras_mes'),
                'mejor_calificacion' => Proveedor::whereNotNull('calificacion')->max('calificacion') ?? 0,
            ];

            // Opciones para filtros
            $filtros = [
                'departamentos' => Proveedor::whereNotNull('departamento')
                    ->distinct()
                    ->orderBy('departamento')
                    ->pluck('departamento'),
                'estados' => ['activo', 'inactivo', 'suspendido'],
                'categorias' => ['alimentos', 'insumos', 'equipos', 'servicios', 'medicamentos', 'mixto'],
                'tipos' => ['empresa', 'persona', 'cooperativa'],
            ];

            return view('proveedores.index', compact('proveedores', 'estadisticas', 'filtros'));
        
        } catch (\Exception $e) {
            Log::error('Error en ProveedorController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la lista de proveedores: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                // Información básica
                'nombre' => ['required', 'string', 'max:150'],
                'nit' => ['nullable', 'string', 'max:20'],
                'tipo' => ['required', 'in:empresa,persona,cooperativa'],
                'categoria' => ['required', 'in:alimentos,insumos,equipos,servicios,medicamentos,mixto'],
                'estado' => ['required', 'in:activo,inactivo,suspendido'],
                
                // Contacto
                'telefono_principal' => ['nullable', 'string', 'max:20'],
                'telefono_secundario' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:100'],
                'sitio_web' => ['nullable', 'url', 'max:150'],
                
                // Dirección
                'direccion' => ['nullable', 'string', 'max:255'],
                'departamento' => ['nullable', 'string', 'max:50'],
                'municipio' => ['nullable', 'string', 'max:50'],
                'zona' => ['nullable', 'string', 'max:10'],
                'codigo_postal' => ['nullable', 'string', 'max:10'],
                
                // Información comercial
                'limite_credito' => ['nullable', 'numeric', 'min:0'],
                'dias_credito' => ['required', 'integer', 'min:0', 'max:365'],
                'forma_pago_preferida' => ['required', 'in:contado,credito,transferencia,cheque'],
                'moneda_preferida' => ['required', 'in:GTQ,USD,EUR'],
                
                // Contacto comercial
                'contacto_comercial_nombre' => ['nullable', 'string', 'max:100'],
                'contacto_comercial_telefono' => ['nullable', 'string', 'max:20'],
                'contacto_comercial_email' => ['nullable', 'email', 'max:100'],
                'contacto_comercial_cargo' => ['nullable', 'string', 'max:80'],
                
                // Información adicional
                'especialidades' => ['nullable', 'string'],
                'condiciones_especiales' => ['nullable', 'string'],
                'notas' => ['nullable', 'string'],
                'requiere_orden_compra' => ['boolean'],
                'acepta_devoluciones' => ['boolean'],
                
                // Certificaciones (como string separado por comas)
                'certificaciones_input' => ['nullable', 'string'],
            ]);

            // Procesar certificaciones
            if (!empty($data['certificaciones_input'])) {
                $certificaciones = array_map('trim', explode(',', $data['certificaciones_input']));
                $data['certificaciones'] = array_filter($certificaciones);
                unset($data['certificaciones_input']);
            }

            // Validar NIT único si se proporciona
            if (!empty($data['nit'])) {
                $nitExistente = Proveedor::where('nit', $data['nit'])
                    ->where('nit', '!=', '')
                    ->exists();
                
                if ($nitExistente) {
                    return back()->with('error', 'Ya existe un proveedor con este NIT.')
                               ->withInput();
                }
            }

            DB::beginTransaction();

            $proveedor = Proveedor::create($data);

            DB::commit();

            return redirect()
                ->route('proveedores.show', $proveedor)
                ->with('success', 'Proveedor creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Error de validación: revisa los datos ingresados.')
                       ->withErrors($e->errors())
                       ->withInput();
                       
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al crear el proveedor: ' . $e->getMessage())
                       ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        try {
            $proveedor->load(['registradoPor', 'actualizadoPor']);

            // Aquí se pueden agregar estadísticas específicas del proveedor
            $estadisticas = [
                'dias_sin_compras' => $proveedor->diasSinCompras(),
                'total_certificaciones' => is_array($proveedor->certificaciones) ? count($proveedor->certificaciones) : 0,
                'porcentaje_cumplimiento_color' => $this->getColorPorcentajeCumplimiento($proveedor->porcentaje_cumplimiento),
                'calificacion_color' => $this->getColorCalificacion($proveedor->calificacion),
            ];

            // Últimas actividades (se pueden agregar cuando se implementen compras/órdenes)
            $actividades = collect(); // Por ahora vacío

            return view('proveedores.show', compact('proveedor', 'estadisticas', 'actividades'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la información del proveedor.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        // Preparar certificaciones para el formulario
        $certificacionesString = '';
        if (is_array($proveedor->certificaciones)) {
            $certificacionesString = implode(', ', $proveedor->certificaciones);
        }

        return view('proveedores.edit', compact('proveedor', 'certificacionesString'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        try {
            $data = $request->validate([
                // Información básica
                'nombre' => ['required', 'string', 'max:150'],
                'nit' => ['nullable', 'string', 'max:20'],
                'tipo' => ['required', 'in:empresa,persona,cooperativa'],
                'categoria' => ['required', 'in:alimentos,insumos,equipos,servicios,medicamentos,mixto'],
                'estado' => ['required', 'in:activo,inactivo,suspendido'],
                
                // Contacto
                'telefono_principal' => ['nullable', 'string', 'max:20'],
                'telefono_secundario' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:100'],
                'sitio_web' => ['nullable', 'url', 'max:150'],
                
                // Dirección
                'direccion' => ['nullable', 'string', 'max:255'],
                'departamento' => ['nullable', 'string', 'max:50'],
                'municipio' => ['nullable', 'string', 'max:50'],
                'zona' => ['nullable', 'string', 'max:10'],
                'codigo_postal' => ['nullable', 'string', 'max:10'],
                
                // Información comercial
                'limite_credito' => ['nullable', 'numeric', 'min:0'],
                'dias_credito' => ['required', 'integer', 'min:0', 'max:365'],
                'forma_pago_preferida' => ['required', 'in:contado,credito,transferencia,cheque'],
                'moneda_preferida' => ['required', 'in:GTQ,USD,EUR'],
                
                // Contacto comercial
                'contacto_comercial_nombre' => ['nullable', 'string', 'max:100'],
                'contacto_comercial_telefono' => ['nullable', 'string', 'max:20'],
                'contacto_comercial_email' => ['nullable', 'email', 'max:100'],
                'contacto_comercial_cargo' => ['nullable', 'string', 'max:80'],
                
                // Información adicional
                'especialidades' => ['nullable', 'string'],
                'condiciones_especiales' => ['nullable', 'string'],
                'notas' => ['nullable', 'string'],
                'requiere_orden_compra' => ['boolean'],
                'acepta_devoluciones' => ['boolean'],
                
                // Certificaciones
                'certificaciones_input' => ['nullable', 'string'],
            ]);

            // Procesar certificaciones
            if (!empty($data['certificaciones_input'])) {
                $certificaciones = array_map('trim', explode(',', $data['certificaciones_input']));
                $data['certificaciones'] = array_filter($certificaciones);
                unset($data['certificaciones_input']);
            } else {
                $data['certificaciones'] = null;
            }

            // Validar NIT único si se proporciona y cambió
            if (!empty($data['nit']) && $data['nit'] !== $proveedor->nit) {
                $nitExistente = Proveedor::where('nit', $data['nit'])
                    ->where('nit', '!=', '')
                    ->where('id', '!=', $proveedor->id)
                    ->exists();
                
                if ($nitExistente) {
                    return back()->with('error', 'Ya existe un proveedor con este NIT.')
                               ->withInput();
                }
            }

            DB::beginTransaction();

            $proveedor->update($data);

            DB::commit();

            return redirect()
                ->route('proveedores.show', $proveedor)
                ->with('success', 'Proveedor actualizado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Error de validación: revisa los datos ingresados.')
                       ->withErrors($e->errors())
                       ->withInput();
                       
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage())
                       ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        try {
            // Verificar si el proveedor tiene relaciones que impidan su eliminación
            // (Aquí se pueden agregar validaciones para órdenes de compra, etc.)
            
            DB::beginTransaction();

            $nombre = $proveedor->nombre;
            $proveedor->delete();

            DB::commit();

            return redirect()
                ->route('proveedores.index')
                ->with('success', "Proveedor '{$nombre}' eliminado exitosamente.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al eliminar proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar el estado del proveedor
     */
    public function cambiarEstado(Request $request, Proveedor $proveedor)
    {
        try {
            $data = $request->validate([
                'estado' => ['required', 'in:activo,inactivo,suspendido'],
                'motivo' => ['nullable', 'string', 'max:255'],
            ]);

            $estadoAnterior = $proveedor->estado;
            
            $proveedor->update([
                'estado' => $data['estado']
            ]);

            // Si se proporciona un motivo, agregarlo a las notas
            if (!empty($data['motivo'])) {
                $fecha = now()->format('d/m/Y H:i');
                $usuario = Auth::user()->name ?? 'Sistema';
                $notaNueva = "[{$fecha}] {$usuario}: Cambio de estado de '{$estadoAnterior}' a '{$data['estado']}'. Motivo: {$data['motivo']}";
                
                $notasActuales = $proveedor->notas ? $proveedor->notas . "\n\n" : '';
                $proveedor->update(['notas' => $notasActuales . $notaNueva]);
            }

            $mensaje = "Estado del proveedor cambiado de '{$estadoAnterior}' a '{$data['estado']}'.";

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar el estado del proveedor.');
        }
    }

    /**
     * Evaluar proveedor
     */
    public function evaluar(Request $request, Proveedor $proveedor)
    {
        try {
            $data = $request->validate([
                'calificacion' => ['required', 'numeric', 'min:1', 'max:5'],
                'comentarios' => ['nullable', 'string'],
                'tiempo_entrega' => ['nullable', 'numeric', 'min:0'],
                'porcentaje_cumplimiento' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            // Actualizar calificación promedio
            $proveedor->actualizarCalificacion($data['calificacion']);

            // Actualizar métricas si se proporcionan
            if (isset($data['tiempo_entrega'])) {
                $tiempoActual = $proveedor->tiempo_entrega_promedio ?? 0;
                $totalEvaluaciones = $proveedor->total_evaluaciones;
                $nuevoPromedio = (($tiempoActual * ($totalEvaluaciones - 1)) + $data['tiempo_entrega']) / $totalEvaluaciones;
                
                $proveedor->update(['tiempo_entrega_promedio' => round($nuevoPromedio, 2)]);
            }

            if (isset($data['porcentaje_cumplimiento'])) {
                $proveedor->update(['porcentaje_cumplimiento' => $data['porcentaje_cumplimiento']]);
            }

            // Agregar comentarios a las notas si se proporcionan
            if (!empty($data['comentarios'])) {
                $fecha = now()->format('d/m/Y H:i');
                $usuario = Auth::user()->name ?? 'Sistema';
                $notaNueva = "[{$fecha}] Evaluación de {$usuario} (★{$data['calificacion']}/5): {$data['comentarios']}";
                
                $notasActuales = $proveedor->notas ? $proveedor->notas . "\n\n" : '';
                $proveedor->update(['notas' => $notasActuales . $notaNueva]);
            }

            return back()->with('success', 'Evaluación registrada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al evaluar proveedor: ' . $e->getMessage());
            return back()->with('error', 'Error al registrar la evaluación.');
        }
    }

    /**
     * Métodos auxiliares
     */
    private function getColorPorcentajeCumplimiento($porcentaje)
    {
        if (is_null($porcentaje)) return 'gray';
        
        if ($porcentaje >= 95) return 'green';
        if ($porcentaje >= 85) return 'yellow';
        if ($porcentaje >= 70) return 'orange';
        return 'red';
    }

    private function getColorCalificacion($calificacion)
    {
        if (is_null($calificacion)) return 'gray';
        
        if ($calificacion >= 4.5) return 'green';
        if ($calificacion >= 4.0) return 'blue';
        if ($calificacion >= 3.5) return 'yellow';
        if ($calificacion >= 3.0) return 'orange';
        return 'red';
    }
}