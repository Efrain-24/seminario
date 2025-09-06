<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Trazabilidad de Cosechas
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            {{-- Filtros --}}
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                    <select name="lote_id" id="lote_id" 
                            class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos los lotes</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}" @selected(request('lote_id') == $lote->id)>{{ $lote->codigo_lote }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Desde</label>
                    <input type="date" name="fecha_inicio" id="fechaInicio" value="{{ request('fecha_inicio') }}"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Hasta</label>
                    <input type="date" name="fecha_fin" id="fechaFin" value="{{ request('fecha_fin') }}"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tipo de Destino</label>
                    <select name="destino" id="tipoDestino" 
                            class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        <option value="cliente_final" @selected(request('destino') == 'cliente_final')>Cliente Final</option>
                        <option value="bodega" @selected(request('destino') == 'bodega')>Bodega</option>
                        <option value="mercado_local" @selected(request('destino') == 'mercado_local')>Mercado Local</option>
                        <option value="exportacion" @selected(request('destino') == 'exportacion')>Exportación</option>
                    </select>
                </div>
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Filtrar</button>
            </form>

            {{-- Acciones --}}
            <div class="flex gap-2">
                <a href="{{ route('cosechas.trazabilidad.create') }}" 
                   class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                    Nueva Cosecha
                </a>
            </div>
        </div>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full text-sm text-gray-800 dark:text-gray-100 divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lote</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peso Neto</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Costo Total</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destino</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trazabilidades as $trazabilidad)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trazabilidad->lote->codigo_lote }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trazabilidad->fecha_cosecha->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ 
                                        $trazabilidad->tipo_cosecha == 'total' 
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' 
                                            : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' 
                                    }}">
                                        {{ ucfirst($trazabilidad->tipo_cosecha) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-medium">{{ number_format($trazabilidad->peso_neto, 2) }} kg</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-medium">Q. {{ number_format($trazabilidad->costo_total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $trazabilidad->destino_tipo)) }}
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $trazabilidad->destino_detalle }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex gap-2 justify-end">
                                        <a href="{{ route('cosechas.trazabilidad.show', $trazabilidad->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200">
                                            Ver
                                        </a>
                                        <a href="{{ route('cosechas.trazabilidad.edit', $trazabilidad->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-yellow-600 hover:bg-yellow-700 text-white transition-colors duration-200">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td colspan="7" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                    No hay registros de cosecha disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Resumen Estadístico -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-blue-200 dark:border-blue-800">
                    <h5 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Total Cosechado</h5>
                    <p class="text-2xl font-semibold text-blue-800 dark:text-blue-200">{{ number_format($estadisticas['total_peso'], 2) }} kg</p>
                </div>
                <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-blue-200 dark:border-blue-800">
                    <h5 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Cosechas Totales</h5>
                    <p class="text-2xl font-semibold text-blue-800 dark:text-blue-200">{{ $estadisticas['total_cosechas'] }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-blue-200 dark:border-blue-800">
                    <h5 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Costo Promedio</h5>
                    <p class="text-2xl font-semibold text-blue-800 dark:text-blue-200">Q. {{ number_format($estadisticas['costo_promedio'], 2) }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-blue-200 dark:border-blue-800">
                    <h5 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Cosechas Parciales</h5>
                    <p class="text-2xl font-semibold text-blue-800 dark:text-blue-200">{{ $estadisticas['cosechas_parciales'] }}</p>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                <div class="px-4">
                    {{ $trazabilidades->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        // Inicializar Select2 para los filtros con estilos personalizados
        $('#lote_id, #tipoDestino').select2({
            theme: 'default',
            width: '100%',
            dropdownParent: $('body'),
            selectionCssClass: 'text-sm rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
            dropdownCssClass: 'text-sm rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100'
        });

        // Función para aplicar filtros
        function aplicarFiltros() {
            let lote = $('#lote_id').val();
            let fechaInicio = $('#fechaInicio').val();
            let fechaFin = $('#fechaFin').val();
            let destino = $('#tipoDestino').val();

            window.location.href = `{{ route('cosechas.trazabilidad.index') }}?` + 
                `lote_id=${lote}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&destino=${destino}`;
        }

        // Eventos de cambio en filtros
        $('#lote_id, #tipoDestino').change(aplicarFiltros);
        $('#fechaInicio, #fechaFin').on('change', aplicarFiltros);

        // Añadir hover effects a las filas de la tabla
        $('tbody tr').hover(
            function() { $(this).addClass('bg-gray-50 dark:bg-gray-700/50'); },
            function() { $(this).removeClass('bg-gray-50 dark:bg-gray-700/50'); }
        );
    });
    </script>
    @endpush
</x-app-layout>
