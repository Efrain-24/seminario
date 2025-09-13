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
        Schema::table('acciones_correctivas', function (Blueprint $table) {
            // Cambiar fecha_detectada por fecha_prevista
            $table->renameColumn('fecha_detectada', 'fecha_prevista');
            
            // Eliminar campos que ya no necesitamos
            $table->dropColumn(['observaciones', 'evidencias']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acciones_correctivas', function (Blueprint $table) {
            $table->renameColumn('fecha_prevista', 'fecha_detectada');
            $table->text('observaciones')->nullable();
            $table->json('evidencias')->nullable();
        });
    }
};
