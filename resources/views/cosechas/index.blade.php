<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Cosechas Parciales
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">

        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between mb-4">
            <a href="{{ route('produccion.cosechas.create') }}"
                class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                Nueva Cosecha
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-right">Cantidad</th>
                        <th class="px-4 py-2 text-right">Peso (kg)</th>
                        <th class="px-4 py-2 text-left">Destino</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cosechas as $c)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $c->fecha?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $c->lote->codigo_lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($c->cantidad_cosechada) }}</td>
                            <td class="px-4 py-2 text-right">
                                {{ $c->peso_cosechado_kg ? number_format($c->peso_cosechado_kg, 2) : '—' }}
                            </td>
                            <td class="px-4 py-2 capitalize">{{ $c->destino }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('produccion.cosechas.edit', $c) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('produccion.cosechas.destroy', $c) }}" method="POST"
                                    class="inline" onsubmit="return confirm('¿Eliminar y revertir stock?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Sin registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $cosechas->links() }}</div>
    </div>
</x-app-layout>
