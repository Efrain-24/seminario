<?php

namespace App\Http\Controllers;

use App\Models\CosechaParcial;
use App\Models\Lote;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

        // Obtener tipo de cambio actual GTQ a USD
        $tipoCambio = TipoCambio::actual()?->valor ?? 7.8;

        // Obtener usuarios para el select de responsable
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('cosechas.create', compact('lotes', 'tipoCambio', 'usuarios'));
    }

    public function store(Request $request)
    {
        try {
            // Log básico para ver si llega la petición
            Log::info('=== INICIANDO STORE ===');
            Log::info('Request method: ' . $request->method());
            Log::info('Request URL: ' . $request->url());
            Log::info('All data: ', $request->all());
            
            // Validación básica
            $rules = [
                'lote_id' => 'required',
                'fecha' => 'required',
                'cantidad_cosechada' => 'required|integer|min:1',
                'peso_cosechado_kg' => 'nullable|numeric|min:0',
                'destino' => 'required|in:venta,muestra,otro'
            ];
            
            // Si es venta, agregar campos de venta
            if ($request->destino === 'venta') {
                $rules['cliente'] = 'required|string|max:255';
                $rules['telefono_cliente'] = 'nullable|string|max:20';
                $rules['precio_kg'] = 'required|numeric|min:0';
                $rules['metodo_pago'] = 'nullable|in:efectivo,transferencia,cheque';
            }
            
            $request->validate($rules);
            
            Log::info('Validación pasada');
            
            // Verificar stock del lote
            $lote = Lote::find($request->lote_id);
            if (!$lote) {
                throw new \Exception('Lote no encontrado');
            }
            
            if ($request->cantidad_cosechada > $lote->cantidad_actual) {
                throw new \Exception('Stock insuficiente. Disponible: ' . $lote->cantidad_actual);
            }
            
            // Crear cosecha
            $cosecha = new CosechaParcial();
            $cosecha->lote_id = $request->lote_id;
            $cosecha->fecha = $request->fecha;
            $cosecha->cantidad_cosechada = $request->cantidad_cosechada;
            $cosecha->peso_cosechado_kg = $request->peso_cosechado_kg ?? 0;
            $cosecha->destino = $request->destino;
            $cosecha->responsable = $request->responsable ?? Auth::user()->name ?? 'Sistema';
            $cosecha->observaciones = $request->observaciones;
            
            // Si es venta, agregar datos de venta
            if ($request->destino === 'venta') {
                $cosecha->cliente = $request->cliente;
                $cosecha->telefono_cliente = $request->telefono_cliente;
                $cosecha->fecha_venta = now();
                $cosecha->precio_kg = $request->precio_kg;
                
                // Calcular totales
                $tipoCambio = TipoCambio::actual();
                $tasaCambio = $tipoCambio && $tipoCambio->valor > 0 ? $tipoCambio->valor : 7.8; // GTQ a USD
                $totalQuetzales = ($cosecha->peso_cosechado_kg ?? 0) * $request->precio_kg;
                
                $cosecha->total_venta = $totalQuetzales;
                $cosecha->tipo_cambio = $tasaCambio;
                $cosecha->total_usd = $totalQuetzales / $tasaCambio;
                $cosecha->metodo_pago = $request->metodo_pago ?? 'efectivo';
                $cosecha->estado_venta = 'completada'; // Directamente completada
            }
            
            $cosecha->save();
            
            Log::info('Cosecha guardada con ID: ' . $cosecha->id);
            
            // Actualizar stock
            $lote->decrement('cantidad_actual', $request->cantidad_cosechada);
            
            Log::info('Stock actualizado');
            
            // Mensaje según el tipo
            $mensaje = $request->destino === 'venta' 
                ? '✅ Venta registrada y completada exitosamente!'
                : '✅ Cosecha registrada correctamente!';
            
            return redirect()
                ->route('produccion.cosechas.index')
                ->with('success', $mensaje);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ', $e->errors());
            return back()->withInput()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Error general en store: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
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
                'precio_unitario'     => ['required', 'numeric', 'min:0'],
            ]);
        }

        $data = $request->validate($rules);
        
        // Renombrar precio_unitario a precio_kg para compatibilidad
        if (isset($data['precio_unitario'])) {
            $data['precio_kg'] = $data['precio_unitario'];
            unset($data['precio_unitario']);
        }

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

    public function panel()
    {
        $mesActual = \Carbon\Carbon::now();
        
        // Estadísticas del mes actual
        $cosechasEsteMes = CosechaParcial::whereMonth('fecha', $mesActual->month)
            ->whereYear('fecha', $mesActual->year)
            ->count();

        $ventasEsteMes = CosechaParcial::where('destino', 'venta')
            ->where('estado_venta', 'completada')
            ->whereMonth('fecha_venta', $mesActual->month)
            ->whereYear('fecha_venta', $mesActual->year)
            ->sum('total_venta');

        $clientesActivos = CosechaParcial::where('destino', 'venta')
            ->where('estado_venta', 'completada')
            ->whereMonth('fecha_venta', $mesActual->month)
            ->whereYear('fecha_venta', $mesActual->year)
            ->distinct('cliente')
            ->count('cliente');

        return view('cosechas.panel', compact(
            'cosechasEsteMes',
            'ventasEsteMes', 
            'clientesActivos'
        ));
    }

    /**
     * Mostrar formulario para completar venta de una cosecha
     */
    public function completarVenta(CosechaParcial $cosecha)
    {
        if ($cosecha->destino !== 'venta') {
            return back()->with('error', 'Esta cosecha no está destinada para venta.');
        }

        if ($cosecha->estado_venta === 'completada') {
            return back()->with('error', 'Esta venta ya está completada.');
        }

        // Si la cosecha ya tiene datos de venta pero no está completada, completarla
        if ($cosecha->cliente && $cosecha->precio_kg) {
            $cosecha->update([
                'estado_venta' => 'completada',
                'fecha_venta' => now()
            ]);

            return redirect()
                ->route('produccion.cosechas.show', $cosecha)
                ->with('success', 'Venta completada exitosamente.')
                ->with('ticket_disponible', true);
        }

        // Cargar lote y tipo de cambio
        $cosecha->load('lote');
        $tipoCambio = \App\Models\TipoCambio::actual();

        return view('cosechas.completar-venta', compact('cosecha', 'tipoCambio'));
    }

    /**
     * Procesar completar venta
     */
    public function procesarVenta(Request $request, CosechaParcial $cosecha)
    {
        $data = $request->validate([
            'cliente' => 'required|string|max:255',
            'telefono_cliente' => 'nullable|string|max:20',
            'precio_kg' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'observaciones_venta' => 'nullable|string'
        ]);

        if ($cosecha->destino !== 'venta') {
            return back()->with('error', 'Esta cosecha no está destinada para venta.');
        }

        $tipoCambio = \App\Models\TipoCambio::actual();
        $totalCord = $cosecha->peso_cosechado_kg * $data['precio_kg'];
        $tasaCambio = $tipoCambio && $tipoCambio->tasa > 0 ? $tipoCambio->tasa : 7.8;
        $totalUsd = $totalCord / $tasaCambio;

        $cosecha->update([
            'cliente' => $data['cliente'],
            'telefono_cliente' => $data['telefono_cliente'] ?? null,
            'precio_kg' => $data['precio_kg'],
            'total_venta' => $totalCord,
            'tipo_cambio' => $tasaCambio,
            'total_usd' => $totalUsd,
            'metodo_pago' => $data['metodo_pago'],
            'estado_venta' => 'completada',
            'fecha_venta' => now(),
            'observaciones_venta' => $data['observaciones_venta'] ?? null
        ]);

        return redirect()
            ->route('produccion.cosechas.show', $cosecha)
            ->with('success', 'Venta completada exitosamente.')
            ->with('ticket_disponible', true);
    }
}
