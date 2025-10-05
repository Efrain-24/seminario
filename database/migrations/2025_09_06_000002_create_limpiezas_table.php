<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('limpiezas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('area');
            $table->string('responsable');
            $table->foreignId('protocolo_sanidad_id')->constrained('protocolo_sanidads')->onDelete('cascade');
            $table->json('actividades_ejecutadas')->nullable(); // Array de actividades con estado ejecutado/no ejecutado y comentarios
            $table->text('observaciones')->nullable(); // Observaciones generales
            $table->enum('estado', ['no_ejecutado', 'en_progreso', 'completado'])->default('no_ejecutado'); // Estado de la limpieza
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limpiezas');
    }
};
