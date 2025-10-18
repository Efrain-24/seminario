@extends('layouts.app')

@section('title', 'Nueva Cosecha Parcial')

@section('content')

<!-- ACTUALIZADO 2025-10-17 -->
<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<!-- Background simple -->
<div class="bg-white dark:bg-gray-800 py-2 relative overflow-hidden">    
    <div class="container mx-auto px-6 relative z-10">

        <!-- Formulario principal con diseño hermoso -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in-up">
                <!-- Header del formulario -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-8 py-2">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Registro de Cosecha
                    </h2>
                    <p class="text-blue-100 text-xs mt-1">Completa todos los campos necesarios para registrar la cosecha</p>
                </div>

                <!-- Cuerpo del formulario -->
                <form method="POST" action="{{ route('produccion.cosechas.store') }}" class="p-8 space-y-8">
                    @csrf
                    <!-- Sección de información básica -->
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Lote -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Lote de Producción *
                            </label>
                            <select name="lote_id" required 
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-200 bg-white">
                                <option value="" class="text-gray-500">Seleccione un lote...</option>
                                @forelse($lotes ?? [] as $lote)
                                    <option value="{{ $lote->id }}" class="text-gray-800">
                                        🏷️ {{ $lote->codigo_lote ?? $lote->nombre ?? 'Lote #' . $lote->id }}
                                        (Stock: {{ $lote->cantidad_actual ?? 0 }} peces)
                                    </option>
                                @empty
                                    <option value="" disabled class="text-red-500">⚠️ No hay lotes disponibles</option>
                                @endforelse
                            </select>
                            @error('lote_id')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Fecha de Cosecha *
                            </label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required 
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-200">
                            @error('fecha')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Cantidad -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                Cantidad (peces) *
                            </label>
                            <input type="number" name="cantidad_cosechada" value="{{ old('cantidad_cosechada', 1) }}" min="1" required 
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-200 transition-all duration-200"
                                placeholder="Ingrese la cantidad de peces">
                            @error('cantidad_cosechada')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Peso -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                                Peso Total (kg)
                            </label>
                            <input type="number" name="peso_cosechado_kg" value="{{ old('peso_cosechado_kg', 0.5) }}" step="0.01" min="0"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Peso en kilogramos">
                            @error('peso_cosechado_kg')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sección de destino -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Información Adicional
                        </h3>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Destino -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Destino *
                                </label>
                                <select name="destino" required onchange="toggleCamposVenta()" id="destino"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-200 transition-all duration-200 bg-white">
                                    <option value="" class="text-gray-500">Seleccione el destino...</option>
                                    <option value="venta" {{ old('destino') == 'venta' ? 'selected' : '' }} class="text-green-600">💰 Venta</option>
                                    <option value="muestra" {{ old('destino') == 'muestra' ? 'selected' : '' }} class="text-blue-600">🔬 Muestra</option>
                                    <option value="otro" {{ old('destino') == 'otro' ? 'selected' : '' }} class="text-gray-600">📦 Otro</option>
                                </select>
                                @error('destino')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Responsable -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Responsable
                                </label>
                                <input type="text" name="responsable" readonly
                                    value="{{ auth()->user()->name ?? 'Sistema' }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-100 text-gray-600 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- CAMPOS DE VENTA con animación elegante -->
                    <div id="campos-venta" class="hidden transform transition-all duration-500 ease-in-out opacity-0 scale-95">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl shadow-inner p-8 border-2 border-green-200">
                            <!-- Header de venta -->
                            <div class="text-center mb-8">
                                <div class="inline-flex items-center justify-center p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full shadow-lg mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-green-800 mb-2">
                                    💰 Datos de Venta
                                </h3>
                                <p class="text-green-600">Complete la información de la venta</p>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Cliente -->
                                <div class="space-y-3">
                                    <label class="flex items-center text-sm font-semibold text-green-800">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Cliente *
                                    </label>
                                    <input type="text" name="cliente" value="{{ old('cliente') }}"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-200 bg-white"
                                        placeholder="Nombre completo del cliente">
                                    @error('cliente')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Teléfono -->
                                <div class="space-y-3">
                                    <label class="flex items-center text-sm font-semibold text-green-800">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        Teléfono
                                    </label>
                                    <input type="tel" name="telefono_cliente" value="{{ old('telefono_cliente') }}"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-200 bg-white"
                                        placeholder="Número de teléfono">
                                    @error('telefono_cliente')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Precio por kg -->
                                <div class="space-y-3">
                                    <label class="flex items-center text-sm font-semibold text-green-800">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Precio por lb (Q) *
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="precio_kg" step="0.01" min="0" value="{{ old('precio_kg') }}"
                                            class="w-full pl-8 pr-4 py-3 rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-200 bg-white"
                                            placeholder="0.00" onchange="calcularTotal()">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-600 font-bold">Q</span>
                                    </div>
                                    @error('precio_kg')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Método de pago -->
                                <div class="space-y-3">
                                    <label class="flex items-center text-sm font-semibold text-green-800">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Método de Pago
                                    </label>
                                    <select name="metodo_pago" 
                                        class="w-full px-4 py-3 rounded-xl border-2 border-green-200 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-200 bg-white">
                                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>🏦 Transferencia</option>
                                        <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>📝 Cheque</option>
                                    </select>
                                </div>

                                <!-- Total calculado con animación -->
                                <div class="md:col-span-2 mt-6">
                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl p-6 shadow-lg text-center text-white">
                                        <div class="flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <h4 class="text-lg font-bold">Total de Venta</h4>
                                        </div>
                                        <div id="total-venta" class="text-4xl font-bold mb-2 animate-pulse">Q 0.00</div>
                                        <div id="total-usd" class="text-emerald-200 text-lg">≈ $0.00 USD (TC: {{ $tipoCambio ?? 7.8 }})</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="space-y-4">
                        <label class="flex items-center text-lg font-semibold text-gray-700">
                            <svg class="w-6 h-6 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Observaciones
                        </label>
                        <textarea name="observaciones" rows="4" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-200 transition-all duration-200 resize-none"
                            placeholder="Agregue cualquier observación o comentario sobre la cosecha...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Botones con gradientes hermosos -->
                    <div class="flex flex-col sm:flex-row justify-center gap-4 pt-8 border-t-2 border-gray-200">
                        <a href="{{ route('produccion.cosechas.index') }}" 
                           class="px-8 py-4 bg-gradient-to-r from-gray-400 to-gray-600 hover:from-gray-500 hover:to-gray-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 text-center">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cosecha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS para animaciones personalizadas -->
<style>
@keyframes fade-in-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.8s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

/* Efecto de hover en los inputs */
input:focus, select:focus, textarea:focus {
    transform: translateY(-2px);
}

/* Animación del total */
#total-venta {
    transition: all 0.3s ease;
}

#total-venta.updating {
    transform: scale(1.1);
    color: #f59e0b !important;
}

/* Ocultar loaders, skeletons y placeholders grandes */
[class*="skeleton"],
[class*="loader"],
[class*="placeholder"],
[role="status"],
[role="progressbar"] {
    display: none !important;
}

/* Asegurar que no haya espacio en blanco grande */
div[class*="h-screen"],
div[class*="min-h-screen"] {
    min-height: auto !important;
}
</style>

<script>
function toggleCamposVenta() {
    const destino = document.getElementById('destino').value;
    const camposVenta = document.getElementById('campos-venta');
    
    if (destino === 'venta') {
        // Mostrar con animación
        camposVenta.classList.remove('hidden');
        setTimeout(() => {
            camposVenta.classList.remove('opacity-0', 'scale-95');
            camposVenta.classList.add('opacity-100', 'scale-100');
        }, 10);
        
        // Hacer campos obligatorios
        const clienteInput = document.querySelector('input[name="cliente"]');
        const precioInput = document.querySelector('input[name="precio_kg"]');
        if (clienteInput) clienteInput.setAttribute('required', 'required');
        if (precioInput) precioInput.setAttribute('required', 'required');
        
        // Calcular total inicial
        calcularTotal();
    } else {
        // Ocultar con animación
        camposVenta.classList.remove('opacity-100', 'scale-100');
        camposVenta.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            camposVenta.classList.add('hidden');
        }, 300);
        
        // Quitar obligatoriedad
        const clienteInput = document.querySelector('input[name="cliente"]');
        const precioInput = document.querySelector('input[name="precio_kg"]');
        if (clienteInput) clienteInput.removeAttribute('required');
        if (precioInput) precioInput.removeAttribute('required');
        
        // Limpiar campos
        if (clienteInput) clienteInput.value = '';
        const telefonoInput = document.querySelector('input[name="telefono_cliente"]');
        if (telefonoInput) telefonoInput.value = '';
        if (precioInput) precioInput.value = '';
        
        // Resetear totales
        const totalVenta = document.getElementById('total-venta');
        const totalUsd = document.getElementById('total-usd');
        if (totalVenta) totalVenta.textContent = 'Q 0.00';
        if (totalUsd) totalUsd.textContent = '≈ $0.00 USD (TC: {{ $tipoCambio ?? 7.8 }})';
    }
}

function calcularTotal() {
    const pesoInput = document.querySelector('input[name="peso_cosechado_kg"]');
    const precioInput = document.querySelector('input[name="precio_kg"]');
    const totalVentaEl = document.getElementById('total-venta');
    const totalUsdEl = document.getElementById('total-usd');
    
    if (!pesoInput || !precioInput || !totalVentaEl || !totalUsdEl) return;
    
    const peso = parseFloat(pesoInput.value) || 0;
    const precioKg = parseFloat(precioInput.value) || 0;
    const tipoCambio = {{ $tipoCambio ?? 7.8 }};
    
    const totalQuetzales = peso * precioKg;
    const totalUsd = totalQuetzales / tipoCambio;
    
    // Animación de actualización
    totalVentaEl.classList.add('updating');
    
    setTimeout(() => {
        totalVentaEl.textContent = 'Q ' + totalQuetzales.toFixed(2);
        totalUsdEl.textContent = '≈ $' + totalUsd.toFixed(2) + ' USD (TC: ' + tipoCambio + ')';
        totalVentaEl.classList.remove('updating');
    }, 200);
}

// Efectos de focus mejorados
function setupInputEffects() {
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('transform', 'scale-105');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('transform', 'scale-105');
        });
    });
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar efectos iniciales
    toggleCamposVenta();
    setupInputEffects();
    
    // Listeners para recálculo automático
    const pesoInput = document.querySelector('input[name="peso_cosechado_kg"]');
    const precioInput = document.querySelector('input[name="precio_kg"]');
    
    if (pesoInput) {
        pesoInput.addEventListener('input', calcularTotal);
        pesoInput.addEventListener('change', calcularTotal);
    }
    
    if (precioInput) {
        precioInput.addEventListener('input', calcularTotal);
        precioInput.addEventListener('change', calcularTotal);
    }
    
    // Animación de entrada para el formulario
    const form = document.querySelector('form');
    if (form) {
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        setTimeout(() => {
            form.style.transition = 'all 0.8s ease-out';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 300);
    }
});
</script>
@endsection
