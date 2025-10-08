<?php


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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    Route::put('password/change', [PasswordChangeController::class, 'update'])->name('password.update');
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
    Route::get('/ganancias', function () {
        $unidades = \App\Models\UnidadProduccion::all();
        $lotes = \App\Models\Lote::with(['unidadProduccion'])->orderBy('created_at', 'desc')->get();
        return view('reportes.ganancias.index', compact('unidades', 'lotes'));
    })->name('ganancias');
    
    Route::get('/ganancias/{lote}', function (\App\Models\Lote $lote) {
        // Cargar relaciones necesarias
        $lote->load(['alimentaciones.tipoAlimento', 'ventas', 'unidadProduccion']);
        
        // Calcular costos detallados
        $totalAlimentacion = $lote->alimentaciones->sum('costo_total');
        
        // Obtener mantenimientos de la unidad de producción (filtrar por fechas relevantes del lote si es necesario)
        $mantenimientos = collect();
        if ($lote->unidadProduccion) {
            $mantenimientos = \App\Models\MantenimientoUnidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
                ->where('estado_mantenimiento', 'completado')
                ->get();
        }
        $totalMantenimientos = $mantenimientos->sum('costo_mantenimiento') ?? 0;
        
        // Obtener limpiezas (filtrar por fechas relevantes del lote si es necesario)
        $limpiezas = collect();
        // Nota: Las limpiezas pueden no estar directamente relacionadas con lotes
        $totalLimpiezas = 0; // Por ahora, dejar en 0 hasta definir la relación correcta
        
        $precioCompraLote = $lote->precio_compra ?? 0;
        
        $totalCostos = $precioCompraLote + $totalAlimentacion + $totalMantenimientos + $totalLimpiezas;
        $totalVentas = $lote->ventas->sum('total_venta');
        $gananciaReal = $totalVentas - $totalCostos;
        $margenGanancia = $totalVentas > 0 ? ($gananciaReal / $totalVentas) * 100 : 0;
        
        // Preparar desglose financiero
        $desglose = [
            'precio_compra_lote' => $precioCompraLote,
            'total_alimentacion' => $totalAlimentacion,
            'total_mantenimientos' => $totalMantenimientos,
            'total_limpiezas' => $totalLimpiezas,
            'total_costos' => $totalCostos,
            'total_ventas' => $totalVentas,
            'ganancia_real' => $gananciaReal,
            'margen_ganancia' => $margenGanancia
        ];
        
        // Preparar detalles para las tablas
        $alimentacionDetalle = $lote->alimentaciones->map(function ($alimentacion) {
            return [
                'fecha' => $alimentacion->fecha_alimentacion ? $alimentacion->fecha_alimentacion->format('d/m/Y') : 'N/A',
                'producto' => $alimentacion->tipoAlimento->nombre ?? 'N/A',
                'cantidad' => $alimentacion->cantidad_kg ?? 0,
                'costo' => $alimentacion->costo_total ?? 0
            ];
        });
        
        $mantenimientoDetalle = $mantenimientos->map(function ($mantenimiento) {
            return [
                'fecha' => $mantenimiento->fecha_mantenimiento ? $mantenimiento->fecha_mantenimiento->format('d/m/Y') : 'N/A',
                'tipo' => $mantenimiento->tipo_mantenimiento ?? 'N/A',
                'descripcion' => $mantenimiento->descripcion_trabajo ?? 'N/A',
                'costo' => $mantenimiento->costo_mantenimiento ?? 0
            ];
        });
        
        $limpiezaDetalle = $limpiezas->map(function ($limpieza) {
            return [
                'fecha' => $limpieza->fecha ? $limpieza->fecha->format('d/m/Y') : 'N/A',
                'tipo' => 'Limpieza',
                'productos' => $limpieza->observaciones ?? 'N/A',
                'costo' => 0 // Las limpiezas pueden no tener costo directo
            ];
        });
        
        $ventasDetalle = $lote->ventas->map(function ($venta) {
            return [
                'fecha' => $venta->fecha_venta,
                'codigo' => $venta->codigo_venta ?? 'N/A',
                'cliente' => $venta->cliente ?? 'N/A',
                'peso_kg' => $venta->cantidad_vendida ?? 0,
                'precio_kg' => $venta->precio_unitario ?? 0,
                'total' => $venta->total_venta ?? 0,
                'estado' => $venta->estado ?? 'completada'
            ];
        });
        
        return view('reportes.ganancias.reporte', compact(
            'lote', 
            'desglose', 
            'alimentacionDetalle', 
            'mantenimientoDetalle', 
            'limpiezaDetalle', 
            'ventasDetalle'
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

require __DIR__ . '/auth.php';
