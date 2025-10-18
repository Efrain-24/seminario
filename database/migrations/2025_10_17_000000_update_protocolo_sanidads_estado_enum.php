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
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            // Cambiar el enum para incluir 'ejecutado'
            // Para MySQL se usa una query directa
            DB::statement("ALTER TABLE protocolo_sanidads MODIFY estado ENUM('vigente', 'obsoleta', 'ejecutado') DEFAULT 'vigente'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocolo_sanidads', function (Blueprint $table) {
            // Revertir al enum anterior
            DB::statement("ALTER TABLE protocolo_sanidads MODIFY estado ENUM('vigente', 'obsoleta') DEFAULT 'vigente'");
        });
    }
};
