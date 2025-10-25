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
        Schema::table('limpiezas', function (Blueprint $table) {
            $table->decimal('costo_total', 10, 2)->nullable()->after('observaciones');
            $table->json('insumos_consumidos')->nullable()->after('costo_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('limpiezas', function (Blueprint $table) {
            $table->dropColumn(['costo_total', 'insumos_consumidos']);
        });
    }
};
