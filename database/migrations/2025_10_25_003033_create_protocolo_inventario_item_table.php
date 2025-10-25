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
        Schema::create('protocolo_inventario_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('protocolo_sanidad_id');
            $table->unsignedBigInteger('inventario_item_id');
            $table->decimal('cantidad', 10, 3)->default(0);
            $table->boolean('es_obligatorio')->default(true);
            $table->timestamps();

            $table->foreign('protocolo_sanidad_id')->references('id')->on('protocolo_sanidads')->onDelete('cascade');
            $table->foreign('inventario_item_id')->references('id')->on('inventario_items')->onDelete('cascade');
            
            $table->unique(['protocolo_sanidad_id', 'inventario_item_id'], 'protocolo_inventario_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocolo_inventario_item');
    }
};
