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
        // Renombrar tabla ventas existente
        Schema::rename('ventas', 'ventas_old');
        
        // Crear nueva tabla ventas (encabezado)
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta', 12)->unique()->comment('Formato: 2025000001');
            $table->year('año')->comment('Año de la venta para correlativo');
            $table->integer('correlativo')->comment('Número correlativo del año');
            $table->date('fecha_venta');
            $table->time('hora_venta');
            
            // Cliente
            $table->enum('tipo_cliente', ['CF', 'NIT'])->default('NIT');
            $table->string('cliente_nombre')->comment('Nombre del cliente');
            $table->string('cliente_nit')->nullable()->comment('NIT del cliente si aplica');
            
            // Totales
            $table->decimal('subtotal', 12, 2)->comment('Subtotal antes de impuestos');
            $table->decimal('impuestos', 12, 2)->default(0)->comment('Impuestos aplicados');
            $table->decimal('total', 12, 2)->comment('Total final de la venta');
            
            // Información adicional
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'credito'])->default('efectivo');
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('completada');
            $table->text('observaciones')->nullable();
            
            // Usuario que registró la venta
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registró la venta');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['año', 'correlativo']);
            $table->index(['fecha_venta', 'estado']);
            $table->index('cliente_nit');
            $table->index('numero_venta');
        });
        
        // Crear tabla detalle_ventas
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('lote_id')->constrained('lotes')->comment('Lote del producto vendido');
            
            // Detalles del producto
            $table->string('producto_nombre')->comment('Nombre del producto (ej: Tilapia, Trucha)');
            $table->string('lote_codigo')->comment('Código del lote');
            $table->integer('cantidad_peces')->comment('Cantidad de peces vendidos');
            $table->decimal('peso_libras', 8, 2)->comment('Peso total en libras');
            $table->decimal('precio_libra', 8, 2)->comment('Precio por libra al momento de la venta');
            $table->decimal('subtotal', 10, 2)->comment('Subtotal de esta línea');
            
            $table->timestamps();
            
            // Índices
            $table->index('venta_id');
            $table->index('lote_id');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
        Schema::rename('ventas_old', 'ventas');
    }
};
