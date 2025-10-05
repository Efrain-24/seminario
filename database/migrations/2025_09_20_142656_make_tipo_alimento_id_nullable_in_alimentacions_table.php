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
        Schema::table('alimentacions', function (Blueprint $table) {
            // Hacer que tipo_alimento_id sea nullable para compatibilidad con inventario
            $table->unsignedBigInteger('tipo_alimento_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alimentacions', function (Blueprint $table) {
            // Revertir tipo_alimento_id a no nullable
            $table->unsignedBigInteger('tipo_alimento_id')->nullable(false)->change();
        });
    }
};
