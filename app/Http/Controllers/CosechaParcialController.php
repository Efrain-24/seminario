<?php

namespace App\Http\Controllers;

use App\Models\CosechaParcial;
use App\Models\Lote;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class CosechaParcialController extends Controller
{
    public function index()
    {
    $q = CosechaParcial::with('lote')->latest('fecha');
    if ($loteId = request('lote_id')) $q->where('lote_id', $loteId);
    if ($desde  = request('desde'))   $q->whereDate('fecha', '>=', $desde);
    if ($hasta  = request('hasta'))   $q->whereDate('fecha', '<=', $hasta);

    $cosechas = $q->paginate(12)->withQueryString();
    $lotes = \App\Models\Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);
    return view('cosechas.index', compact('cosechas', 'lotes'));
    }

    public function create()
    {
        // Usamos codigo_lote como "nombre" para el <select>
        $lotes = Lote::orderBy('codigo_lote')
            ->get(['id', 'codigo_lote as nombre', 'cantidad_actual']);

        // Obtener tipo de cambio actual
        $tipoCambio = TipoCambio::actual()?->tasa ?? 7.8;

        // Obtener usuarios para el select de responsable
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('cosechas.create', compact('lotes', 'tipoCambio', 'usuarios'));
    }

    // ✅ ESTE ES EL QUE FALTABA
    public function store(Request $request)
    {
        // Validación básica
        $rules = [
            'lote_id'            => ['required', 'exists:lotes,id'],
            'fecha'              => ['required', 'date'],
            'cantidad_cosechada' => ['required', 'integer', 'min:1'],
            'peso_cosechado_kg'  => ['nullable', 'numeric', 'min:0'],
            'destino'            => ['required', 'in:venta,muestra,otro'],
            'responsable'        => ['nullable', 'string', 'max:120'],
            'observaciones'      => ['nullable', 'string'],
        ];

        // Si el destino es venta, agregar validaciones de venta
        if ($request->destino === 'venta') {
            $rules = array_merge($rules, [
                'cliente'             => ['required', 'string', 'max:255'],
                'telefono_cliente'    => ['nullable', 'string', 'max:20'],
                'precio_kg'           => ['required', 'numeric', 'min:0'],
            ]);
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data) {
            $lote = Lote::lockForUpdate()->findOrFail($data['lote_id']);

            if ($data['cantidad_cosechada'] > $lote->cantidad_actual) {
                $v = Validator::make([], []);
                $v->errors()->add(
                    'cantidad_cosechada',
                    'La cantidad cosechada no puede superar el stock actual del lote (' . $lote->cantidad_actual . ').'
                );
                throw new \Illuminate\Validation\ValidationException($v);
            }

            // Crear la cosecha
            $cosecha = CosechaParcial::create([
                'lote_id'            => $data['lote_id'],
                'fecha'              => $data['fecha'],
                'cantidad_cosechada' => $data['cantidad_cosechada'],
                'peso_cosechado_kg'  => $data['peso_cosechado_kg'] ?? null,
                'destino'            => $data['destino'],
                'responsable'        => $data['responsable'] ?? null,
                'observaciones'      => $data['observaciones'] ?? null,
            ]);

            // Si es una venta, procesar datos de venta automáticamente
            if ($data['destino'] === 'venta' && isset($data['cliente'])) {
                $tipoCambio = TipoCambio::actual();
                $tasaCambio = $tipoCambio && $tipoCambio->tasa > 0 ? $tipoCambio->tasa : 7.8;
                
                // Calcular total según la unidad de venta
                if ($data['unidad_venta'] === 'libra') {
                    // Convertir kg a libras (1 kg = 2.20462 libras)
                    $pesoLibras = $cosecha->peso_cosechado_kg * 2.20462;
                    $totalCord = $pesoLibras * $data['precio_unitario'];
                    $precioKg = $data['precio_unitario'] / 2.20462; // Para almacenar compatibilidad
                } else {
                    // Venta por pez
                    $totalCord = $cosecha->cantidad_cosechada * $data['precio_unitario'];
                    $precioKg = $totalCord / ($cosecha->peso_cosechado_kg ?: 1); // Precio equivalente por kg
                }
                
                $totalUsd = $totalCord / $tasaCambio;

                $cosecha->update([
                    'cliente' => $data['cliente'],
                    'telefono_cliente' => $data['telefono_cliente'] ?? null,
                    'email_cliente' => null,
                    'fecha_venta' => now(),
                    'precio_kg' => $precioKg,
                    'total_venta' => $totalCord,
                    'tipo_cambio' => $tasaCambio,
                    'total_usd' => $totalUsd,
                    'metodo_pago' => 'efectivo',
                    'estado_venta' => 'completada',
                    'observaciones_venta' => 'Venta por ' . ($data['unidad_venta'] === 'libra' ? 'libra' : 'pez'),
                ]);
            }

            // Descontar del stock
            $lote->decrement('cantidad_actual', (int) $data['cantidad_cosechada']);
        });

        return redirect()
            ->route('produccion.cosechas.index')
            ->with('success', 'Cosecha parcial registrada correctamente.');
    }

    public function edit(CosechaParcial $cosecha)
    {
        $cosecha->load('lote');

        // Trae TODOS los lotes como MODELOS con alias 'nombre'
        $lotes = Lote::select('id', 'codigo_lote as nombre', 'cantidad_actual')
            ->orderBy('codigo_lote')
            ->get();

        // Obtener tipo de cambio actual
        $tipoCambio = TipoCambio::actual()?->tasa ?? 7.8;

        // Obtener usuarios para el select de responsable
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('cosechas.edit', compact('cosecha', 'lotes', 'tipoCambio', 'usuarios'));
    }


    public function update(Request $request, CosechaParcial $cosecha)
    {
        // Validación básica
        $rules = [
            'fecha'              => ['required', 'date'],
            'cantidad_cosechada' => ['required', 'integer', 'min:1'],
            'peso_cosechado_kg'  => ['nullable', 'numeric', 'min:0'],
            'destino'            => ['required', 'in:venta,muestra,otro'],
            'responsable'        => ['nullable', 'string', 'max:120'],
            'observaciones'      => ['nullable', 'string'],
        ];

        // Si el destino es venta, agregar validaciones de venta
        if ($request->destino === 'venta') {
            $rules = array_merge($rules, [
                'cliente'             => ['required', 'string', 'max:255'],
                'telefono_cliente'    => ['nullable', 'string', 'max:20'],
                'precio_kg'           => ['required', 'numeric', 'min:0'],
            ]);
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $cosecha) {
            $cosecha->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($cosecha->lote_id);

            $anterior = (int) $cosecha->cantidad_cosechada;
            $nueva    = (int) $data['cantidad_cosechada'];
            $delta    = $nueva - $anterior; // + => se descuenta más; - => se devuelve stock

            if ($delta > 0 && $delta > $lote->cantidad_actual) {
                $v = Validator::make([], []);
                $v->errors()->add(
                    'cantidad_cosechada',
                    'El ajuste excede el stock disponible del lote (' . $lote->cantidad_actual . ').'
                );
                throw new \Illuminate\Validation\ValidationException($v);
            }

            $cosecha->update($data);

            // Si es una venta, procesar datos de venta automáticamente
            if ($data['destino'] === 'venta' && isset($data['cliente'])) {
                $tipoCambio = TipoCambio::actual();
                $totalCord = $cosecha->peso_cosechado_kg * $data['precio_kg'];
                $tasaCambio = $tipoCambio && $tipoCambio->tasa > 0 ? $tipoCambio->tasa : 7.8;
                $totalUsd = $totalCord / $tasaCambio;

                $cosecha->update([
                    'cliente' => $data['cliente'],
                    'telefono_cliente' => $data['telefono_cliente'] ?? null,
                    'email_cliente' => null, // Campo removido del formulario
                    'fecha_venta' => $cosecha->fecha_venta ?: now(), // Mantener fecha original si existe
                    'precio_kg' => $data['precio_kg'],
                    'total_venta' => $totalCord,
                    'tipo_cambio' => $tasaCambio,
                    'total_usd' => $totalUsd,
                    'metodo_pago' => 'efectivo', // Por defecto efectivo
                    'estado_venta' => 'completada',
                    'observaciones_venta' => null, // Campo removido del formulario
                ]);
            } elseif ($data['destino'] !== 'venta') {
                // Si cambió de venta a otro destino, limpiar datos de venta
                $cosecha->update([
                    'cliente' => null,
                    'telefono_cliente' => null,
                    'email_cliente' => null,
                    'fecha_venta' => null,
                    'precio_kg' => null,
                    'total_venta' => null,
                    'tipo_cambio' => null,
                    'total_usd' => null,
                    'metodo_pago' => null,
                    'estado_venta' => null,
                    'observaciones_venta' => null,
                ]);
            }

            if ($delta > 0) {
                $lote->decrement('cantidad_actual', $delta);
            } elseif ($delta < 0) {
                $lote->increment('cantidad_actual', -$delta);
            }
        });

        return redirect()
            ->route('produccion.cosechas.index')
            ->with('success', 'Cosecha parcial actualizada.');
    }

    public function destroy(CosechaParcial $cosecha)
    {
        DB::transaction(function () use ($cosecha) {
            $cosecha->load('lote');
            $lote = Lote::lockForUpdate()->findOrFail($cosecha->lote_id);

            // Revertir stock
            $lote->increment('cantidad_actual', (int) $cosecha->cantidad_cosechada);
            $cosecha->delete();
        });

        return back()->with('success', 'Cosecha parcial eliminada y stock revertido.');
    }

    /**
     * Mostrar formulario para completar venta de una cosecha
     */
    /**
     * Generar y descargar ticket de venta
     */
    public function generarTicket(CosechaParcial $cosecha)
    {
        if (!$cosecha->esVenta() || $cosecha->estado_venta !== 'completada') {
            return back()->with('error', 'No se puede generar el ticket para esta cosecha.');
        }

        // Cargar relaciones necesarias
        $cosecha->load('lote');
        
        // Generar el PDF
        $pdf = PDF::loadView('cosechas.ticket', compact('cosecha'));
        $pdf->setPaper('letter', 'portrait');
        
        // Nombre del archivo
        $nombreArchivo = 'ticket-venta-' . $cosecha->codigo_venta . '.pdf';
        
        // Retornar el PDF para descarga
        return $pdf->download($nombreArchivo);
    }

    /**
     * Ver ticket de venta en el navegador
     */
    public function verTicket(CosechaParcial $cosecha)
    {
        if (!$cosecha->esVenta() || $cosecha->estado_venta !== 'completada') {
            return back()->with('error', 'No se puede ver el ticket para esta cosecha.');
        }

        // Cargar relaciones necesarias
        $cosecha->load('lote');
        
        // Generar el PDF
        $pdf = PDF::loadView('cosechas.ticket', compact('cosecha'));
        $pdf->setPaper('letter', 'portrait');
        
        // Mostrar en el navegador
        return $pdf->stream('ticket-venta-' . $cosecha->codigo_venta . '.pdf');
    }

    /**
     * Mostrar detalles de una cosecha
     */
    public function show(CosechaParcial $cosecha)
    {
        $cosecha->load(['lote', 'user']);
        return view('cosechas.show', compact('cosecha'));
    }
}
