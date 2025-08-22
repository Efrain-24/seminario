<?php

namespace App\Http\Controllers;

use App\Models\InventarioItem;
use Illuminate\Http\Request;
use App\Models\Bodega;

class InventarioItemController extends Controller
{
    public function index(Request $request)
    {
        // Panel principal de inventario
        $items   = InventarioItem::with('existencias.bodega')->orderBy('nombre')->get();
        $low     = $items->filter(fn($i) => $i->stockTotal() < $i->stock_minimo);
        $bodegas = Bodega::orderBy('nombre')->get();

        return view('inventario.index', compact('items', 'low', 'bodegas'));
    }

    public function create()
    {
        return view('inventario.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'sku'    => ['nullable', 'string', 'max:120', 'unique:inventario_items,sku'],
            'tipo'   => ['required', 'in:alimento,insumo'],
            'unidad_base' => ['required', 'in:kg,lb,unidad,litro'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'descripcion'  => ['nullable', 'string'],
        ]);
        InventarioItem::create($data);
        return redirect()->route('produccion.inventario.items.index')->with('success', 'Ítem creado.');
    }

    public function edit(InventarioItem $item)
    {
        return view('inventario.items.edit', compact('item'));
    }

    public function update(Request $request, InventarioItem $item)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'sku'    => ['nullable', 'string', 'max:120', "unique:inventario_items,sku,{$item->id}"],
            'tipo'   => ['required', 'in:alimento,insumo'],
            'unidad_base' => ['required', 'in:kg,lb,unidad,litro'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'descripcion'  => ['nullable', 'string'],
        ]);
        $item->update($data);
        return redirect()->route('produccion.inventario.items.index')->with('success', 'Ítem actualizado.');
    }

    public function destroy(InventarioItem $item)
    {
        $item->delete();
        return back()->with('success', 'Ítem eliminado.');
    }
}
