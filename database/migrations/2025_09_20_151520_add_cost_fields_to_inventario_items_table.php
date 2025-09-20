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
        Schema::table('inventario_items', function (Blueprint $table) {
            // Agregar campos de costo
            $table->decimal('costo_unitario', 12, 2)->nullable()->after('stock_minimo')->comment('Costo por unidad base (kg, litro, etc.)');
            $table->decimal('precio_promedio', 12, 2)->nullable()->after('costo_unitario')->comment('Precio promedio ponderado');
            $table->string('moneda', 3)->default('GTQ')->after('precio_promedio')->comment('Moneda del costo');
            $table->date('fecha_ultimo_costo')->nullable()->after('moneda')->comment('Fecha del último costo registrado');
            
            // Campos opcionales para control de costos más detallado
            $table->decimal('costo_minimo', 12, 2)->nullable()->after('fecha_ultimo_costo')->comment('Costo mínimo histórico');
            $table->decimal('costo_maximo', 12, 2)->nullable()->after('costo_minimo')->comment('Costo máximo histórico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_items', function (Blueprint $table) {
            $table->dropColumn([
                'costo_unitario',
                'precio_promedio', 
                'moneda',
                'fecha_ultimo_costo',
                'costo_minimo',
                'costo_maximo'
            ]);
        });
    }
};
