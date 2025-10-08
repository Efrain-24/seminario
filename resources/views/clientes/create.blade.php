@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="max-w-lg mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Nuevo Cliente</h2>
    <form action="{{ route('clientes.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full border rounded px-3 py-2" required>
            @error('nombre')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Documento</label>
            <input type="text" name="documento" value="{{ old('documento') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex justify-end">
            <a href="{{ route('clientes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
        </div>
    </form>
</div>
@endsection
