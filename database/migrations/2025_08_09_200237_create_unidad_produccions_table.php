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
        Schema::create('unidad_produccions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->enum('tipo', ['tanque', 'estanque']);
            $table->decimal('capacidad_maxima', 10, 2)->nullable(); // en m³ o litros
            $table->decimal('area', 10, 2)->nullable(); // en m²
            $table->decimal('profundidad', 8, 2)->nullable(); // en metros
            $table->enum('estado', ['activo', 'mantenimiento', 'inactivo'])->default('activo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_construccion')->nullable();
            $table->date('ultimo_mantenimiento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_produccions');
    }
};
