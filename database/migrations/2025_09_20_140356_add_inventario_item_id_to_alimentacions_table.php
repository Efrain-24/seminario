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
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->unsignedBigInteger('inventario_item_id')->nullable()->after('tipo_alimento_id');
            $table->foreign('inventario_item_id')->references('id')->on('inventario_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alimentacions', function (Blueprint $table) {
            $table->dropForeign(['inventario_item_id']);
            $table->dropColumn('inventario_item_id');
        });
    }
};
