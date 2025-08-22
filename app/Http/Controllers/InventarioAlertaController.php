<?php

namespace App\Http\Controllers;

use App\Models\{InventarioItem, InventarioLote, Bodega};
use Illuminate\Http\Request;

class InventarioAlertaController extends Controller
{
    public function index(Request $request)
    {
        $dias = (int)($request->input('dias', 30));           // horizonte de vencimiento
        $tipo = $request->input('tipo');                      // alimento|insumo|null
        $bodegaId = $request->input('bodega_id');

        // Items con stock bajo
        $items = InventarioItem::with(['existencias' => function ($q) use ($bodegaId) {
            if ($bodegaId) $q->where('bodega_id', $bodegaId);
        }])
            ->when($tipo, fn($q) => $q->where('tipo', $tipo))
            ->orderBy('nombre')
            ->get();

        $bajos = $items->filter(function ($i) {
            $total = $i->stockTotal();   // método que ya tienes
            return $i->stock_minimo > 0 && $total < $i->stock_minimo;
        });

        // Lotes por vencer / vencidos
        $lotesQuery = InventarioLote::with(['item', 'bodega'])
            ->conStock()
            ->when($tipo, fn($q) => $q->whereHas('item', fn($qq) => $qq->where('tipo', $tipo)))
            ->when($bodegaId, fn($q) => $q->where('bodega_id', $bodegaId));

        $vencidos   = (clone $lotesQuery)->vencidos()->orderBy('fecha_vencimiento')->get();
        $porVencer  = (clone $lotesQuery)->porVencer($dias)->orderBy('fecha_vencimiento')->get();

        $bodegas = Bodega::orderBy('nombre')->get();

        return view('inventario.alertas.index', compact('bajos', 'vencidos', 'porVencer', 'bodegas', 'dias', 'tipo', 'bodegaId'));
    }
}
