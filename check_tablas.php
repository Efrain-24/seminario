<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ver todas las tablas
$tablas = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()");
echo "Tablas disponibles:\n";
foreach ($tablas as $tabla) {
    echo "  - {$tabla->TABLE_NAME}\n";
}
