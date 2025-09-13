<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolo_sanidads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha_implementacion');
            $table->string('responsable');
            $table->json('actividades')->nullable(); // Array de actividades del protocolo
            $table->integer('version')->default(1); // Versión del protocolo
            $table->enum('estado', ['vigente', 'obsoleta'])->default('vigente'); // Estado del protocolo
            $table->unsignedBigInteger('protocolo_base_id')->nullable(); // Referencia al protocolo original
            $table->timestamps();
            
            $table->foreign('protocolo_base_id')->references('id')->on('protocolo_sanidads')->onDelete('set null');
            $table->index(['estado', 'protocolo_base_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolo_sanidads');
    }
};
