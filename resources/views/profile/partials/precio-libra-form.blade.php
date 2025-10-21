<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Precio por Libra') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Configura el precio por libra actual para las ventas del día.') }}
        </p>
    </header>

    <!-- Precio Actual -->
    @php
        $precioActual = \App\Models\PrecioLibra::precioActual();
    @endphp

    @if($precioActual)
        <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        Precio actual: <span class="font-bold">Q{{ number_format($precioActual->precio, 2) }}</span> por libra
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400">
                        Registrado el: {{ $precioActual->created_at->format('d/m/Y H:i') }}
                        | Por: {{ $precioActual->usuario->name }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    No hay precio configurado. Establece el precio por libra para el día.
                </p>
            </div>
        </div>
    @endif

    <!-- Formulario para nuevo precio -->
    <form method="post" action="{{ route('precio-libra.store') }}" class="mt-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="precio" :value="__('Nuevo Precio (Q/libra)')" />
                <x-text-input id="precio" name="precio" type="number" step="0.01" min="0.01" class="mt-1 block w-full" 
                             :value="old('precio')" required autofocus placeholder="0.00" />
                <x-input-error class="mt-2" :messages="$errors->get('precio')" />
            </div>

            <div>
                <x-input-label for="observaciones" :value="__('Observaciones (opcional)')" />
                <textarea id="observaciones" name="observaciones" rows="2" 
                         class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                         placeholder="Motivo del cambio de precio...">{{ old('observaciones') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('observaciones')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Establecer Precio') }}</x-primary-button>
        </div>
    </form>

    <!-- Historial de precios -->
    <div class="mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">
            Historial de Precios
        </h3>
        
        @php
            $historial = \App\Models\PrecioLibra::with('usuario')
                                               ->orderBy('created_at', 'desc')
                                               ->limit(10)
                                               ->get();
        @endphp

        @if($historial->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Precio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fecha Registro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Observaciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($historial as $precio)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Q{{ number_format($precio->precio, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $precio->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $precio->usuario->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($precio->activo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                        {{ $precio->observaciones ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>No hay historial de precios registrado.</p>
            </div>
        @endif
    </div>
</section>