<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Usuario') }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->hasPermission('users.update'))
                    <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                @endif
                
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Información del Usuario -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Avatar y Nombre -->
                        <div class="flex items-center space-x-4 md:col-span-2">
                            <div class="flex-shrink-0 h-20 w-20">
                                <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ $user->email }}
                                </p>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Email Verificado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Email Pendiente
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información Detallada -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Estado</h4>
                            @if($user->estado === 'activo')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Activo</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Inactivo</span>
                            @endif
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Rol del Usuario</h4>
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $user->roleDisplayName }}
                                </span>
                            @elseif($user->role === 'manager')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $user->roleDisplayName }}
                                </span>
                            @elseif($user->role === 'empleado')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ $user->roleDisplayName }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $user->roleDisplayName }}
                                </span>
                            @endif
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Fecha de Registro</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Última Actualización</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        @if($user->email_verified_at)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Email Verificado el</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif

                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Estado de Contraseña</h4>
                            @if($user->hasTemporaryPassword())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contraseña Temporal
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contraseña Personal
                                </span>
                            @endif
                            @if($user->password_changed_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Última actualización: 
                                    @if(is_string($user->password_changed_at))
                                        {{ $user->password_changed_at }}
                                    @else
                                        {{ $user->password_changed_at->format('d/m/Y H:i:s') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones del Usuario -->
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Acciones</h4>
                        <div class="flex flex-wrap gap-4">
                            <!-- Botón Editar -->
                            <a href="{{ route('users.edit', $user) }}" style="background-color: #2563eb !important; color: white !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: bold;">
                                Editar Usuario
                            </a>
                            
                            <!-- Botón Reset de Contraseña -->
                            @if($user->hasTemporaryPassword() || !$user->password_changed_at)
                            <form method="POST" action="{{ route('users.reset-password', $user) }}" onsubmit="return confirm('¿Generar nueva contraseña temporal y enviar por email?')" style="display: inline;">
                                @csrf
                                <button type="submit" style="background-color: #d97706 !important; color: white !important; padding: 12px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                                    🔑 Resetear Contraseña
                                </button>
                            </form>
                            @endif
                            
                            <!-- Botón Eliminar -->
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background-color: #dc2626 !important; color: white !important; padding: 12px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                                    Eliminar Usuario
                                </button>
                            </form>
                            
                            <!-- Botón Volver -->
                            <a href="{{ route('users.index') }}" style="background-color: #4b5563 !important; color: white !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: bold;">
                                ← Volver a la Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
