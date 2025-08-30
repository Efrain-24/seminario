<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-full max-w-md text-center">
            @if(isset($already) && $already)
                <h1 class="text-2xl font-bold text-green-600 mb-4">¡Correo ya verificado!</h1>
                <p class="mb-6">Tu correo electrónico ya había sido verificado previamente.</p>
            @else
                <h1 class="text-2xl font-bold text-green-600 mb-4">¡Correo verificado correctamente!</h1>
                <p class="mb-6">Tu correo electrónico ha sido validado exitosamente. Ahora puedes iniciar sesión.</p>
            @endif
            <a href="{{ route('login') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">Iniciar sesión</a>
        </div>
    </div>
</x-app-layout>
