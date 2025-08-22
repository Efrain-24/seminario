@props(['cosecha' => null, 'lotes' => collect()])

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Lote</label>
        <select name="lote_id"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                       text-gray-900 dark:text-gray-100 p-2"
            {{ $cosecha ? 'disabled' : '' }} required>
            <option value="">{{ __('Seleccione…') }}</option>
            @foreach ($lotes as $l)
                <option value="{{ $l->id }}" @selected(old('lote_id', optional($cosecha)->lote_id) == $l->id)>
                    {{ $l->nombre ?? ($l->codigo_lote ?? 'Lote #' . $l->id) }}
                    ({{ __('stock') }}: {{ $l->cantidad_actual ?? 0 }})
                </option>
            @endforeach
        </select>
        @if ($cosecha)
            <input type="hidden" name="lote_id" value="{{ $cosecha->lote_id }}">
        @endif
        @error('lote_id')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Fecha</label>
        <input type="date" name="fecha"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('fecha', optional(optional($cosecha)->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            required>
        @error('fecha')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Cantidad cosechada (peces)</label>
        <input type="number" min="1" name="cantidad_cosechada"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('cantidad_cosechada', optional($cosecha)->cantidad_cosechada) }}" required>
        @error('cantidad_cosechada')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Peso total (kg) (opcional)</label>
        <input type="number" step="0.01" min="0" name="peso_cosechado_kg"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('peso_cosechado_kg', optional($cosecha)->peso_cosechado_kg) }}">
        @error('peso_cosechado_kg')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Destino</label>
        <select name="destino"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                       text-gray-900 dark:text-gray-100 p-2"
            required>
            @foreach (['venta', 'consumo', 'muestra', 'otro'] as $opt)
                <option @selected(old('destino', optional($cosecha)->destino) == $opt)>{{ $opt }}</option>
            @endforeach
        </select>
        @error('destino')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Responsable</label>
        <input type="text" name="responsable"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('responsable', optional($cosecha)->responsable) }}">
        @error('responsable')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Observaciones</label>
        <textarea name="observaciones" rows="3"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                         text-gray-900 dark:text-gray-100 p-2">{{ old('observaciones', optional($cosecha)->observaciones) }}</textarea>
        @error('observaciones')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
