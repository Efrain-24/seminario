<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alimentacion;
use App\Models\TipoAlimento;
use App\Models\Lote;
use App\Models\User;
use App\Models\Bodega;
use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\InventarioMovimiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AlimentacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Alimentacion::with(['lote.unidadProduccion', 'tipoAlimento', 'inventarioItem', 'usuario']);

        // Filtros
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        if ($request->filled('inventario_item_id')) {
            $query->where('inventario_item_id', $request->inventario_item_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_alimentacion', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Ordenar por fecha más reciente
        $alimentaciones = $query->orderBy('fecha_alimentacion', 'desc')
            ->orderBy('hora_alimentacion', 'desc')
            ->paginate(15);

        // Estadísticas del período actual
        $fechaInicio = $request->filled('fecha_inicio') ? $request->fecha_inicio : Carbon::now()->startOfMonth()->toDateString();
        $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : Carbon::now()->endOfMonth()->toDateString();

        $estadisticas = [
            'total_alimentaciones' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->count(),
            'total_cantidad_kg' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->sum('cantidad_kg'),
            'costo_total' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])->sum('costo_total'),
            'promedio_diario' => Alimentacion::whereBetween('fecha_alimentacion', [$fechaInicio, $fechaFin])
                ->selectRaw('AVG(cantidad_kg) as promedio')
                ->value('promedio') ?? 0,
        ];

        // Datos para los filtros
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        
        // Solo mostrar tipos de alimento que tienen inventario conectado y con stock disponible
        $tiposAlimento = TipoAlimento::where('activo', true)
            ->whereNotNull('inventario_item_id')
            ->whereHas('inventarioItem.existencias', function($query) {
                $query->where('stock_actual', '>', 0);
            })
            ->with('inventarioItem.existencias.bodega')
            ->orderBy('nombre')
            ->get();
        
        $usuarios = User::whereIn('role', ['admin', 'manager', 'empleado'])->orderBy('name')->get();

        return view('alimentacion.index', compact(
            'alimentaciones',
            'estadisticas',
            'lotes',
            'tiposAlimento',
            'usuarios',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function create()
    {
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        $bodegas = \App\Models\Bodega::orderBy('nombre')->get();
        // Solo mostrar alimentos que tienen existencias en cualquier bodega
        $alimentosInventario = \App\Models\InventarioItem::where('tipo', 'alimento')
            ->whereHas('existencias', function($q) {
                $q->where('stock_actual', '>', 0);
            })
            ->with('existencias.bodega')
            ->orderBy('nombre')
            ->get();
        
        // Crear estructura de datos para JavaScript - DIRECTO de tu módulo de inventario
        $existenciasPorBodega = [];
        
        foreach ($bodegas as $bodega) {
            $existenciasPorBodega[$bodega->id] = [];
            
            // Traer SOLO los items de inventario que son alimentos con stock en esta bodega
            $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
                ->where('stock_actual', '>', 0)
                ->whereHas('item', function($query) {
                    $query->where('tipo', 'alimento'); // Solo alimentos de tu inventario
                })
                ->with(['item'])
                ->get();
            
            foreach ($existencias as $existencia) {
                $item = $existencia->item;
                
                // Buscar el último costo_unitario de la entrada de compra para este item
                $entradaDetalle = \App\Models\EntradaCompraDetalle::where('item_id', $item->id)
                    ->orderByDesc('created_at')
                    ->first();
                $costo_unitario = $entradaDetalle ? $entradaDetalle->costo_unitario : ($item->costo_unitario ? round($item->costo_unitario, 2) : 0);
                
                $existenciasPorBodega[$bodega->id][] = [
                    'inventario_item_id' => $item->id, // Usar el ID del item de inventario
                    'nombre_completo' => $item->nombre,
                    'sku' => $item->sku,
                    'descripcion' => $item->descripcion ?: 'Alimento para acuicultura',
                    'cantidad_disponible' => round($existencia->stock_actual, 2),
                    'unidad' => $item->unidad_base,
                    'stock_minimo' => round($item->stock_minimo, 2),
                    'costo_unitario' => $costo_unitario,
                    'tiene_costo' => !is_null($costo_unitario)
                ];
            }
        }
        
        return view('alimentacion.create', compact('lotes', 'alimentosInventario', 'bodegas', 'existenciasPorBodega'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'inventario_item_id' => 'required|exists:inventario_items,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'fecha_alimentacion' => 'required|date|before_or_equal:today',
            'hora_alimentacion' => 'required|date_format:H:i',
            'cantidad_kg' => 'required|numeric|min:0.01|max:9999.99',
            'metodo_alimentacion' => 'required|in:' . implode(',', array_keys(Alimentacion::getMetodosAlimentacion())),
            'estado_peces' => 'nullable|in:' . implode(',', array_keys(Alimentacion::getEstadosPeces())),
            'porcentaje_consumo' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Verificar que el item seleccionado es realmente un alimento
        $inventarioItem = InventarioItem::find($validated['inventario_item_id']);
        if (!$inventarioItem || $inventarioItem->tipo !== 'alimento') {
            return back()->withErrors(['inventario_item_id' => 'El item seleccionado no es un alimento válido.']);
        }

        // Buscar si existe un TipoAlimento asociado a este InventarioItem para compatibilidad
        $tipoAlimento = TipoAlimento::where('inventario_item_id', $inventarioItem->id)->first();
        if ($tipoAlimento) {
            $validated['tipo_alimento_id'] = $tipoAlimento->id;
        }

        // Verificar stock disponible en la bodega especificada
        $existencia = InventarioExistencia::where('item_id', $validated['inventario_item_id'])
            ->where('bodega_id', $validated['bodega_id'])
            ->first();
        
        if (!$existencia || $existencia->stock_actual < $validated['cantidad_kg']) {
            return back()->withErrors(['cantidad_kg' => 'No hay suficiente stock disponible en la bodega seleccionada.']);
        }

        // Calcular costo total basado en el costo del inventario
        // Si la unidad base es lb, usar la cantidad tal cual. Si es kg, convertir a kg.
        $cantidad = $validated['cantidad_kg'];
        if ($inventarioItem->unidad_base === 'kg') {
            // Si el usuario ingresa en libras, convertir a kg (1 kg = 2.20462 lb)
            $cantidad = $cantidad / 2.20462;
        }
        // Buscar el último costo_unitario de la entrada de compra
        $entradaDetalle = \App\Models\EntradaCompraDetalle::where('item_id', $inventarioItem->id)
            ->orderByDesc('created_at')
            ->first();
        $costo_unitario = $entradaDetalle ? $entradaDetalle->costo_unitario : ($inventarioItem->costo_unitario ? round($inventarioItem->costo_unitario, 2) : 0);
        $validated['costo_total'] = $cantidad * $costo_unitario;

        // Combinar fecha y hora
        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha_alimentacion'] . ' ' . $validated['hora_alimentacion']);
        $validated['fecha_alimentacion'] = $fechaHora->toDateString();
        $validated['hora_alimentacion'] = $fechaHora->toTimeString();
        $validated['usuario_id'] = Auth::id();

        // Crear el registro de alimentación
        $alimentacion = Alimentacion::create($validated);

        // Reducir el stock del inventario automáticamente
        $existencia->stock_actual -= $validated['cantidad_kg'];
        $existencia->save();

        // Crear movimiento de inventario para llevar el registro
        InventarioMovimiento::create([
            'item_id' => $validated['inventario_item_id'],
            'bodega_id' => $validated['bodega_id'],
            'tipo' => 'salida',
            'cantidad_base' => $validated['cantidad_kg'],
            'unidad_origen' => $inventarioItem->unidad_base,
            'cantidad_origen' => $validated['cantidad_kg'],
            'referencia_type' => 'App\Models\Alimentacion',
            'referencia_id' => $alimentacion->id,
            'fecha' => now(),
            'descripcion' => 'Alimentación de lote - Reducción automática de stock',
            'user_id' => Auth::id()
        ]);

        return redirect()->route('alimentacion.index')
            ->with('success', 'Registro de alimentación creado exitosamente. Stock actualizado automáticamente.');
    }

    public function show(Alimentacion $alimentacion)
    {
        return view('alimentacion.show', compact('alimentacion'));
    }

    public function edit(Alimentacion $alimentacion)
    {
        $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
        $tiposAlimento = TipoAlimento::where('activo', true)->orderBy('nombre')->get();
        $bodegas = Bodega::orderBy('nombre')->get();
        $usuarios = User::whereIn('role', ['admin', 'manager', 'empleado'])->orderBy('name')->get();

        return view('alimentacion.edit', compact('alimentacion', 'lotes', 'tiposAlimento', 'bodegas', 'usuarios'));
    }

    public function update(Request $request, Alimentacion $alimentacion)
    {
        $validated = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'inventario_item_id' => 'required|exists:inventario_items,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'fecha_alimentacion' => 'required|date|before_or_equal:today',
            'hora_alimentacion' => 'required|date_format:H:i',
            'cantidad_kg' => 'required|numeric|min:0.01|max:9999.99',
            'metodo_alimentacion' => 'required|in:' . implode(',', array_keys(Alimentacion::getMetodosAlimentacion())),
            'estado_peces' => 'nullable|in:' . implode(',', array_keys(Alimentacion::getEstadosPeces())),
            'porcentaje_consumo' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Verificar que el item seleccionado es realmente un alimento
        $inventarioItem = InventarioItem::find($validated['inventario_item_id']);
        if (!$inventarioItem || $inventarioItem->tipo !== 'alimento') {
            return back()->withErrors(['inventario_item_id' => 'El item seleccionado no es un alimento válido.']);
        }

        // Verificar stock disponible en la bodega especificada
        $existencia = InventarioExistencia::where('item_id', $validated['inventario_item_id'])
            ->where('bodega_id', $validated['bodega_id'])
            ->first();
        
        if (!$existencia || $existencia->stock_actual < $validated['cantidad_kg']) {
            return back()->withErrors(['cantidad_kg' => 'No hay suficiente stock disponible en la bodega seleccionada.']);
        }

        // Combinar fecha y hora
        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha_alimentacion'] . ' ' . $validated['hora_alimentacion']);
        $validated['fecha_alimentacion'] = $fechaHora->toDateString();
        $validated['hora_alimentacion'] = $fechaHora->toTimeString();

        $alimentacion->update($validated);

        return redirect()->route('alimentacion.index')
            ->with('success', 'Registro de alimentación actualizado exitosamente.');
    }

    public function destroy(Alimentacion $alimentacion)
    {
        $alimentacion->delete();

        return redirect()->route('alimentacion.index')
            ->with('success', 'Registro de alimentación eliminado exitosamente.');
    }
}