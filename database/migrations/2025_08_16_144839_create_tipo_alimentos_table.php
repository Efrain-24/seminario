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
        Schema::create('tipo_alimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('marca', 100)->nullable();
            $table->enum('categoria', ['concentrado', 'pellet', 'hojuela', 'artesanal', 'vivo', 'suplemento']);
            $table->decimal('proteina', 5, 2)->nullable();
            $table->decimal('grasa', 5, 2)->nullable();
            $table->decimal('fibra', 5, 2)->nullable();
            $table->decimal('humedad', 5, 2)->nullable();
            $table->decimal('ceniza', 5, 2)->nullable();
            $table->enum('presentacion', ['sacos', 'bolsas', 'granel', 'unidades']);
            $table->decimal('peso_presentacion', 8, 2)->nullable();
            $table->decimal('costo_por_kg', 10, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['categoria', 'activo']);
            $table->index(['nombre', 'marca']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_alimentos');
    }
};
