@php($b = $bodega ?? null)

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Nombre</label>
    <input name="nombre" value="{{ old('nombre', $b->nombre ?? '') }}"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
        required>
    @error('nombre')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Ubicación</label>
    <input name="ubicacion" value="{{ old('ubicacion', $b->ubicacion ?? '') }}"
        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    @error('ubicacion')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>
