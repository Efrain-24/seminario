<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Lote: {{ $lote->codigo_lote }} (ID: {{ $lote->id }})
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-10">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Editar Lote <span class="text-sm font-normal text-gray-500 dark:text-gray-400">ID: {{ $lote->id }}</span></h2>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
            <p class="font-semibold mb-2">Corrige los siguientes errores:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('produccion.lotes.update', $lote) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block mb-2 font-semibold">ID del Lote</label>
                <input type="text" value="{{ $lote->id }}" readonly class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" />
            </div>
            <div>
                <label class="block mb-2 font-semibold">Especie</label>
                <input type="text" name="especie" value="{{ old('especie', $lote->especie) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">Cantidad Inicial</label>
                <input type="number" name="cantidad_inicial" value="{{ old('cantidad_inicial', $lote->cantidad_inicial) }}" class="w-full border rounded px-3 py-2" min="1">
                {{-- Mantener cantidad original si el usuario la deja vacía --}}
                <input type="hidden" name="cantidad_inicial_backup" value="{{ $lote->cantidad_inicial }}">
            </div>
            <div>
                <label class="block mb-2 font-semibold">Peso Promedio Inicial (kg)</label>
                <input type="number" step="0.001" name="peso_promedio_inicial" value="{{ old('peso_promedio_inicial', $lote->peso_promedio_inicial ? number_format($lote->peso_promedio_inicial, 3, '.', '') : '') }}" class="w-full border rounded px-3 py-2" placeholder="0.011" min="0.011" max="0.99">
                <p class="text-xs text-gray-500 mt-1">Rango permitido: 0.011 a 0.99 kg (no enteros).</p>
                @error('peso_promedio_inicial')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block mb-2 font-semibold">Talla Promedio Inicial (cm)</label>
                <input type="number" step="0.01" name="talla_promedio_inicial" value="{{ old('talla_promedio_inicial', $lote->talla_promedio_inicial) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block mb-2 font-semibold">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $lote->fecha_inicio ? $lote->fecha_inicio->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">Unidad de Producción</label>
                <select name="unidad_produccion_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Selecciona --</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_produccion_id', $lote->unidad_produccion_id) == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 font-semibold">Observaciones</label>
                <textarea name="observaciones" class="w-full border rounded px-3 py-2" rows="3">{{ old('observaciones', $lote->observaciones) }}</textarea>
            </div>
        </div>
        <div class="mt-8 flex justify-between">
            <div>
                <button type="button" onclick="if(confirm('¿Estás seguro de que deseas eliminar este lote? Esta acción no se puede deshacer.')) { document.getElementById('deleteForm').submit(); }" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded mr-2">🗑️ Eliminar Lote</button>
            </div>
            <div>
                <a href="{{ route('produccion.lotes.show', $lote) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2">Cancelar</a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded shadow">Actualizar</button>
            </div>
        </div>
    </form>

    <!-- Formulario oculto para eliminar -->
    <form id="deleteForm" action="{{ route('produccion.lotes.destroy', $lote) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    </div>
    </div>
    </div>
</x-app-layout>
