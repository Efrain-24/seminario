<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = new Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Crear acción correctiva
$user = App\Models\User::first();
if ($user) {
    $accion = App\Models\AccionCorrectiva::create([
        'titulo' => 'Reparar sistema de filtración',
        'descripcion' => 'El sistema de filtracón del tanque 3 presenta fallas en la bomba principal que requiere reparación inmediata',
        'fecha_prevista' => '2025-09-20',
        'fecha_limite' => '2025-09-25',
        'user_id' => $user->id,
        'estado' => 'pendiente'
    ]);
    
    echo "✅ Acción correctiva creada con ID: " . $accion->id . "\n";
    echo "📋 Título: " . $accion->titulo . "\n";
    echo "👤 Responsable: " . $accion->responsable->name . "\n";
} else {
    echo "❌ No se encontró ningún usuario\n";
}
