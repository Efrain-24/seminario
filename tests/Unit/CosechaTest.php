<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CosechaParcial;
use App\Models\Lote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CosechaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $lote;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Usuario Test',
            'email' => 'tesst@example.com',
            'password' => bcrypt('password'),
            'role' => 'generico'
        ]);

        $this->lote = Lote::create([
            'codigo_lote' => 'LOTE-TEST-001',
            'cantidad_actual' => 500,
            'cantidad_inicial' => 1000,
            'nombre' => 'Lote de Prueba',
            'especie' => 'Tilapia',
            'fecha_inicio' => now()->subDays(30)
        ]);
    }

    /** @test */
    public function puede_ingresar_nueva_cosecha()
    {
        // Crear una nueva cosecha
        $cosecha = CosechaParcial::create([
            'lote_id' => $this->lote->id,
            'fecha' => '',
            'cantidad_cosechada' => 100,
            'peso_cosechado_kg' => 45.5,
            'destino' => 'venta',
            'responsable' => 'Juan Pérez',
            'observaciones' => 'Primera cosecha del lote',
            'user_id' => $this->user->id,
        ]);

        // Verificar
        $this->assertDatabaseHas('cosechas_parciales', [
            'id' => $cosecha->id,
            'lote_id' => $this->lote->id,
            'cantidad_cosechada' => 1,
            'peso_cosechado_kg' => 45.5,
            'destino' => 'venta',
            'responsable' => 'Juan Pérez',
            'observaciones' => 'Primera cosecha del lote',
            'user_id' => $this->user->id,
        ]);
        //confirmar
        $this->assertNotNull($cosecha->id);
        echo "Cosecha ingresada exitosamente con ID: " . $cosecha->id;
    }
}