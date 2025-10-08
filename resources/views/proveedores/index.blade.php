<x-app-layout>
    <x-sl        </div>
    </x-slot>
    
    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">ame="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('compras.panel') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                        <svg class="w-6 h-6 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Gestión de Proveedores
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Administra tu red de proveedores y mantén relaciones comerciales efectivas</p>
                </div>
            </div>
            <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Proveedor
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center mb-6" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center mb-6" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Estadísticas resumidas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Proveedores</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Proveedores Activos</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['activos'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldos Pendientes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">GTQ {{ number_format($estadisticas['total_saldo_pendiente'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Compras este Mes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">GTQ {{ number_format($estadisticas['compras_mes_actual'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de búsqueda y filtros compacta --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('proveedores.index') }}" id="filtros-form" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <input type="text" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                   name="busqueda" 
                                   value="{{ request('busqueda') }}" 
                                   placeholder="Buscar por nombre, NIT, código o email...">
                        </div>
                        <div class="flex items-center gap-2">
                            @if(request()->hasAny(['busqueda', 'estado', 'categoria', 'tipo', 'departamento', 'solo_con_credito', 'solo_con_saldo']))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    {{ collect(request()->all())->filter()->count() }} filtros activos
                                </span>
                            @endif
                            <button type="button" 
                                    onclick="openFiltersModal()" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filtros
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Buscar
                            </button>
                            @if(request()->hasAny(['busqueda', 'estado', 'categoria', 'tipo', 'departamento', 'solo_con_credito', 'solo_con_saldo']))
                                <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>

                        {{-- Campos ocultos para mantener filtros --}}
                        <input type="hidden" name="estado" value="{{ request('estado') }}">
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <input type="hidden" name="departamento" value="{{ request('departamento') }}">
                        <input type="hidden" name="solo_con_credito" value="{{ request('solo_con_credito') }}">
                        <input type="hidden" name="solo_con_saldo" value="{{ request('solo_con_saldo') }}">
                    </form>
                </div>
            </div>

            {{-- Tabla de proveedores --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Lista de Proveedores ({{ $proveedores->total() }} resultados)
                    </h3>
                </div>
                
                @if($proveedores->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Calificación</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saldo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Última Compra</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($proveedores as $proveedor)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150" onclick="window.location.href='{{ route('proveedores.show', $proveedor) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <code class="px-2 py-1 text-xs font-mono bg-amber-100 text-amber-800 rounded dark:bg-amber-900/30 dark:text-amber-300">
                                                {{ $proveedor->codigo_formateado }}
                                            </code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $proveedor->nombre }}</div>
                                                @if($proveedor->nit)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">NIT: {{ $proveedor->nit }}</div>
                                                @endif
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($proveedor->tipo) }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ ucfirst($proveedor->categoria) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($proveedor->estado == 'activo')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    Activo
                                                </span>
                                            @elseif($proveedor->estado == 'inactivo')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                    Inactivo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                    {{ ucfirst($proveedor->estado) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                @if($proveedor->telefono_principal)
                                                    <div class="text-sm text-gray-900 dark:text-gray-100 flex items-center">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                        {{ $proveedor->telefono_principal }}
                                                    </div>
                                                @endif
                                                @if($proveedor->email)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ Str::limit($proveedor->email, 20) }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-400">Sin email</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($proveedor->calificacion)
                                                <div class="flex items-center justify-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $proveedor->calificacion)
                                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($proveedor->calificacion, 1) }}/5.0</div>
                                            @else
                                                <span class="text-sm text-gray-400">Sin calificar</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($proveedor->saldo_actual > 0)
                                                <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ $proveedor->saldo_formateado }}</span>
                                            @elseif($proveedor->saldo_actual < 0)
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $proveedor->saldo_formateado }}</span>
                                            @else
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $proveedor->moneda_preferida }} 0.00</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($proveedor->fecha_ultima_compra)
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $proveedor->fecha_ultima_compra->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    hace {{ $proveedor->diasSinCompras() }} días
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">Nunca</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('proveedores.show', $proveedor) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20" 
                                                   title="Ver detalles">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('proveedores.edit', $proveedor) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded hover:bg-yellow-50 dark:hover:bg-yellow-900/20" 
                                                   title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20" 
                                                        title="Eliminar" 
                                                        onclick="confirmarEliminacion({{ $proveedor->id }})">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $proveedores->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No se encontraron proveedores</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            @if(request()->hasAny(['busqueda', 'estado', 'categoria', 'tipo', 'departamento', 'solo_con_credito', 'solo_con_saldo']))
                                Intenta ajustar los filtros de búsqueda.
                            @else
                                Comienza agregando tu primer proveedor.
                            @endif
                        </p>
                        @if(!request()->hasAny(['busqueda', 'estado', 'categoria', 'tipo', 'departamento', 'solo_con_credito', 'solo_con_saldo']))
                            <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agregar Primer Proveedor
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal de Filtros --}}
    <div id="filters-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" onclick="closeFiltersModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtros de Búsqueda
                        </h3>
                        <button type="button" onclick="closeFiltersModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('proveedores.index') }}" id="modal-filtros-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="modal-estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                        id="modal-estado" name="estado">
                                    <option value="">Todos los estados</option>
                                    @foreach($filtros['estados'] as $est)
                                        <option value="{{ $est }}" {{ request('estado') == $est ? 'selected' : '' }}>
                                            {{ ucfirst($est) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal-categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoría</label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                        id="modal-categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    @foreach($filtros['categorias'] as $cat)
                                        <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal-tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                        id="modal-tipo" name="tipo">
                                    <option value="">Todos los tipos</option>
                                    @foreach($filtros['tipos'] as $tip)
                                        <option value="{{ $tip }}" {{ request('tipo') == $tip ? 'selected' : '' }}>
                                            {{ ucfirst($tip) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal-departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departamento</label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                        id="modal-departamento" name="departamento">
                                    <option value="">Todos los departamentos</option>
                                    @foreach($filtros['departamentos'] as $dep)
                                        <option value="{{ $dep }}" {{ request('departamento') == $dep ? 'selected' : '' }}>
                                            {{ $dep }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal-busqueda" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Búsqueda General</label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white" 
                                       id="modal-busqueda" name="busqueda" 
                                       value="{{ request('busqueda') }}" 
                                       placeholder="Nombre, NIT, código...">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Opciones Adicionales</label>
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50" 
                                           id="modal-solo_con_credito" name="solo_con_credito" value="1" 
                                           {{ request('solo_con_credito') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Solo con crédito</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50" 
                                           id="modal-solo_con_saldo" name="solo_con_saldo" value="1" 
                                           {{ request('solo_con_saldo') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Solo con saldo pendiente</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex flex-col sm:flex-row gap-3 sm:justify-between">
                    <div class="flex gap-2">
                        <button type="button" onclick="clearAllFilters()" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Limpiar Todo
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeFiltersModal()" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                        <button type="button" onclick="applyFilters()" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación para eliminación --}}
    <div x-data="{ open: false, proveedorId: null }" 
         x-show="open" 
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="open = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                Confirmar Eliminación
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    ¿Estás seguro de que deseas eliminar este proveedor? Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form :action="`/proveedores/${proveedorId}`" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmarEliminacion(proveedorId) {
            // Usar Alpine.js para manejar el modal
            const modalElement = document.querySelector('[x-data]');
            if (modalElement) {
                modalElement._x_dataStack[0].open = true;
                modalElement._x_dataStack[0].proveedorId = proveedorId;
            }
        }

        // Funciones para el modal de filtros
        function openFiltersModal() {
            document.getElementById('filters-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeFiltersModal() {
            document.getElementById('filters-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function applyFilters() {
            // Actualizar los campos ocultos del formulario principal con los valores del modal
            const mainForm = document.getElementById('filtros-form');
            const modalForm = document.getElementById('modal-filtros-form');
            
            const campos = ['estado', 'categoria', 'tipo', 'departamento', 'busqueda'];
            const checkboxes = ['solo_con_credito', 'solo_con_saldo'];
            
            // Actualizar campos select e input
            campos.forEach(campo => {
                const modalField = modalForm.querySelector(`[name="${campo}"]`);
                const mainField = mainForm.querySelector(`[name="${campo}"]`);
                if (modalField && mainField) {
                    mainField.value = modalField.value;
                }
            });
            
            // Actualizar checkboxes
            checkboxes.forEach(campo => {
                const modalField = modalForm.querySelector(`[name="${campo}"]`);
                const mainField = mainForm.querySelector(`[name="${campo}"]`);
                if (modalField && mainField) {
                    mainField.value = modalField.checked ? '1' : '';
                }
            });
            
            // Enviar el formulario principal
            mainForm.submit();
        }

        function clearAllFilters() {
            // Limpiar todos los campos del modal
            const modalForm = document.getElementById('modal-filtros-form');
            const selects = modalForm.querySelectorAll('select');
            const inputs = modalForm.querySelectorAll('input[type="text"]');
            const checkboxes = modalForm.querySelectorAll('input[type="checkbox"]');
            
            selects.forEach(select => select.selectedIndex = 0);
            inputs.forEach(input => input.value = '');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            
            // Aplicar los filtros limpios
            applyFilters();
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFiltersModal();
            }
        });

        // Auto-submit del formulario principal al cambiar la búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('#filtros-form input[name="busqueda"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 3 || this.value.length === 0) {
                            document.getElementById('filtros-form').submit();
                        }
                    }, 500);
                });
            }
        });
    </script>
</x-app-layout>