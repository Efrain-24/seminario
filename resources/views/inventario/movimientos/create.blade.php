{{-- ... campos: Ítem, Bodega, Fecha ... --}}

@if ($tipo !== 'ajuste')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Cantidad</label>
            <input type="number" step="0.0001" min="0.0001" name="cantidad"
                class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                required>
            @error('cantidad')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Unidad</label>
            <select name="unidad"
                class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                required>
                <option value="kg">kg</option>
                <option value="lb">lb</option>
                <option value="unidad">unidad</option>
                <option value="litro">litro</option>
            </select>
            @error('unidad')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>
    </div>
@else
    <div>
        <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Nuevo stock (unidad base)</label>
        <input type="number" step="0.0001" name="nuevo_stock"
            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            required>
        @error('nuevo_stock')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>
@endif

{{-- ⬇️ AQUI: SOLO PARA ENTRADA --}}
@if ($tipo === 'entrada')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote (opcional)</label>
            <input name="lote"
                class="w-full p-2 rounded border dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        </div>
        <div>
            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Fecha de vencimiento (opcional)</label>
            <input type="date" name="fecha_vencimiento"
                class="w-full p-2 rounded border dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        </div>
    </div>
@endif

{{-- luego sigue tu campo Descripción y los botones --}}
<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Descripción</label>
    <input name="descripcion"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    @error('descripcion')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>
