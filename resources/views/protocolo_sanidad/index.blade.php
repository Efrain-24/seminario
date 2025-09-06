<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Protocolos de Sanidad</h2>
    </x-slot>
    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            <div class="flex gap-2">
                <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Ver Limpiezas</a>
            </div>
            <a href="{{ route('protocolo-sanidad.create') }}" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Nuevo Protocolo</a>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Fecha de Implementación</th>
                        <th class="px-4 py-2 text-left">Responsable</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($protocolos as $protocolo)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $protocolo->nombre }}</td>
                            <td class="px-4 py-2">{{ $protocolo->descripcion }}</td>
                            <td class="px-4 py-2">{{ $protocolo->fecha_implementacion }}</td>
                            <td class="px-4 py-2">{{ $protocolo->responsable }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('protocolo-sanidad.show', $protocolo) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Ver</a>
                                <form action="{{ route('protocolo-sanidad.destroy', $protocolo) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
