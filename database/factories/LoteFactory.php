<?php

namespace Database\Factories;

use App\Models\Lote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lote>
 */
class LoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo_lote' => 'TEST-' . fake()->year() . '-' . fake()->numberBetween(100, 999),
            'fecha_siembra' => fake()->dateTimeBetween('-6 months', '-1 month'),
            'cantidad_inicial' => $cantidadInicial = fake()->numberBetween(500, 2000),
            'cantidad_actual' => fake()->numberBetween(200, $cantidadInicial),
            'especie' => fake()->randomElement(['Tilapia', 'Carpa', 'Bagre', 'Mojarra']),
            'densidad_inicial' => fake()->randomFloat(2, 0.5, 2.0),
            'densidad_actual' => fake()->randomFloat(2, 0.3, 1.8),
            'temperatura_agua' => fake()->randomFloat(1, 22.0, 28.0),
            'ph_agua' => fake()->randomFloat(1, 6.5, 8.5),
            'oxigeno_disuelto' => fake()->randomFloat(1, 4.0, 8.0),
            'estado' => fake()->randomElement(['activo', 'cosechado', 'mantenimiento']),
            'ubicacion' => 'Estanque ' . fake()->numberBetween(1, 20),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }
}