@props(['mortalidad' => null, 'lotes' => collect()])


<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Lote</label>
        <select name="lote_id"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            {{ $mortalidad ? 'disabled' : '' }} required>
            <option value="">Seleccione…</option>
            @foreach ($lotes as $l)
                <option value="{{ $l->id }}" @selected(old('lote_id', optional($mortalidad)->lote_id) == $l->id)>
                    {{ $l->nombre ?? $l->codigo_lote }} (stock: {{ $l->cantidad_actual ?? 0 }})
                </option>
            @endforeach
        </select>
        @if ($mortalidad)
            <input type="hidden" name="lote_id" value="{{ $mortalidad->lote_id }}">
        @endif
        @error('lote_id')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>


    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Fecha</label>
        <input type="date" name="fecha"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('fecha', optional(optional($mortalidad)->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            required>
        @error('fecha')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>


    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Cantidad (peces)</label>
        <input type="number" min="1" name="cantidad"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('cantidad', optional($mortalidad)->cantidad) }}" required>
        @error('cantidad')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>


    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Causa probable (opcional)</label>
        <input type="text" name="causa"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('causa', optional($mortalidad)->causa) }}">
        @error('causa')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>


    <div class="md:col-span-2">
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Observaciones</label>
        <textarea name="observaciones" rows="3"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">{{ old('observaciones', optional($mortalidad)->observaciones) }}</textarea>
        @error('observaciones')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
