<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                Detalles de Cosecha
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('cosechas.trazabilidad.index') }}" 
                   class="px-4 py-2 text-sm rounded bg-gray-600 hover:bg-gray-700 text-white">
                    Volver
                </a>
                <a href="{{ route('cosechas.trazabilidad.edit', $trazabilidad->id) }}" 
                   class="px-4 py-2 text-sm rounded bg-yellow-600 hover:bg-yellow-700 text-white">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información Básica -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información Básica</h3>
                </div>
                <div class="p-4">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lote</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $trazabilidad->lote->codigo_lote }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Cosecha</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $trazabilidad->fecha_cosecha->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Cosecha</dt>
                            <dd class="text-sm">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $trazabilidad->tipo_cosecha == 'total' 
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' 
                                        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' }}">
                                    {{ ucfirst($trazabilidad->tipo_cosecha) }}
                                </span>
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado del Registro</dt>
                            <dd class="text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                                    Activo
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
                
            <!-- Pesos y Cantidades -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pesos y Cantidades</h3>
                    </div>
                    <div class="p-4">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="grid grid-cols-2 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Peso Bruto</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($trazabilidad->peso_bruto, 2) }} kg</dd>
                            </div>
                            <div class="grid grid-cols-2 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Peso Neto</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($trazabilidad->peso_neto, 2) }} kg</dd>
                            </div>
                            <div class="grid grid-cols-2 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Diferencia</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($trazabilidad->peso_bruto - $trazabilidad->peso_neto, 2) }} kg</dd>
                            </div>
                            @if($trazabilidad->unidades)
                                <div class="grid grid-cols-2 gap-4 py-3">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unidades</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($trazabilidad->unidades) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

            <!-- Información de Costos -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información de Costos</h3>
                </div>
                <div class="p-4">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mano de Obra</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">Q. {{ number_format($trazabilidad->costo_mano_obra, 2) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Insumos</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">Q. {{ number_format($trazabilidad->costo_insumos, 2) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Operativos</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">Q. {{ number_format($trazabilidad->costo_operativo, 2) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3 bg-gray-50 dark:bg-gray-700">
                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Total</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-gray-100">Q. {{ number_format($trazabilidad->costo_total, 2) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Costo por kg</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">Q. {{ number_format($trazabilidad->costo_total / $trazabilidad->peso_neto, 2) }}/kg</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Información de Destino -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información de Destino</h3>
                </div>
                <div class="p-4">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Destino</dt>
                            <dd class="text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $trazabilidad->destino_tipo)) }}
                                </span>
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Detalle</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $trazabilidad->destino_detalle }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notas</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $trazabilidad->notas ?? 'Sin notas adicionales' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($trazabilidad->tipo_cosecha === 'parcial')
            <!-- Historial de Cosechas del Lote -->
            <div class="col-span-2">
                <div class="bg-amber-50 dark:bg-amber-900/20 shadow rounded-lg overflow-hidden">
                    <div class="border-b border-amber-200 dark:border-amber-700/60 px-4 py-3 bg-amber-100/80 dark:bg-amber-800/40">
                        <h3 class="text-lg font-medium text-amber-900 dark:text-amber-100">Otras Cosechas del Mismo Lote</h3>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-amber-200 dark:divide-amber-700/60">
                                <thead class="bg-amber-50 dark:bg-amber-800/40">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Peso Neto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Costo Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Destino</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-amber-50/50 divide-y divide-amber-200 dark:divide-amber-700/60 dark:bg-amber-900/10">
                                    @foreach($otrasCosechas as $otra)
                                    <tr class="hover:bg-amber-100/50 dark:hover:bg-amber-800/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-900 dark:text-amber-100">
                                            {{ $otra->fecha_cosecha->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $otra->tipo_cosecha == 'total' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200'
                                                    : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' }}">
                                                {{ ucfirst($otra->tipo_cosecha) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-900 dark:text-amber-100">
                                            {{ number_format($otra->peso_neto, 2) }} kg
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-900 dark:text-amber-100">
                                            Q. {{ number_format($otra->costo_total, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-900 dark:text-amber-100">
                                            {{ $otra->destino_detalle }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('cosechas.trazabilidad.show', $otra->id) }}" 
                                               class="text-amber-700 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    <!-- Botón flotante para cambiar tema claro/oscuro -->
    <button id="theme-toggle" type="button"
        class="fixed bottom-6 right-6 z-50 p-3 rounded-full shadow-lg bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-900 transition-colors"
        aria-label="Cambiar tema">
        <svg id="theme-toggle-light-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 4.95l-.71-.71M6.34 6.34l-.71-.71" />
        </svg>
        <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
        </svg>
    </button>
    <script>
        // Detectar y aplicar preferencia guardada
        if (localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        // Iconos
        const themeToggleBtn = document.getElementById('theme-toggle');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }
        }
        updateIcons();
        themeToggleBtn.addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateIcons();
        });
    </script>
</x-app-layout>
