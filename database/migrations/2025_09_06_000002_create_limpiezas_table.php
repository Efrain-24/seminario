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
            $table->text('descripcion')->nullable();
            $table->string('responsable');
            $table->foreignId('protocolo_sanidad_id')->constrained('protocolo_sanidads')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limpiezas');
    }
};
