<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cambiar Contraseña') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(Auth::user()->hasTemporaryPassword())
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Contraseña Temporal Detectada
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>Estás usando una contraseña temporal. Por tu seguridad, debes cambiarla antes de continuar usando el sistema.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Contraseña Actual -->
                        <div class="mb-4">
                            <x-input-label for="current_password" :value="__('Contraseña Actual')" />
                            <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autofocus />
                            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Nueva Contraseña')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <div class="text-xs text-gray-500 mt-1">
                                <p class="font-medium">La contraseña debe cumplir los siguientes requisitos:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Mínimo 8 caracteres</li>
                                    <li>Al menos una letra minúscula</li>
                                    <li>Al menos una letra mayúscula</li>
                                    <li>Al menos un número</li>
                                    <li>Al menos un carácter especial (@$!%*#?&._-)</li>
                                </ul>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        </div>

                        <div class="flex items-center justify-end">
                            @if(!Auth::user()->hasTemporaryPassword())
                                <a href="{{ route('dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 mr-2">
                                    Cancelar
                                </a>
                            @endif
                            <x-primary-button class="shadow-md">
                                {{ __('Cambiar Contraseña') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
