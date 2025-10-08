
@extends('layouts.app')

@section('title', 'Factura de Venta - ' . $venta->codigo_venta)

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container" style="max-width: 800px; margin: 0 auto;">
    <!-- Encabezado de la factura -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-14 mb-2">
            <h2 class="text-2xl font-bold text-gray-900">Piscicultura</h2>
            <p class="text-gray-600 text-sm">Dirección de la empresa<br>Tel: 555-1234<br>Email: info@piscicultura.com</p>
        </div>
        <div class="text-right">
            <div style="border: 1px solid #222; padding: 32px 32px 16px 32px; background: #fff; font-family: 'Segoe UI', Arial, sans-serif;">
            <h3 class="text-xl font-bold text-gray-700">Factura</h3>
            <p class="text-gray-600">No. <span class="font-mono">{{ $venta->codigo_venta }}</span></p>
            <p class="text-gray-600">Fecha: <span class="font-mono">{{ $venta->fecha_venta->format('d/m/Y') }}</span></p>
        </div>
    </div>

    <!-- Datos del cliente -->
    <div class="mb-8">
        <h4 class="font-semibold text-gray-800 mb-2">Cliente</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div><span class="font-medium">Nombre:</span> {{ $venta->cliente }}</div>
            <div><span class="font-medium">Código:</span> {{ $venta->cliente_codigo ?? '-' }}</div>
            <div><span class="font-medium">Teléfono:</span> {{ $venta->telefono_cliente ?? '-' }}</div>
            <div><span class="font-medium">Email:</span> {{ $venta->email_cliente ?? '-' }}</div>
            <div class="md:col-span-2"><span class="font-medium">Dirección:</span> {{ $venta->cliente_direccion ?? '-' }}</div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="mb-8">
        <table class="min-w-full text-sm border border-gray-300 rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b text-left">Producto</th>
                    <th class="px-4 py-2 border-b">SKU</th>
                    <th class="px-4 py-2 border-b">Cantidad</th>
                    <th class="px-4 py-2 border-b">Precio Unitario</th>
                    <th class="px-4 py-2 border-b">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($venta->detalles as $detalle)
                    @php $subtotal += $detalle->total; @endphp
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $detalle->nombre_articulo }}</td>
                        <td class="px-4 py-2 border-b text-center">{{ optional(App\Models\InventarioItem::find($detalle->articulo_id))->sku ?? '-' }}</td>
                        <td class="px-4 py-2 border-b text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                        <td class="px-4 py-2 border-b text-right">Q{{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-4 py-2 border-b text-right">Q{{ number_format($detalle->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="flex justify-end mb-8">
        <div class="w-full md:w-1/2">
            <div class="flex justify-between py-2 border-b">
                <span class="font-medium">Subtotal</span>
                <span>Q{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="font-medium">Total</span>
                <span class="text-lg font-bold">Q{{ number_format($venta->total ?? $subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="font-medium">Total USD</span>
                <span>${{ number_format($venta->total_usd ?? 0, 2) }} (TC: {{ number_format($venta->tipo_cambio ?? 0, 4) }})</span>
            </div>
        </div>
    </div>

    <!-- Observaciones y pie de página -->
    @if($venta->observaciones)
        <div class="mb-6">
            <span class="font-medium text-gray-700">Observaciones:</span>
            <div class="bg-gray-50 border border-gray-200 rounded p-3 mt-1 text-gray-800 text-sm">{{ $venta->observaciones }}</div>
        </div>
    @endif

    <div class="text-center text-gray-500 text-xs mt-8 border-t pt-4">
        ¡Gracias por su compra!<br>
        Esta factura es válida como comprobante de venta.
    </div>
            </div>
</div>
@endsection