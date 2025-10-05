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
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            $table->timestamp('fecha_ejecucion')->nullable()->after('estado');
            $table->text('observaciones_ejecucion')->nullable()->after('fecha_ejecucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            $table->dropColumn(['fecha_ejecucion', 'observaciones_ejecucion']);
        });
    }
};
