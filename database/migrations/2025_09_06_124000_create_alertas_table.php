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
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->string('tipo_alerta');
            $table->string('periodo')->nullable();
            $table->text('detalles');
            $table->decimal('peso_actual', 8, 2)->nullable();
            $table->decimal('peso_esperado', 8, 2)->nullable();
            $table->decimal('porcentaje_desviacion', 8, 2)->nullable();
            $table->decimal('tasa_crecimiento', 8, 2)->nullable();
            $table->decimal('consumo_alimento_reciente', 8, 2)->nullable();
            $table->decimal('factor_conversion_alimento', 8, 2)->nullable();
            $table->integer('dias_desviacion')->nullable();
            $table->text('observaciones_alimentacion')->nullable();
            $table->json('historico_pesos')->nullable();
            // Campos de enfermedad
            $table->string('nombre_enfermedad')->nullable();
            $table->integer('cantidad_afectados')->nullable();
            $table->decimal('porcentaje_afectados', 8, 2)->nullable();
            $table->enum('nivel_riesgo', ['alto', 'medio', 'bajo'])->nullable();
            $table->string('estado_tratamiento')->nullable();
            $table->text('descripcion_tratamiento')->nullable();
            $table->date('fecha_deteccion')->nullable();
            $table->date('fecha_inicio_tratamiento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
