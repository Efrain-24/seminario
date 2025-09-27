<?php

namespace Database\Factories;

use App\Models\CosechaParcial;
use App\Models\Lote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CosechaParcial>
 */
class CosechaParcialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CosechaParcial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidadCosechada = fake()->numberBetween(20, 200);
        $pesoKg = fake()->randomFloat(2, $cantidadCosechada * 0.3, $cantidadCosechada * 0.8);
        $destino = fake()->randomElement(['venta', 'muestra', 'otro']);

        $data = [
            'lote_id' => Lote::factory(),
            'fecha' => fake()->dateTimeBetween('-1 month', 'now'),
            'cantidad_cosechada' => $cantidadCosechada,
            'peso_cosechado_kg' => $pesoKg,
            'destino' => $destino,
            'responsable' => fake()->name(),
            'observaciones' => fake()->optional()->sentence(),
        ];

        // Si es venta, agregar datos de venta
        if ($destino === 'venta') {
            $precioKg = fake()->randomFloat(2, 8.0, 25.0);
            $totalVenta = $pesoKg * $precioKg;
            $tipoCambio = 7.8;
            
            $data = array_merge($data, [
                'cliente' => fake()->name() . ' Guatemala',
                'telefono_cliente' => fake()->optional()->phoneNumber(),
                'email_cliente' => fake()->optional()->safeEmail(),
                'fecha_venta' => fake()->dateTimeBetween('-1 week', 'now'),
                'precio_kg' => $precioKg,
                'total_venta' => $totalVenta,
                'tipo_cambio' => $tipoCambio,
                'total_usd' => $totalVenta / $tipoCambio,
                'metodo_pago' => fake()->randomElement(['efectivo', 'transferencia', 'cheque', 'tarjeta']),
                'estado_venta' => 'completada',
                'observaciones_venta' => fake()->optional()->sentence(),
            ]);
        }

        return $data;
    }

    /**
     * Indicate that the cosecha is for sale.
     */
    public function venta(): static
    {
        return $this->state(function (array $attributes) {
            $pesoKg = $attributes['peso_cosechado_kg'] ?? 25.0;
            $precioKg = fake()->randomFloat(2, 10.0, 20.0);
            $totalVenta = $pesoKg * $precioKg;
            $tipoCambio = 7.8;

            return [
                'destino' => 'venta',
                'cliente' => fake()->name() . ' Guatemala',
                'telefono_cliente' => fake()->phoneNumber(),
                'fecha_venta' => now(),
                'precio_kg' => $precioKg,
                'total_venta' => $totalVenta,
                'tipo_cambio' => $tipoCambio,
                'total_usd' => $totalVenta / $tipoCambio,
                'metodo_pago' => 'efectivo',
                'estado_venta' => 'completada',
            ];
        });
    }

    /**
     * Indicate that the cosecha is just a sample.
     */
    public function muestra(): static
    {
        return $this->state(fn (array $attributes) => [
            'destino' => 'muestra',
        ]);
    }
}