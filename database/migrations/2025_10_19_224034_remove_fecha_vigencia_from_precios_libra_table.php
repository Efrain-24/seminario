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
        Schema::table('precios_libra', function (Blueprint $table) {
            $table->dropColumn('fecha_vigencia');
            $table->dropIndex(['fecha_vigencia', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('precios_libra', function (Blueprint $table) {
            $table->date('fecha_vigencia')->comment('Fecha desde la cual está vigente este precio');
            $table->index(['fecha_vigencia', 'activo']);
        });
    }
};
