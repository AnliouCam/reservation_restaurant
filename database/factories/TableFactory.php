<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => 'T' . fake()->unique()->numberBetween(1, 100),
            'capacite' => fake()->randomElement([2, 4, 6, 8]),
            'statut' => 'disponible',
            'zone_id' => Zone::factory(),
        ];
    }
}
