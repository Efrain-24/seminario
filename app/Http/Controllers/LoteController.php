<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Models\InventarioItem;
use App\Models\InventarioMovimiento;
use App\Models\InventarioExistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['unidadProduccion', 'seguimientos'])
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('produccion.lotes', compact('lotes'));
    }

    public function show(Lote $lote)
    {
        $lote->load(['unidadProduccion', 'seguimientos', 'traslados']);
        
        return view('produccion.show-lote', compact('lote'));
    }

    public function create()
    {
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        
        return view('produccion.create-lote', compact('unidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_lote' => 'required|string|unique:lotes',
            'especie' => 'required|string',
            'cantidad_inicial' => 'required|integer|min:1',
            'peso_promedio_inicial' => 'required|numeric|min:0',
            'talla_promedio_inicial' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ]);

        $validated['cantidad_actual'] = $validated['cantidad_inicial'];
        $validated['estado'] = 'activo';

        $lote = Lote::create($validated);

        // Descontar el artículo "pez" del inventario
        try {
            $this->descontarPezDelInventario($lote);
        } catch (\Exception $e) {
            // Logging del error pero no impedir la creación del lote
            Log::warning("Error al descontar peces del inventario para el lote {$lote->codigo_lote}: " . $e->getMessage());
        }

        return redirect()->route('lotes.show', $lote)
            ->with('success', 'Lote creado exitosamente.');
    }

    /**
     * Descontar peces del inventario cuando se crea un lote
     */
    private function descontarPezDelInventario(Lote $lote)
    {
        // Buscar el artículo "pez" en el inventario (case-insensitive)
        $itemPez = InventarioItem::whereRaw('LOWER(nombre) = ?', ['pez'])
            ->orWhereRaw('LOWER(sku) = ?', ['pez'])
            ->first();

        if (!$itemPez) {
            Log::warning("No se encontró el artículo 'pez' en el inventario para descontar en el lote {$lote->codigo_lote}");
            return;
        }

        // Obtener la unidad base del item
        $unidadBase = $itemPez->unidad_base ?? 'unidad';

        // Crear registro de movimiento de inventario
        InventarioMovimiento::create([
            'item_id' => $itemPez->id,
            'bodega_id' => null, // No especificamos bodega, es descuento general
            'tipo' => 'salida', // Tipo de movimiento: salida del inventario
            'cantidad_base' => $lote->cantidad_inicial,
            'unidad_origen' => $unidadBase,
            'cantidad_origen' => $lote->cantidad_inicial,
            'referencia_type' => 'App\Models\Lote',
            'referencia_id' => $lote->id,
            'fecha' => now(),
            'descripcion' => "Descuento de peces por creación del lote {$lote->codigo_lote}",
            'user_id' => Auth::id() ?? null
        ]);

        // Descontar del stock en las existencias
        $existencias = InventarioExistencia::where('item_id', $itemPez->id)->get();
        
        $cantidadADescontar = $lote->cantidad_inicial;

        foreach ($existencias as $existencia) {
            if ($cantidadADescontar <= 0) break;

            $cantidadDescontada = min($cantidadADescontar, $existencia->stock_actual);
            $existencia->stock_actual -= $cantidadDescontada;
            $existencia->save();

            $cantidadADescontar -= $cantidadDescontada;
        }

        Log::info("Se descargaron {$lote->cantidad_inicial} peces del inventario para el lote {$lote->codigo_lote}");
    }

    /**
     * Devolver peces al inventario cuando se elimina un lote
     */
    private function devolverPezAlInventario(Lote $lote)
    {
        // Buscar el artículo "pez" en el inventario (case-insensitive)
        $itemPez = InventarioItem::whereRaw('LOWER(nombre) = ?', ['pez'])
            ->orWhereRaw('LOWER(sku) = ?', ['pez'])
            ->first();

        if (!$itemPez) {
            Log::warning("No se encontró el artículo 'pez' en el inventario para devolver en el lote {$lote->codigo_lote}");
            return;
        }

        // Obtener la unidad base del item
        $unidadBase = $itemPez->unidad_base ?? 'unidad';

        // Verificar si el lote ya fue descargado (debe haber un movimiento de salida)
        $movimientoDescarga = InventarioMovimiento::where([
            'item_id' => $itemPez->id,
            'referencia_type' => 'App\Models\Lote',
            'referencia_id' => $lote->id,
            'tipo' => 'salida'
        ])->first();

        // Si no existe movimiento de descarga, no hay nada que devolver
        if (!$movimientoDescarga) {
            Log::info("No hay movimiento de descarga para devolver peces del lote {$lote->codigo_lote}");
            return;
        }

        // Crear registro de movimiento de inventario (entrada/devolución)
        InventarioMovimiento::create([
            'item_id' => $itemPez->id,
            'bodega_id' => null, // No especificamos bodega, es devolución general
            'tipo' => 'entrada', // Tipo de movimiento: entrada al inventario
            'cantidad_base' => $lote->cantidad_inicial,
            'unidad_origen' => $unidadBase,
            'cantidad_origen' => $lote->cantidad_inicial,
            'referencia_type' => 'App\Models\Lote',
            'referencia_id' => $lote->id,
            'fecha' => now(),
            'descripcion' => "Devolución de peces por eliminación del lote {$lote->codigo_lote}",
            'user_id' => Auth::id() ?? null
        ]);

        // Sumar al stock en las existencias
        $existencias = InventarioExistencia::where('item_id', $itemPez->id)->get();
        
        // Si no hay existencias, crear una por defecto (primera bodega o sin bodega específica)
        if ($existencias->isEmpty()) {
            InventarioExistencia::create([
                'item_id' => $itemPez->id,
                'bodega_id' => null,
                'stock_actual' => $lote->cantidad_inicial
            ]);
        } else {
            // Agregar los peces a la primera existencia encontrada
            $primeraExistencia = $existencias->first();
            $primeraExistencia->stock_actual += $lote->cantidad_inicial;
            $primeraExistencia->save();
        }

        Log::info("Se devolvieron {$lote->cantidad_inicial} peces al inventario para el lote {$lote->codigo_lote}");
    }

    public function edit(Lote $lote)
    {
        $unidades = UnidadProduccion::where('estado', 'activo')->get();
        
        return view('produccion.lotes.edit', compact('lote', 'unidades'));
    }

    public function update(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'codigo_lote' => 'required|string|unique:lotes,codigo_lote,' . $lote->id,
            'especie' => 'required|string',
            'cantidad_inicial' => 'required|integer|min:1',
            'peso_promedio_inicial' => 'required|numeric|min:0',
            'talla_promedio_inicial' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'unidad_produccion_id' => 'required|exists:unidad_produccions,id',
            'observaciones' => 'nullable|string'
        ]);

        $lote->update($validated);

        return redirect()->route('lotes.show', $lote)
            ->with('success', 'Lote actualizado exitosamente.');
    }

    public function destroy(Lote $lote)
    {
        // Devolver los peces al inventario antes de desactivar
        try {
            $this->devolverPezAlInventario($lote);
        } catch (\Exception $e) {
            // Logging del error pero no impedir la eliminación del lote
            Log::warning("Error al devolver peces al inventario para el lote {$lote->codigo_lote}: " . $e->getMessage());
        }

        $lote->update(['estado' => 'inactivo']);

        return redirect()->route('lotes.index')
            ->with('success', 'Lote desactivado exitosamente.');
    }

    public function historial(Lote $lote)
    {
        $seguimientos = $lote->seguimientos()
            ->with('usuario')
            ->orderBy('fecha_seguimiento', 'desc')
            ->paginate(15);

        return view('produccion.lotes.historial', compact('lote', 'seguimientos'));
    }
}
