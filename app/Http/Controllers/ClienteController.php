<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'documento' => 'nullable|string|max:50',
        ]);

        $cliente = Cliente::create($request->all());

        if (!$cliente->id) {
            // Log para depuración
            // Puedes revisar storage/logs/laravel.log
            // para verificar si el cliente se guardó correctamente
            \Log::error('Error al guardar el cliente', ['data' => $request->all()]);
            return redirect()->route('clientes.create')->with('error', 'No se pudo asignar un ID al cliente.');
        }

        \Log::info('Cliente creado correctamente', ['cliente' => $cliente]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente con ID: ' . $cliente->id);
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'documento' => 'nullable|string|max:50',
        ]);
        $cliente->update($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $clientes = Cliente::query()
            ->where('nombre', 'like', "%$q%")
            ->orWhere('documento', 'like', "%$q%")
            ->orderBy('nombre')
            ->limit(15)
            ->get(['id','nombre','direccion','documento']);
        return response()->json($clientes);
    }
}
