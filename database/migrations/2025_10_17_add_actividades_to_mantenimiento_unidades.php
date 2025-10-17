<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mantenimiento_unidades', function (Blueprint $table) {
            $table->json('actividades')->nullable()->after('requiere_traslado_peces'); // Array de actividades
            $table->json('actividades_ejecutadas')->nullable()->after('actividades'); // Array de actividades con estado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimiento_unidades', function (Blueprint $table) {
            $table->dropColumn('actividades');
            $table->dropColumn('actividades_ejecutadas');
        });
    }
};
