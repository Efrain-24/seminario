<?php
// Vista para ocultar módulos de aplicación por rol
// resources/views/roles/ocultar-modulos.blade.php
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ocultar módulos de aplicación para el rol: ') . $role->display_name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('roles.ocultar-modulos.update', $role) }}">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg font-bold mb-4">Selecciona los módulos que este rol podrá ver:</h3>
                        @php
                            $modulos = [
                                'unidades' => 'Unidades',
                                'produccion' => 'Producción',
                                'inventarios' => 'Inventarios',
                                'usuarios_roles' => 'Usuarios y Roles',
                                'acciones_correctivas' => 'Acciones Correctivas',
                                'protocolos_limpieza' => 'Protocolos y Limpieza',
                                'ventas' => 'Ventas (Cosechas)',
                                'compras_proveedores' => 'Compras y Proveedores',
                                'reportes' => 'Reportes',
                            ];
                            $roleModules = $role->modules ?? [];
                            // Si $roleModules es una colección, convertir a array de strings
                            $roleModulesArr = is_a($roleModules, 'Illuminate\\Database\\Eloquent\\Collection') ? $roleModules->pluck('module')->toArray() : (array)$roleModules;
                        @endphp
                        <div class="grid grid-cols-2 gap-2 mb-6">
                            @foreach($modulos as $key => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="modules[]" value="{{ $key }}" class="form-checkbox h-5 w-5 text-indigo-600" {{ in_array($key, old('modules', $roleModulesArr)) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-end">
                            <a href="{{ route('roles.show', $role) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-2">Cancelar</a>
                            <x-primary-button class="shadow-md">Guardar cambios</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
