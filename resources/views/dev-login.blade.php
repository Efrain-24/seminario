<!DOCTYPE html>
<html>
<head>
    <title>Login Rápido - Desarrollo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px auto; max-width: 600px; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #005a87; }
        .user-card { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔑 Login Rápido para Desarrollo</h2>
        <p>Selecciona un usuario para hacer login automático:</p>
        
        @foreach($users as $user)
            <div class="user-card">
                <h4>{{ $user->name }}</h4>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Rol:</strong> {{ $user->role ? $user->role->name : 'Sin rol' }}</p>
                <form method="POST" action="{{ route('dev.login') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit" class="btn">Iniciar Sesión como {{ $user->name }}</button>
                </form>
            </div>
        @endforeach
    </div>
</body>
</html>
