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
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            // Campos para la venta
            $table->string('codigo_venta')->nullable()->unique();
            $table->string('cliente')->nullable();
            $table->string('telefono_cliente', 20)->nullable();
            $table->string('email_cliente')->nullable();
            $table->date('fecha_venta')->nullable();
            $table->decimal('precio_kg', 10, 2)->nullable();
            $table->decimal('total_venta', 10, 2)->nullable();
            $table->decimal('tipo_cambio', 8, 4)->nullable();
            $table->decimal('total_usd', 10, 2)->nullable();
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'credito'])->nullable();
            $table->enum('estado_venta', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->text('observaciones_venta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_venta',
                'cliente', 
                'telefono_cliente',
                'email_cliente',
                'fecha_venta',
                'precio_kg',
                'total_venta',
                'tipo_cambio',
                'total_usd',
                'metodo_pago',
                'estado_venta',
                'observaciones_venta'
            ]);
        });
    }
};
