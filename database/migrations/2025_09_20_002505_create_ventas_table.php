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
            $table->string('codigo_venta')->unique();
            $table->string('cliente');
            $table->string('telefono_cliente')->nullable();
            $table->string('email_cliente')->nullable();
            $table->date('fecha_venta');
            $table->decimal('cantidad_kg', 10, 2);
            $table->decimal('precio_kg', 10, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('tipo_cambio', 8, 4);
            $table->decimal('total_usd', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'credito']);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('cosecha_parcial_id')->constrained('cosechas_parciales')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha_venta', 'estado']);
            $table->index('cliente');
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
