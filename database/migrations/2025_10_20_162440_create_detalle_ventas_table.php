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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->string('producto_nombre'); // Nombre del producto (ej: "Carpa", "Tilapia")
            $table->string('producto_codigo'); // Código del lote (ej: "CAR-2025-001")
            $table->integer('cantidad_peces'); // Cantidad de peces vendidos
            $table->decimal('peso_libras', 8, 2); // Peso en libras
            $table->decimal('precio_unitario', 8, 2); // Precio por libra
            $table->decimal('subtotal', 10, 2); // peso_libras * precio_unitario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
