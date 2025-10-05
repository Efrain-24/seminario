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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, supervisor, usuario
            $table->string('display_name'); // Administrador, Supervisor, Usuario
            $table->string('description')->nullable(); // Descripción del rol
            $table->json('permissions')->nullable(); // Permisos del rol
            $table->boolean('is_active')->default(true); // Si el rol está activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
