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
        Schema::create('alimentacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade'); // Lote que recibe la alimentación
            $table->foreignId('tipo_alimento_id')->constrained('tipo_alimentos'); // Tipo de alimento utilizado
            $table->foreignId('usuario_id')->constrained('users'); // Usuario responsable de la alimentación
            $table->date('fecha_alimentacion'); // Fecha de la alimentación
            $table->time('hora_alimentacion'); // Hora de la alimentación
            $table->decimal('cantidad_kg', 8, 3); // Cantidad en kilogramos
            $table->decimal('costo_total', 10, 2)->nullable(); // Costo total de esta alimentación
            $table->enum('metodo_alimentacion', ['manual', 'automatico', 'semi_automatico'])->default('manual'); // Método de alimentación
            $table->integer('frecuencia_diaria')->default(1); // Veces que se alimenta al día
            $table->decimal('temperatura_agua', 5, 2)->nullable(); // Temperatura del agua al momento de alimentar
            $table->decimal('ph_agua', 4, 2)->nullable(); // pH del agua
            $table->decimal('oxigeno_disuelto', 5, 2)->nullable(); // Oxígeno disuelto mg/L
            $table->enum('estado_peces', ['normal', 'poco_apetito', 'muy_activos', 'estresados', 'enfermedad'])->nullable(); // Estado observado de los peces
            $table->decimal('porcentaje_consumo', 5, 2)->nullable(); // Porcentaje de alimento consumido (0-100)
            $table->text('observaciones')->nullable(); // Observaciones generales
            $table->json('condiciones_climaticas')->nullable(); // Condiciones climáticas (JSON: temperatura, humedad, lluvia, etc.)
            $table->timestamps();
            
            // Índices
            $table->index(['lote_id', 'fecha_alimentacion']);
            $table->index(['usuario_id', 'fecha_alimentacion']);
            $table->index(['tipo_alimento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alimentacions');
    }
};
