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
        Schema::create('precios_libra', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio', 8, 2)->comment('Precio por libra en quetzales');
            $table->date('fecha_vigencia')->comment('Fecha desde la cual está vigente este precio');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registró este precio');
            $table->boolean('activo')->default(true)->comment('Si este precio está activo o no');
            $table->text('observaciones')->nullable()->comment('Observaciones sobre el cambio de precio');
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['fecha_vigencia', 'activo']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios_libra');
    }
};
