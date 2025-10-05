<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrada_compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrada_id')->constrained('entrada_compras')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventario_items')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('descripcion')->nullable();
            $table->decimal('cantidad', 15,3);
            $table->string('unidad', 20)->nullable();
            $table->decimal('costo_unitario', 15,4);
            $table->decimal('subtotal', 15,2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrada_compra_detalles');
    }
};
