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
        Schema::table('unidad_produccions', function (Blueprint $table) {
            // Cambiar el enum de tipo para incluir tipos generales de acuicultura
            $table->dropColumn('tipo');
        });

        Schema::table('unidad_produccions', function (Blueprint $table) {
            $table->enum('tipo', [
                'tanque',
                'estanque',
                'jaula',
                'sistema_especializado'
            ])->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad_produccions', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });

        Schema::table('unidad_produccions', function (Blueprint $table) {
            $table->enum('tipo', ['tanque', 'estanque'])->after('nombre');
        });
    }
};
