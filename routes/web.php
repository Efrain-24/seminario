<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\TipoAlimentoController;
use App\Http\Controllers\CosechaParcialController;
use App\Http\Controllers\ControlProduccionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta de prueba temporal para tipos de alimento
Route::get('/test-tipos-alimento', function (Request $request) {
    $controller = new TipoAlimentoController();
    return $controller->index($request);
})->middleware('auth')->name('test.tipos.alimento');

Route::get('/', function () {
    return redirect()->route('aplicaciones');
});

// Ruta de prueba de permisos
Route::get('/test-permisos', function () {
    $user = App\Models\User::where('email', 'admin@piscicultura.com')->first();

    if (!$user) {
        return 'Usuario no encontrado';
    }

    $permisos = [
        'alimentacion.view' => $user->hasPermission('alimentacion.view'),
        'alimentacion.create' => $user->hasPermission('alimentacion.create'),
        'alimentacion.edit' => $user->hasPermission('alimentacion.edit'),
        'alimentacion.delete' => $user->hasPermission('alimentacion.delete'),
    ];

    // Simular autenticación para prueba
    \Illuminate\Support\Facades\Auth::login($user);

    return view('test-permisos', compact('user', 'permisos'));
})->name('test.permisos');

// Ruta de prueba de alimentación sin middleware
Route::get('/test-alimentacion-simple', [App\Http\Controllers\AlimentacionController::class, 'index'])->name('test.alimentacion.simple');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/aplicaciones', function () {
    return view('aplicaciones');
})->middleware(['auth', 'verified'])->name('aplicaciones');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para gestión de usuarios (las rutas más específicas van primero)
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

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

Route::middleware('auth')->prefix('produccion')->name('produccion.')->group(function () {
    Route::get('/', [ProduccionController::class, 'index'])->name('index');

    // Rutas de Lotes
    Route::get('/lotes', [ProduccionController::class, 'gestionLotes'])->name('lotes')->middleware('permission:ver_lotes');
    Route::get('/lotes/create', [ProduccionController::class, 'createLote'])->name('lotes.create')->middleware('permission:crear_lotes');
    Route::post('/lotes', [ProduccionController::class, 'storeLote'])->name('lotes.store')->middleware('permission:crear_lotes');
    Route::get('/lotes/{lote}', [ProduccionController::class, 'showLote'])->name('lotes.show')->middleware('permission:ver_lotes');

    // Rutas de Unidades
    Route::get('/unidades', [ProduccionController::class, 'gestionUnidades'])->name('unidades')->middleware('permission:ver_unidades');
    Route::get('/unidades/create', [ProduccionController::class, 'createUnidad'])->name('unidades.create')->middleware('permission:crear_unidades');
    Route::post('/unidades', [ProduccionController::class, 'storeUnidad'])->name('unidades.store')->middleware('permission:crear_unidades');
    Route::get('/unidades/{unidad}', [ProduccionController::class, 'showUnidad'])->name('unidades.show')->middleware('permission:ver_unidades');
    Route::get('/unidades/{unidad}/edit', [ProduccionController::class, 'editUnidad'])->name('unidades.edit')->middleware('permission:editar_unidades');
    Route::put('/unidades/{unidad}', [ProduccionController::class, 'updateUnidad'])->name('unidades.update')->middleware('permission:editar_unidades');
    Route::delete('/unidades/{unidad}', [ProduccionController::class, 'destroyUnidad'])->name('unidades.destroy')->middleware('permission:eliminar_unidades');
    Route::get('/unidades/generate-code/{tipo}', [ProduccionController::class, 'generateUnidadCode'])->name('unidades.generate-code');

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

    // ✅ Registro de Cosechas Parciales
    Route::resource('cosechas', CosechaParcialController::class)
        ->parameters(['cosechas' => 'cosecha'])   // para usar {cosecha} en vez de {cosechas}
        ->names('cosechas');
});

// Rutas de Alimentación
Route::middleware('auth')->prefix('alimentacion')->name('alimentacion.')->group(function () {
    Route::get('/', [App\Http\Controllers\AlimentacionController::class, 'index'])->name('index')->middleware('permission:alimentacion.view');
    Route::get('/create', [App\Http\Controllers\AlimentacionController::class, 'create'])->name('create')->middleware('permission:alimentacion.create');
    Route::post('/', [App\Http\Controllers\AlimentacionController::class, 'store'])->name('store')->middleware('permission:alimentacion.create');
    Route::get('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'show'])->name('show')->middleware('permission:alimentacion.view');
    Route::get('/{alimentacion}/edit', [App\Http\Controllers\AlimentacionController::class, 'edit'])->name('edit')->middleware('permission:alimentacion.edit');
    Route::put('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'update'])->name('update')->middleware('permission:alimentacion.edit');
    Route::delete('/{alimentacion}', [App\Http\Controllers\AlimentacionController::class, 'destroy'])->name('destroy')->middleware('permission:alimentacion.delete');

    // Rutas para tipos de alimento
    Route::get('/tipos-alimento', [TipoAlimentoController::class, 'index'])->name('tipos-alimento.index');
    Route::get('/tipos-alimento/create', [TipoAlimentoController::class, 'create'])->name('tipos-alimento.create')->middleware('permission:alimentacion.create');
    Route::post('/tipos-alimento', [TipoAlimentoController::class, 'store'])->name('tipos-alimento.store')->middleware('permission:alimentacion.create');
    Route::get('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'show'])->name('tipos-alimento.show')->middleware('permission:alimentacion.view');
    Route::get('/tipos-alimento/{tipoAlimento}/edit', [TipoAlimentoController::class, 'edit'])->name('tipos-alimento.edit')->middleware('permission:alimentacion.edit');
    Route::put('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'update'])->name('tipos-alimento.update')->middleware('permission:alimentacion.edit');
    Route::delete('/tipos-alimento/{tipoAlimento}', [TipoAlimentoController::class, 'destroy'])->name('tipos-alimento.destroy')->middleware('permission:alimentacion.delete');
    Route::patch('/tipos-alimento/{tipoAlimento}/toggle', [TipoAlimentoController::class, 'toggle'])->name('tipos-alimento.toggle')->middleware('permission:alimentacion.edit');
});

require __DIR__ . '/auth.php';
