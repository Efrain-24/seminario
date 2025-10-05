<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('acciones_correctivas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo'); // título breve de la acción
            $table->text('descripcion'); // detalle de la acción correctiva
            $table->foreignId('user_id') // responsable
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->date('fecha_detectada'); // cuándo se detectó el problema
            $table->date('fecha_limite')->nullable(); // plazo para corregir
            $table->enum('estado', ['pendiente','en_progreso','completada','cancelada'])
                  ->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->json('evidencias')->nullable(); // archivos de evidencia
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_correctivas');
    }
};
