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
        Schema::create('seguimiento_acciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accion_correctiva_id')->constrained('acciones_correctivas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('descripcion'); // Descripción del seguimiento
            $table->string('archivo_evidencia')->nullable(); // Ruta del archivo de evidencia
            $table->string('nombre_archivo_original')->nullable(); // Nombre original del archivo
            $table->string('tipo_archivo')->nullable(); // Tipo MIME del archivo
            $table->integer('tamaño_archivo')->nullable(); // Tamaño del archivo en bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimiento_acciones');
    }
};
