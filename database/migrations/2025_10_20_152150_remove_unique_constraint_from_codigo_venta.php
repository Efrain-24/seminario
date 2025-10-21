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
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            $table->dropUnique(['codigo_venta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            $table->unique('codigo_venta');
        });
    }
};
