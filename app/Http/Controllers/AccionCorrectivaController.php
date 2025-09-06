<?php

namespace App\Http\Controllers;

use App\Models\AccionCorrectiva;
use App\Models\User;
use Illuminate\Http\Request;

class AccionCorrectivaController extends Controller
{
    public function index()
    {
        $acciones = AccionCorrectiva::with('responsable')->latest()->paginate(10);
        return view('acciones_correctivas.index', compact('acciones'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('acciones_correctivas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'fecha_detectada' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_detectada',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'observaciones' => 'nullable|string',
        ]);
        AccionCorrectiva::create($request->all());
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva registrada correctamente.');
    }

    public function edit(AccionCorrectiva $acciones_correctiva)
    {
        $usuarios = User::orderBy('name')->get();
        return view('acciones_correctivas.edit', [
            'accion' => $acciones_correctiva,
            'usuarios' => $usuarios
        ]);
    }
        public function show(AccionCorrectiva $acciones_correctiva)
    {
        return view('acciones_correctivas.show', ['accion' => $acciones_correctiva]);
    }

    public function update(Request $request, AccionCorrectiva $acciones_correctiva)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'fecha_detectada' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_detectada',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'observaciones' => 'nullable|string',
        ]);
        $acciones_correctiva->update($request->all());
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva actualizada correctamente.');
    }

    public function destroy(AccionCorrectiva $acciones_correctiva)
    {
        $acciones_correctiva->delete();
        return redirect()->route('acciones_correctivas.index')->with('success','Acción correctiva eliminada correctamente.');
    }
}
