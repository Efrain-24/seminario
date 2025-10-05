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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_lote')->unique();
            $table->string('especie');
            $table->integer('cantidad_inicial');
            $table->integer('cantidad_actual');
            $table->decimal('peso_promedio_inicial', 8, 2)->nullable();
            $table->decimal('talla_promedio_inicial', 8, 2)->nullable();
            $table->date('fecha_inicio');
            $table->unsignedBigInteger('unidad_produccion_id')->nullable();
            $table->enum('estado', ['activo', 'trasladado', 'cosechado', 'inactivo'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
