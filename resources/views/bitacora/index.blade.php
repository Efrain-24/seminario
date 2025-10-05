@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i data-lucide="book-open-check" class="w-8 h-8 text-yellow-500"></i>
            Bitácora de Actividades
        </h1>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i> Volver
        </a>
    </div>

    <form method="GET" class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-wrap gap-4 items-end">
        <div>
            <label for="usuario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario</label>
            <select name="usuario_id" id="usuario_id" class="form-select w-48">
                <option value="">Todos</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" @if(request('usuario_id') == $usuario->id) selected @endif>{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-input w-36" value="{{ request('fecha_inicio') }}">
        </div>
        <div>
            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-input w-36" value="{{ request('fecha_fin') }}">
        </div>
        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                <i data-lucide="filter" class="w-5 h-5 mr-2"></i> Filtrar
            </button>
        </div>
    </form>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acción</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detalles</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha y Hora</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($registros as $registro)
                <tr class="hover:bg-yellow-50 dark:hover:bg-yellow-900/30 transition">
                    <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-blue-500"></i>
                        <span class="font-semibold">{{ $registro->user ? $registro->user->name : 'Desconocido' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <i data-lucide="activity" class="w-4 h-4 mr-1"></i>
                            {{ $registro->accion }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button type="button" class="text-yellow-700 dark:text-yellow-300 hover:underline focus:outline-none" onclick="this.nextElementSibling.classList.toggle('hidden')">
                            Ver Detalles
                        </button>
                        <div class="hidden mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/40 rounded text-xs text-black !text-black shadow" style="color:#000 !important;">
                            @php
                                $detalles = collect(explode('|', $registro->detalles))
                                    ->map(function($item) {
                                        $item = trim($item);
                                        if (stripos($item, 'password:') !== false) return null; // Oculta el hash
                                        [$label, $valor] = array_pad(explode(':', $item, 2), 2, '');
                                        return $label && $valor ? '<li><span class="font-semibold">'.e(trim($label)).':</span> '.e(trim($valor)).'</li>' : null;
                                    })
                                    ->filter()
                                    ->implode('');
                            @endphp
                            <ul class="list-disc pl-4">{!! $detalles ?: '<li>Sin detalles</li>' !!}</ul>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                            {{ $registro->created_at->format('d/m/Y H:i') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No hay registros en la bitácora para los filtros seleccionados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $registros->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
