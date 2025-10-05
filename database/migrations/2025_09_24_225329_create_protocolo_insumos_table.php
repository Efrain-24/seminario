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
        Schema::create('protocolo_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocolo_sanidad_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventario_item_id')->constrained()->onDelete('cascade');
            $table->decimal('cantidad_necesaria', 10, 3)->default(0); // Cantidad que se necesita
            $table->string('unidad', 20); // kg, litro, unidad, etc.
            $table->boolean('es_obligatorio')->default(true); // Si es obligatorio o opcional
            $table->text('notas')->nullable(); // Notas sobre el uso del insumo
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['protocolo_sanidad_id', 'inventario_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocolo_insumos');
    }
};
