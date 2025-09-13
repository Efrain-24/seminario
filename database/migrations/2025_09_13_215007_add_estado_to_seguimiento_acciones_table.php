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
        Schema::table('seguimiento_acciones', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'eliminado'])->default('activo')->after('tamaño_archivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguimiento_acciones', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
