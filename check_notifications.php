<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Notificacion;

echo "=== REVISIÓN DE NOTIFICACIONES EN BASE DE DATOS ===\n";
echo "Total de notificaciones: " . Notificacion::count() . "\n\n";

if (Notificacion::count() > 0) {
    echo "LISTADO DE NOTIFICACIONES:\n";
    echo str_repeat("-", 80) . "\n";
    
    Notificacion::all()->each(function($notificacion) {
        echo "ID: {$notificacion->id}\n";
        echo "Tipo: {$notificacion->tipo}\n";
        echo "Título: {$notificacion->titulo}\n";
        echo "Mensaje: " . substr($notificacion->mensaje, 0, 100) . "...\n";
        echo "Creada: {$notificacion->created_at}\n";
        echo str_repeat("-", 40) . "\n";
    });
} else {
    echo "✅ No hay notificaciones en la base de datos\n";
}
