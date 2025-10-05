@extends('layouts.app')
@section('title','Entrada #'.$entrada->id)
@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold">Entrada de Inventario #{{ $entrada->id }}</h1>
            <p class="text-sm text-gray-600">Fecha ingreso: {{ $entrada->fecha_ingreso?->format('d/m/Y') }}</p>
            @if($entrada->numero_documento)
                <p class="text-sm">Factura/Documento: <span class="font-mono">{{ $entrada->numero_documento }}</span></p>
            @endif
        </div>
        <div class="text-right">
            <p class="font-semibold">Proveedor</p>
            <p>{{ $entrada->proveedor->nombre ?? '-' }}</p>
            @if($entrada->moneda)
                <p class="text-xs text-gray-500 mt-1">Moneda: {{ $entrada->moneda }} @if($entrada->tipo_cambio) (TC {{ number_format($entrada->tipo_cambio,4) }}) @endif</p>
            @endif
        </div>
    </div>

    <table class="w-full text-sm border mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border text-left">Ítem</th>
                <th class="p-2 border text-right">Cantidad</th>
                <th class="p-2 border text-right">Costo Unit.</th>
                <th class="p-2 border text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entrada->detalles as $d)
                <tr>
                    <td class="p-2 border">{{ $d->item->nombre ?? '-' }}</td>
                    <td class="p-2 border text-right">{{ number_format($d->cantidad,3) }}</td>
                    <td class="p-2 border text-right">{{ number_format($d->costo_unitario,4) }}</td>
                    <td class="p-2 border text-right">{{ number_format($d->subtotal,2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <th colspan="3" class="p-2 border text-right">Subtotal</th>
                <th class="p-2 border text-right">{{ number_format($entrada->subtotal,2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="p-2 border text-right">Impuesto</th>
                <th class="p-2 border text-right">{{ number_format($entrada->impuesto,2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="p-2 border text-right">Total</th>
                <th class="p-2 border text-right font-semibold">{{ number_format($entrada->total,2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($entrada->observaciones)
        <div class="mb-6">
            <p class="font-semibold">Observaciones</p>
            <p class="text-sm">{{ $entrada->observaciones }}</p>
        </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('entradas.index') }}" class="px-4 py-2 bg-gray-300 rounded">Volver</a>
    </div>
</div>
@endsection
