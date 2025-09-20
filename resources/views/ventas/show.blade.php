@extends('layouts.app')

@section('title', 'Detalle de Venta - ' . $venta->codigo_venta)

@section('content')
<div class="container mx-auto px-6 py-8">
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