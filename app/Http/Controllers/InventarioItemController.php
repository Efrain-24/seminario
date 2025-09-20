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

    public function show(InventarioItem $item)
    {
        $item->load(['existencias.bodega', 'movimientos.bodega', 'movimientos.user']);
        $bodegas = Bodega::orderBy('nombre')->get();
        
        // Últimos movimientos del item
        $movimientos = $item->movimientos()->with(['bodega', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
            
        // Stock por bodega
        $stockPorBodega = $bodegas->map(function ($bodega) use ($item) {
            $existencia = $item->existencias->firstWhere('bodega_id', $bodega->id);
            return (object) [
                'bodega' => $bodega,
                'stock' => $existencia ? $existencia->stock_actual : 0
            ];
        });

        return view('inventario.items.show', compact('item', 'bodegas', 'movimientos', 'stockPorBodega'));
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
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['nullable', 'in:GTQ,USD,EUR'],
            'fecha_ultimo_costo' => ['nullable', 'date'],
        ]);
        
        // Generar SKU automáticamente si no se proporciona
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateUniqueSKU($data['tipo'], $data['nombre']);
        }
        
        // Configurar valores por defecto para campos de costo
        if (empty($data['moneda'])) {
            $data['moneda'] = 'GTQ';
        }
        
        if (!empty($data['costo_unitario']) && empty($data['fecha_ultimo_costo'])) {
            $data['fecha_ultimo_costo'] = now()->toDateString();
        }
        
        $item = InventarioItem::create($data);
        
        // Si se proporcionó un costo, inicializar los rangos mín/máx
        if (!empty($data['costo_unitario'])) {
            $item->update([
                'costo_minimo' => $data['costo_unitario'],
                'costo_maximo' => $data['costo_unitario'],
            ]);
        }
        
        // Redirigir directamente al formulario de entrada para registrar stock inicial
        return redirect()
            ->route('produccion.inventario.movimientos.create', 'entrada')
            ->with('success', 'Ítem creado exitosamente. Ahora registra el stock inicial.')
            ->with('item_id', $item->id);
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
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['nullable', 'in:GTQ,USD,EUR'],
            'fecha_ultimo_costo' => ['nullable', 'date'],
        ]);
        
        // Configurar valores por defecto para campos de costo
        if (empty($data['moneda'])) {
            $data['moneda'] = 'GTQ';
        }
        
        // Si se actualiza el costo, actualizar también la fecha
        if (!empty($data['costo_unitario']) && $data['costo_unitario'] != $item->costo_unitario) {
            if (empty($data['fecha_ultimo_costo'])) {
                $data['fecha_ultimo_costo'] = now()->toDateString();
            }
            
            // Actualizar rangos mín/máx usando el método del modelo
            $item->actualizarCosto($data['costo_unitario']);
        }
        
        $item->update($data);
        return redirect()->route('produccion.inventario.items.index')->with('success', 'Ítem actualizado.');
    }

    public function destroy(InventarioItem $item)
    {
        $item->delete();
        return back()->with('success', 'Ítem eliminado.');
    }

    /**
     * Generar un SKU único
     */
    private function generateUniqueSKU($tipo, $nombre)
    {
        // Prefijo según el tipo
        $prefijos = [
            'alimento' => 'ALM',
            'insumo' => 'INS',
            'medicamento' => 'MED',
            'equipo' => 'EQP'
        ];
        
        $prefijo = $prefijos[$tipo] ?? 'ITM';
        
        // Procesar nombre para crear sufijo
        $palabras = explode(' ', trim($nombre));
        $sufijo = '';
        
        if (count($palabras) === 1) {
            // Una sola palabra, tomar primeras 4 letras
            $sufijo = strtoupper(substr($palabras[0], 0, 4));
        } elseif (count($palabras) === 2) {
            // Dos palabras, tomar primeras 2 letras de cada una
            $sufijo = strtoupper(substr($palabras[0], 0, 2) . substr($palabras[1], 0, 2));
        } else {
            // Más de dos palabras, tomar primera letra de las primeras 4 palabras
            $sufijo = strtoupper(implode('', array_map(function($palabra) {
                return substr($palabra, 0, 1);
            }, array_slice($palabras, 0, 4))));
        }
        
        // Rellenar sufijo si es muy corto
        $sufijo = str_pad($sufijo, 3, 'X');
        
        // Generar SKU y verificar unicidad
        $attempts = 0;
        do {
            $numero = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $sku = "{$prefijo}-{$sufijo}-{$numero}";
            $exists = InventarioItem::where('sku', $sku)->exists();
            $attempts++;
        } while ($exists && $attempts < 100);
        
        return $sku;
    }
}
