<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any NULL actividades to empty array
        DB::table('mantenimiento_unidades')
            ->whereNull('actividades')
            ->update(['actividades' => json_encode([])]);
        
        // Update any NULL actividades_ejecutadas to empty array
        DB::table('mantenimiento_unidades')
            ->whereNull('actividades_ejecutadas')
            ->update(['actividades_ejecutadas' => json_encode([])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NULL if needed
        DB::table('mantenimiento_unidades')
            ->where('actividades', json_encode([]))
            ->update(['actividades' => null]);
        
        DB::table('mantenimiento_unidades')
            ->where('actividades_ejecutadas', json_encode([]))
            ->update(['actividades_ejecutadas' => null]);
    }
};
