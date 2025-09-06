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
    Schema::create('enfermedades', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->date('fecha');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfermedads');
    }
};
