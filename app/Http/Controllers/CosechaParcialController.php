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

        // Obtener todas las cosechas
        $todasLasCosechas = $q->get();
        
        // Agrupar por código de venta para las ventas, mostrar individual para otros destinos
        $cosechasAgrupadas = $todasLasCosechas->groupBy(function($cosecha) {
            // Si es venta y tiene código, agrupar por código
            if ($cosecha->destino === 'venta' && $cosecha->codigo_venta) {
                return 'venta_' . $cosecha->codigo_venta;
            }
            // Si no es venta o no tiene código, mostrar individual
            return 'individual_' . $cosecha->id;
        })->map(function($grupo) {
            $primera = $grupo->first();
            
            // Si es un grupo de venta (múltiples registros con mismo código)
            if ($grupo->count() > 1 && $primera->destino === 'venta') {
                return (object) [
                    'es_grupo_venta' => true,
                    'codigo_venta' => $primera->codigo_venta,
                    'fecha' => $primera->fecha,
                    'fecha_venta' => $primera->fecha_venta,
                    'tipo_cliente' => $primera->tipo_cliente,
                    'cliente_nombre' => $primera->cliente_nombre,
                    'cliente_nit' => $primera->cliente_nit,
                    'total_venta' => $grupo->sum('total_venta'),
                    'cantidad_productos' => $grupo->count(),
                    'peso_total' => $grupo->sum('peso_cosechado_kg'),
                    'estado_venta' => $primera->estado_venta,
                    'detalles' => $grupo, // Todos los productos de la venta
                    'created_at' => $primera->created_at
                ];
            }
            
            // Si es registro individual
            return (object) [
                'es_grupo_venta' => false,
                'cosecha' => $primera
            ];
        })->sortByDesc('created_at');

        // Paginar manualmente los resultados agrupados
        $page = request('page', 1);
        $perPage = 12;
        $total = $cosechasAgrupadas->count();
        $items = $cosechasAgrupadas->slice(($page - 1) * $perPage, $perPage);
        
        $cosechas = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );
        $cosechas->withQueryString();

        $lotes = \App\Models\Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);
        return view('cosechas.index', compact('cosechas', 'lotes'));
    }

    public function create()
    {
        // Obtener lotes activos con código y especie
        $lotes = Lote::where('estado', 'activo')
            ->orderBy('codigo_lote')
            ->get(['id', 'codigo_lote', 'especie', 'cantidad_actual']);

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

    /**
     * Mostrar vista de factura para crear múltiples cosechas
     */
    public function createFactura()
    {
        $lotes = Lote::where('estado', 'activo')
                    ->where('cantidad_actual', '>', 0)
                    ->with('unidadProduccion')
                    ->orderBy('codigo_lote')
                    ->get();

        return view('cosechas.factura', compact('lotes'));
    }

    /**
     * Guardar múltiples cosechas desde la vista estilo factura
     */
    public function storeMultiple(Request $request)
    {
        // TEMPORAL: Debug para ver qué llega
        \Illuminate\Support\Facades\Log::info('=== DATOS RECIBIDOS EN CONTROLADOR ===', [
            'tipo_cliente' => $request->tipo_cliente,
            'cliente_nombre' => $request->cliente_nombre,
            'cliente_nit' => $request->cliente_nit,
            'productos' => $request->productos,
            'all_request' => $request->all()
        ]);

        // Validar datos básicos
        $request->validate([
            'tipo_cliente' => 'required|in:CF,NIT',
            'cliente_nombre' => 'required_if:tipo_cliente,NIT|nullable|string|max:255',
            'cliente_nit' => 'required_if:tipo_cliente,NIT|nullable|string|max:15',
            'productos' => 'required|array|min:1',
            'productos.*.lote_id' => 'required|integer|exists:lotes,id',
            'productos.*.cantidad_peces' => 'required|integer|min:1',
            'productos.*.peso_libras' => 'required|numeric|min:0.01'
        ], [
            'cliente_nombre.required_if' => 'El nombre del cliente es obligatorio para clientes con NIT.',
            'cliente_nit.required_if' => 'El NIT es obligatorio para clientes con NIT.',
            'productos.required' => 'Debe agregar al menos un producto.',
            'productos.*.lote_id.required' => 'Cada producto debe tener un lote válido.',
            'productos.*.cantidad_peces.required' => 'Cada producto debe tener una cantidad de peces.',
            'productos.*.peso_libras.required' => 'Cada producto debe tener un peso en libras.'
        ]);

        try {
            DB::beginTransaction();

            // DEBUG: Log de datos recibidos
            \Illuminate\Support\Facades\Log::info('=== DATOS RECIBIDOS EN STORE MULTIPLE ===', [
                'tipo_cliente' => $request->tipo_cliente,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_nit' => $request->cliente_nit,
                'productos_count' => count($request->productos ?? [])
            ]);

            // El precio debe estar configurado en cada lote individualmente

            // Generar número de venta único
            $numeroVenta = $this->generarNumeroVenta();
            $fechaVenta = now()->toDateString();
            $userId = Auth::id();

            // Calcular totales
            $totalVenta = 0;
            $cosechasCreadas = [];

            // Procesar cada producto como un detalle de la misma venta
            foreach ($request->productos as $producto) {
                $lote = Lote::lockForUpdate()->find($producto['lote_id']);
                
                if (!$lote) {
                    throw new \Exception("El lote con ID {$producto['lote_id']} no existe.");
                }

                if ($lote->cantidad_actual < $producto['cantidad_peces']) {
                    throw new \Exception("El lote {$lote->codigo_lote} ({$lote->especie}) no tiene suficientes peces. Disponible: {$lote->cantidad_actual}, solicitado: {$producto['cantidad_peces']}");
                }

                // El lote debe tener precio configurado
                $precioLibra = $lote->precio_libra;

                if (!$precioLibra || $precioLibra <= 0) {
                    throw new \Exception("El lote {$lote->codigo_lote} no tiene precio configurado. Configure el precio del lote antes de realizar ventas.");
                }                // Calcular subtotal para este producto
                $pesoLibras = (float) $producto['peso_libras'];
                $subtotal = $pesoLibras * $precioLibra;
                $totalVenta += $subtotal;

                // Crear registro de cosecha con el mismo número de venta (SIN UNIQUE)
                $cosecha = CosechaParcial::create([
                    'lote_id' => $producto['lote_id'],
                    'fecha' => $fechaVenta,
                    'cantidad_cosechada' => $producto['cantidad_peces'],
                    'peso_cosechado_kg' => $pesoLibras * 0.453592, // Convertir libras a kg
                    'destino' => 'venta',
                    'responsable' => Auth::user()->name,
                    'observaciones' => "Venta #{$numeroVenta} - {$lote->especie} ({$lote->codigo_lote})",
                    'user_id' => $userId,
                    'precio_kg' => $precioLibra / 0.453592, // Precio por kg
                    'total_venta' => $subtotal,
                    'estado_venta' => 'completada',
                    'fecha_venta' => now(),
                    'codigo_venta' => $numeroVenta, // MISMO CÓDIGO PARA TODOS LOS PRODUCTOS
                    'tipo_cliente' => $request->tipo_cliente,
                    'cliente' => $request->tipo_cliente === 'CF' ? 'Consumidor Final' : $request->cliente_nombre,
                    'cliente_nombre' => $request->tipo_cliente === 'CF' ? 'Consumidor Final' : $request->cliente_nombre,
                    'cliente_nit' => $request->tipo_cliente === 'CF' ? null : $request->cliente_nit,
                    'nombre_cliente' => $request->tipo_cliente === 'CF' ? 'Consumidor Final' : $request->cliente_nombre,
                    'direccion_cliente' => null,
                    'telefono_cliente' => null
                ]);

                // Actualizar stock del lote
                $lote->cantidad_actual -= $producto['cantidad_peces'];
                $lote->save();

                $cosechasCreadas[] = [
                    'id' => $cosecha->id,
                    'lote_codigo' => $lote->codigo_lote,
                    'especie' => $lote->especie,
                    'cantidad_peces' => $producto['cantidad_peces'],
                    'peso_libras' => $pesoLibras,
                    'precio_libra' => $precioLibra,
                    'subtotal' => $subtotal
                ];
            }

            DB::commit();

            \Illuminate\Support\Facades\Log::info('=== VENTA CREADA EXITOSAMENTE ===', [
                'numero_venta' => $numeroVenta,
                'total_productos' => count($cosechasCreadas),
                'total_venta' => $totalVenta,
                'cliente' => $request->tipo_cliente === 'CF' ? 'Consumidor Final' : $request->cliente_nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => "Venta #{$numeroVenta} registrada exitosamente con " . count($cosechasCreadas) . " productos.",
                'data' => [
                    'numero_venta' => $numeroVenta,
                    'total_productos' => count($cosechasCreadas),
                    'total_venta' => $totalVenta,
                    'productos' => $cosechasCreadas,
                    'cliente' => [
                        'tipo' => $request->tipo_cliente,
                        'nombre' => $request->tipo_cliente === 'CF' ? 'Consumidor Final' : $request->cliente_nombre,
                        'nit' => $request->tipo_cliente === 'CF' ? null : $request->cliente_nit
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('=== ERROR AL CREAR VENTA ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarNumeroVenta()
    {
        $year = date('Y');
        $lastNumber = CosechaParcial::whereYear('fecha', $year)
            ->whereNotNull('codigo_venta')
            ->orderBy('codigo_venta', 'desc')
            ->value('codigo_venta');

        if ($lastNumber) {
            $number = intval(substr($lastNumber, -6)) + 1;
        } else {
            $number = 1;
        }

        return $year . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    private function extraerProductosDelRequest(Request $request)
    {
        $productos = [];
        
        if ($request->has('productos')) {
            foreach ($request->productos as $producto) {
                $productos[] = [
                    'lote_id' => $producto['lote_id'],
                    'cantidad_peces' => $producto['cantidad_peces'],
                    'peso_libras' => $producto['peso_libras']
                ];
            }
        }
        
        return $productos;
    }
}
