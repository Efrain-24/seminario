@extends('layouts.app')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Editar Lote</h2>
    <form action="{{ route('lotes.update', $lote) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block mb-2 font-semibold">Especie</label>
                <input type="text" name="especie" value="{{ old('especie', $lote->especie) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">Cantidad Inicial</label>
                <input type="number" name="cantidad_inicial" value="{{ old('cantidad_inicial', $lote->cantidad_inicial) }}" class="w-full border rounded px-3 py-2" min="1" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">Peso Promedio Inicial (kg)</label>
                <input type="number" step="0.01" name="peso_promedio_inicial" value="{{ old('peso_promedio_inicial', $lote->peso_promedio_inicial) }}" class="w-full border rounded px-3 py-2">
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
        <div class="mt-8 flex justify-end">
            <a href="{{ route('lotes.show', $lote) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancelar</a>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded">Actualizar</button>
        </div>
    </form>
</div>
@endsection
