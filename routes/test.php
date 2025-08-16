<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/test-permisos', function () {
    $user = User::where('email', 'admin@piscicultura.com')->first();
    
    if (!$user) {
        return 'Usuario no encontrado';
    }
    
    $permisos = [
        'alimentacion.view' => $user->hasPermission('alimentacion.view'),
        'alimentacion.create' => $user->hasPermission('alimentacion.create'),
        'alimentacion.edit' => $user->hasPermission('alimentacion.edit'),
        'alimentacion.delete' => $user->hasPermission('alimentacion.delete'),
    ];
    
    return view('test-permisos', compact('user', 'permisos'));
})->name('test.permisos');
