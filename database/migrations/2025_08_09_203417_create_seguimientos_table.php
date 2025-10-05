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
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_seguimiento');
            $table->integer('cantidad_actual')->nullable();
            $table->integer('mortalidad')->default(0);
            $table->decimal('peso_promedio', 8, 2)->nullable();
            $table->decimal('talla_promedio', 8, 2)->nullable();
            $table->decimal('temperatura_agua', 5, 2)->nullable();
            $table->decimal('ph_agua', 4, 2)->nullable();
            $table->decimal('oxigeno_disuelto', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('tipo_seguimiento', ['rutinario', 'muestreo', 'mortalidad', 'traslado'])->default('rutinario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
