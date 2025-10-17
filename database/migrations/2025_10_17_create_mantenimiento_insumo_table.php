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
        if (!Schema::hasTable('mantenimiento_insumo')) {
            Schema::create('mantenimiento_insumo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mantenimiento_unidad_id')->constrained('mantenimiento_unidades')->onDelete('cascade');
                $table->foreignId('inventario_item_id')->constrained('inventario_items')->onDelete('cascade');
                $table->integer('cantidad')->default(1);
                $table->decimal('costo_unitario', 10, 2)->nullable();
                $table->decimal('costo_total', 10, 2)->nullable();
                $table->timestamps();
                
                $table->unique(['mantenimiento_unidad_id', 'inventario_item_id'], 'mant_insumo_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_insumo');
    }
};
