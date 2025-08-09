<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProduccionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('/lotes', [ProduccionController::class, 'gestionLotes'])->name('lotes');
    Route::get('/lotes/create', [ProduccionController::class, 'createLote'])->name('lotes.create');
    Route::post('/lotes', [ProduccionController::class, 'storeLote'])->name('lotes.store');
    Route::get('/lotes/{lote}', [ProduccionController::class, 'showLote'])->name('lotes.show');
    
    // Rutas de Unidades
    Route::get('/unidades', [ProduccionController::class, 'gestionUnidades'])->name('unidades');
    Route::get('/unidades/create', [ProduccionController::class, 'createUnidad'])->name('unidades.create');
    Route::post('/unidades', [ProduccionController::class, 'storeUnidad'])->name('unidades.store');
    Route::get('/unidades/{unidad}', [ProduccionController::class, 'showUnidad'])->name('unidades.show');
    
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
    
    // Rutas de mantenimientos
    Route::get('/mantenimientos/{unidad?}', [ProduccionController::class, 'gestionMantenimientos'])->name('mantenimientos');
    Route::get('/mantenimientos/crear/{unidad?}', [ProduccionController::class, 'crearMantenimiento'])->name('mantenimientos.crear');
    Route::post('/mantenimientos', [ProduccionController::class, 'storeMantenimiento'])->name('mantenimientos.store');
    Route::get('/mantenimiento/{mantenimiento}', [ProduccionController::class, 'showMantenimiento'])->name('mantenimiento.show');
    Route::patch('/mantenimiento/{mantenimiento}/iniciar', [ProduccionController::class, 'iniciarMantenimiento'])->name('mantenimiento.iniciar');
    Route::patch('/mantenimiento/{mantenimiento}/completar', [ProduccionController::class, 'completarMantenimiento'])->name('mantenimiento.completar');
    Route::patch('/mantenimiento/{mantenimiento}/cancelar', [ProduccionController::class, 'cancelarMantenimiento'])->name('mantenimiento.cancelar');
});


require __DIR__ . '/auth.php';
