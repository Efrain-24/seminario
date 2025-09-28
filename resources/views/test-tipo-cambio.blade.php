<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Tipo de Cambio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-4">Test - Tipo de Cambio</h1>
        
        <x-tipo-cambio />
        
        <div class="mt-8 p-4 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-2">Información del Sistema</h2>
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Laravel:</strong> {{ app()->version() }}</p>
            <p><strong>Servidor:</strong> {{ request()->getHost() }}:{{ request()->getPort() }}</p>
        </div>
    </div>
</body>
</html>