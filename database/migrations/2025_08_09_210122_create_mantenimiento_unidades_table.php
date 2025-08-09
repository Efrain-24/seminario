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
        Schema::create('mantenimiento_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_produccion_id')->constrained('unidad_produccions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_mantenimiento');
            $table->enum('tipo_mantenimiento', ['preventivo', 'correctivo', 'limpieza', 'reparacion', 'inspeccion', 'desinfeccion'])->default('preventivo');
            $table->enum('estado_mantenimiento', ['programado', 'en_proceso', 'completado', 'cancelado'])->default('programado');
            $table->text('descripcion_trabajo');
            $table->text('materiales_utilizados')->nullable();
            $table->decimal('costo_mantenimiento', 10, 2)->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->text('observaciones_antes')->nullable();
            $table->text('observaciones_despues')->nullable();
            $table->date('proxima_revision')->nullable();
            $table->boolean('requiere_vaciado')->default(false);
            $table->boolean('requiere_traslado_peces')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_unidades');
    }
};
