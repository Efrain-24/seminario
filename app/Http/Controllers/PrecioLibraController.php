<?php

namespace App\Http\Controllers;

use App\Models\PrecioLibra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecioLibraController extends Controller
{
    /**
     * Almacenar un nuevo precio por libra
     */
    public function store(Request $request)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0.01|max:999.99',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio debe ser mayor a Q0.00.',
            'precio.max' => 'El precio no puede ser mayor a Q999.99.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.'
        ]);

        try {
            PrecioLibra::establecerPrecio(
                $request->precio,
                Auth::id(),
                $request->observaciones
            );

            return redirect()->back()->with('success', 'Precio por libra establecido correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al establecer el precio: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el precio actual por libra (API)
     */
    public function getPrecioActual()
    {
        $precio = PrecioLibra::precioActual();
        
        if ($precio) {
            return response()->json([
                'success' => true,
                'precio' => $precio->precio,
                'fecha_registro' => $precio->created_at->format('Y-m-d'),
                'usuario' => $precio->usuario->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay precio configurado'
        ], 404);
    }
}
