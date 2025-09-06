<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccionCorrectiva;
use App\Models\User;
use Illuminate\Support\Str;

class AccionCorrectivaSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::pluck('id')->toArray();
        if (empty($usuarios)) {
            // Si no hay usuarios, crea uno de prueba
            $user = User::factory()->create(['name' => 'Usuario Demo', 'email' => 'demo@example.com']);
            $usuarios = [$user->id];
        }
        foreach (range(1, 10) as $i) {
            AccionCorrectiva::create([
                'titulo' => 'Acción Correctiva ' . $i,
                'descripcion' => 'Descripción de la acción correctiva número ' . $i,
                'user_id' => $usuarios[array_rand($usuarios)],
                'fecha_detectada' => now()->subDays(rand(1, 30)),
                'fecha_limite' => now()->addDays(rand(1, 30)),
                'estado' => collect(['pendiente', 'en_progreso', 'completada', 'cancelada'])->random(),
                'observaciones' => Str::random(20),
            ]);
        }
    }
}
