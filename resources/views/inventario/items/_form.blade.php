@php($it = $item ?? null)

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Nombre</label>
    <input name="nombre" value="{{ old('nombre', $it->nombre ?? '') }}"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
        required>
    @error('nombre')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">SKU</label>
    <input name="sku" value="{{ old('sku', $it->sku ?? '') }}"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    @error('sku')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tipo</label>
        <select name="tipo"
            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            required>
            @foreach (['alimento', 'insumo'] as $t)
                <option value="{{ $t }}" @selected(old('tipo', $it->tipo ?? '') == $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        @error('tipo')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Unidad base</label>
        <select name="unidad_base"
            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            required>
            @foreach (['kg', 'lb', 'unidad', 'litro'] as $u)
                <option value="{{ $u }}" @selected(old('unidad_base', $it->unidad_base ?? '') == $u)>{{ $u }}</option>
            @endforeach
        </select>
        @error('unidad_base')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Stock mínimo</label>
    <input type="number" step="0.001" min="0" name="stock_minimo"
        value="{{ old('stock_minimo', $it->stock_minimo ?? 0) }}"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    @error('stock_minimo')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Descripción</label>
    <textarea name="descripcion" rows="3"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">{{ old('descripcion', $it->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>
