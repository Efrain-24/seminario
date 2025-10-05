<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CosechaParcial;
use App\Models\Lote;
use App\Models\TipoCambio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VentaControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $lote;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->lote = Lote::factory()->create([
            'cantidad_actual' => 500,
        ]);

        // Crear tipo de cambio
        TipoCambio::create([
            'tasa' => 7.8,
            'fecha' => now(),
        ]);
    }

    /** @test */
    public function puede_mostrar_formulario_crear_venta()
    {
        $response = $this->actingAs($this->user)
                         ->get('/cosechas/create');

        $response->assertStatus(200);
        $response->assertSee('Crear Nueva Cosecha');
        $response->assertSee('Cliente');
        $response->assertSee('Precio por libra');
    }

    /** @test */
    public function puede_crear_venta_por_libra_via_post()
    {
        $pesoKg = 20.0;
        $precioLibra = 12.50;
        $pesoLibras = $pesoKg * 2.20462;
        $totalEsperado = $pesoLibras * $precioLibra;

        $datosVenta = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 40,
            'peso_cosechado_kg' => $pesoKg,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'observaciones' => 'Venta de prueba',
            // Datos de venta
            'cliente' => 'Juan Pérez Guatemala',
            'telefono_cliente' => '1234-5678',
            'unidad_venta' => 'libra',
            'precio_unitario' => $precioLibra,
        ];

        $response = $this->actingAs($this->user)
                         ->post('/cosechas', $datosVenta);

        $response->assertStatus(302); // Redirect después de crear
        $response->assertRedirect('/cosechas');

        // Verificar que se creó en la base de datos
        $this->assertDatabaseHas('cosechas_parciales', [
            'cliente' => 'Juan Pérez Guatemala',
            'destino' => 'venta',
            'estado_venta' => 'completada',
        ]);
    }

    /** @test */
    public function puede_crear_venta_por_pez_via_post()
    {
        $cantidadPeces = 80;
        $precioPorPez = 9.75;
        $totalEsperado = $cantidadPeces * $precioPorPez;

        $datosVenta = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => $cantidadPeces,
            'peso_cosechado_kg' => 35.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'observaciones' => 'Venta por pez',
            // Datos de venta
            'cliente' => 'María López Guatemala',
            'telefono_cliente' => '9876-5432',
            'unidad_venta' => 'pez',
            'precio_unitario' => $precioPorPez,
        ];

        $response = $this->actingAs($this->user)
                         ->post('/cosechas', $datosVenta);

        $response->assertStatus(302);

        // Verificar creación
        $this->assertDatabaseHas('cosechas_parciales', [
            'cliente' => 'María López Guatemala',
            'destino' => 'venta',
            'estado_venta' => 'completada',
            'observaciones_venta' => 'Venta por pez',
        ]);
    }

    /** @test */
    public function valida_campos_requeridos_para_venta()
    {
        $datosIncompletos = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 40,
            'peso_cosechado_kg' => 20.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            // Faltan: cliente, precio_unitario, unidad_venta
        ];

        $response = $this->actingAs($this->user)
                         ->post('/cosechas', $datosIncompletos);

        $response->assertStatus(302); // Redirect con errores
        $response->assertSessionHasErrors(['cliente', 'precio_unitario', 'unidad_venta']);
    }

    /** @test */
    public function puede_generar_ticket_pdf()
    {
        // Crear una venta primero
        $cosecha = CosechaParcial::create([
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 50,
            'peso_cosechado_kg' => 25.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'cliente' => 'Cliente Test Guatemala',
            'telefono_cliente' => '1234-5678',
            'precio_kg' => 15.0,
            'total_venta' => 375.0,
            'tipo_cambio' => 7.8,
            'total_usd' => 48.08,
            'estado_venta' => 'completada',
        ]);

        $response = $this->actingAs($this->user)
                         ->get("/cosechas/{$cosecha->id}/ticket");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function actualiza_stock_del_lote_al_crear_venta()
    {
        $stockInicial = $this->lote->cantidad_actual;
        $cantidadCosechada = 100;

        $datosVenta = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => $cantidadCosechada,
            'peso_cosechado_kg' => 45.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'cliente' => 'Cliente Stock Test',
            'unidad_venta' => 'libra',
            'precio_unitario' => 14.0,
        ];

        $this->actingAs($this->user)
             ->post('/cosechas', $datosVenta);

        // Verificar que el stock se redujo
        $this->lote->refresh();
        $this->assertEquals($stockInicial - $cantidadCosechada, $this->lote->cantidad_actual);
    }

    /** @test */
    public function no_permite_cosecha_mayor_al_stock()
    {
        $this->lote->update(['cantidad_actual' => 50]);

        $datosVenta = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 100, // Más que el stock (50)
            'peso_cosechado_kg' => 45.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'cliente' => 'Cliente Test',
            'unidad_venta' => 'pez',
            'precio_unitario' => 8.0,
        ];

        $response = $this->actingAs($this->user)
                         ->post('/cosechas', $datosVenta);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function puede_ver_lista_de_cosechas_con_ventas()
    {
        // Crear algunas cosechas con ventas
        CosechaParcial::create([
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 30,
            'peso_cosechado_kg' => 15.0,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'cliente' => 'Cliente Lista 1',
            'estado_venta' => 'completada',
            'total_venta' => 450.0,
        ]);

        CosechaParcial::create([
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 25,
            'peso_cosechado_kg' => 12.0,
            'destino' => 'muestra',
            'responsable' => $this->user->name,
        ]);

        $response = $this->actingAs($this->user)
                         ->get('/cosechas');

        $response->assertStatus(200);
        $response->assertSee('Cliente Lista 1');
        $response->assertSee('Q 450.00'); // Mostrar total en quetzales
        $response->assertSee('Descargar Ticket');
    }

    /** @test */
    public function calcula_correctamente_totales_en_quetzales()
    {
        $pesoKg = 10.0;
        $precioLibra = 16.0;
        $pesoLibras = $pesoKg * 2.20462; // 22.0462 libras
        $totalEsperadoQ = $pesoLibras * $precioLibra; // ≈ Q352.74
        $totalEsperadoUSD = $totalEsperadoQ / 7.8; // ≈ $45.22

        $datosVenta = [
            'lote_id' => $this->lote->id,
            'fecha' => now()->format('Y-m-d'),
            'cantidad_cosechada' => 20,
            'peso_cosechado_kg' => $pesoKg,
            'destino' => 'venta',
            'responsable' => $this->user->name,
            'cliente' => 'Cliente Cálculo',
            'unidad_venta' => 'libra',
            'precio_unitario' => $precioLibra,
        ];

        $this->actingAs($this->user)
             ->post('/cosechas', $datosVenta);

        // Verificar que los cálculos son correctos
        $cosecha = CosechaParcial::where('cliente', 'Cliente Cálculo')->first();
        
        $this->assertNotNull($cosecha);
        $this->assertEqualsWithDelta($totalEsperadoQ, $cosecha->total_venta, 0.01);
        $this->assertEqualsWithDelta($totalEsperadoUSD, $cosecha->total_usd, 0.01);
        $this->assertEquals(7.8, $cosecha->tipo_cambio);
    }
}