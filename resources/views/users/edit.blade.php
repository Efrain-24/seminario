<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Usuario') }}: {{ $user->name }}
            </h2>
            <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Rol -->
                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Rol')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @foreach(\App\Models\User::getAvailableRoles() as $value => $label)
                                    <option value="{{ $value }}" {{ (old('role', $user->role) === $value) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                            <!-- Estado -->
                            <div class="mb-4">
                                <x-input-label for="estado" :value="__('Estado')" />
                                <select id="estado" name="estado" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="activo" {{ old('estado', $user->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado', $user->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>
                            <!-- Ocultar módulos (solo para admin) -->
@if(Auth::user() && Auth::user()->isAdmin())
    <hr class="my-6 border-gray-300 dark:border-gray-600">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Módulos visibles para este usuario</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Selecciona los módulos que el usuario podrá ver en el sistema:</p>
        @php
            $modulos = [
                'unidades' => 'Unidades',
                'produccion' => 'Producción',
                'inventarios' => 'Inventarios',
                'usuarios' => 'Usuarios',
                'roles' => 'Roles',
                'acciones_correctivas' => 'Acciones Correctivas',
                'protocolos' => 'Protocolos',
                'limpieza' => 'Limpieza',
                'ventas' => 'Ventas',
                'compras' => 'Compras',
                'proveedores' => 'Proveedores',
                'bitacora' => 'Bitácora',
            ];
            $userModules = $user->modules ? $user->modules->pluck('module')->toArray() : [];
        @endphp
        <div class="grid grid-cols-2 gap-2 mt-2">
            @foreach($modulos as $key => $label)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="modules[]" value="{{ $key }}" class="form-checkbox h-5 w-5 text-indigo-600" {{ in_array($key, old('modules', $userModules)) ? 'checked' : '' }}>
                    <span class="ml-2">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('modules')" class="mt-2" />
    </div>
@endif
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 mr-2">
                                Cancelar
                            </a>
                            <x-primary-button class="shadow-md">
                                {{ __('Actualizar Usuario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
