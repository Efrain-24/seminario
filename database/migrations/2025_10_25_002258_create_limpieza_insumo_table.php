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
        Schema::create('limpieza_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limpieza_id')->constrained('limpiezas')->onDelete('cascade');
            $table->foreignId('inventario_item_id')->constrained('inventario_items')->onDelete('cascade');
            $table->decimal('cantidad_planificada', 10, 3)->default(0);
            $table->decimal('cantidad_real', 10, 3);
            $table->decimal('costo_unitario', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->timestamps();
            
            // Índices únicos para evitar duplicados
            $table->unique(['limpieza_id', 'inventario_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limpieza_insumo');
    }
};
