<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_xxxxxx_add_inventario_relaciones.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tipo_alimentos', function (Blueprint $t) {
            $t->foreignId('inventario_item_id')->nullable()->after('id')
                ->constrained('inventario_items')->nullOnDelete();
        });
        Schema::table('alimentacions', function (Blueprint $t) {
            $t->foreignId('bodega_id')->nullable()->after('lote_id')
                ->constrained('bodegas')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('tipo_alimentos', function (Blueprint $t) {
            $t->dropConstrainedForeignId('inventario_item_id');
        });
        Schema::table('alimentacions', function (Blueprint $t) {
            $t->dropConstrainedForeignId('bodega_id');
        });
    }
};
