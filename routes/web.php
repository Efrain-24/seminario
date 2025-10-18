<?php
use App\Http\Controllers\Reportes\ReporteGananciasController;
use Illuminate\Support\Facades\Route;
use App\Models\Lote;
use App\Models\Insumo;
use Illuminate\Http\Request;

// Rutas para reportes
Route::middleware(['auth', 'redirect.temp.password'])->prefix('reportes')->name('reportes.')->group(function () {
    Route::get('ganancias', [ReporteGananciasController::class, 'index'])->name('ganancias');
    Route::get('ganancias/{lote?}', function (Request $request, $lote = null) {
        $unidades = \App\Models\UnidadProduccion::all();
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();
        $loteSeleccionado = $lote ? \App\Models\Lote::with('unidadProduccion')->find($lote) : null;

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // --- Compra Lote ---
        $precioUnitarioPez = $loteSeleccionado->precio_unitario_pez ?? 0;
        $precioCompraLote = $loteSeleccionado && $precioUnitarioPez > 0 ? $loteSeleccionado->cantidad_inicial * $precioUnitarioPez : 0;

        // --- Alimentación ---
        $alimentaciones = $loteSeleccionado ? $loteSeleccionado->alimentaciones()->with('inventarioItem')->get() : collect();
        $totalAlimentacion = $alimentaciones->sum('costo_total');
        $alimentacionDetalle = $alimentaciones->map(function($a) {
            return [
                'fecha' => optional($a->fecha_alimentacion)->format('d/m/Y'),
                'producto' => $a->inventarioItem->nombre ?? 'N/A',
                'cantidad' => $a->cantidad_kg,
                'costo' => $a->costo_total,
            ];
        });

        // --- Mantenimientos ---
        $mantenimientos = $loteSeleccionado ? \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->get() : collect();
        $totalMantenimientos = $mantenimientos->sum('costo_mantenimiento');
        $mantenimientoDetalle = $mantenimientos->map(function($m) {
            return [
                'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                'tipo' => $m->tipo_mantenimiento,
                'descripcion' => $m->descripcion_trabajo,
                'costo' => $m->costo_mantenimiento,
            ];
        });

        // --- Limpiezas ---
        $limpiezas = $loteSeleccionado ? \App\Models\Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->get() : collect();
        $totalLimpiezas = $limpiezas->sum('costo') ?? 0;
        $limpiezaDetalle = $limpiezas->map(function($l) {
            return [
                'fecha' => optional($l->fecha)->format('d/m/Y'),
                'tipo' => $l->protocoloSanidad->nombre ?? 'N/A',
                'productos' => is_array($l->actividades_ejecutadas) ? implode(', ', array_map(fn($a) => is_array($a) ? ($a['descripcion'] ?? '') : $a, $l->actividades_ejecutadas)) : '',
                'costo' => $l->costo ?? 0,
            ];
        });

        // --- Ventas ---
        $ventasQuery = $loteSeleccionado ? $loteSeleccionado->ventas() : null;
        if ($ventasQuery && $fechaInicio && $fechaFin) {
            $ventasQuery->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        $ventas = $ventasQuery ? $ventasQuery->get() : collect();
        $totalVentas = $ventas->sum('total_venta');
        $ventasDetalle = $ventas->map(function($v) {
            return [
                'fecha' => optional($v->fecha_venta)->format('d/m/Y'),
                'codigo' => $v->codigo_venta,
                'cliente' => $v->cliente,
                'peso_kg' => $v->peso_cosechado_kg,
                'precio_kg' => $v->precio_kg,
                'total' => $v->total_venta,
                'estado' => $v->estado_venta,
            ];
        });

        // --- Biomasa final ---
        $biomasaFinalKg = $loteSeleccionado->biomasa ?? 0;
        $biomasaFinalLb = $biomasaFinalKg * 2.20462;

        // --- Costos totales y ganancia ---
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        // --- Gráfica ---
        $grafica = $ventas->isNotEmpty() ? [
            'labels' => $ventas->pluck('fecha_venta')->map(fn($fecha) => optional($fecha)->format('d/m/Y'))->toArray(),
            'data' => $ventas->pluck('total_venta')->toArray(),
        ] : null;

        $desglose = [
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia,
            'precio_compra_lote' => $precioCompraLote,
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
        ];

        return view('reportes.ganancias.reporte', compact(
            'unidades',
            'lotes',
            'loteSeleccionado',
            'desglose',
            'alimentacionDetalle',
            'mantenimientoDetalle',
            'limpiezaDetalle',
            'ventasDetalle',
            'biomasaFinalKg',
            'biomasaFinalLb',
            'grafica',
            'fechaInicio',
            'fechaFin'
        ));
    })->name('ganancias.reporte');

    Route::get('ganancias/detalles/{lote?}', function (Request $request, $lote = null) {
        // Obtener el lote del parámetro URL o del query string
        $loteId = $lote ?? $request->input('lote_id');
        
        if (!$loteId) {
            return redirect()->route('reportes.ganancias')->with('error', 'Por favor selecciona un lote');
        }

        $loteSeleccionado = \App\Models\Lote::with('unidadProduccion')->find($loteId);
        if (!$loteSeleccionado) {
            abort(404, 'Lote no encontrado');
        }

        // --- Consumo de alimento con detalles ---
        $alimentaciones = $loteSeleccionado->alimentaciones()->with('inventarioItem')->get();
        $alimentacionDetalle = $alimentaciones->map(function($a) {
            // Obtener el precio de compra del inventario item
            $precioCompra = $a->inventarioItem->costo_unitario ?? $a->inventarioItem->precio_promedio ?? 0;
            $costoTotal = $a->cantidad_kg * $precioCompra;
            return [
                'fecha' => optional($a->fecha_alimentacion)->format('d/m/Y'),
                'producto' => $a->inventarioItem->nombre ?? 'N/A',
                'cantidad_kg' => $a->cantidad_kg,
                'precio_compra' => $precioCompra,
                'costo_total' => $costoTotal,
            ];
        })->toArray();
        $costoTotalAlimento = collect($alimentacionDetalle)->sum('costo_total');

        // --- Costos de mantenimientos completados (incluyendo insumos) ---
        // Buscar mantenimientos completados de la unidad de producción
        $mantenimientos = \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)
            ->where('estado_mantenimiento', 'completado')
            ->with('insumos')
            ->get();
        
        $protocoloDetalle = $mantenimientos->map(function($m) {
            // Calcular costo total de insumos de este mantenimiento
            $costoInsumos = 0;
            if ($m->insumos && count($m->insumos) > 0) {
                $costoInsumos = $m->insumos->sum(function($insumo) {
                    return $insumo->pivot->costo_total ?? ($insumo->pivot->cantidad * ($insumo->pivot->costo_unitario ?? 0));
                });
            }
            
            // Costo del protocolo y costo de insumos separados
            $costoProtocolo = $m->costo_mantenimiento ?? 0;
            
            return [
                'nombre' => $m->tipo_mantenimiento,
                'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                'descripcion' => $m->descripcion_trabajo,
                'costo_protocolo' => $costoProtocolo,
                'costo_insumos' => $costoInsumos,
                'costo_total' => $costoProtocolo + $costoInsumos,
            ];
        })->toArray();
        
        $costoTotalProtocolos = collect($protocoloDetalle)->sum('costo_protocolo');
        $costoTotalInsumos = collect($protocoloDetalle)->sum('costo_insumos');

        // --- Desglose de insumos utilizados ---
        $insumoDetalle = [];
        foreach ($mantenimientos as $m) {
            if ($m->insumos && count($m->insumos) > 0) {
                foreach ($m->insumos as $insumo) {
                    $cantidad = $insumo->pivot->cantidad ?? 0;
                    $costoUnitario = $insumo->pivot->costo_unitario ?? 0;
                    $costoTotal = $insumo->pivot->costo_total ?? ($cantidad * $costoUnitario);
                    
                    $insumoDetalle[] = [
                        'nombre' => $insumo->nombre,
                        'protocolo' => $m->tipo_mantenimiento,
                        'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                        'cantidad' => $cantidad,
                        'unidad' => $insumo->unidad ?? 'unidad',
                        'costo_unitario' => $costoUnitario,
                        'costo_total' => $costoTotal,
                    ];
                }
            }
        }

        // --- Mortalidad ---
        // Obtener la mortalidad registrada del lote
        $cantidadMortalidad = $loteSeleccionado->cantidad_inicial - $loteSeleccionado->cantidad_actual;
        if ($cantidadMortalidad < 0) {
            $cantidadMortalidad = 0;
        }
        
        // Obtener el precio unitario de la última compra del artículo "Pez"
        $precioUnitarioPez = 0;
        $itemPez = \App\Models\InventarioItem::where('nombre', 'Pez')->orWhere('nombre', 'like', '%pez%')->first();
        if ($itemPez) {
            // Obtener la última entrada de compra de este item
            $ultimaEntrada = \App\Models\EntradaCompra::with('detalles')
                ->whereHas('detalles', function($query) use ($itemPez) {
                    $query->where('item_id', $itemPez->id);
                })
                ->orderBy('id', 'desc')
                ->first();
            
            if ($ultimaEntrada && $ultimaEntrada->detalles) {
                $detalleItem = $ultimaEntrada->detalles->firstWhere('item_id', $itemPez->id);
                if ($detalleItem) {
                    $precioUnitarioPez = $detalleItem->costo_unitario ?? 0;
                }
            }
        }
        
        $costoMortalidad = $cantidadMortalidad * $precioUnitarioPez;

        // --- Precio de compra del pez ---
        $precioCompraPez = $loteSeleccionado->cantidad_inicial * $precioUnitarioPez;

        // --- Ingresos por Ventas ---
        $ventasDelLote = $loteSeleccionado->ventas()->get();
        $totalIngresosVentas = $ventasDelLote->sum('total_venta');

        // --- Ventas Potenciales ---
        $ultimaVenta = $loteSeleccionado->ventas()->orderBy('id', 'desc')->first();
        $ultimoPrecioVenta = 0;
        if ($ultimaVenta) {
            // Calcular el precio por kg de la última venta
            $ultimoPrecioVenta = $ultimaVenta->peso_cosechado_kg > 0 ? $ultimaVenta->total_venta / $ultimaVenta->peso_cosechado_kg : 0;
        }
        $ventasPotenciales = $loteSeleccionado->cantidad_actual * $ultimoPrecioVenta;

        // --- Totales ---
        $totalCostos = $costoTotalAlimento + $costoTotalProtocolos + $costoTotalInsumos + $precioCompraPez + $costoMortalidad;
        
        // --- Subtotal (Ingresos - Costos) ---
        $totalIngresos = $totalIngresosVentas + $ventasPotenciales;
        $costosSinPrecioCompra = $costoTotalAlimento + $costoTotalProtocolos + $costoTotalInsumos + $costoMortalidad;
        $subtotal = $totalIngresos - $costosSinPrecioCompra;

        return view('reportes.ganancias.detalles', compact(
            'loteSeleccionado',
            'alimentacionDetalle',
            'costoTotalAlimento',
            'protocoloDetalle',
            'costoTotalProtocolos',
            'insumoDetalle',
            'costoTotalInsumos',
            'precioCompraPez',
            'cantidadMortalidad',
            'costoMortalidad',
            'totalCostos',
            'totalIngresosVentas',
            'ventasPotenciales',
            'subtotal'
        ));
    })->name('ganancias.detalles');
});


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\TipoAlimentoController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\CosechaParcialController;
use App\Http\Controllers\ControlProduccionController;
use App\Http\Controllers\MortalidadController;
use App\Http\Controllers\AlertaAnomaliaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\InventarioItemController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\InventarioMovimientoController;
use App\Http\Controllers\InventarioAlertaController;
use App\Http\Controllers\ProtocoloSanidadController;
use App\Http\Controllers\LimpiezaController;
use App\Http\Controllers\AccionCorrectivaController;
use App\Http\Controllers\UnidadProduccionController;
use App\Http\Controllers\TrazabilidadCosechaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ClienteController; // añadido
use App\Http\Controllers\EntradaCompraController;

use Illuminate\Support\Facades\Auth;

// CRUD de Clientes
Route::middleware(['auth'])->group(function () {
    Route::get('clientes/buscar', [ClienteController::class, 'search'])->name('clientes.search'); // ruta ajax
    Route::resource('clientes', App\Http\Controllers\ClienteController::class);
});
// Historial de limpiezas por unidad
Route::get('/limpieza/historial-unidad/{codigo}', [App\Http\Controllers\LimpiezaController::class, 'historialUnidad'])->name('limpieza.historial_unidad');


// Ocultar módulos de aplicación por rol
Route::get('/roles/{role}/ocultar-modulos', [RoleController::class, 'ocultarModulos'])->name('roles.ocultar-modulos');
Route::put('/roles/{role}/ocultar-modulos', [RoleController::class, 'actualizarModulos'])->name('roles.ocultar-modulos.update');

Route::get('/produccion/unidades/{unidad}/mortalidad-log', [MortalidadController::class, 'logPorUnidad'])->name('produccion.unidades.mortalidad_log');
Route::get('produccion/lotes/{lote}/mortalidad-log', [\App\Http\Controllers\MortalidadLogController::class, 'show'])->name('produccion.lotes.mortalidad_log')->middleware('auth');

// Ruta de prueba para tipo de cambio
// Ruta de prueba para tipo de cambio
Route::get('/test-tipo-cambio', function () {
    return view('test-tipo-cambio');
});

Route::get('/', function () {
    return view('welcome');
});

// RUTA DE PRUEBA TEMPORAL - SIN AUTENTICACIÓN
Route::post('/test-cosecha', function(Request $request) {
    \Illuminate\Support\Facades\Log::info('=== RUTA DE PRUEBA FUNCIONANDO ===');
    \Illuminate\Support\Facades\Log::info('Datos recibidos:', $request->all());
    return response()->json(['status' => 'success', 'message' => 'Datos recibidos correctamente', 'data' => $request->all()]);
})->name('test.cosecha');
// FIN RUTA DE PRUEBA

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'redirect.temp.password'])
    ->name('dashboard');

Route::get('/aplicaciones', function () {
    return view('aplicaciones');
})->middleware(['auth', 'verified', 'redirect.temp.password'])->name('aplicaciones');

// Rutas de Paneles de Módulos
Route::middleware(['auth', 'verified', 'redirect.temp.password'])->group(function () {
    Route::get('/unidades/panel', function () {
        return view('unidades.panel');
    })->name('unidades.panel');
    
        Route::get('/unidades/{codigo}/protocolos', [ProtocoloSanidadController::class, 'protocolosPorUnidad'])->name('unidades.protocolos');

    Route::get('/produccion/panel', function () {
        return view('produccion.panel');
    })->name('produccion.panel');
    
    Route::get('/inventarios/panel', function () {
        return view('inventarios.panel');
    })->name('inventarios.panel');
    
    Route::get('/usuarios/panel', function () {
        return view('usuarios.panel');
    })->name('usuarios.panel');
    
    Route::get('/acciones-correctivas/panel', function () {
        return view('acciones-correctivas.panel');
    })->name('acciones-correctivas.panel');
    
    Route::get('/protocolos/panel', function () {
        return view('protocolos.panel');
    })->name('protocolos.panel');
    
    Route::get('/ventas/panel', [VentaController::class, 'panel'])->name('ventas.panel');
    
    Route::get('/cosechas/panel', [CosechaParcialController::class, 'panel'])->name('cosechas.panel');
    
    Route::get('/compras/panel', function () {
        return view('compras.panel');
    })->name('compras.panel');
});

// Rutas de Ventas
Route::middleware(['auth'])->group(function () {
    Route::resource('ventas', VentaController::class);
    Route::patch('ventas/{venta}/completar', [VentaController::class, 'completar'])->name('ventas.completar');
    Route::patch('ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
    
    // Rutas para tickets de venta
    Route::get('ventas/{venta}/ticket/descargar', [VentaController::class, 'generarTicket'])->name('ventas.ticket.descargar');
    Route::get('ventas/{venta}/ticket/ver', [VentaController::class, 'verTicket'])->name('ventas.ticket.ver');
});

// Rutas de Trazabilidad de Cosechas
Route::middleware(['auth'])->group(function () {
    Route::get('/cosechas/trazabilidad', [TrazabilidadCosechaController::class, 'index'])->name('cosechas.trazabilidad.index');
    Route::get('/cosechas/trazabilidad/crear', [TrazabilidadCosechaController::class, 'create'])->name('cosechas.trazabilidad.create');
    Route::post('/cosechas/trazabilidad', [TrazabilidadCosechaController::class, 'store'])->name('cosechas.trazabilidad.store');
    Route::get('/cosechas/trazabilidad/{trazabilidad}', [TrazabilidadCosechaController::class, 'show'])->name('cosechas.trazabilidad.show');
    Route::get('/cosechas/trazabilidad/{trazabilidad}/editar', [TrazabilidadCosechaController::class, 'edit'])->name('cosechas.trazabilidad.edit');
    Route::put('/cosechas/trazabilidad/{trazabilidad}', [TrazabilidadCosechaController::class, 'update'])->name('cosechas.trazabilidad.update');
    Route::delete('/cosechas/trazabilidad/{trazabilidad}', [TrazabilidadCosechaController::class, 'destroy'])->name('cosechas.trazabilidad.destroy');
});

Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::delete('mantenimientos/eliminar/{id}', [App\Http\Controllers\LimpiezaController::class, 'eliminarMantenimiento'])->name('mantenimientos.eliminar');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para gestión de usuarios (las rutas más específicas van primero)
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:users.edit');

    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // Rutas para gestión de roles (las rutas más específicas van primero)
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });
});

Route::middleware(['auth', 'redirect.temp.password'])->prefix('produccion')->name('produccion.')->group(function () {
    Route::get('/', [ProduccionController::class, 'index'])->name('index');
        // Ruta para eliminar mantenimiento
        Route::delete('/mantenimiento/{mantenimiento}/eliminar', [ProduccionController::class, 'eliminarMantenimiento'])->name('mantenimientos.eliminar');
        Route::delete('/mantenimiento/{mantenimiento}/eliminar-ciclo', [ProduccionController::class, 'eliminarCiclo'])->name('mantenimientos.eliminarCiclo');

    // Rutas de Lotes
    Route::get('/lotes', [ProduccionController::class, 'gestionLotes'])->name('lotes')->middleware('permission:lotes.view');
    Route::get('/lotes/create', [ProduccionController::class, 'createLote'])->name('lotes.create')->middleware('permission:lotes.create');
    Route::post('/lotes', [ProduccionController::class, 'storeLote'])->name('lotes.store')->middleware('permission:lotes.create');
    Route::get('/lotes/{lote}', [ProduccionController::class, 'showLote'])->name('lotes.show')->middleware('permission:lotes.view');
    Route::get('/lotes/{lote}/edit', [ProduccionController::class, 'editLote'])->name('lotes.edit')->middleware('permission:lotes.edit');
    Route::put('/lotes/{lote}', [ProduccionController::class, 'updateLote'])->name('lotes.update')->middleware('permission:lotes.edit');
    Route::delete('/lotes/{lote}', [ProduccionController::class, 'destroyLote'])->name('lotes.destroy')->middleware('permission:lotes.delete');

    // Rutas de Unidades - Ahora usando UnidadProduccionController
    Route::get('/unidades', [UnidadProduccionController::class, 'index'])->name('unidades.index')->middleware('permission:unidades.view');
    Route::get('/unidades/create', [UnidadProduccionController::class, 'create'])->name('unidades.create')->middleware('permission:unidades.create');
    Route::post('/unidades', [UnidadProduccionController::class, 'store'])->name('unidades.store')->middleware('permission:unidades.create');
    Route::get('/unidades/{unidad}', [UnidadProduccionController::class, 'show'])->name('unidades.show')->middleware('permission:unidades.view');
    Route::get('/unidades/{unidad}/edit', [UnidadProduccionController::class, 'edit'])->name('unidades.edit')->middleware('permission:unidades.edit');
    Route::put('/unidades/{unidad}', [UnidadProduccionController::class, 'update'])->name('unidades.update')->middleware('permission:unidades.edit');
    Route::patch('/unidades/{unidad}/toggle-estado', [UnidadProduccionController::class, 'toggleEstado'])->name('unidades.toggle-estado')->middleware('permission:unidades.delete');
    Route::delete('/unidades/{unidad}', [UnidadProduccionController::class, 'destroy'])->name('unidades.destroy')->middleware('permission:unidades.delete');
    Route::get('/unidades/generate-code/{tipo}', [UnidadProduccionController::class, 'generateCodigo'])->name('unidades.generate-code');
    Route::get('/unidades/{unidad}/historial', [UnidadProduccionController::class, 'historial'])->name('unidades.historial')->middleware('permission:unidades.view');

    // Otras rutas
    Route::get('/traslados', [ProduccionController::class, 'gestionTraslados'])->name('traslados');
    Route::get('/seguimiento-lotes', [ProduccionController::class, 'seguimientoLotes'])->name('seguimiento.lotes');
    Route::get('/seguimiento-unidades', [ProduccionController::class, 'seguimientoUnidades'])->name('seguimiento.unidades');

    // Rutas de seguimientos específicos
    Route::get('/lotes/{lote}/seguimiento/crear', [ProduccionController::class, 'crearSeguimiento'])->name('lotes.seguimiento.crear');
    Route::post('/lotes/{lote}/seguimiento', [ProduccionController::class, 'storeSeguimiento'])->name('lotes.seguimiento.store');
    Route::get('/lotes/{lote}/seguimientos', [ProduccionController::class, 'verSeguimientos'])->name('lotes.seguimientos.ver');

    // Rutas de traslados
    Route::get('/traslados/crear/{lote?}', [ProduccionController::class, 'crearTraslado'])->name('traslados.crear');
    Route::post('/traslados', [ProduccionController::class, 'storeTraslado'])->name('traslados.store');
    Route::get('/traslados/{traslado}', [ProduccionController::class, 'showTraslado'])->name('traslados.show');
    Route::patch('/traslados/{traslado}/completar', [ProduccionController::class, 'completarTraslado'])->name('traslados.completar');
    Route::patch('/traslados/{traslado}/cancelar', [ProduccionController::class, 'cancelarTraslado'])->name('traslados.cancelar');

    // Rutas de mantenimientos (las rutas más específicas van primero)
    Route::get('/mantenimientos/crear/{unidad?}', [ProduccionController::class, 'crearMantenimiento'])->name('mantenimientos.crear')->middleware('permission:crear_mantenimientos');
    Route::get('/mantenimientos/historial/{unidad?}', [ProduccionController::class, 'historialMantenimientos'])->name('mantenimientos.historial')->middleware('permission:ver_mantenimientos');
    Route::post('/mantenimientos', [ProduccionController::class, 'storeMantenimiento'])->name('mantenimientos.store')->middleware('permission:crear_mantenimientos');
    Route::get('/mantenimientos/{unidad?}', [ProduccionController::class, 'gestionMantenimientos'])->name('mantenimientos')->middleware('permission:ver_mantenimientos');
    Route::get('/mantenimiento/{mantenimiento}', [ProduccionController::class, 'showMantenimiento'])->name('mantenimientos.show')->middleware('permission:ver_mantenimientos');
    Route::get('/mantenimiento/{mantenimiento}/editar', [ProduccionController::class, 'editMantenimiento'])->name('mantenimientos.edit')->middleware('permission:editar_mantenimientos');
    Route::put('/mantenimiento/{mantenimiento}', [ProduccionController::class, 'updateMantenimiento'])->name('mantenimientos.update')->middleware('permission:editar_mantenimientos');
    Route::patch('/mantenimiento/{mantenimiento}/iniciar', [ProduccionController::class, 'iniciarMantenimiento'])->name('mantenimientos.iniciar')->middleware('permission:editar_mantenimientos');
    Route::patch('/mantenimiento/{mantenimiento}/completar', [ProduccionController::class, 'completarMantenimiento'])->name('mantenimientos.completar')->middleware('permission:editar_mantenimientos');
    Route::patch('/mantenimiento/{mantenimiento}/cancelar', [ProduccionController::class, 'cancelarMantenimiento'])->name('mantenimientos.cancelar')->middleware('permission:editar_mantenimientos');

    Route::get('/control',                     [ControlProduccionController::class, 'index'])->name('control.index');
    Route::get('/control/lote/{lote}',         [ControlProduccionController::class, 'show'])->name('control.show');
    Route::post('/control/lote/{lote}/fecha',  [ControlProduccionController::class, 'predecirHastaFecha'])->name('control.pred.fecha');
    Route::post('/control/lote/{lote}/peso',   [ControlProduccionController::class, 'predecirParaPeso'])->name('control.pred.peso');

    // Registro de Cosechas Parciales
    Route::resource('cosechas', CosechaParcialController::class)
        ->parameters(['cosechas' => 'cosecha'])   // para usar {cosecha} en vez de {cosechas}
        ->names('cosechas');

    // Rutas adicionales para ventas de cosechas
    Route::get('cosechas/{cosecha}/completar-venta', [CosechaParcialController::class, 'completarVenta'])->name('cosechas.completar-venta');
    Route::put('cosechas/{cosecha}/procesar-venta', [CosechaParcialController::class, 'procesarVenta'])->name('cosechas.procesar-venta');
    Route::get('cosechas/{cosecha}/ticket/descargar', [CosechaParcialController::class, 'generarTicket'])->name('cosechas.ticket.descargar');
    Route::get('cosechas/{cosecha}/ticket/ver', [CosechaParcialController::class, 'verTicket'])->name('cosechas.ticket.ver');

    // 1) Primero los gráficos
    Route::get('mortalidades/graficos', [MortalidadController::class, 'charts'])
        ->name('mortalidades.charts');

    // 2) Luego el resource
    Route::resource('mortalidades', MortalidadController::class)
        ->parameters(['mortalidades' => 'mortalidad'])
        ->names('mortalidades')
        ->whereNumber('mortalidad')      // evita que "graficos" sea tomado como id
        ->except(['show']);              // opcional: si no tienes página show

    Route::get('alertas', [AlertaAnomaliaController::class, 'index'])
        ->name('produccion.alertas.index');

    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');


    // CRUD Ítems y Bodegas
    Route::resource('inventario/items', InventarioItemController::class)->names('inventario.items');
    Route::resource('inventario/bodegas', BodegaController::class)->names('inventario.bodegas');

    // Movimientos (kardex + crear movimiento)
    Route::get('inventario/movimientos', [InventarioMovimientoController::class, 'index'])
        ->name('inventario.movimientos.index');
    Route::get('inventario/movimientos/create/{tipo}', [InventarioMovimientoController::class, 'create'])
        ->whereIn('tipo', ['entrada', 'salida', 'ajuste'])
        ->name('inventario.movimientos.create');
    Route::post('inventario/movimientos', [InventarioMovimientoController::class, 'store'])
        ->name('inventario.movimientos.store');

    Route::get('inventario/alertas', [InventarioAlertaController::class, 'index'])
        ->name('inventario.alertas.index');
});

// Rutas de Lotes (independientes del prefijo produccion)
Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::resource('lotes', LoteController::class);
    Route::get('lotes/{lote}/historial', [LoteController::class, 'historial'])->name('lotes.historial');
});

// Rutas de Seguimientos (usar ProduccionController temporalmente)
Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::get('seguimientos', [ProduccionController::class, 'seguimientoLotes'])->name('seguimientos.index');
    Route::get('seguimientos/create', [ProduccionController::class, 'crearSeguimiento'])->name('seguimientos.create');
    Route::post('seguimientos', [ProduccionController::class, 'storeSeguimiento'])->name('seguimientos.store');
});

// Rutas de Mantenimiento de Unidades (usar ProduccionController temporalmente)
Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::get('mantenimiento-unidades', [ProduccionController::class, 'gestionMantenimientos'])->name('mantenimiento-unidades.index');
    Route::get('mantenimiento-unidades/create', [ProduccionController::class, 'crearMantenimiento'])->name('mantenimiento-unidades.create');
    Route::post('mantenimiento-unidades', [ProduccionController::class, 'storeMantenimiento'])->name('mantenimiento-unidades.store');
    Route::get('mantenimiento-unidades/{mantenimiento}', [ProduccionController::class, 'showMantenimiento'])->name('mantenimiento-unidades.show');
    Route::patch('mantenimiento-unidades/{mantenimiento}/iniciar', [ProduccionController::class, 'iniciarMantenimiento'])->name('mantenimiento-unidades.iniciar');
    Route::patch('mantenimiento-unidades/{mantenimiento}/completar', [ProduccionController::class, 'completarMantenimiento'])->name('mantenimiento-unidades.completar');
});

// Rutas de Tipos de Alimentos (independientes)
Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::resource('tipos-alimentos', TipoAlimentoController::class);
    Route::patch('tipos-alimentos/{tipoAlimento}/toggle', [TipoAlimentoController::class, 'toggle'])->name('tipos-alimentos.toggle');
});

// Rutas de Alimentación
Route::middleware(['auth', 'redirect.temp.password'])->prefix('alimentacion')->name('alimentacion.')->group(function () {
    // Rutas para tipos de alimento (primero)
    Route::get('/tipos-alimento', [TipoAlimentoController::class, 'index'])->name('tipos-alimento.index');
    Route::get('/tipos-alimento/create', [TipoAlimentoController::class, 'create'])->name('tipos-alimento.create')->middleware('permission:alimentacion.create');
    Route::post('/tipos-alimento', [TipoAlimentoController::class, 'store'])->name('tipos-alimento.store')->middleware('permission:alimentacion.create');
    Route::get('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'show'])->name('tipos-alimento.show')->middleware('permission:alimentacion.view');
    Route::get('/tipos-alimento/{tipoAlimento}/edit', [TipoAlimentoController::class, 'edit'])->name('tipos-alimento.edit')->middleware('permission:alimentacion.edit');
    Route::put('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'update'])->name('tipos-alimento.update')->middleware('permission:alimentacion.edit');
    Route::delete('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'destroy'])->name('tipos-alimento.destroy')->middleware('permission:alimentacion.delete');
    Route::patch('/tipos-alimento/{tipoAlimento}/toggle', [TipoAlimentoController::class, 'toggle'])->name('tipos-alimento.toggle')->middleware('permission:alimentacion.edit');

    // Luego las rutas de Alimentación
    Route::get('/', [App\Http\Controllers\AlimentacionController::class, 'index'])->name('index')->middleware('permission:alimentacion.view');
    Route::get('/create', [App\Http\Controllers\AlimentacionController::class, 'create'])->name('create')->middleware('permission:alimentacion.create');
    Route::post('/', [App\Http\Controllers\AlimentacionController::class, 'store'])->name('store')->middleware('permission:alimentacion.create');
    Route::get('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'show'])->name('show')->middleware('permission:alimentacion.view');
    Route::get('/{alimentacion}/edit', [App\Http\Controllers\AlimentacionController::class, 'edit'])->name('edit')->middleware('permission:alimentacion.edit');
    Route::put('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'update'])->name('update')->middleware('permission:alimentacion.edit');
    Route::delete('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'destroy'])->name('destroy')->middleware('permission:alimentacion.delete');
});

// Rutas de Notificaciones (AJAX)
Route::middleware(['auth', 'redirect.temp.password'])->prefix('notificaciones')->name('notificaciones.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificacionController::class, 'index'])->name('index');
    Route::get('/todas', [App\Http\Controllers\NotificacionController::class, 'todas'])->name('todas');
    Route::get('/count', [App\Http\Controllers\NotificacionController::class, 'count'])->name('count');
    Route::patch('/{notificacion}/marcar-leida', [App\Http\Controllers\NotificacionController::class, 'marcarComoLeida'])->name('marcar-leida');
    Route::patch('/{notificacion}/marcar-resuelta', [App\Http\Controllers\NotificacionController::class, 'marcarComoResuelta'])->name('marcar-resuelta');
    Route::post('/marcar-todas-leidas', [App\Http\Controllers\NotificacionController::class, 'marcarTodasComoLeidas'])->name('marcar-todas-leidas');
    Route::delete('/{notificacion}', [App\Http\Controllers\NotificacionController::class, 'destroy'])->name('destroy');
    Route::post('/generar-reales', [App\Http\Controllers\NotificacionController::class, 'generarReales'])->name('generar-reales');
    Route::post('/programar-automaticas', [App\Http\Controllers\NotificacionController::class, 'programarAutomaticas'])->name('programar-automaticas');
});

require __DIR__ . '/auth.php';

// Rutas para cambio de contraseña (sin middleware de contraseña temporal)
Route::middleware('auth')->group(function () {
    Route::get('password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::put('password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

// Rutas de Protocolo de Sanidad
Route::middleware(['auth', 'redirect.temp.password'])->group(function () {
    Route::resource('protocolo-sanidad', ProtocoloSanidadController::class);
    Route::get('protocolo-sanidad/{protocoloSanidad}/nueva-version', [ProtocoloSanidadController::class, 'crearNuevaVersion'])->name('protocolo-sanidad.nueva-version');
    Route::post('protocolo-sanidad/{protocoloSanidad}/nueva-version', [ProtocoloSanidadController::class, 'guardarNuevaVersion'])->name('protocolo-sanidad.guardar-nueva-version');
    Route::patch('protocolo-sanidad/{protocoloSanidad}/marcar-obsoleto', [ProtocoloSanidadController::class, 'marcarObsoleto'])->name('protocolo-sanidad.marcar-obsoleto');
    Route::post('protocolo-sanidad/{protocoloSanidad}/ejecutar', [ProtocoloSanidadController::class, 'ejecutar'])->name('protocolo-sanidad.ejecutar');
    Route::resource('limpieza', LimpiezaController::class);
    Route::post('limpieza/completar', [LimpiezaController::class, 'completar'])->name('limpieza.completar');
    Route::get('limpieza/protocolo/{protocolo}/actividades', [LimpiezaController::class, 'getProtocoloActividades'])->name('limpieza.protocolo.actividades');
});

// Rutas de Unidades de Producción (independientes)
Route::middleware(['auth'])->group(function () {
    // Importante: forzamos el nombre del parámetro a 'unidad' (Laravel singulariza 'unidades' -> 'unidade' por defecto)
    // para que coincida con las vistas que usan route('unidades.show', ['unidad' => $unidad->id])
    Route::resource('unidades', UnidadProduccionController::class)
        ->parameters(['unidades' => 'unidad']);
    Route::patch('unidades/{unidad}/toggle-estado', [UnidadProduccionController::class, 'toggleEstado'])->name('unidades.toggle-estado');
    Route::get('unidades/generate-code/{tipo}', [UnidadProduccionController::class, 'generateCodigo'])->name('unidades.generate-code');
    Route::get('unidades/{unidad}/historial', [UnidadProduccionController::class, 'historial'])->name('unidades.historial');
});

// Rutas de Acciones Correctivas  
Route::middleware(['auth'])->group(function () {
    Route::resource('acciones_correctivas', AccionCorrectivaController::class)->parameters(['acciones_correctivas' => 'accion']);
    Route::patch('acciones_correctivas/{accion}/cambiar-estado', [AccionCorrectivaController::class, 'cambiarEstado'])->name('acciones_correctivas.cambiar-estado');
    Route::post('acciones_correctivas/{accion}/seguimiento', [AccionCorrectivaController::class, 'agregarSeguimiento'])->name('acciones_correctivas.agregarSeguimiento');
    Route::get('acciones_correctivas/{accion}/seguimiento/{seguimiento}/editar', [AccionCorrectivaController::class, 'editarSeguimiento'])->name('acciones_correctivas.editarSeguimiento');
    Route::put('acciones_correctivas/{accion}/seguimiento/{seguimiento}', [AccionCorrectivaController::class, 'actualizarSeguimiento'])->name('acciones_correctivas.actualizarSeguimiento');
    Route::delete('acciones_correctivas/{accion}/seguimiento/{seguimiento}', [AccionCorrectivaController::class, 'eliminarSeguimiento'])->name('acciones_correctivas.eliminarSeguimiento');
});

// Rutas de Proveedores
Route::middleware(['auth'])->group(function () {
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::get('proveedores-buscar', [ProveedorController::class,'search'])->name('proveedores.search');
    Route::patch('proveedores/{proveedor}/cambiar-estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.cambiar-estado');
    Route::patch('proveedores/{proveedor}/evaluar', [ProveedorController::class, 'evaluar'])->name('proveedores.evaluar');

    // Entradas (antes "órdenes de compra")
    Route::resource('entradas', EntradaCompraController::class)->only(['index','create','store','show']);
});

// Grupo de rutas para el módulo Bitácora
Route::middleware(['auth'])->prefix('bitacora')->name('bitacora.')->group(function () {
    Route::get('/', [BitacoraController::class, 'index'])->name('index');
    // Aquí puedes agregar más rutas del módulo si lo necesitas
});

// Rutas para eliminar seguimientos
Route::delete('/seguimientos/{seguimiento}', [App\Http\Controllers\SeguimientoController::class, 'destroy'])->name('seguimientos.destroy');

// Rutas de Reportes
Route::middleware(['auth', 'redirect.temp.password'])->prefix('reportes')->name('reportes.')->group(function () {
    // Reportes de Ganancias (usando funciones temporalmente)
    Route::get('/ganancias', [\App\Http\Controllers\Reportes\ReporteGananciasController::class, 'index'])->name('ganancias');
    
    Route::get('/ganancias/{lote?}', function (Request $request, $lote = null) {
        $unidades = \App\Models\UnidadProduccion::all();
        $lotes = \App\Models\Lote::with('unidadProduccion')->get();
        $loteSeleccionado = $lote ? \App\Models\Lote::with('unidadProduccion')->find($lote) : null;

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // --- Compra Lote ---
        $precioUnitarioPez = $loteSeleccionado->precio_unitario_pez ?? 0;
        $precioCompraLote = $loteSeleccionado && $precioUnitarioPez > 0 ? $loteSeleccionado->cantidad_inicial * $precioUnitarioPez : 0;

        // --- Alimentación ---
        $alimentaciones = $loteSeleccionado ? $loteSeleccionado->alimentaciones()->with('inventarioItem')->get() : collect();
        $totalAlimentacion = $alimentaciones->sum('costo_total');
        $alimentacionDetalle = $alimentaciones->map(function($a) {
            return [
                'fecha' => optional($a->fecha_alimentacion)->format('d/m/Y'),
                'producto' => $a->inventarioItem->nombre ?? 'N/A',
                'cantidad' => $a->cantidad_kg,
                'costo' => $a->costo_total,
            ];
        });

        // --- Mantenimientos ---
        $mantenimientos = $loteSeleccionado ? \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $loteSeleccionado->unidad_produccion_id)->get() : collect();
        $totalMantenimientos = $mantenimientos->sum('costo_mantenimiento');
        $mantenimientoDetalle = $mantenimientos->map(function($m) {
            return [
                'fecha' => optional($m->fecha_mantenimiento)->format('d/m/Y'),
                'tipo' => $m->tipo_mantenimiento,
                'descripcion' => $m->descripcion_trabajo,
                'costo' => $m->costo_mantenimiento,
            ];
        });

        // --- Limpiezas ---
        $limpiezas = $loteSeleccionado ? \App\Models\Limpieza::where('area', $loteSeleccionado->unidadProduccion->nombre)->get() : collect();
        $totalLimpiezas = $limpiezas->sum('costo') ?? 0;
        $limpiezaDetalle = $limpiezas->map(function($l) {
            return [
                'fecha' => optional($l->fecha)->format('d/m/Y'),
                'tipo' => $l->protocoloSanidad->nombre ?? 'N/A',
                'productos' => is_array($l->actividades_ejecutadas) ? implode(', ', array_map(fn($a) => is_array($a) ? ($a['descripcion'] ?? '') : $a, $l->actividades_ejecutadas)) : '',
                'costo' => $l->costo ?? 0,
            ];
        });

        // --- Ventas ---
        $ventasQuery = $loteSeleccionado ? $loteSeleccionado->ventas() : null;
        if ($ventasQuery && $fechaInicio && $fechaFin) {
            $ventasQuery->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        $ventas = $ventasQuery ? $ventasQuery->get() : collect();
        $totalVentas = $ventas->sum('total_venta');
        $ventasDetalle = $ventas->map(function($v) {
            return [
                'fecha' => optional($v->fecha_venta)->format('d/m/Y'),
                'codigo' => $v->codigo_venta,
                'cliente' => $v->cliente,
                'peso_kg' => $v->peso_cosechado_kg,
                'precio_kg' => $v->precio_kg,
                'total' => $v->total_venta,
                'estado' => $v->estado_venta,
            ];
        });

        // --- Biomasa final ---
        $biomasaFinalKg = $loteSeleccionado->biomasa ?? 0;
        $biomasaFinalLb = $biomasaFinalKg * 2.20462;

        // --- Costos totales y ganancia ---
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;

        // --- Gráfica ---
        $grafica = $ventas->isNotEmpty() ? [
            'labels' => $ventas->pluck('fecha_venta')->map(fn($fecha) => optional($fecha)->format('d/m/Y'))->toArray(),
            'data' => $ventas->pluck('total_venta')->toArray(),
        ] : null;

        $desglose = [
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia,
            'precio_compra_lote' => $precioCompraLote,
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
        ];

        return view('reportes.ganancias.reporte', compact(
            'unidades',
            'lotes',
            'loteSeleccionado',
            'desglose',
            'alimentacionDetalle',
            'mantenimientoDetalle',
            'limpiezaDetalle',
            'ventasDetalle',
            'biomasaFinalKg',
            'biomasaFinalLb',
            'grafica',
            'fechaInicio',
            'fechaFin'
        ));
    })->name('ganancias.reporte');
    
    // Panel de Reportes
    Route::get('/panel', function () {
        return view('reportes.panel');
    })->name('panel');
    
    // Reportes de Usuarios (futuro)
    Route::get('/usuarios', function () {
        return view('reportes.usuarios.index');
    })->name('usuarios');
});

Route::get('/compras/insumos', function () {
        $comprasInsumos = \App\Models\EntradaCompra::whereHas('bodega', function ($query) {
            $query->where('nombre', 'like', '%suministro/insumo%');
        })->whereDoesntHave('detalle', function ($query) {
            $query->where('producto', 'like', '%pez%');
        })->get();

        return view('compras.insumos', compact('comprasInsumos'));
    })->name('compras.insumos');

require __DIR__ . '/auth.php';
