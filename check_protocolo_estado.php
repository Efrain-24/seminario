<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ver protocolos ejecutados
$result = DB::select("SELECT id, nombre, estado, fecha_ejecucion FROM protocolo_sanidads WHERE estado = 'ejecutado' LIMIT 10");
echo "Protocolos con estado 'ejecutado':\n";
foreach ($result as $row) {
    echo "  - {$row->id}: {$row->nombre} ({$row->estado})\n";
}

// Ver todos los estados únicos
$estados = DB::select("SELECT DISTINCT estado FROM protocolo_sanidads");
echo "\nEstados disponibles:\n";
foreach ($estados as $e) {
    echo "  - {$e->estado}\n";
}

// Ver estructura de columna estado
$estructura = DB::select("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'protocolo_sanidads' AND COLUMN_NAME = 'estado'");
echo "\nTipo de dato de columna estado:\n";
foreach ($estructura as $e) {
    echo "  - {$e->COLUMN_TYPE}\n";
}
