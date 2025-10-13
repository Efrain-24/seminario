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
        if (!Schema::hasTable('mantenimiento_insumos')) {
            Schema::create('mantenimiento_insumos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mantenimiento_unidad_id')->constrained('mantenimiento_unidades')->onDelete('cascade');
                $table->foreignId('inventario_item_id')->constrained('inventario_items')->onDelete('cascade');
                $table->decimal('cantidad_utilizada', 10, 3);
                $table->decimal('costo_unitario', 10, 2)->default(0);
                $table->decimal('costo_total', 10, 2)->default(0);
                $table->text('observaciones')->nullable();
                $table->timestamps();

                // Índices
                $table->index(['mantenimiento_unidad_id', 'inventario_item_id'], 'mant_insumos_idx');
            });
        } else {
            // Si la tabla existe, agregar las columnas faltantes
            Schema::table('mantenimiento_insumos', function (Blueprint $table) {
                if (!Schema::hasColumn('mantenimiento_insumos', 'mantenimiento_unidad_id')) {
                    $table->foreignId('mantenimiento_unidad_id')->constrained('mantenimiento_unidades')->onDelete('cascade');
                }
                if (!Schema::hasColumn('mantenimiento_insumos', 'inventario_item_id')) {
                    $table->foreignId('inventario_item_id')->constrained('inventario_items')->onDelete('cascade');
                }
                if (!Schema::hasColumn('mantenimiento_insumos', 'cantidad_utilizada')) {
                    $table->decimal('cantidad_utilizada', 10, 3);
                }
                if (!Schema::hasColumn('mantenimiento_insumos', 'costo_unitario')) {
                    $table->decimal('costo_unitario', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('mantenimiento_insumos', 'costo_total')) {
                    $table->decimal('costo_total', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('mantenimiento_insumos', 'observaciones')) {
                    $table->text('observaciones')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_insumos');
    }
};
