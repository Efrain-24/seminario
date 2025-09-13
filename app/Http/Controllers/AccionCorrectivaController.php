<?php

namespace App\Http\Controllers;

use App\Models\AccionCorrectiva;
use App\Models\User;
use Illuminate\Http\Request;

class AccionCorrectivaController extends Controller
{
    // El middleware se aplica en las rutas, no aquí

    public function index(Request $request)
    {
        $query = AccionCorrectiva::with('responsable');
        
        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }
        
        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        // Filtro por responsable
        if ($request->filled('responsable')) {
            $query->where('user_id', $request->responsable);
        }
        
        $acciones = $query->orderBy('id', 'desc')->paginate(10);
        $usuarios = User::orderBy('name')->get();
        
        return view('acciones_correctivas.index', compact('acciones', 'usuarios'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('acciones_correctivas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        // Filtrar archivos vacíos del request
        if ($request->has('evidencias')) {
            $evidenciasLimpias = array_filter($request->file('evidencias') ?? [], function($archivo) {
                return $archivo !== null && $archivo->isValid();
            });
            $request->merge(['evidencias' => $evidenciasLimpias]);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'fecha_prevista' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_prevista',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'user_id.required' => 'El responsable es obligatorio.',
            'fecha_prevista.required' => 'La fecha prevista es obligatoria.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser posterior o igual a la fecha prevista.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        AccionCorrectiva::create($request->only([
            'titulo',
            'descripcion', 
            'user_id',
            'fecha_prevista',
            'fecha_limite',
            'estado'
        ]));
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva registrada correctamente.');
    }

    public function edit(AccionCorrectiva $accion)
    {
        $usuarios = User::orderBy('name')->get();
        return view('acciones_correctivas.edit', [
            'accion' => $accion,
            'usuarios' => $usuarios
        ]);
    }
        public function show(AccionCorrectiva $accion)
    {
        // Cargar solo seguimientos activos
        $accion->load(['responsable']);
        $accion->load(['seguimientos' => function($query) {
            $query->activos()->with('usuario');
        }]);
        
        $usuarios = \App\Models\User::orderBy('name')->get();
        return view('acciones_correctivas.show', [
            'accion' => $accion,
            'usuarios' => $usuarios
        ]);
    }

    public function update(Request $request, AccionCorrectiva $accion)
    {
        // Filtrar archivos vacíos del request
        if ($request->has('evidencias')) {
            $evidenciasLimpias = array_filter($request->file('evidencias') ?? [], function($archivo) {
                return $archivo !== null && $archivo->isValid();
            });
            $request->merge(['evidencias' => $evidenciasLimpias]);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'fecha_prevista' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_prevista',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'user_id.required' => 'El responsable es obligatorio.',
            'fecha_prevista.required' => 'La fecha prevista es obligatoria.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser posterior o igual a la fecha prevista.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        $accion->update($request->only([
            'titulo',
            'descripcion', 
            'user_id',
            'fecha_prevista',
            'fecha_limite',
            'estado'
        ]));
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva actualizada correctamente.');
    }

    public function destroy(AccionCorrectiva $accion)
    {
        $accion->delete();
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva eliminada correctamente.');
    }
    
    public function cambiarEstado(Request $request, AccionCorrectiva $accion)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada'
        ]);
        
        $estadoAnterior = $accion->estado;
        $accion->update(['estado' => $request->estado]);
        
        $estados = [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso', 
            'completada' => 'Completada',
            'cancelada' => 'Cancelada'
        ];
        
        return redirect()->route('acciones_correctivas.index')
            ->with('success', "Estado cambiado de '{$estados[$estadoAnterior]}' a '{$estados[$request->estado]}'");
    }

    public function agregarSeguimiento(Request $request, AccionCorrectiva $accion)
    {
        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'archivo_evidencia' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
            'cambiar_estado' => 'nullable|in:pendiente,en_progreso,completada,cancelada',
        ], [
            'descripcion.required' => 'La descripción del seguimiento es obligatoria.',
            'descripcion.max' => 'La descripción no debe exceder 1000 caracteres.',
            'archivo_evidencia.file' => 'El archivo de evidencia debe ser un archivo válido.',
            'archivo_evidencia.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
            'archivo_evidencia.max' => 'El archivo no debe ser mayor a 10MB.',
            'cambiar_estado.in' => 'El estado seleccionado no es válido.',
        ]);

        // Verificar que hay un usuario autenticado
        $user = \Illuminate\Support\Facades\Auth::user();
        $userId = $user ? $user->id : null;
        if (!$userId) {
            // Si no hay usuario autenticado, usar el primer usuario disponible (admin)
            $userId = \App\Models\User::first()->id ?? 1;
        }

        $seguimientoData = [
            'accion_correctiva_id' => $accion->id,
            'user_id' => $userId,
            'descripcion' => $request->descripcion,
        ];

        // Registrar cambio de estado si se especificó
        if ($request->filled('cambiar_estado')) {
            $seguimientoData['estado_anterior'] = $accion->estado;
            $seguimientoData['estado_nuevo'] = $request->cambiar_estado;
        }

        // Manejar archivo de evidencia
        if ($request->hasFile('archivo_evidencia')) {
            $archivo = $request->file('archivo_evidencia');
            if ($archivo->isValid()) {
                $nombreOriginal = $archivo->getClientOriginalName();
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
                $ruta = $archivo->storeAs('seguimientos', $nombreArchivo, 'public');
                
                $seguimientoData['archivo_evidencia'] = $ruta;
                $seguimientoData['nombre_archivo_original'] = $nombreOriginal;
                $seguimientoData['tipo_archivo'] = $archivo->getMimeType();
                $seguimientoData['tamaño_archivo'] = $archivo->getSize();
            }
        }

        // Si hay cambio de estado, agregar información automáticamente a la descripción
        if ($request->filled('cambiar_estado')) {
            $estados = [
                'pendiente' => 'Pendiente',
                'en_progreso' => 'En Progreso',
                'completada' => 'Completada',
                'cancelada' => 'Cancelada'
            ];
            
            $estadoAnterior = $accion->estado;
            $estadoNuevo = $request->cambiar_estado;
            
            // Agregar información del cambio de estado a la descripción automáticamente
            $seguimientoData['descripcion'] .= "\n\n[Sistema] Estado cambiado de '{$estados[$estadoAnterior]}' a '{$estados[$estadoNuevo]}'";
        }

        // Crear el seguimiento
        \App\Models\SeguimientoAccion::create($seguimientoData);

        // Cambiar estado si se especificó
        $mensaje = 'Seguimiento agregado correctamente.';
        if ($request->filled('cambiar_estado')) {
            $estadoAnterior = $accion->estado;
            $accion->update(['estado' => $request->cambiar_estado]);
            
            $mensaje .= ' Estado cambiado de "' . $estados[$estadoAnterior] . '" a "' . $estados[$request->cambiar_estado] . '".';
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $mensaje]);
        }

        return redirect()->route('acciones_correctivas.show', $accion)
            ->with('success', $mensaje);
    }

    public function editarSeguimiento(AccionCorrectiva $accion, \App\Models\SeguimientoAccion $seguimiento)
    {
        // Verificar que el seguimiento pertenece a la acción correctiva
        if ($seguimiento->accion_correctiva_id !== $accion->id) {
            abort(404);
        }

        return response()->json([
            'id' => $seguimiento->id,
            'descripcion' => $seguimiento->descripcion,
            'archivo_evidencia' => $seguimiento->archivo_evidencia,
            'nombre_archivo_original' => $seguimiento->nombre_archivo_original,
        ]);
    }

    public function actualizarSeguimiento(Request $request, AccionCorrectiva $accion, \App\Models\SeguimientoAccion $seguimiento)
    {
        // Verificar que el seguimiento pertenece a la acción correctiva
        if ($seguimiento->accion_correctiva_id !== $accion->id) {
            abort(404);
        }

        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'archivo_evidencia' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'descripcion.required' => 'La descripción del seguimiento es obligatoria.',
            'descripcion.max' => 'La descripción no debe exceder 1000 caracteres.',
            'archivo_evidencia.file' => 'El archivo de evidencia debe ser un archivo válido.',
            'archivo_evidencia.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
            'archivo_evidencia.max' => 'El archivo no debe ser mayor a 10MB.',
        ]);

        // Actualizar datos básicos
        $seguimiento->descripcion = $request->descripcion;

        // Manejar archivo de evidencia si se subió uno nuevo
        if ($request->hasFile('archivo_evidencia')) {
            $archivo = $request->file('archivo_evidencia');
            if ($archivo->isValid()) {
                // Eliminar archivo anterior si existe
                if ($seguimiento->archivo_evidencia && \Storage::disk('public')->exists($seguimiento->archivo_evidencia)) {
                    \Storage::disk('public')->delete($seguimiento->archivo_evidencia);
                }

                // Guardar nuevo archivo
                $nombreOriginal = $archivo->getClientOriginalName();
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
                $ruta = $archivo->storeAs('seguimientos', $nombreArchivo, 'public');
                
                $seguimiento->archivo_evidencia = $ruta;
                $seguimiento->nombre_archivo_original = $nombreOriginal;
                $seguimiento->tipo_archivo = $archivo->getMimeType();
                $seguimiento->tamaño_archivo = $archivo->getSize();
            }
        }

        $seguimiento->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Seguimiento actualizado correctamente.']);
        }

        return redirect()->route('acciones_correctivas.show', $accion)
            ->with('success', 'Seguimiento actualizado correctamente.');
    }

    public function eliminarSeguimiento(AccionCorrectiva $accion, \App\Models\SeguimientoAccion $seguimiento)
    {
        // Verificar que el seguimiento pertenece a la acción correctiva
        if ($seguimiento->accion_correctiva_id !== $accion->id) {
            abort(404);
        }

        // Solo cambiar el estado a 'eliminado' en lugar de eliminar físicamente
        $seguimiento->update(['estado' => 'eliminado']);

        return redirect()->route('acciones_correctivas.show', $accion)
            ->with('success', 'Seguimiento eliminado correctamente.');
    }
}
