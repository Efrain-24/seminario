<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_xxxxxx_create_inventario_movimientos_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventario_movimientos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('inventario_items');
            $t->foreignId('bodega_id')->constrained('bodegas');
            $t->enum('tipo', ['entrada', 'salida', 'ajuste'])->index();
            $t->decimal('cantidad_base', 12, 3); // convertido a unidad_base del item
            $t->string('unidad_origen', 16)->nullable(); // p.ej. lb si vino en libras
            $t->decimal('cantidad_origen', 12, 3)->nullable();
            // referencia al origen (alimentación, compra, etc.)
            $t->nullableMorphs('referencia'); // referencia_type, referencia_id
            $t->date('fecha');
            $t->string('descripcion')->nullable();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
