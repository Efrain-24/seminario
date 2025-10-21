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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique(); // Código correlativo
            $table->date('fecha_venta');
            $table->enum('tipo_cliente', ['CF', 'NIT'])->default('CF');
            $table->string('cliente_nombre');
            $table->string('cliente_nit')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('completada');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que hizo la venta
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
