<?php

namespace App\Http\Controllers;

use App\Models\EntradaCompra;
use App\Models\EntradaCompraDetalle;
use App\Models\Proveedor;
use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EntradaCompraController extends Controller
{
    public function index()
    {
        $entradas = EntradaCompra::with('proveedor')->orderByDesc('id')->paginate(15);
        
        // Calcular estadísticas con TODOS los registros
        $totalEntradas = EntradaCompra::count();
        $sumaTotal = EntradaCompra::sum('total');
        $promedioEntrada = $totalEntradas > 0 ? $sumaTotal / $totalEntradas : 0;
        
        return view('entradas.index', compact('entradas', 'totalEntradas', 'sumaTotal', 'promedioEntrada'));
    }

    public function create()
    {
    $proveedores = Proveedor::orderBy('nombre')->get();
    $items = InventarioItem::orderBy('nombre')->get();
    $bodegas = \App\Models\Bodega::orderBy('nombre')->get();
    return view('entradas.create', compact('proveedores', 'items', 'bodegas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // corregido a tabla proveedores
            'proveedor_id' => 'required|exists:proveedores,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'numero_documento' => 'nullable|string|max:50',
            'fecha_documento' => 'nullable|date',
            'fecha_ingreso' => 'required|date',
            'moneda' => 'required|string|max:5',
            'tipo_cambio' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.item_id' => 'required|exists:inventario_items,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.costo_unitario' => 'required|numeric|min:0',
        ], [
            'detalles.required' => 'Debe agregar al menos un ítem',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($data['detalles'] as $d) {
                $subtotal += $d['cantidad'] * $d['costo_unitario'];
            }
            $impuesto = 0; // se puede calcular luego
            $total = $subtotal + $impuesto;

            $entrada = EntradaCompra::create([
                'proveedor_id' => $data['proveedor_id'],
                'numero_documento' => $data['numero_documento'] ?? null,
                'fecha_documento' => $data['fecha_documento'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'moneda' => $data['moneda'],
                'tipo_cambio' => $data['tipo_cambio'] ?? null,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            foreach ($data['detalles'] as $d) {
                $detalle = EntradaCompraDetalle::create([
                    'entrada_id' => $entrada->id,
                    'item_id' => $d['item_id'],
                    'descripcion' => $d['descripcion'] ?? null,
                    'cantidad' => $d['cantidad'],
                    'unidad' => $d['unidad'] ?? null,
                    'costo_unitario' => $d['costo_unitario'],
                    'subtotal' => $d['cantidad'] * $d['costo_unitario'],
                ]);

                // Actualizar o crear existencia en la bodega seleccionada
                $bodegaId = $data['bodega_id'];
                $existencia = InventarioExistencia::firstOrCreate(
                    ['item_id' => $d['item_id'], 'bodega_id' => $bodegaId],
                    ['stock_actual' => 0]
                );
                $existencia->stock_actual += $d['cantidad'];
                $existencia->save();

                // Registrar movimiento en la bodega seleccionada
                InventarioMovimiento::create([
                    'item_id' => $d['item_id'],
                    'bodega_id' => $bodegaId,
                    'tipo' => 'entrada',
                    'cantidad_base' => $d['cantidad'],
                    'descripcion' => 'Entrada por compra',
                    'fecha' => $entrada->fecha_ingreso,
                    'referencia_type' => EntradaCompra::class,
                    'referencia_id' => $entrada->id,
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('entradas.show', $entrada)->with('success', 'Entrada registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando entrada: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error', 'Error al guardar la entrada.')->withInput();
        }
    }

    public function show(EntradaCompra $entrada)
    {
        $entrada->load(['proveedor', 'detalles.item']);
        return view('entradas.show', compact('entrada'));
    }
}
