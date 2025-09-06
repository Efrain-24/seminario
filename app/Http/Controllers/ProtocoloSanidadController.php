<?php

namespace App\Http\Controllers;

use App\Models\ProtocoloSanidad;
use App\Models\User;
use Illuminate\Http\Request;

class ProtocoloSanidadController extends Controller
{
    public function index()
    {
        $protocolos = ProtocoloSanidad::all();
        return view('protocolo_sanidad.index', compact('protocolos'));
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
        ]);
        ProtocoloSanidad::create($request->all());
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
        ]);
        $protocoloSanidad->update($request->all());
        return redirect()->route('protocolo-sanidad.index');
    }

    public function destroy(ProtocoloSanidad $protocoloSanidad)
    {
        $protocoloSanidad->delete();
        return redirect()->route('protocolo-sanidad.index');
    }
}
