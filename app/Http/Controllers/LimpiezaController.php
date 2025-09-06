<?php

namespace App\Http\Controllers;

use App\Models\Limpieza;
use App\Models\ProtocoloSanidad;
use App\Models\User;
use Illuminate\Http\Request;

class LimpiezaController extends Controller
{
    public function index()
    {
        $limpiezas = Limpieza::with('protocoloSanidad')->get();
        return view('limpieza.index', compact('limpiezas'));
    }

    public function create()
    {
        $protocolos = ProtocoloSanidad::all();
        $usuarios = User::active()->get();
        return view('limpieza.create', compact('protocolos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'area' => 'required',
            'responsable' => 'required',
            'protocolo_sanidad_id' => 'required|exists:protocolo_sanidads,id',
        ]);
        Limpieza::create($request->all());
        return redirect()->route('limpieza.index');
    }

    public function show(Limpieza $limpieza)
    {
        return view('limpieza.show', compact('limpieza'));
    }

    public function edit(Limpieza $limpieza)
    {
        $protocolos = ProtocoloSanidad::all();
        $usuarios = User::active()->get();
        return view('limpieza.edit', compact('limpieza', 'protocolos', 'usuarios'));
    }

    public function update(Request $request, Limpieza $limpieza)
    {
        $request->validate([
            'fecha' => 'required|date',
            'area' => 'required',
            'responsable' => 'required',
            'protocolo_sanidad_id' => 'required|exists:protocolo_sanidads,id',
        ]);
        $limpieza->update($request->all());
        return redirect()->route('limpieza.index');
    }

    public function destroy(Limpieza $limpieza)
    {
        $limpieza->delete();
        return redirect()->route('limpieza.index');
    }
}
