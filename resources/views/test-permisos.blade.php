<!DOCTYPE html>
<html>
<head>
    <title>Test Permisos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; } 
        .error { color: red; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Permisos - Módulo Alimentación</h1>
        
        <div style="background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h2>👤 Información del Usuario</h2>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Rol:</strong> {{ $user->role }}</p>
            <p><strong>Es Admin:</strong> {{ $user->isAdmin() ? '✅ SI' : '❌ NO' }}</p>
        </div>

        <div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>🔐 Permisos de Alimentación:</h3>
            <ul style="list-style: none; padding: 0;">
                @foreach($permisos as $permiso => $tiene)
                    <li style="padding: 5px 0;">
                        <span style="font-weight: bold;">{{ $permiso }}:</span> 
                        <span class="{{ $tiene ? 'success' : 'error' }}">
                            {{ $tiene ? '✅ PERMITIDO' : '❌ DENEGADO' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div style="background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>🧪 Enlaces de Prueba:</h3>
            <ul>
                <li><a href="{{ route('test.alimentacion.simple') }}" target="_blank">🔗 Probar Alimentación (Sin Middleware)</a></li>
                <li><a href="{{ route('alimentacion.index') }}" target="_blank">🔗 Ir a Alimentación (Con Middleware)</a></li>
                <li><a href="{{ route('dashboard') }}" target="_blank">🔗 Ir al Dashboard</a></li>
                <li><a href="{{ route('login') }}" target="_blank">🔗 Página de Login</a></li>
            </ul>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>⚠️ Instrucciones:</h3>
            <ol>
                <li>Si los permisos muestran "PERMITIDO" pero aún obtienes error 403, el problema está en el middleware</li>
                <li>Prueba primero el enlace "Sin Middleware" para verificar que el controlador funciona</li>
                <li>Luego prueba el enlace "Con Middleware" para verificar los permisos</li>
                <li>Si necesitas autenticarte, ve al Dashboard y usa: admin@piscicultura.com / admin123</li>
            </ol>
        </div>
    </div>
</body>
</html>
