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
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('unidad_origen_id')->nullable()->constrained('unidad_produccions')->onDelete('set null');
            $table->foreignId('unidad_destino_id')->constrained('unidad_produccions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seguimiento_id')->nullable()->constrained('seguimientos')->onDelete('set null');
            $table->date('fecha_traslado');
            $table->integer('cantidad_trasladada');
            $table->integer('cantidad_perdida')->default(0);
            $table->decimal('peso_promedio_traslado', 8, 2)->nullable();
            $table->enum('motivo_traslado', ['crecimiento', 'sobrepoblacion', 'mejores_condiciones', 'mantenimiento', 'clasificacion', 'otro'])->default('crecimiento');
            $table->enum('estado_traslado', ['planificado', 'en_proceso', 'completado', 'cancelado'])->default('planificado');
            $table->text('observaciones_origen')->nullable();
            $table->text('observaciones_destino')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
