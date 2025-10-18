<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ver TODOS los protocolos
$protocolos = DB::select("SELECT id, nombre, estado, fecha_ejecucion, unidad_produccion_id FROM protocolo_sanidads LIMIT 20");
echo "Todos los protocolos:\n";
if (empty($protocolos)) {
    echo "  (No hay protocolos)\n";
} else {
    foreach ($protocolos as $p) {
        echo "  ID:{$p->id} | {$p->nombre} | Estado: {$p->estado} | Fecha ejecución: {$p->fecha_ejecucion} | Unidad: {$p->unidad_produccion_id}\n";
    }
}

// Contar
$count = DB::select("SELECT COUNT(*) as total FROM protocolo_sanidads");
echo "\nTotal de protocolos: " . $count[0]->total . "\n";
