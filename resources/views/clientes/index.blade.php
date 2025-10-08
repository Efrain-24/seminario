@extends('layouts.app')

@section('title', 'Clientes')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">Nuevo Cliente</a>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($clientes as $cliente)
                <tr>
                    <td class="px-4 py-2">{{ $cliente->nombre }}</td>
                    <td class="px-4 py-2">{{ $cliente->telefono }}</td>
                    <td class="px-4 py-2">{{ $cliente->email }}</td>
                    <td class="px-4 py-2">{{ $cliente->documento }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:underline mr-2">Editar</a>
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar cliente?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clientes->links() }}</div>
</div>
@endsection
