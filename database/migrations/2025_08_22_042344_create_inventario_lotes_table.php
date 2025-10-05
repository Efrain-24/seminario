<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventario_lotes', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('item_id');
            $t->unsignedBigInteger('bodega_id');
            $t->string('lote')->nullable();                    // código/lote
            $t->date('fecha_ingreso')->nullable();
            $t->date('fecha_vencimiento')->nullable();         // si aplica (insumos/alimento)
            $t->decimal('stock_lote', 14, 3)->default(0);      // en unidad_base del ítem
            $t->timestamps();

            $t->index(['item_id', 'bodega_id']);
            $t->index('fecha_vencimiento');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventario_lotes');
    }
};
