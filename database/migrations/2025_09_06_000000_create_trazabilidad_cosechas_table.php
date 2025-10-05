<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para crear la tabla de trazabilidad de cosechas
     */
    public function up()
    {
        Schema::create('trazabilidad_cosechas', function (Blueprint $table) {
            // Campos de identificación
            $table->id();
            $table->foreignId('lote_id')
                  ->constrained('lotes')
                  ->onDelete('cascade')
                  ->comment('Referencia al lote cosechado');
            
            // Información básica de la cosecha
            $table->datetime('fecha_cosecha')
                  ->comment('Fecha y hora en que se realizó la cosecha');
            $table->enum('tipo_cosecha', ['parcial', 'total'])
                  ->comment('Indica si es una cosecha parcial o total del lote');
            
            // Datos de peso y cantidad
            $table->decimal('peso_bruto', 10, 2)
                  ->comment('Peso total incluyendo empaques y hielo');
            $table->decimal('peso_neto', 10, 2)
                  ->comment('Peso neto del producto cosechado');
            $table->integer('unidades')
                  ->nullable()
                  ->comment('Número de unidades cuando aplique');
            
            // Información de costos
            $table->decimal('costo_mano_obra', 10, 2)
                  ->default(0)
                  ->comment('Costo del personal involucrado en la cosecha');
            $table->decimal('costo_insumos', 10, 2)
                  ->default(0)
                  ->comment('Costo de materiales como hielo, empaques, etc.');
            $table->decimal('costo_operativo', 10, 2)
                  ->default(0)
                  ->comment('Otros costos operativos');
            $table->decimal('costo_total', 10, 2)
                  ->comment('Suma total de todos los costos');
            
            // Información de destino
            $table->enum('destino_tipo', ['cliente_final', 'bodega', 'mercado_local', 'exportacion'])
                  ->comment('Categoría del destino de la cosecha');
            $table->string('destino_detalle')
                  ->comment('Descripción específica del destino');
            
            // Campos adicionales
            $table->text('notas')
                  ->nullable()
                  ->comment('Observaciones adicionales de la cosecha');
            $table->timestamps();
            
            // Índices para optimizar las consultas
            $table->index('fecha_cosecha', 'idx_fecha_cosecha');
            $table->index(['lote_id', 'fecha_cosecha'], 'idx_lote_fecha');
        });
    }

    /**
     * Revierte la migración eliminando la tabla
     */
    public function down()
    {
        Schema::dropIfExists('trazabilidad_cosechas');
    }
};
