<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('cosechas_parciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('fecha');
            $table->unsignedInteger('cantidad_cosechada'); // peces
            $table->decimal('peso_cosechado_kg', 8, 2)->nullable(); // peso total (kg), opcional
            $table->enum('destino', ['venta', 'consumo', 'muestra', 'otro'])->default('venta');
            $table->string('responsable', 120)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('cosechas_parciales');
    }
};
