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
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            $table->enum('tipo_cliente', ['CF', 'NIT'])->default('CF')->after('cliente');
            $table->string('cliente_nit', 20)->nullable()->after('tipo_cliente');
            $table->string('cliente_nombre')->nullable()->after('cliente_nit');
            $table->string('nombre_cliente')->nullable()->after('cliente_nombre');
            $table->string('direccion_cliente')->nullable()->after('nombre_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cosechas_parciales', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_cliente',
                'cliente_nit',
                'cliente_nombre',
                'nombre_cliente',
                'direccion_cliente'
            ]);
        });
    }
};
