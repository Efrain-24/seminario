<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                Nuevo Artículo de Inventario
            </h2>
            <div class="flex items-center space-x-2 text-sm">
                <span class="px-3 py-1 rounded-full bg-blue-500 text-white">
                    Creación
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-4xl mx-auto px-4">
        @if ($errors->any())
            <div class="mb-6 rounded-lg p-4 bg-red-50 border border-red-200 dark:bg-red-900/30 dark:border-red-700">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-800 dark:text-red-200 font-medium">Errores en el formulario:</span>
                </div>
                <ul class="list-disc list-inside text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Información contextual --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 dark:bg-blue-900/20 dark:border-blue-700">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-blue-800 dark:text-blue-200">
                    Crear nuevo artículo de inventario
                </span>
            </div>
            <p class="text-sm text-blue-700 dark:text-blue-300">
                Define los productos, equipos e insumos que manejas en tu operación piscícola. Cada artículo tendrá control de stock, alertas y trazabilidad completa.
            </p>
        </div>

        {{-- Formulario --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Información del Artículo
                </h3>
            </div>

            <form method="POST" action="{{ route('produccion.inventario.items.store') }}" class="p-6 space-y-6">
                @csrf
                @include('inventario.items._form')
                
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('produccion.inventario.items.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Crear Artículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
