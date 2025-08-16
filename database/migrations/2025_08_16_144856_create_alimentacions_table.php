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
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('tipo_alimento_id')->constrained('tipo_alimentos');
            $table->foreignId('usuario_id')->constrained('users');
            $table->date('fecha_alimentacion');
            $table->time('hora_alimentacion');
            $table->decimal('cantidad_kg', 8, 3);
            $table->decimal('costo_total', 10, 2)->nullable();
            $table->enum('metodo_alimentacion', ['manual', 'automatico', 'semi_automatico'])->default('manual');
            $table->integer('frecuencia_diaria')->default(1);
            $table->decimal('temperatura_agua', 5, 2)->nullable();
            $table->decimal('ph_agua', 4, 2)->nullable();
            $table->decimal('oxigeno_disuelto', 5, 2)->nullable();
            $table->enum('estado_peces', ['normal', 'poco_apetito', 'muy_activos', 'estresados', 'enfermedad'])->nullable();
            $table->decimal('porcentaje_consumo', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->json('condiciones_climaticas')->nullable();
            $table->timestamps();
            
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
