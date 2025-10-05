<?php

// app/Http/Controllers/InventarioController.php
namespace App\Http\Controllers;

use App\Models\{InventarioItem, InventarioExistencia, Bodega};
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $items = InventarioItem::with('existencias.bodega')->orderBy('nombre')->get();
        $low   = $items->filter(fn($i) => $i->stockTotal() < $i->stock_minimo);

        $bodegas = Bodega::orderBy('nombre')->get();
        return view('inventario.index', compact('items', 'low', 'bodegas'));
    }
}
