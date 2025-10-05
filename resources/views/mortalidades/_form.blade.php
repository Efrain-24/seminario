@props(['mortalidad' => null, 'lotes' => collect(), 'unidades' => collect()])


<div class="grid md:grid-cols-2 gap-4">

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Tanque/Unidad</label>
        <select name="unidad_produccion_id" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2" required>
            <option value="">Seleccione…</option>
            @foreach ($unidades as $u)
                <option value="{{ $u->id }}" @selected(old('unidad_produccion_id', optional($mortalidad)->unidad_produccion_id) == $u->id)>
                    {{ $u->nombre ?? $u->codigo }} ({{ $u->tipo }})
                </option>
            @endforeach
        </select>
        @error('unidad_produccion_id')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Lote</label>
        <select name="lote_id"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            {{ $mortalidad ? 'disabled' : '' }} required>
            <option value="">Seleccione…</option>
            @foreach ($lotes as $l)
                <option value="{{ $l->id }}" data-unidad="{{ $l->unidad_produccion_id }}" @selected(old('lote_id', optional($mortalidad)->lote_id) == $l->id)>
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
        <select name="causa_select" id="causa_select" onchange="mostrarOtroCausa(this)"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            <option value="">Seleccione…</option>
            <option value="Manejo inadecuado" @selected((old('causa', optional($mortalidad)->causa) == 'Manejo inadecuado'))>Manejo inadecuado</option>
            <option value="Baja oxigenación" @selected((old('causa', optional($mortalidad)->causa) == 'Baja oxigenación'))>Baja oxigenación</option>
            <option value="Enfermedad" @selected((old('causa', optional($mortalidad)->causa) == 'Enfermedad'))>Enfermedad</option>
            <option value="Predación" @selected((old('causa', optional($mortalidad)->causa) == 'Predación'))>Predación</option>
            <option value="Condiciones ambientales" @selected((old('causa', optional($mortalidad)->causa) == 'Condiciones ambientales'))>Condiciones ambientales</option>
            <option value="Otro" @selected((old('causa', optional($mortalidad)->causa) != null && !in_array(old('causa', optional($mortalidad)->causa), ['Manejo inadecuado','Baja oxigenación','Enfermedad','Predación','Condiciones ambientales'])))>Otro</option>
        </select>
        <input type="text" name="causa" id="causa_otro" placeholder="Especifique otra causa" style="display:none; margin-top:0.5rem;"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
            value="{{ (old('causa', optional($mortalidad)->causa) != null && !in_array(old('causa', optional($mortalidad)->causa), ['Manejo inadecuado','Baja oxigenación','Enfermedad','Predación','Condiciones ambientales']) ? old('causa', optional($mortalidad)->causa) : '') }}">
        @error('causa')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        <script>
        function mostrarOtroCausa(sel) {
            var otro = document.getElementById('causa_otro');
            if(sel.value === 'Otro') {
                otro.style.display = '';
                otro.required = true;
            } else {
                otro.style.display = 'none';
                otro.required = false;
                if(sel.value && sel.value !== 'Otro') {
                    otro.value = '';
                }
            }
        }
        // Mostrar el campo si ya estaba seleccionado 'Otro' al recargar
        document.addEventListener('DOMContentLoaded', function() {
            var sel = document.getElementById('causa_select');
            if(sel.value === 'Otro') mostrarOtroCausa(sel);
        });
        document.addEventListener('DOMContentLoaded', function() {
            var sel = document.getElementById('causa_select');
            if(sel.value === 'Otro') mostrarOtroCausa(sel);
        });
        </script>
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
