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
        Schema::create('tipo_alimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Nombre del tipo de alimento
            $table->string('marca', 100)->nullable(); // Marca comercial
            $table->enum('categoria', ['concentrado', 'pellet', 'hojuela', 'artesanal', 'vivo', 'suplemento']); // Categoría del alimento
            $table->decimal('proteina', 5, 2)->nullable(); // Porcentaje de proteína
            $table->decimal('grasa', 5, 2)->nullable(); // Porcentaje de grasa
            $table->decimal('fibra', 5, 2)->nullable(); // Porcentaje de fibra
            $table->decimal('humedad', 5, 2)->nullable(); // Porcentaje de humedad
            $table->decimal('ceniza', 5, 2)->nullable(); // Porcentaje de ceniza
            $table->enum('presentacion', ['sacos', 'bolsas', 'granel', 'unidades']); // Presentación del alimento
            $table->decimal('peso_presentacion', 8, 2)->nullable(); // Peso por presentación (kg)
            $table->decimal('costo_por_kg', 10, 2)->nullable(); // Costo por kilogramo
            $table->text('descripcion')->nullable(); // Descripción adicional
            $table->boolean('activo')->default(true); // Estado del tipo de alimento
            $table->timestamps();
            
            // Índices
            $table->index(['categoria', 'activo']);
            $table->index(['nombre', 'marca']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_alimentos');
    }
};
