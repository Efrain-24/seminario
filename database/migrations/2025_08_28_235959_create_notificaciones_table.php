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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('info'); // info, warning, error, success
            $table->string('titulo');
            $table->text('mensaje');
            $table->json('datos')->nullable(); // datos adicionales como IDs relacionados
            $table->string('icono')->nullable(); // ícono de Lucide
            $table->string('url')->nullable(); // enlace relacionado
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_vencimiento')->nullable(); // opcional para auto-eliminar
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // null = para todos los usuarios
            $table->timestamps();
            
            $table->index(['user_id', 'leida']);
            $table->index(['tipo', 'leida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
