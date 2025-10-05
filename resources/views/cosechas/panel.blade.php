@extends('layouts.app')

@section('title', 'Panel de Cosechas')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Panel de Cosechas
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Centro de control para gestión de cosechas y ventas
            </p>
        </div>
        <div class="flex items-center">
            <a href="{{ route('aplicaciones') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Gestión de Cosechas y Comercialización
                </span>
            </div>
        </div>
    </div>

    <!-- Información del Tipo de Cambio -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="lg:col-span-1">
            <x-tipo-cambio />
        </div>
        <div class="lg:col-span-3">
            <!-- Estadísticas rápidas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $cosechasEsteMes ?? 0 }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Cosechas Este Mes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">Q{{ number_format($ventasEsteMes ?? 0, 2) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ventas Este Mes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $clientesActivos ?? 0 }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Clientes Activos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submódulos del Sistema de Cosechas -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
        <div class="p-8">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-8">Módulos del Sistema de Cosechas</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-8">
                <!-- Submódulo Cosechas Parciales -->
                <div class="group relative">
                    <a href="{{ route('produccion.cosechas.index') }}" class="block">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-2xl p-8 border border-emerald-200 dark:border-emerald-700 hover:shadow-xl transition-all duration-300 group-hover:scale-[1.02]">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                                <svg class="w-6 h-6 text-emerald-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Cosechas y Ventas</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">Registro completo de cosechas parciales, control de inventario y gestión de ventas integrada</p>
                            
                            <!-- Funciones disponibles -->
                            <div class="pt-6 border-t border-emerald-200 dark:border-emerald-700">
                                <p class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold mb-4">Funcionalidades:</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Registrar
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Pesar
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Vender
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Tickets
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Submódulo Trazabilidad -->
                <div class="group relative">
                    <a href="{{ route('cosechas.trazabilidad.index') }}" class="block">
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 dark:from-teal-900/30 dark:to-teal-800/30 rounded-2xl p-8 border border-teal-200 dark:border-teal-700 hover:shadow-xl transition-all duration-300 group-hover:scale-[1.02]">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-16 h-16 bg-teal-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <svg class="w-6 h-6 text-teal-400 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Trazabilidad</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">Sistema completo de trazabilidad desde siembra hasta venta, con reportes y análisis detallados</p>
                            
                            <!-- Funciones disponibles -->
                            <div class="pt-6 border-t border-teal-200 dark:border-teal-700">
                                <p class="text-sm text-teal-600 dark:text-teal-400 font-semibold mb-4">Funcionalidades:</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Seguimiento
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Análisis
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                        </svg>
                                        Reportes
                                    </span>
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Histórico
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Acciones Rápidas</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('produccion.cosechas.create') }}" 
                       class="flex items-center justify-center px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nueva Cosecha
                    </a>
                    <a href="{{ route('produccion.cosechas.index', ['destino' => 'venta']) }}" 
                       class="flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Ver Ventas
                    </a>
                    <a href="{{ route('cosechas.trazabilidad.create') }}" 
                       class="flex items-center justify-center px-6 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Análisis
                    </a>
                    <a href="{{ route('produccion.cosechas.index') }}" 
                       class="flex items-center justify-center px-6 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Ver Todo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection