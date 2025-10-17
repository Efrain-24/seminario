<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('proveedores.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detalles del Proveedor') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('proveedores.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $proveedor->nombre }}
                    </h2>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $proveedor->codigo_formateado ?? 'PROV-' . str_pad($proveedor->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ ucfirst($proveedor->categoria) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors duration-200" onclick="mostrarEvaluacion()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Evaluar
                </button>
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Alertas --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Estadísticas resumidas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Calificación</p>
                            @if($proveedor->calificacion)
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($proveedor->calificacion, 1) }}/5.0</p>
                                <div class="flex items-center mt-1">
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
                            @else
                                <p class="text-2xl font-bold text-gray-400">Sin calificar</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Compras</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $proveedor->moneda_preferida ?? 'GTQ' }} {{ number_format($proveedor->total_compras_historico ?? 0, 2) }}
                            </p>
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
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Actual</p>
                            @if($proveedor->saldo_actual > 0)
                                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ $proveedor->moneda_preferida ?? 'GTQ' }} {{ number_format($proveedor->saldo_actual, 2) }}
                                </p>
                            @elseif($proveedor->saldo_actual < 0)
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $proveedor->moneda_preferida ?? 'GTQ' }} {{ number_format(abs($proveedor->saldo_actual), 2) }}
                                </p>
                            @else
                                <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">
                                    {{ $proveedor->moneda_preferida ?? 'GTQ' }} 0.00
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 14a7 7 0 110-14 7 7 0 010 14zM13 9h3m-3 0l3 3m-3-3l3-3"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Última Compra</p>
                            @if($proveedor->fecha_ultima_compra)
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $proveedor->fecha_ultima_compra->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    hace {{ now()->diffInDays($proveedor->fecha_ultima_compra) }} días
                                </p>
                            @else
                                <p class="text-lg font-bold text-gray-400">Nunca</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información Principal en Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Información Básica --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Información Básica
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nombre</label>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->nombre }}</p>
                                    </div>
                                    @if($proveedor->nit)
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIT</label>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->nit }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Código</label>
                                        <p class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                            {{ $proveedor->codigo_formateado ?? 'PROV-' . str_pad($proveedor->id, 3, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Tipo</label>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($proveedor->tipo) }}</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado</label>
                                        <div class="mt-1">
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
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Fecha Registro</label>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Última Actualización</label>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Categoría</label>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ ucfirst($proveedor->categoria) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información de Contacto --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Información de Contacto
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Contacto Principal</h4>
                                    @if($proveedor->telefono_principal)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->telefono_principal }}</span>
                                    </div>
                                    @endif
                                    @if($proveedor->email)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <a href="mailto:{{ $proveedor->email }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $proveedor->email }}
                                        </a>
                                    </div>
                                    @endif
                                    @if($proveedor->sitio_web)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                        </svg>
                                        <a href="{{ $proveedor->sitio_web }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $proveedor->sitio_web }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                                @if($proveedor->contacto_comercial_nombre)
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Contacto Comercial</h4>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $proveedor->contacto_comercial_nombre }}</p>
                                        @if($proveedor->contacto_comercial_cargo)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $proveedor->contacto_comercial_cargo }}</p>
                                        @endif
                                    </div>
                                    @if($proveedor->contacto_comercial_telefono)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->contacto_comercial_telefono }}</span>
                                    </div>
                                    @endif
                                    @if($proveedor->contacto_comercial_email)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <a href="mailto:{{ $proveedor->contacto_comercial_email }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $proveedor->contacto_comercial_email }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @if($proveedor->direccion)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Dirección</h4>
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->direccion }}</p>
                                        @if($proveedor->departamento || $proveedor->municipio)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $proveedor->municipio }}{{ $proveedor->municipio && $proveedor->departamento ? ', ' : '' }}{{ $proveedor->departamento }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Panel Lateral --}}
                <div class="space-y-6">
                    {{-- Información Comercial --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Información Comercial
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Moneda Preferida</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->moneda_preferida ?? 'GTQ' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Forma de Pago</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($proveedor->forma_pago_preferida ?? 'No especificado') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Días de Crédito</label>
                                @if($proveedor->dias_credito > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $proveedor->dias_credito }} días
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Solo contado
                                    </span>
                                @endif
                            </div>
                            @if($proveedor->limite_credito)
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Límite de Crédito</label>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">
                                    {{ $proveedor->moneda_preferida ?? 'GTQ' }} {{ number_format($proveedor->limite_credito, 2) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones Rápidas --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-600 dark:hover:bg-yellow-900/30" onclick="mostrarEvaluacion()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Evaluar Proveedor
                            </button>
                            @if($proveedor->email)
                            <a href="mailto:{{ $proveedor->email }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-600 dark:hover:bg-blue-900/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Enviar Email
                            </a>
                            @endif
                            @if($proveedor->telefono_principal)
                            <a href="tel:{{ $proveedor->telefono_principal }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-600 dark:hover:bg-green-900/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Llamar
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Notas adicionales --}}
                    @if($proveedor->especialidades || $proveedor->notas)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Información Adicional
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if($proveedor->especialidades)
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Especialidades</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">{{ $proveedor->especialidades }}</p>
                            </div>
                            @endif
                            @if($proveedor->notas)
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Notas</label>
                                <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $proveedor->notas }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarEvaluacion() {
            // Implementar modal con Alpine.js si es necesario
            alert('Función de evaluación en desarrollo');
        }

        function mostrarCambioEstado() {
            // Implementar modal con Alpine.js si es necesario
            alert('Función de cambio de estado en desarrollo');
        }
    </script>
</x-app-layout>