<?php
namespace App\Http\Controllers;

use App\Models\Seguimiento;
use Illuminate\Http\Request;

class SeguimientoController extends Controller
{
    public function destroy(Seguimiento $seguimiento)
    {
        // Recuperar la mortalidad al inventario del lote
        $lote = $seguimiento->lote;
        if ($lote && $seguimiento->mortalidad > 0) {
            $lote->cantidad_actual += $seguimiento->mortalidad;
            // Calcular el nuevo total de peso
            $peso_promedio = $lote->peso_promedio_inicial;
            // Si hay seguimientos previos, usar el último peso_promedio
            $ultimoSeguimiento = $lote->seguimientos()->orderByDesc('fecha_seguimiento')->where('id', '!=', $seguimiento->id)->first();
            if ($ultimoSeguimiento && $ultimoSeguimiento->peso_promedio) {
                $peso_promedio = $ultimoSeguimiento->peso_promedio;
            }
            $lote->total_peso = round($lote->cantidad_actual * $peso_promedio, 2);
            $lote->save();
        }
        $seguimiento->delete();
        return back()->with('success', 'Seguimiento eliminado correctamente.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'user_id' => 'required|exists:users,id',
            'fecha_seguimiento' => 'required|date',
            'cantidad_actual' => 'required|integer|min:0',
            'mortalidad' => 'nullable|integer|min:0',
            'peso_promedio' => 'nullable|numeric|min:0',
            'talla_promedio' => 'nullable|numeric|min:0',
            'temperatura_agua' => 'nullable|numeric',
            'ph_agua' => 'nullable|numeric',
            'oxigeno_disuelto' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'tipo_seguimiento' => 'required|string',
        ]);

        $seguimiento = Seguimiento::create($validated);

        // Descontar mortalidad del lote
        if (!empty($validated['mortalidad']) && $validated['mortalidad'] > 0) {
            $lote = \App\Models\Lote::find($validated['lote_id']);
            if ($lote) {
                $lote->cantidad_actual = max(0, $lote->cantidad_actual - $validated['mortalidad']);
                $lote->save();
            }
        }

        return redirect()->back()->with('success', 'Seguimiento registrado correctamente.');
    }
}
