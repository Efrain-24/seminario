@extends('layouts.app')

@section('title', 'Detalle de Cosecha')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto px-6 py-8">
    <!-- Notificación especial para ticket cuando se completa la venta -->
    @if(session('ticket_disponible') && $cosecha->estado_venta === 'completada')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-green-800">
                        ¡Venta completada exitosamente!
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-green-700">
                            Su ticket de venta está listo para descargar. Puede verlo en línea o descargarlo en formato PDF.
                        </p>
                    </div>
                    <div class="mt-4">
                        <div class="flex space-x-3">
                            <a href="{{ route('produccion.cosechas.ticket.ver', $cosecha) }}" 
                               target="_blank"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver Ticket
                            </a>
                            <a href="{{ route('produccion.cosechas.ticket.descargar', $cosecha) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Detalle de Cosecha
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ $cosecha->lote->codigo_lote ?? 'Sin lote asignado' }} - {{ $cosecha->fecha->format('d/m/Y') }}
            </p>
        </div>
        <div class="flex space-x-3">
            <!-- Botones de Tickets (solo si es venta completada) -->
            @if($cosecha->destino === 'venta' && $cosecha->estado_venta === 'completada')
                <div class="flex space-x-2">
                    <a href="{{ route('produccion.cosechas.ticket.ver', $cosecha) }}" 
                       target="_blank"
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver Ticket
                    </a>
                    <a href="{{ route('produccion.cosechas.ticket.descargar', $cosecha) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Descargar Ticket
                    </a>
                </div>
            @endif

            <!-- Completar venta si está pendiente -->
            @if($cosecha->destino === 'venta' && $cosecha->estado_venta !== 'completada')
                <a href="{{ route('produccion.cosechas.completar-venta', $cosecha) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Completar Venta
                </a>
            @endif
            
            <a href="{{ route('produccion.cosechas.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ← Volver
            </a>
            <a href="{{ route('produccion.cosechas.edit', $cosecha) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Información de la Cosecha -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Información de la Cosecha
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Lote
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->lote->codigo_lote ?? 'No asignado' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha de Cosecha
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->fecha->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cantidad Cosechada
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ number_format($cosecha->cantidad_cosechada) }} peces
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Peso Cosechado
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->peso_cosechado_kg ? number_format($cosecha->peso_cosechado_kg, 2) . ' kg' : 'No registrado' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Destino
                    </label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $cosecha->destino === 'venta' ? 'bg-green-100 text-green-800' : ($cosecha->destino === 'muestra' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($cosecha->destino) }}
                    </span>
                </div>

                @if($cosecha->responsable)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Responsable
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $cosecha->responsable }}
                        </p>
                    </div>
                @endif

                @if($cosecha->observaciones)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Observaciones
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                            {{ $cosecha->observaciones }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información de Venta (solo si es para venta) -->
        @if($cosecha->destino === 'venta')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Información de Venta
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Estado de Venta
                        </label>
                        @if($cosecha->estado_venta === 'completada')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Completada
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pendiente
                            </span>
                        @endif
                    </div>

                    @if($cosecha->codigo_venta)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Código de Venta
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100 font-mono">
                                {{ $cosecha->codigo_venta }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->cliente)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cliente
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $cosecha->cliente }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->telefono_cliente)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Teléfono
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $cosecha->telefono_cliente }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->precio_kg)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Precio por kg
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                Q{{ number_format($cosecha->precio_kg, 2) }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->total_venta)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Total de Venta
                            </label>
                            <div class="space-y-1">
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Q{{ number_format($cosecha->total_venta, 2) }}
                                </p>
                                @if($cosecha->total_usd)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ${{ number_format($cosecha->total_usd, 2) }} USD 
                                        @if($cosecha->tipo_cambio)
                                            (TC: {{ number_format($cosecha->tipo_cambio, 4) }})
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($cosecha->fecha_venta)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha de Venta
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $cosecha->fecha_venta->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->metodo_pago)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Método de Pago
                            </label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $cosecha->metodo_pago === 'efectivo' ? 'bg-green-100 text-green-800' : 
                                   ($cosecha->metodo_pago === 'transferencia' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $cosecha->metodo_pago)) }}
                            </span>
                        </div>
                    @endif

                    @if($cosecha->observaciones_venta)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Observaciones de Venta
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                {{ $cosecha->observaciones_venta }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Información del Lote (en toda la fila) -->
        @if($cosecha->lote)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 lg:col-span-2">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Información del Lote
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Código del Lote
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-mono">
                            {{ $cosecha->lote->codigo_lote }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cantidad Actual
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ number_format($cosecha->lote->cantidad_actual) }} peces
                        </p>
                    </div>

                    @if($cosecha->lote->especie)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Especie
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $cosecha->lote->especie }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->lote->fecha_siembra)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha de Siembra
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $cosecha->lote->fecha_siembra->format('d/m/Y') }}
                            </p>
                        </div>
                    @endif

                    @if($cosecha->lote->estado)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Estado del Lote
                            </label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($cosecha->lote->estado) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection