<?php

namespace App\Http\Controllers;

use App\Models\{InventarioMovimiento, InventarioItem, Bodega};
use App\Services\InventarioService;
use Illuminate\Http\Request;

class InventarioMovimientoController extends Controller
{
    public function index(Request $request)
    {
        $items   = InventarioItem::orderBy('nombre')->get(['id', 'nombre']);
        $bodegas = Bodega::orderBy('nombre')->get(['id', 'nombre']);

        $q = InventarioMovimiento::with(['item', 'bodega'])->orderByDesc('fecha')->orderByDesc('id');

        if ($request->filled('item_id'))   $q->where('item_id', $request->item_id);
        if ($request->filled('bodega_id')) $q->where('bodega_id', $request->bodega_id);
        if ($request->filled('tipo'))      $q->where('tipo', $request->tipo);
        if ($request->filled('desde'))     $q->whereDate('fecha', '>=', $request->desde);
        if ($request->filled('hasta'))     $q->whereDate('fecha', '<=', $request->hasta);

        $movs = $q->paginate(20)->withQueryString();

        return view('inventario.movimientos.index', compact('items', 'bodegas', 'movs'));
    }

    public function create(string $tipo)
    {
        abort_unless(in_array($tipo, ['entrada', 'salida', 'ajuste']), 404);
        $items   = InventarioItem::orderBy('nombre')->get();
        $bodegas = Bodega::orderBy('nombre')->get();
        return view('inventario.movimientos.create', compact('tipo', 'items', 'bodegas'));
    }

    public function store(Request $request, InventarioService $svc)
    {
        $tipo = $request->input('tipo');
        abort_unless(in_array($tipo, ['entrada', 'salida', 'ajuste']), 404);

        $rules = [
            'item_id'   => ['required', 'exists:inventario_items,id'],
            'bodega_id' => ['required', 'exists:bodegas,id'],
            'fecha'     => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:200'],
        ];

        if ($tipo === 'ajuste') {
            $rules['nuevo_stock'] = ['required', 'numeric'];
        } else {
            $rules['cantidad'] = ['required', 'numeric', 'min:0.0001'];
            $rules['unidad']   = ['required', 'in:kg,lb,unidad,litro'];
        }

        $data = $request->validate($rules);

        $item   = InventarioItem::findOrFail($request->item_id);
        $bodega = Bodega::findOrFail($request->bodega_id);

        // Ejecutar con el servicio (que ya registra el movimiento con user_id)
        if ($tipo === 'entrada') {
            $svc->entrada($item, $bodega, (float)$request->cantidad, $request->unidad, $request->descripcion);
        } elseif ($tipo === 'salida') {
            $svc->salida($item, $bodega, (float)$request->cantidad, $request->unidad, $request->descripcion);
        } else { // ajuste: nuevo stock en unidad_base
            $svc->ajuste($item, $bodega, (float)$request->nuevo_stock, $request->descripcion);
        }

        // Sobrescribir fecha si el usuario indicó otra (ajusta el movimiento recién creado)
        if ($request->filled('fecha')) {
            $last = InventarioMovimiento::where('item_id', $item->id)->where('bodega_id', $bodega->id)
                ->orderByDesc('id')->first();
            if ($last) {
                $last->update(['fecha' => $request->fecha]);
            }
        }

        return redirect()->route('produccion.inventario.movimientos.index')
            ->with('success', 'Movimiento registrado.');
    }
}
