<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    public function index()
    {
        $bodegas = Bodega::orderBy('nombre')->paginate(15);
        return view('inventario.bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('inventario.bodegas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:160'],
        ]);
        Bodega::create($data);
        return redirect()->route('produccion.inventario.bodegas.index')->with('success', 'Bodega creada.');
    }

    public function edit(Bodega $bodega)
    {
        return view('inventario.bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, Bodega $bodega)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:160'],
        ]);
        $bodega->update($data);
        return redirect()->route('produccion.inventario.bodegas.index')->with('success', 'Bodega actualizada.');
    }

    public function destroy(Bodega $bodega)
    {
        $bodega->delete();
        return back()->with('success', 'Bodega eliminada.');
    }
}
