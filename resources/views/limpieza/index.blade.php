<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('protocolos.panel') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Registros de Limpieza</h2>
            </div>
        </div>
    </x-slot>
    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 rounded p-3 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        <!-- Botón para abrir filtros y filtros rápidos -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-3">
                <!-- Botón para abrir modal de filtros -->
                <button onclick="abrirModalFiltros()" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtros
                    @if(request()->hasAny(['buscar', 'estado', 'filtro_area', 'protocolo', 'responsable', 'fecha_desde', 'fecha_hasta']))
                        <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ collect(request()->only(['buscar', 'estado', 'filtro_area', 'protocolo', 'responsable', 'fecha_desde', 'fecha_hasta']))->filter()->count() }}</span>
                    @endif
                </button>

                @if(request()->hasAny(['buscar', 'estado', 'filtro_area', 'protocolo', 'responsable', 'fecha_desde', 'fecha_hasta']))
                    <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar Filtros
                    </a>
                @endif
            </div>

            <!-- Filtros rápidos por fecha -->
            <div class="flex gap-1">
                <span class="text-xs text-gray-500 self-center mr-2">Fechas:</span>
                <button type="button" onclick="aplicarFechaRapida(7)" class="px-3 py-1 text-xs rounded bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors">Últimos 7 días</button>
                <button type="button" onclick="aplicarFechaRapida(30)" class="px-3 py-1 text-xs rounded bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors">Último mes</button>
                <button type="button" onclick="aplicarFechaRapida(90)" class="px-3 py-1 text-xs rounded bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors">Últimos 3 meses</button>
            </div>
        </div>

        <!-- Modal de Filtros -->
        <div id="modalFiltros" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <!-- Header del modal -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtros de Búsqueda
                    </h3>
                    <button onclick="cerrarModalFiltros()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 12L6 6"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Formulario de filtros -->
                <form method="GET" action="{{ route('limpieza.index') }}" class="mt-4">
                    <div class="space-y-6">
                        <!-- Búsqueda general -->
                        <div>
                            <label for="buscar_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Búsqueda General
                            </label>
                            <input type="text" name="buscar" id="buscar_modal" value="{{ request('buscar') }}" 
                                   placeholder="Buscar en área, responsable, observaciones..." 
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Filtros en grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Estado -->
                            <div>
                                <label for="estado_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Estado
                                </label>
                                <select name="estado" id="estado_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todos los estados</option>
                                    <option value="no_ejecutado" {{ request('estado') == 'no_ejecutado' ? 'selected' : '' }}>No Ejecutado</option>
                                    <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                            </div>

                            <!-- Tipo de área -->
                            <div>
                                <label for="filtro_area_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Tipo de Área
                                </label>
                                <select name="filtro_area" id="filtro_area_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todas las áreas</option>
                                    <option value="unidades" {{ request('filtro_area') == 'unidades' ? 'selected' : '' }}>Unidades de Producción</option>
                                    <option value="bodegas" {{ request('filtro_area') == 'bodegas' ? 'selected' : '' }}>Bodegas</option>
                                    <option value="otras" {{ request('filtro_area') == 'otras' ? 'selected' : '' }}>Otras áreas</option>
                                </select>
                            </div>

                            <!-- Protocolo -->
                            <div>
                                <label for="protocolo_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Protocolo
                                </label>
                                <select name="protocolo" id="protocolo_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todos los protocolos</option>
                                    @foreach($protocolos as $protocolo)
                                        <option value="{{ $protocolo->id }}" {{ request('protocolo') == $protocolo->id ? 'selected' : '' }}>
                                            {{ $protocolo->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Responsable -->
                            <div>
                                <label for="responsable_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Responsable
                                </label>
                                <select name="responsable" id="responsable_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todos los responsables</option>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable }}" {{ request('responsable') == $responsable ? 'selected' : '' }}>
                                            {{ $responsable }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filtros de fecha -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Rango de Fechas
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="fecha_desde_modal" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                                    <input type="date" name="fecha_desde" id="fecha_desde_modal" value="{{ request('fecha_desde') }}" 
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="fecha_hasta_modal" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                                    <input type="date" name="fecha_hasta" id="fecha_hasta_modal" value="{{ request('fecha_hasta') }}" 
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones del modal -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="cerrarModalFiltros()" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Funciones del modal
            function abrirModalFiltros() {
                document.getElementById('modalFiltros').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function cerrarModalFiltros() {
                document.getElementById('modalFiltros').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Cerrar modal al hacer click fuera de él
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('modalFiltros');
                if (event.target === modal) {
                    cerrarModalFiltros();
                }
            });

            // Función para aplicar filtro de fecha rápida
            function aplicarFechaRapida(dias) {
                const hoy = new Date();
                const fechaHasta = hoy.toISOString().split('T')[0];
                
                const fechaDesde = new Date();
                fechaDesde.setDate(hoy.getDate() - dias);
                const fechaDesdeStr = fechaDesde.toISOString().split('T')[0];
                
                // Crear formulario para enviar
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = "{{ route('limpieza.index') }}";
                
                const inputDesde = document.createElement('input');
                inputDesde.type = 'hidden';
                inputDesde.name = 'fecha_desde';
                inputDesde.value = fechaDesdeStr;
                
                const inputHasta = document.createElement('input');
                inputHasta.type = 'hidden';
                inputHasta.name = 'fecha_hasta';
                inputHasta.value = fechaHasta;
                
                form.appendChild(inputDesde);
                form.appendChild(inputHasta);
                document.body.appendChild(form);
                form.submit();
            }

            // Cerrar modal con ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    cerrarModalFiltros();
                }
            });
        </script>

        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            <div class="flex items-center gap-4">
                <!-- Contador de resultados -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">{{ $limpiezas->count() }}</span> 
                    {{ $limpiezas->count() == 1 ? 'registro encontrado' : 'registros encontrados' }}
                </div>

                <!-- Filtros activos -->
                @if(request()->hasAny(['buscar', 'estado', 'filtro_area', 'protocolo', 'responsable', 'fecha_desde', 'fecha_hasta']))
                    <div class="flex flex-wrap gap-2">
                        @if(request('buscar'))
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                Búsqueda: "{{ request('buscar') }}"
                            </span>
                        @endif
                        @if(request('estado'))
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                Estado: {{ ucfirst(str_replace('_', ' ', request('estado'))) }}
                            </span>
                        @endif
                        @if(request('filtro_area'))
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Área: {{ request('filtro_area') == 'unidades' ? 'Unidades' : (request('filtro_area') == 'bodegas' ? 'Bodegas' : 'Otras') }}
                            </span>
                        @endif
                        @if(request('responsable'))
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                Responsable: {{ request('responsable') }}
                            </span>
                        @endif
                        @if(request('fecha_desde') || request('fecha_hasta'))
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                Fechas: {{ request('fecha_desde') ?: '...' }} - {{ request('fecha_hasta') ?: '...' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-gray-600 hover:bg-gray-700 text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Ver Protocolos
                </a>
                <a href="{{ route('limpieza.create') }}" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Nuevo Registro</a>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Área</th>
                        <th class="px-4 py-2 text-left">Responsable</th>
                        <th class="px-4 py-2 text-left">Protocolo</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Progreso</th>
                        <th class="px-4 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($limpiezas as $limpieza)
                        @php
                            $totalActividades = $limpieza->actividades_ejecutadas ? count($limpieza->actividades_ejecutadas) : 0;
                            $actividadesCompletadas = $limpieza->actividades_ejecutadas ? collect($limpieza->actividades_ejecutadas)->where('completada', true)->count() : 0;
                            $porcentaje = $totalActividades > 0 ? round(($actividadesCompletadas / $totalActividades) * 100) : 0;
                        @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('limpieza.show', $limpieza) }}'">
                            <td class="px-4 py-2">{{ $limpieza->fecha }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    @if(str_starts_with($limpieza->area, 'Unidad:'))
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                                            <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                        </svg>
                                    @elseif(str_starts_with($limpieza->area, 'Bodega:'))
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    <span class="text-sm">{{ $limpieza->area }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">{{ $limpieza->responsable }}</td>
                            <td class="px-4 py-2">{{ $limpieza->protocoloSanidad->nombre_completo ?? '' }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    @if($limpieza->estado === 'completado')
                                        <span class="px-2 py-1 text-xs rounded font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Completado</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20" title="No editable - Registro completado">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($limpieza->estado === 'en_progreso')
                                        <span class="px-2 py-1 text-xs rounded font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">En Progreso</span>
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" title="Editable">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">No Ejecutado</span>
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" title="Editable">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                @if($totalActividades > 0)
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $actividadesCompletadas }}/{{ $totalActividades }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Sin actividades</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
