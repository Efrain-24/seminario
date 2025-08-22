<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_inventario_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventario_items', function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('sku')->nullable()->unique();
            $t->enum('tipo', ['alimento', 'insumo'])->index();
            $t->enum('unidad_base', ['kg', 'lb', 'unidad', 'litro'])->default('kg');
            $t->decimal('stock_minimo', 12, 3)->default(0);
            $t->text('descripcion')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventario_items');
    }
};
