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
        Schema::table('lotes', function (Blueprint $table) {
            $table->date('fecha_siembra')->nullable()->after('fecha_inicio');
            $table->date('fecha_cosecha')->nullable()->after('fecha_siembra');
            $table->decimal('peso_promedio_actual', 8, 2)->nullable()->after('talla_promedio_inicial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn(['fecha_siembra', 'fecha_cosecha', 'peso_promedio_actual']);
        });
    }
};
