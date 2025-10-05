<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_xxxxxx_create_inventario_existencias_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventario_existencias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('inventario_items')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('bodega_id')->constrained('bodegas')->cascadeOnUpdate()->restrictOnDelete();
            $t->decimal('stock_actual', 12, 3)->default(0); // siempre en unidad_base del item
            $t->timestamps();
            $t->unique(['item_id', 'bodega_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventario_existencias');
    }
};
