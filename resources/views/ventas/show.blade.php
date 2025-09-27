@extends('layouts.app')

@section('title', 'Detalle de Venta - ' . $venta->codigo_venta)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Notificación especial para ticket cuando se completa la venta -->
    @if(session('ticket_disponible') && $venta->estado === 'completada')
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
                            <a href="{{ route('ventas.ticket.ver', $venta) }}" 
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
                            <a href="{{ route('ventas.ticket.descargar', $venta) }}" 
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
                Detalle de Venta
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ $venta->codigo_venta }}
            </p>
        </div>
        <div class="flex space-x-3">
            <!-- Botones de Tickets -->
            <div class="flex space-x-2">
                <a href="{{ route('ventas.ticket.ver', $venta) }}" 
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
                <a href="{{ route('ventas.ticket.descargar', $venta) }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Descargar Ticket
                </a>
            </div>
            
            <a href="{{ route('ventas.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ← Volver
            </a>
            @if($venta->estado === 'pendiente')
                <form action="{{ route('ventas.completar', $venta) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Completar Venta
                    </button>
                </form>
            @endif
            <a href="{{ route('ventas.edit', $venta) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Información de la Venta -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Información de la Venta
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Estado
                    </label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $venta->estado_badge }}">
                        {{ ucfirst($venta->estado) }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha de Venta
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $venta->fecha_venta->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cantidad
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ number_format($venta->cantidad_kg, 2) }} kg
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Precio por kg
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        Q{{ number_format($venta->precio_kg, 2) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Total
                    </label>
                    <div class="space-y-1">
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Q{{ number_format($venta->total, 2) }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ${{ number_format($venta->total_usd, 2) }} USD (TC: {{ number_format($venta->tipo_cambio, 4) }})
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Método de Pago
                    </label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $venta->metodo_pago_badge }}">
                        {{ ucfirst(str_replace('_', ' ', $venta->metodo_pago)) }}
                    </span>
                </div>

                @if($venta->observaciones)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Observaciones
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                            {{ $venta->observaciones }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Información del Cliente
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $venta->cliente }}
                    </p>
                </div>

                @if($venta->telefono_cliente)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Teléfono
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $venta->telefono_cliente }}
                        </p>
                    </div>
                @endif

                @if($venta->email_cliente)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $venta->email_cliente }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información de la Cosecha -->
        @if($venta->cosechaParcial)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 lg:col-span-2">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    Información de la Cosecha
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Lote
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $venta->cosechaParcial->lote->codigo_lote ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fecha de Cosecha
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $venta->cosechaParcial->fecha->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Peso Cosechado
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ number_format($venta->cosechaParcial->peso_cosechado_kg, 2) }} kg
                        </p>
                    </div>

                    @if($venta->cosechaParcial->destino)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Destino Original
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $venta->cosechaParcial->destino }}
                            </p>
                        </div>
                    @endif

                    @if($venta->cosechaParcial->responsable)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Responsable de Cosecha
                            </label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $venta->cosechaParcial->responsable }}
                            </p>
                        </div>
                    @endif
                </div>

                @if($venta->cosechaParcial->observaciones)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Observaciones de la Cosecha
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                            {{ $venta->cosechaParcial->observaciones }}
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection