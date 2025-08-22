<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Nueva Cosecha Parcial
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
            <form action="{{ route('produccion.cosechas.store') }}" method="POST" class="space-y-4">
                @csrf
                @include('cosechas._form', ['cosecha' => null, 'lotes' => $lotes])
                <div class="flex justify-end gap-2">
                    <a href="{{ route('produccion.cosechas.index') }}"
                        class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        Cancelar
                    </a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
